<?php

namespace App\Http\Livewire\Order;

use App\Models\User;
use Livewire\Component;
use App\Models\Order\Order; 
use App\Events\PaymentRefund;
use App\Constants\OrderStatus;
use App\Constants\OrderMessages;
use App\Constants\PaymentMessage;
use App\Models\Order\Transaction;
use App\Models\Driver\UserDriver;
use App\Models\Order\OrderDelivery;
use Illuminate\Support\Facades\DB;
use App\Constants\OrderStatusLabel;
use App\Constants\OrderPaymentStatus;
use App\Constants\OrderDriverStatus;
use App\Events\InstantPushNotification;
use App\Constants\OrderProviderMessages;
use App\Constants\OrderPaymentStatusLabel;
use App\Http\Filters\DriverAssignFilter;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Details extends Component
{
    use AuthorizesRequests;
    public $order;
    public $driver;
    public $allStatus;
    public $order_id;
    public $statusLabels;
    public $orderPaymentStatus;
    public $orderPaymentStatusLabel;
    public $search;
    public $searchResultDrivers;
    public $selected_user_id = "";
    protected $listeners = ['statusUpdate','paymentStatusUpdate','confirm','PaymentRefund','driverSubmit'];
   
    public function mount($id) {
        $this->order_id = $id;
        $this->order = Order::with('store', 'orderProducts', 'TransactionHistory', 'OrderDriver', 'orderUpdateHistory', 'user', 'store.storeAddress')->find($this->order_id);
        $this->driver = $this->order->OrderDriver ? User::find($this->order->OrderDriver->assing_to_id) : '';
        
        $orderStatusConstant = new OrderStatus();
        $this->allStatus = $orderStatusConstant->getConstants(); 
        
        $orderStatusLabelConstant = new OrderStatusLabel();
        $this->statusLabels = $orderStatusLabelConstant->getConstants(); 

     
        $PaymentStatus = new OrderPaymentStatus();
        $this->orderPaymentStatus = $PaymentStatus->getConstants();
      
        $StatusLabelConstant = new OrderPaymentStatusLabel();
        $this->orderPaymentStatusLabel =   $StatusLabelConstant->getConstants();

        $this->searchResultDrivers = collect();
       
    }

    public function render()
    {
        return view('livewire.order.details');
    }


    /**
     * update order status
     *
     * @return response()
     */
    public function statusUpdate($status)
    {   

        $order = Order::findOrfail($this->order_id);
        if ($status == $order->order_status) {
            $this->dispatchBrowserEvent('alert', 
            ['type' => 'warning',  'message' => __('orders.Order status already updated')]);
            return false;
        }
        $order->UpdateHistory($this->order_id,$status);
        $order->update(['order_status' => $status]);
        $this->order = Order::with('store', 'orderProducts', 'TransactionHistory', 'OrderDriver', 'orderUpdateHistory', 'user', 'store.storeAddress')->find($this->order_id);     
       
        if ($status == OrderStatus::CANCELLED) {
            $transaction = Transaction::where('order_id', $this->order_id)->where('status',OrderPaymentStatus::COMPLETED)->count();
            if ($transaction > 0) {
                $this->dispatchBrowserEvent('swal:confirm', [
                    'action' => 'PaymentRefund',
                    'type' => 'warning',  
                    'confirmButtonText' => __('orders.Yes refund it'),
                    'cancelButtonText' => __('orders.No cancel'),
                    'message' => __('orders.Are you sure'), 
                    'text' => __('orders.you refund an order, you send payment back to the customer')
                ]);    
            }
        }

        $message = $this->_getMessages($status,$this->order->order_number);  
        event(new InstantPushNotification($this->order->user_id, [
            "title" =>  $message['title'],
            "body" =>   $message['body'],
            "data" => [
                'order_id' => $this->order->id,
                'type' => 'order',
                'status' => $status,
            ]
        ]));
   
    }




    public function paymentStatusUpdate($TxnStatus ,$id){
      $transaction = Transaction::findOrFail($id);

      if ($TxnStatus == $transaction->status) {
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'warning',  'message' => __('Payment status already updated')]);
        return false;
      }
    
      OrderTransactionHistory($id ,$TxnStatus);
      
      $transaction->whereId($id)->update(['status'=>$TxnStatus]);
      $this->order = Order::with('store', 'orderProducts', 'TransactionHistory', 'OrderDriver', 'orderUpdateHistory', 'user', 'store.storeAddress')->find($this->order_id);  
      $message = $this->_getTxnMessage($TxnStatus,$this->order->order_number);  
      if ($TxnStatus == OrderPaymentStatus::REFUNDED) {
        $this->dispatchBrowserEvent('swal:confirm', [
            'action' => 'PaymentRefund',
            'type' => 'warning',  
            'confirmButtonText' => __('orders.Yes refund it'),
            'cancelButtonText' => __('orders.No cancel'),
            'message' => __('orders.Are you sure'), 
            'text' => __('orders.you refund an order, you send payment back to the customer')
        ]);    
      }
      event(new InstantPushNotification($transaction->user_id, [
          "title" =>  $message['title'],
          "body" =>   $message['body'],
          "data" => [
              'order_id' => $this->order->id,
              'type' => 'order',
              'status' => $TxnStatus,
          ]
      ]));
        
    }

    public function _getMessages($status,$order_number){
        $title = '';
        $body = '';
        $duration = 0;
        $orderNumber = $order_number;
        switch ($status) {
            case OrderStatus::PENDING:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::PENDING],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::PENDING],['order_number' => $orderNumber]);
            break;
            case OrderStatus::SCHEDULE:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::SCHEDULE],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::SCHEDULE],['order_number' => $orderNumber]);
            break;

            case OrderStatus::ACCEPTED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::ACCEPTED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::ACCEPTED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::DECLINED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::DECLINED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::DECLINED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::COMPLETED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::COMPLETED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::COMPLETED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::AWAITING_PAYMENT:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::AWAITING_PAYMENT],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::AWAITING_PAYMENT],['order_number' => $orderNumber]);
            break;

            case OrderStatus::AWAITING_FULFILLMENT:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::AWAITING_FULFILLMENT],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::AWAITING_FULFILLMENT],['order_number' => $orderNumber]);
            break;

            case OrderStatus::AWAITING_SHIPMENT:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::AWAITING_SHIPMENT],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::AWAITING_SHIPMENT],['order_number' => $orderNumber]);
            break;

            case OrderStatus::AWAITING_PICKUP:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::AWAITING_PICKUP],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::AWAITING_PICKUP],['order_number' => $orderNumber]);
            break;

            case OrderStatus::PARTIALLY_SHIPPED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::PARTIALLY_SHIPPED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::PARTIALLY_SHIPPED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::SHIPPED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::SHIPPED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::SHIPPED],['duration' => $duration,'order_number' => $orderNumber]);
            break;

            case OrderStatus::CANCELLED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::CANCELLED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::CANCELLED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::REFUNDED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::REFUNDED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::REFUNDED],['order_number' => $orderNumber]);
            break;

            case OrderStatus::PARTIALLY_REFUNDED:
                $title =  __('order/customer_message_push_title.'.OrderMessages::PUSH_TITLE[OrderStatus::PARTIALLY_REFUNDED],['order_number' => $orderNumber]);
                $body = __('order/customer_message_push_body.'.OrderMessages::PUSH_BODY[OrderStatus::PARTIALLY_REFUNDED],['order_number' => $orderNumber]);
            break;
        }

        return ['title' => $title, 'body' => $body];

    }

    public function _getTxnMessage($TxnStatus){
        $title = '';
        $body = '';

        switch ($TxnStatus) {
            case OrderPaymentStatus::PENDING:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::PENDING]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::PENDING]);
            break;
            case OrderPaymentStatus::HOLD:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::HOLD]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::HOLD]);
            break;

            case OrderStatus::CANCELLED:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::CANCELLED]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::CANCELLED]);
            break;

            case OrderStatus::DECLINED:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::DECLINED]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::DECLINED]);
            break;

            case OrderStatus::COMPLETED:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::COMPLETED]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::COMPLETED]);
            break;

            case OrderStatus::REFUNDED:
                $title =  __('order/payment_message_push_title.'.PaymentMessage::PUSH_TITLE[OrderPaymentStatus::REFUNDED]);
                $body = __('order/payment_message_push_body.'.PaymentMessage::PUSH_BODY[OrderPaymentStatus::REFUNDED]);
            break;

        }

        return ['title' => $title, 'body' => $body];
    }

    /**
     * order amount refund
     *
     * @return response()
     */
    public function PaymentRefund()
    {
        event(new PaymentRefund($this->order_id,[]));
        $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => __('orders.Transaction Status updated successfully')]);

        $this->location->refresh();
    }

    public function updatedSearch()
    {   
        $this->searchResultDrivers = "";
        $this->selected_user_id = "";

        if($this->search) {
            $filters = new DriverAssignFilter();
            $drivers = UserDriver::with(['user'])->filter($filters, $this->order)->whereHas('user',function($query){
                                            $query->where(DB::raw('lower(name)'), 'like', '%'.$this->search.'%')
                                            ->orWhere('phone', 'like', '%'.$this->search.'%');
                                        })->get();
          
            foreach($drivers as $key => $driver) {
                if (!UserDriver::OrderDeliveryCount($driver->user_id)) {
                    $this->searchResultDrivers = $drivers; 
                }else {
                    $this->searchResultDrivers = collect();
                }
            }
        }else {
            $this->searchResultDrivers = collect();
        }
    }
 
    public function selectedUser($userId) {
        if($this->selected_user_id  == $userId) {
             $this->selected_user_id = "";
        } else {
             $this->selected_user_id = $userId;
        }
    }

    public function resetField () {
        $this->search = '';  
    }


    public function driverSubmit() {

        if(!$this->selected_user_id) {
            $this->dispatchBrowserEvent('alert', 
            ['type' => 'success',  'message' => 'Please select a driver!']);
            return false;
        }
       
         $driverDetail = User::whereId($this->selected_user_id)->first();

         OrderDelivery::where('order_id', $this->order->id)
                       ->update([
                           "delivery_status" => OrderDriverStatus::ACCEPTED,
                           "deliver_by"   => $driverDetail->name,
                           "assing_to_id" => $driverDetail->id,
                        ]);        

         order::where('id', $this->order->id)->update(["order_status"=> OrderStatus::AWAITING_SHIPMENT]);

         $orderObj = new Order();
         $orderObj->UpdateHistory($this->order->id, OrderStatus::AWAITING_SHIPMENT);
         
         $title =  __('order/driver_message_push_title.The order has been accepted');
         $body =   __('order/driver_message_push_body.The order has been accepted from the restaurant , you will be contacted by the restaurant to prepare for the event');
         event(new InstantPushNotification($this->order->user_id, [
             "title" =>  $title,
             "body" =>   $body,
             "data" => [
                 'order_id' => $this->order->id,
                 'type' => 'order',
                 'status' =>OrderStatus::AWAITING_SHIPMENT,
             ]
         ]));

         $message = $this->_getMessages(OrderStatus::AWAITING_SHIPMENT, $this->order->order_number);  
         event(new InstantPushNotification($this->order->user_id, [
             "title" =>  $message['title'],
             "body" =>   $message['body'],
             "data" => [
                 'order_id' => $this->order->id,
                 'type' => 'order',
                 'status' =>OrderStatus::AWAITING_SHIPMENT,
             ]
         ]));
  
        
        $this->dispatchBrowserEvent('alert', 
        ['type' => 'success',  'message' => __('orders.Driver add Successfully!')]);
        $this->resetField();  

        return redirect(request()->header('Referer'));
        
    }



}
