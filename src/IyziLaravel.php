<?php

namespace Rodosgrup\IyziLaravel;

use Iyzipay\Model\ApiTest;
use Iyzipay\Options;
use Rodosgrup\IyziLaravel\Exceptions\Iyzipay\IyzipayAuthenticationException;
use Rodosgrup\IyziLaravel\Exceptions\Iyzipay\IyzipayConnectionException as IyzipayIyzipayConnectionException;

class IyziLaravel
{
    /** @options */
    protected $options;

    public function __construct($config = [])
    {
        $this->config = config('iyzi-laravel');
        //$this->apiStart();
        //$this->checkControl();
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
        dd(config('baseUrl'));
        $this->options->setBaseUrl(config('baseUrl'));
        $this->options->setApiKey(config('apiKey'));
        $this->options->setSecretKey(config('secretKey'));
    }

    public function hiyziLaravel()
    {
        echo 'Merhaba dünya';
    }
}
