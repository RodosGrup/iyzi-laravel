# iyzi-laravel

It is an iyzico payment service package developed for Laravel Library.

## Installation


```bash
composer require rodosgrup/iyzi-laravel
```

## Usage
#### Application Launch
```php
$pay = app()->iyzico;
```
#### Single Payment
```php
//Parameters to Send Single Payment
$data = [
    'Price' => '1',
    'BasketId' => 'TR3999',
    'CardHolderName' => 'Rodos Grup',
    'CardNumber' => '4059030000000009',
    'ExpireMonth' => '12',
    'ExpireYear' => '2030',
    'Cvc' => '322',
    'BuyerId' => 'Rodos Grup',
    'Name' => 'Batuhan',
    'Surname' => 'Haymana',
    'GsmNumber' => '536*******',
    'Email' => 'batuhan@rodosgrup.com',
    'IdentityNumber' => '12345678912',
    'Address' => 'Arabacıalanı mah. Mehmet Akif Ersoy Cad No 33 /J-K Serdivan /SAKARYA',
    'idItem' => '1',
    'Pname' => 'Aile Danışmanlığı Eğitimi Sertifika Programı',
    'Category' => 'Eğitim'
];

$start = $pay->startSinglePayment($data);
```
#### Create Card
```php
//Parameters to Send for Credit Card Storage
$data = [
    'Email' => 'batuhan@rodosgrup.com',
    'ExternalId' => 'TR-231231111123',
    'Alias' => 'Burası benim ilk kartım',
    'CardHolderName' => 'Batuhan Haymana',
    'CardNumber' => '4059030000000009',
    'ExpireMonth' => '12',
    'ExpireYear' => '2030'
]

$start = $pay->createCard($data);
```
#### Delete Card
```php
//Parameters to Send to Delete Credit Card
$userKey = 'WIN9SoDhzmqMKAOQ174GoSW63Iw=';
$cardToken = 'qLm9Ler4ThhY0hE8xRnhT67maX0='

$start = $pay->deleteCard($userKey,$cardToken);
```
#### Query Registered Card
```php
//Parameters to Send for Registered Card Inquiry
$userKey = 'WIN9SoDhzmqMKAOQ174GoSW63Iw=';

$start = $pay->cardList($userKey);
```
## Working Status
* Single Payment ✓
* Card Transactions ✓

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](./LICENSE.md)
