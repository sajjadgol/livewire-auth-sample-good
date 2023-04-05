<?php

namespace App\Models\Gatways;

use App\Constants\OrderPaymentStatus;
use App\Models\Order\Order;
use App\Models\Order\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;

use function PHPUnit\Framework\returnSelf;

class Hyperpay extends Model
{
    // for success
    protected const success_pattren = '/^(000.000.|000.100.1|000.[36]|000.400.[1][12]0)/';
    protected const successManualReviewCodePattern = '/^(000.400.0[^3]|000.400.100)/';
    //pending
    protected const pending_pattren = '/^(000\.200)/';

    // failure response
    protected const failure_pattren =  [
        '/^(800\.[17]00|800\.800\.[123])/',
        '/^(900\.[1234]00|000\.400\.030)/',
        '/^(800\.[56]|999\.|600\.1|800\.800\.[84])/',
        '/^(100\.400\.[0-3]|100\.380\.100|100\.380\.11|100\.380\.4|100\.380\.5)/',
        '/^(800\.1[123456]0)/',
        '/^(600\.[23]|500\.[12]|800\.121)/',
        '/^(800\.[32])/',
        '/^(800\.400\.2|100\.390)/',
        '/^(800\.400\.1)/',
        '/^(100\.[13]50)/',
        '/^(300\.100\.100)/',
        '/^(100\.39[765])/',
        '/^(100\.39[765])/',
        '/^(100\.250|100\.360)/',
        '/^(700\.[1345][05]0)/',
        '/^(200\.[123]|100\.[53][07]|800\.900|100\.[69]00\.500)/',
        '/^(100\.800)/',
        '/^(100\.700|100\.900\.[123467890][00-99])/',
        '/^(100\.100|100.2[01])/',
        '/^(100\.55)/',
        '/^(100\.380\.[23]|100\.380\.101)/',
        '/^(000\.100\.2)/',
        '/^(000\.400\.[1][0-9][1-9]|000\.400\.2)/'
    ];

    private $config;
    public  function __construct()
    {
        $this->config = config('payments');
        $this->config['gatewayes'] = ($this->config && array_key_exists('gatewayes' ,$this->config)) ? Arr::where( $this->config['gatewayes'], function ($value) {
            return ($value['enabled'] ?? null) == true;
        }) : [];
    }



    public  function Status($id = false)
    {
        $method = $this->config['gatewayes']['card'] ?? null;
        $environment = $this->config['environment'] ?? 'test';
        $url = $this->config['endpoints'][$environment]['url'] ?? null ;
        $url .= 'checkouts/'.$id.'/payment';
        $url .= "?entityId=".$method['entity_id'];

            // hyperpay http request
            $response = Http::swithoutVerifying()->asForm()->withToken($method['access_token'])->get($url);
            $response = $response->json();
            $result = self::ResponseValidate($response);

        return $result;
    }

     public  function Refund($transaction,$order){
        if (empty($transaction) || empty($order)) {
            return false;
        }
        if (empty($transaction->refrence_id)) {
            return false;
        }
        $method = $this->config['gatewayes']['card'] ?? null;
        $environment = $this->config['environment'] ?? 'test';
        $url = $this->config['endpoints'][$environment]['url'] ?? null ;
        $url .= 'payments/'.$transaction->refrence_id;
        // request params
        $data = [
            "entityId" => $method['entity_id'],
            "amount" => $order->total_amount,
            "currency" => $method['currency'],
            "paymentType" => 'RF',
        ];
        // hyperpay http request
        $response = Http::withoutVerifying()->asForm()->withToken($method['access_token'])->post($url, $data);
        $response = $response->json();
        $result = self::ResponseValidate($response);
        return $result;
    }



    /**
     * validate http response
     *
     * @param [json]  $response
     * @return $response
     */
    public static function ResponseValidate($response)
    {
        $code =  ($response['result']['code'] ?? '');
        $success = preg_match(self::success_pattren , $code) || preg_match(self::successManualReviewCodePattern , $code) ;
        $pending = preg_match(self::pending_pattren , $code);
        $failure = self::PregGrep($code);
        if ($success) {
            $response['status'] = OrderPaymentStatus::SUCCESS;
        }else if ($pending) {
            $response['status'] = OrderPaymentStatus::PENDING;
        }else if($failure){
            $response['status'] = OrderPaymentStatus::FAILED;
        }else{
            $response['status'] = OrderPaymentStatus::PENDING;
        }
        return $response;
    }
     /**
     * validate failure response code
     *
     * @param [type] $code
     * @return true/false
     */
    public static function PregGrep($code = null)
    {
        if ($code) {
            $pattern =  self::failure_pattren;
            foreach ($pattern as $key => $value) {
                if (preg_match($value, $code)) {
                    return true;
                }
            }
        }
        return false;
    }

}
