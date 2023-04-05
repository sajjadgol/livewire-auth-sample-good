<?php

use App\Models\Order\Order;
use App\Models\Order\OrderStatusHistory;
use App\Models\Order\Transaction;

if(!function_exists("OrderTransactionHistory")){
    function OrderTransactionHistory($id,$new_status)
    {
        $txn = Transaction::find($id);
        $orderHistory =  OrderStatusHistory::create(
            [
                'user_id' => $txn->user_id,
                'role' => 'customer',
                'order_id' => $txn->order_id,
                'old_status' => $txn->status,
                'new_status' => $new_status,    
                'title'  => __('order/customer_message_status.:type status has been changed :old_status to :new_status :txn_id',['type' => "Transaction",'old_status' => ucfirst(str_replace('_', ' ', $txn->status)),'new_status' => ucfirst(str_replace('_', ' ', $new_status)), 'txn_id' => ($txn->transaction_id) ? '('.$txn->transaction_id.')': null]),
            ]
            );
            return $orderHistory;
        
    }

}