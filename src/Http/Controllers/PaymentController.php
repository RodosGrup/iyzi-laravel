<?php

namespace RodosGrup\IyziLaravel\Http\Controllers;

use Illuminate\Http\Request;
use Iyzipay\Model\Locale;
use Iyzipay\Model\ThreedsPayment;
use Iyzipay\Options;
use Iyzipay\Request\CreateThreedsPaymentRequest;

class PaymentController extends Controller
{
    public function return(Request $request)
    {
        $options = new Options();
        $options->setBaseUrl(config('iyzi-laravel.baseUrl'));
        $options->setApiKey(config('iyzi-laravel.apiKey'));
        $options->setSecretKey(config('iyzi-laravel.secretKey'));

        $pay = new CreateThreedsPaymentRequest();
        $pay->setLocale(Locale::TR);
        $pay->setConversationId($request->conversationId);
        $pay->setPaymentId($request->paymentId);
        $pay->setConversationData($request->conversationData);

        $threedsPayment = ThreedsPayment::create($pay, $options);

        return redirect()->route(config('iyzi-laravel.returnUrl') ?? 'iyzico.laravel.gateway')
            ->with([
                'content' => [
                    'payment' => $request->all(),
                    'info' => json_decode(collect($threedsPayment)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"])
                ]
            ]);
    }
}
