<?php

namespace RodosGrup\IyziLaravel;

use Iyzipay\Model\Address;
use Iyzipay\Model\ApiTest;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\ThreedsInitialize;
use Iyzipay\Options;
use Iyzipay\Request\CreatePaymentRequest;
use RodosGrup\IyziLaravel\Exceptions\Iyzipay\IyzipayAuthenticationException;
use RodosGrup\IyziLaravel\Exceptions\Iyzipay\IyzipayConnectionException as IyzipayIyzipayConnectionException;
use Illuminate\Support\Str;
use Iyzipay\Model\Card;
use Iyzipay\Model\CardInformation;
use Iyzipay\Model\CardList;
use Iyzipay\Model\Payment;
use Iyzipay\Request\CreateCardRequest;
use Iyzipay\Request\DeleteCardRequest;
use Iyzipay\Request\RetrieveCardListRequest;
use RodosGrup\IyziLaravel\Extra\StorageCard;

class IyziLaravel
{
    /** @options */
    protected $options;

    public function __construct($config = [])
    {
        $this->apiStart();
        $this->checkControl();
    }

    /** 
     * The control of whether a connection is established with the API is provided here. 
     */
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

    /** when the application starts it gets the config data from here */
    private function apiStart()
    {
        $this->options = new Options();
        $this->options->setBaseUrl(config('iyzi-laravel.baseUrl'));
        $this->options->setApiKey(config('iyzi-laravel.apiKey'));
        $this->options->setSecretKey(config('iyzi-laravel.secretKey'));
    }

    /**  
     * the process of receiving individual payments is carried out. 
     * array $attributes
     */
    public function startSinglePayment(array $attributes)
    {
        $paymentRequest = new CreatePaymentRequest();
        $paymentRequest->setLocale(Locale::TR);
        $paymentRequest->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
        $paymentRequest->setPrice($attributes['Price']);
        $paymentRequest->setPaidPrice($attributes['Price']);
        $paymentRequest->setCurrency(Currency::TL);
        $paymentRequest->setInstallment(1);
        $paymentRequest->setBasketId($attributes['BasketId']);
        $paymentRequest->setPaymentChannel(PaymentChannel::WEB);
        $paymentRequest->setPaymentGroup(PaymentGroup::PRODUCT);
        $paymentRequest->setCallbackUrl(route('iyzico.laravel.return'));

        $card = new PaymentCard();
        $card->setCardHolderName($attributes['CardHolderName']);
        $card->setCardNumber($attributes['CardNumber']);
        $card->setExpireMonth($attributes['ExpireMonth']);
        $card->setExpireYear($attributes['ExpireYear']);
        $card->setCvc($attributes['Cvc']);
        $card->setRegisterCard(0);

        $paymentRequest->setPaymentCard($card);

        $buyer = new Buyer();
        $buyer->setId(1);
        $buyer->setName($attributes['Name']);
        $buyer->setSurname($attributes['Surname']);
        $buyer->setGsmNumber($attributes['GsmNumber']);
        $buyer->setEmail($attributes['Email']);
        $buyer->setIdentityNumber($attributes['IdentityNumber']);
        $buyer->setRegistrationAddress($attributes['Address']);
        $buyer->setIp($_SERVER["REMOTE_ADDR"]);
        $buyer->setCity($attributes['City']);
        $buyer->setCountry("Turkey");

        $paymentRequest->setBuyer($buyer);

        $shipping = new Address();
        $shipping->setContactName($attributes['Name'] . " " . $attributes['Surname']);
        $shipping->setCity($attributes['City']);
        $shipping->setCountry("Turkey");
        $shipping->setAddress($attributes['Address']);

        $paymentRequest->setShippingAddress($shipping);

        $salesPerson = new Address();
        $salesPerson->setContactName(config('iyzi-laravel.billingName'));
        $salesPerson->setCity(config('iyzi-laravel.billingCity'));
        $salesPerson->setCountry("Turkey");
        $salesPerson->setAddress(config('iyzi-laravel.billingAddress'));

        $paymentRequest->setBillingAddress($salesPerson);

        $items = array();

        $item = new BasketItem();
        $item->setId($attributes['idItem']);
        $item->setName($attributes['Pname']);
        $item->setCategory1($attributes['Category']);
        $item->setItemType(BasketItemType::VIRTUAL);
        $item->setPrice($attributes['Price']);

        $items[0] = $item;

        $paymentRequest->setBasketItems($items);

        $createPay = ThreedsInitialize::create($paymentRequest, $this->options);

        if (collect($createPay)->toArray()["\x00Iyzipay\IyzipayResource\x00status"] === 'success') {

            if ($attributes['storageCard'] ?? false) {
                $this->storageCard([
                    'Email' => $attributes['Email'],
                    'Payment_Id' => $attributes['Payment_Id'] ?? Str::random(5) . time(),
                    'ExternalId' => $attributes['ExternalId'] ?? Str::random(5) . time(),
                    'User_Id' => $attributes['UserId'],
                    'Alias' => $attributes['Name'] . ' kartı',
                    'CardHolderName' => $attributes['CardHolderName'],
                    'CardNumber' => $attributes['CardNumber'],
                    'ExpireMonth' => $attributes['ExpireMonth'],
                    'ExpireYear' => $attributes['ExpireYear']
                ]);
            }

            return redirect()->route('iyzico.laravel.gateway')
                ->with([
                    'content' => $createPay->getHtmlContent()
                ]);
        }
    }

    /**
     * Storage payment with a previously stored card
     * array $atturibustes
     */
    public function paymentStorageCard(array $attributes)
    {
        $request = new CreatePaymentRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
        $request->setPrice($attributes['Price']);
        $request->setPaidPrice($attributes['Price']);
        $request->setCurrency(Currency::TL);
        $request->setInstallment(1);
        $request->setBasketId($attributes['BasketId']);
        $request->setPaymentChannel(PaymentChannel::WEB);
        $request->setPaymentGroup(PaymentGroup::PRODUCT);

        $paymentCard = new PaymentCard();
        $paymentCard->setCardUserKey($attributes['UserKey']);
        $paymentCard->setCardToken($attributes['CardToken']);

        $request->setPaymentCard($paymentCard);

        $buyer = new Buyer();
        $buyer->setId(1);
        $buyer->setName($attributes['Name']);
        $buyer->setSurname($attributes['Surname']);
        $buyer->setGsmNumber($attributes['GsmNumber']);
        $buyer->setEmail($attributes['Email']);
        $buyer->setIdentityNumber($attributes['IdentityNumber']);
        $buyer->setRegistrationAddress($attributes['Address']);
        $buyer->setIp(isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '127.0.0.1');
        $buyer->setCity($attributes['City']);
        $buyer->setCountry("Turkey");

        $request->setBuyer($buyer);

        $shipping = new Address();
        $shipping->setContactName($attributes['Name'] . " " . $attributes['Surname']);
        $shipping->setCity($attributes['City']);
        $shipping->setCountry("Turkey");
        $shipping->setAddress($attributes['Address']);

        $request->setShippingAddress($shipping);

        $salesPerson = new Address();
        $salesPerson->setContactName(config('iyzi-laravel.billingName'));
        $salesPerson->setCity(config('iyzi-laravel.billingCity'));
        $salesPerson->setCountry("Turkey");
        $salesPerson->setAddress(config('iyzi-laravel.billingAddress'));

        $request->setBillingAddress($salesPerson);

        $items = array();

        $item = new BasketItem();
        $item->setId($attributes['idItem']);
        $item->setName($attributes['Pname']);
        $item->setCategory1($attributes['Category']);
        $item->setItemType(BasketItemType::VIRTUAL);
        $item->setPrice($attributes['Price']);

        $items[0] = $item;

        $request->setBasketItems($items);

        $payment = Payment::create($request, $this->options);

        return json_decode(collect($payment)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }

    /** It is a function to store the sent card information.
     * array $attuributes
     */
    public function storageCard(array $attributes)
    {
        $userKey = StorageCard::userFind($attributes['Email']);
        if ($userKey) {
            return $this->storingSecondCard($attributes, $attributes['Email'], $userKey);
        }

        $request = new CreateCardRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
        $request->setEmail($attributes['Email']);
        $request->setExternalId($attributes['ExternalId']);

        $infoCard = new CardInformation();
        $infoCard->setCardAlias($attributes['Alias']);
        $infoCard->setCardHolderName($attributes['CardHolderName']);
        $infoCard->setCardNumber($attributes['CardNumber']);
        $infoCard->setExpireMonth($attributes['ExpireMonth']);
        $infoCard->setExpireYear($attributes['ExpireYear']);

        $request->setCard($infoCard);

        $keepCard = Card::create($request, $this->options);
        $solution = json_decode(collect($keepCard)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);

        $this->modelStorageCard(collect($solution)->toArray(), $attributes['Email'], $attributes['User_Id']);

        return $solution;
    }

    public function storingSecondCard(array $attributes, string $email = null, string $userKey = null)
    {
        if (!StorageCard::cardFind($attributes['CardNumber'])) {
            $query = new CreateCardRequest();
            $query->setLocale(Locale::TR);
            $query->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
            $query->setCardUserKey($userKey ?? $attributes['UserKey']);

            $infoCard = new CardInformation();
            $infoCard->setCardAlias($attributes['Alias']);
            $infoCard->setCardHolderName($attributes['CardHolderName']);
            $infoCard->setCardNumber($attributes['CardNumber']);
            $infoCard->setExpireMonth($attributes['ExpireMonth']);
            $infoCard->setExpireYear($attributes['ExpireYear']);

            $query->setCard($infoCard);

            $card = Card::create($query, $this->options);

            $solution = json_decode(collect($card)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);

            $this->modelStorageCard(collect($solution)->toArray(), $email, $attributes['User_Id']);

            return $solution;
        }

        return ['status' => 'success', 'message' => 'Kart daha önceden kayıt altına alınmış'];
    }

    /**
     * This field is the field where we transfer the results from the api to the database.
     * array $attributes;
     * string $email;
     * string $user 
     */
    public function modelStorageCard(array $attributes = [], string $email, string $user)
    {
        dd($attributes);
        StorageCard::addCreditCard(collect($attributes)->toArray(), $email, $user);
    }

    /**
     * Used to query the cards that are hidden
     * string $userKey
     */
    public function cardList(string $UserKey)
    {
        $info = new RetrieveCardListRequest();
        $info->setLocale(Locale::TR);
        $info->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
        $info->setCardUserKey($UserKey);

        $list = CardList::retrieve($info, $this->options);

        return json_decode(collect($list)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }

    /**
     * Deletion of previously added cards is done
     * string $userKey
     * string $cardToken
     */
    public function deleteCard(string $UserKey, string $cardToken)
    {
        $info = new DeleteCardRequest();
        $info->setLocale(Locale::TR);
        $info->setConversationId($attributes['Payment_Id'] ?? Str::random(5) . time());
        $info->setCardToken($cardToken);
        $info->setCardUserKey($UserKey);

        $delete = Card::delete($info, $this->options);
        $deleteModel = StorageCard::cardDelete($UserKey, $cardToken);

        return json_decode(collect($delete)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }
}
