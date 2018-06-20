<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function Psy\debug;

class PayPalController extends Controller
{
    public $apiUrl = "";
    public $paypalUrl = "";

    public function index(){
        $this->splitPay();
    }

    public function getPaymentOptions($payKey){

    }

    public function setPaymentDetails(){

    }

    public function createPayRequest(){

    }

    public function __paypalSend($data, $call){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        return json_decode(curl_exec($ch), TRUE);
    }

    public function splitPay(){
        $createPacket = [
            'actionType' => 'PAY',
            'currencyCode' => 'USD',
            'receiverList' => [
                'receiver' => [
                    [
                        'amount' => '1.00',
                        'email' => 'bujar.begisholli-facilitator@atis.al'
                    ],
                    [
                        'amount' => '2.00',
                        'email' => 'serviceprovider1@at.com'
                    ]
                ]
            ],
            'returnUrl' => 'http://bagus.dev/store',
            'cancelUrl' => 'http://bagus.dev/store',
            'requestEnvelope' => [
                'errorLanguage' => 'en_US',
                'detailLevel' => 'ReturnAll'
            ]
        ];

        $response = $this->__paypalSend($createPacket, "PAY");
        var_dump($response);
    }
}
