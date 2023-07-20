<?php

namespace RodosGrup\IyziLaravel;

use Iyzipay\Model\ApiTest;
use Iyzipay\Options;
use RodosGrup\IyziLaravel\Exceptions\Iyzipay\IyzipayAuthenticationException;
use RodosGrup\IyziLaravel\Exceptions\Iyzipay\IyzipayConnectionException as IyzipayIyzipayConnectionException;

class IyziLaravel
{
    /** @options */
    protected $options;

    public function __construct($config = [])
    {
        $this->apiStart();
        $this->checkControl();
    }

    private function checkControl()
    {
        try {
            $control = ApiTest::retrieve($this->options);
        } catch (\Exception $e) {
            throw new IyzipayIyzipayConnectionException();
        }

        if ($control->getStatus() != 'success') {
            throw new IyzipayAuthenticationException();
        }
    }

    private function apiStart()
    {
        $this->options = new Options();
        $this->options->setBaseUrl(config('iyzi-laravel.baseUrl'));
        $this->options->setApiKey(config('iyzi-laravel.apiKey'));
        $this->options->setSecretKey(config('iyzi-laravel.secretKey'));
    }

    public function hiyziLaravel()
    {
        echo 'Merhaba dünya';
    }
}
