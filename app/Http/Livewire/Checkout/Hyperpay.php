<?php

namespace App\Http\Livewire\Checkout;

use App\Constants\Gatways;
use App\Constants\OrderPaymentStatus;
use App\Constants\OrderStatus;
use App\Constants\PaymentMethodCode;
use App\Constants\Strings;
use App\Models\Order\Order;
use App\Models\Order\Transaction;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Nette\Utils\Random;

class Hyperpay extends Component
{
    protected $order_id, $amount, $transaction_id, $is_validate = true;
    private $user, $order_address; 
    public function mount()
    {
        $this->order_id = request()->order_id;
        // User validate
        $this->user = User::where('id', request()->user_id)->first();
        if (!$this->user) {
            Session::flash('payment-error',trans('payment.Please login with customer privileges and try again'));
            $this-> is_validate = false;
            return false;
        }

        if(!$this->user->status) {
            Session::flash('payment-error',trans('payment.Your account has been deactivated, Please contact to administrator'), ['error'=>'Unauthorised']);
            $this-> is_validate = false;
            return false;
        }

        // Order validate
        $order = Order::whereIN('order_status', [OrderStatus::AWAITING_PAYMENT,OrderStatus::AWAITING_FULFILLMENT])->find($this->order_id);
        if (!$order) {
            $this-> is_validate = false;
            Session::flash('payment-error',trans('payment.Invalid Order Id,Order not found'));
            return false;
        }
        // Transaction id generated and updated
        $txn = Transaction::where('order_id',$order->id)->whereIn('status',[OrderPaymentStatus::HOLD,OrderPaymentStatus::COMPLETED])->where('user_id',$this->user->id)->first();
        if ($txn) {
            $this-> is_validate = false;
            Session::flash('payment-error',trans('payment.Invalid transaction,please contact to administrator'));
            return false;
        }
        
        if ($order->is_sharing_order) {
            $participent = Order::where('id',$order->id)->whereHas('orderParticipant',function ($query)
            {
                $query->where('user_id', $this->user->id);
            })->first();

            if (!isset($participent->orderParticipant) && $order->user_id != $this->user->id) {
                $this->is_validate = false;
                Session::flash('payment-error',trans('payment.Invalid transaction,please contact to administrator'));
                return false;
            }else if (isset($participent->orderParticipant)) {
                if ($participent->orderParticipant->payment_status == OrderPaymentStatus::COMPLETED) {
                    $this->is_validate = false;
                    Session::flash('payment-error',trans('payment.Invalid transaction,please contact to administrator'));
                    return false;
                }            
            }
        }
        $amount = isset($participent->orderParticipant->participent_amount) ? $participent->orderParticipant->participent_amount: $order->payble_amount;
        $this->amount = round($amount,2);
        $this->transaction_id = base64_encode($order->store_id.'#'.$order->id.'#'.$order->user_id.Random::generate(4));
        // create transaction record
        $gateway_type = Gatways::HYPERPAY;
        $transaction_new  = Transaction::create([
            'order_id'  =>  $order->id,
            'user_id'   =>  $order->user_id,
            'store_id'  => $order->store_id,
            'payment_method_code' =>  PaymentMethodCode::CARD,
            'transaction_type'   =>  Strings::ONLINE,
            'payment_mode'   =>  PaymentMethodCode::CARD,
            'status'   =>  OrderPaymentStatus::PENDING,
            'content'   =>  PaymentMethodCode::CARD." Payment",
            'user_id'   =>  $this->user->id,
            'amount' =>  $this->amount,
            'gatway_type' => $gateway_type,
            'transaction_id' => $this->transaction_id
        ]);
        if (!$transaction_new) {
            $this->is_validate = false;
            Session::flash('payment-error',trans('payment.Something went`s wrong, try again'));
            return false;
        }
        OrderTransactionHistory($transaction_new->id ,OrderPaymentStatus::PENDING);
        $this->order_address = $order->getShippingAddress();
    }

    public function GoBack()
    {
        redirect()->route('payment.status', [
            'status' => OrderPaymentStatus::CANCELLED,
            'discription' => Session::get('payment-error'),
        ]);
    }
 
    public function render()
    { 
        if($this->is_validate){
            return view('livewire.payment.checkout', [
                'amount' => $this->amount,
                'transaction_id' => $this->transaction_id,
                'user' => $this->user,
                'details' => $this->order_address
            ]);
        }else{
            return view('livewire.payment.error', [
                'amount' => $this->amount,
                'transaction_id' => $this->transaction_id
            ]);
        }
    }
}
