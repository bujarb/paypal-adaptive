<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PayPalController extends Controller
{
    public $apiUrl = "https://svcs.sandbox.paypal.com/AdaptivePayments/";
    public $paypalUrl = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=";
    public $headers = [];


    public $apiUser = 'serviceprovider1_api1.at.com';
    public $apiPassword = '2B94EBNY3YA7YSVU';
    public $apiSignature = 'ANAhfQvjC-U8ClXWFXJwsmR-MeJKARgB1DmUbKaPk-tGkh3zOclOLXrb';
    public $appId = 'APP-80W284485P519543T';
    public $envelope = [];

    public function __construct(){
        
        $this->headers = [
            "X-PAYPAL-SECURITY-USERID: ".$this->apiUser,
            "X-PAYPAL-SECURITY-PASSWORD: ".$this->apiPassword,
            "X-PAYPAL-SECURITY-SIGNATURE: ".$this->apiSignature,
            "X-PAYPAL-REQUEST-DATA-FORMAT: JSON",
            "X-PAYPAL-RESPONSE-DATA-FORMAT: JSON",
            "X-PAYPAL-APPLICATION-ID: ".$this->appId //
        ];

        $this->envelope = [
            'errorLanguage' => 'en_US',
            'detailLevel' => 'ReturnAll'
        ];
    }

    public function _paypalSend($data, $call){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.$call);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        return json_decode(curl_exec($ch), true);
    }

    public function getPaymentOptions($payKey){

        $packet = [
            'requestEnvelope' => $this->envelope,
            'payKey' => $payKey
        ];

        return $this->_paypalSend($packet,"GetPaymentOptions");

    }

    public function splitPay(){

        $createPacket = [
            'actionType' => 'PAY',
            'currencyCode' => 'USD',
            'receiverList' => [
                'receiver' => [
                    [
                        'amount' => '4.00',
                        'email' => 'begisholli.bujar@gmail.com'
                    ],
                    [
                        'amount' => '4.00',
                        'email' => 'bujar.begisholli-facilitator@atis.al'
                    ]
                ]
            ],
            'returnUrl' => 'http://127.0.0.1:8000/store',
            'cancelUrl' => 'http://127.0.0.1:8000/store',
            'requestEnvelope' => $this->envelope
        ];

        $response = $this->_paypalSend($createPacket,'Pay');

        $payKey = $response['payKey'];

        $detailsPacket = [
            'requestEnvelope' => $this->envelope,
            'payKey' => $payKey,
            'receiverOptions' => [
                [
                    'receiver' => ['email' => 'begisholli.bujar@gmail.com'],
                    'invoiceData' => [
                        'item' => [
                            [
                                'name' => 'product1',
                                'price' => '2.00'
                            ],
                            [
                                'name' => 'product1',
                                'price' => '2.00'
                            ]
                        ]
                    ]
                ],
                [
                    'receiver' => ['email' => 'bujar.begisholli-facilitator@atis.al'],
                    'invoiceData' => [
                        'item' => [
                            [
                                'name' => 'product2',
                                'price' => '2.00',
                                'identifier' => 'p1'
                            ],
                            [
                                'name' => 'product2',
                                'price' => '2.00',
                                'identifier' => 'p1'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response1 = $this->_paypalSend($detailsPacket,"SetPaymentOptions");
        $dets = $this->getPaymentOptions($payKey);


        $url = $this->paypalUrl.$payKey;

        return Redirect::to($url);
    }
}
