<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    private $ordersData;

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function __construct($ordersData)
    {
        $this->ordersData = $ordersData;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
        return collect($this->ordersData);
    }

     /**
     * Here you select the row that you want in the file
     *
     * @return response()
     */

    public function map($order): array
    {   
        $payment=[];
        if(!$order->TransactionHistory->isEmpty()){
            foreach($order->TransactionHistory as $orderTransaction) {
                $payment['method'][]=$orderTransaction->payment_method_code;
                $payment['status'][]=$orderTransaction->status;
            }
        }
         $payment_method = isset($payment['method']) ? implode(",",$payment['method']) : "";
         $payment_status = isset($payment['status']) ? implode(",",$payment['status']) : "";

        return [
            $order->order_number,
            $order->created_at,
            $order->order_status,
            $order->store->name,
            $order->user->name,
            $order->user->phone,
            $order->total_amount,
            $order->discount_amount,
            $order->delivery_amount,
            $order->tax_amount,
            $payment_method,
            $payment_status,
        ];
    }

    /**
     * Here you select the header that you want in the file
     *
     * @return response()
     */

    public function headings(): array
    {
        return [
            'Order Number',
            'Date',
            'Status',
            'Store',
            'Customer',
            'Phone',
            'Amount',
            'Discount Amount',
            'Shipping Amount',
            'Tax Amount',
            'Payment Method',
            'Payment Status',
        ];
    }
}
