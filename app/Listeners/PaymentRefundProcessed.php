<?php

namespace App\Listeners;

use App\Constants\Gatways;
use App\Events\PaymentRefund;
use App\Models\Gatways\Hyperpay;
use App\Models\Order\Order;
use App\Models\Order\Transaction;
use App\Constants\OrderPaymentStatus;
use App\Events\InstantPushNotification;
use App\Models\User;

class PaymentRefundProcessed
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PaymentRefundProcessed  $event
     * @return void
     */
    public function handle(PaymentRefund $event)
    {
        if (!$event->order_id) {
            return true;
        }
        $order_id = $event->order_id;
        $order = Order::find($order_id);
        if(!$order){
            return true;
        }

        $transactions = Transaction::where('order_id', $order->id)->get();
        if(empty($transactions)){
            return true;
        }
        foreach ($transactions as $key => $transaction) {
            if ($transaction->status == OrderPaymentStatus::COMPLETED) {
                    switch ($transaction->gatway_type) {
                        //request to hyperpay payment gatway
                        case Gatways::HYPERPAY:
                                $hyperpay = new Hyperpay;
                                $result =  $hyperpay->Refund($transaction,$order);
                                if($result['status'] == OrderPaymentStatus::SUCCESS ||  $result['status'] == OrderPaymentStatus::PENDING){
                                    OrderTransactionHistory($transaction->id,OrderPaymentStatus::REFUNDED);
                                    Transaction::where('id', $transaction->id)->update([
                                        'status' => OrderPaymentStatus::REFUNDED,
                                        'content' => isset($result['result']['description']) ? $result['result']['description'] : $transaction->content,
                                    ]);
                                    return true;
                                }else{
                                    OrderTransactionHistory($transaction->id,OrderPaymentStatus::REFUNDED_FAIL);
                                    Transaction::where('id', $transaction->id)->update([
                                        'status' => OrderPaymentStatus::REFUNDED_FAIL,
                                        'content' => isset($result['result']['description']) ? $result['result']['description'] : $transaction->content,
                                    ]);

                                    $admin_id = User::whereHas(
                                        'roles', function($q){
                                            $q->where('guard_name', 'web');
                                            $q->where('name','Admin');
                                        }
                                    )->first()->id;

                                    $title =  __('order.customer_message_push_title.Refund process faild',['order_number' => $order->order_number]);
                                    $body =   __('order/customer_message_push_body.Refund process faild, Please update payment status',['order_number' => $order->order_number]);

                                    self::OrderNotification($admin_id,$order->id,$title,$body);
                                }
                                return true;
                        break;

                        default:
                            OrderTransactionHistory($transaction->id,OrderPaymentStatus::REFUNDED);
                            Transaction::where('id', $transaction->id)->update(['status' => OrderPaymentStatus::REFUNDED]);
                            return true;
                        break;
                    }
                }else{
                    OrderTransactionHistory($transaction->id,OrderPaymentStatus::CANCELLED);
                    Transaction::where('id', $transaction->id)->update(['status' => OrderPaymentStatus::CANCELLED]);
                    return true;
                }
        }
        return true;
    }


     /**
     * To send refund fail notification to admin
     *
     * @param [type] $id
     * @param [type] $orderId
     * @param [type] $title
     * @param [type] $body
     * @return void
     */

     public static function OrderNotification($id, $orderId, $title, $body)
     {
         event(new InstantPushNotification($id, [
                 "title" => $title,
                 "body" => $body,
                 "role" => 'admin',
                 "data" => [
                     'order_id' => $orderId,
                 ],
             ]));
     }


}
