# iyzi-laravel

It is an iyzico payment service package developed for Laravel Library.

## Installation


```bash
composer require rodosgrup/iyzi-laravel
```

```env
IYZI_API_KEY=
IYZI_SECRET_KEY=
IYZI_BASE_URL=https://sandbox-api.iyzipay.com

BILLING_NAME="RODOS GRUP"
BILLING_CITY="SAKARYA"
BILLING_ADDRESS="Arabacıalanı mah. Mehmet Akif Ersoy Cad No 33 /J-K Serdivan /SAKARYA"
```

## Usage
#### Application Launch
```php
$iyzi = app()->iyzico;
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

$start = $iyzi->startSinglePayment($data);
```
#### Card Storage
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

$start = $iyzi->storageCard($data);
```
#### Storage Card Payment
```php
//Parameters to Send Storage Card Payment
$data = [
    'Price' => '1',
    'BasketId' => 'TR3999',
    'UserKey' => 'WIN9SoDhzmqMKAOQ174GoSW63Iw=',
    'CardToken' => 'qLm9Ler4ThhY0hE8xRnhT67maX0=',
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

$start = $iyzi->paymentStorageCard($data);
```
#### Delete Card
```php
//Parameters to Send to Delete Credit Card
$UserKey = 'WIN9SoDhzmqMKAOQ174GoSW63Iw=';
$cardToken = 'qLm9Ler4ThhY0hE8xRnhT67maX0='

$start = $iyzi->deleteCard($UserKey,$cardToken);
```
#### Query Registered Card
```php
//Parameters to Send for Registered Card Inquiry
$UserKey = 'WIN9SoDhzmqMKAOQ174GoSW63Iw=';

$start = $iyzi->cardList($UserKey);
```
#### Storing the second card belonging to the user
```php
//Parameters required to store the second card of the user
$data = [
    'UserKey' => 'WIN9SoDhzmqMKAOQ174GoSW63Iw=',
    'Alias' => 'Bu benim ikinci kartım',
    'CardHolderName' => 'Batuhan Haymana',
    'CardNumber' => '4987490000000002',
    'ExpireMonth' => '12',
    'ExpireYear' => '2030'
];

$start = $iyzi->storingSecondCard($data);
```
## Working Status
* Single Payment ✓
* Card Transactions ✓
    - Card Storage
    - Query Stored Card
    - Delete Stored Card
    - Keep Second Card
* Get paid with a storage card. ✓

## Contributing

Pull requests are welcome. For major changes, please open an issue first
to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License

[MIT](./LICENSE.md)
