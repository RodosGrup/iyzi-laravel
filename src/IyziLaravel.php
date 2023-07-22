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
use Iyzipay\Request\CreateCardRequest;
use Iyzipay\Request\DeleteCardRequest;
use Iyzipay\Request\RetrieveCardListRequest;

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
        $paymentRequest->setConversationId(Str::random(5) . time());
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
        $buyer->setId($attributes['BuyerId']);
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

        $RodosGrup = new Address();
        $RodosGrup->setContactName("Rodos Yazılım");
        $RodosGrup->setCity("Sakarya");
        $RodosGrup->setCountry("Turkey");
        $RodosGrup->setAddress("Arabacıalanı mah. Mehmet Akif Ersoy Cad No 33 /J-K Serdivan /SAKARYA");

        $paymentRequest->setBillingAddress($RodosGrup);

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

        print_r($createPay);
    }

    /** It is a function to store the sent card information.
     * array $attuributes
     */
    public function createCard(array $attributes)
    {
        $request = new CreateCardRequest();
        $request->setLocale(Locale::TR);
        $request->setConversationId(Str::random(5) . time());
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

        return json_decode(collect($keepCard)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }

    /**
     * Used to query the cards that are hidden
     * string $userKey
     */
    public function cardList(string $userKey)
    {
        $info = new RetrieveCardListRequest();
        $info->setLocale(Locale::TR);
        $info->setConversationId(Str::random(5) . time());
        $info->setCardUserKey($userKey);

        $list = CardList::retrieve($info, $this->options);

        return json_decode(collect($list)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }

    /**
     * Deletion of previously added cards is done
     * string $userKey
     * string $cardToken
     */
    public function deleteCard(string $userKey, string $cardToken)
    {
        $info = new DeleteCardRequest();
        $info->setLocale(Locale::TR);
        $info->setConversationId(Str::random(5) . time());
        $info->setCardToken($cardToken);
        $info->setCardUserKey($userKey);

        $delete = Card::delete($info, $this->options);

        return json_decode(collect($delete)->toArray()["\x00Iyzipay\ApiResource\x00rawResult"]);
    }
}
