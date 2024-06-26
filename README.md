# `Bangladesh Nagad Payment System Implement`

Laravel Nagad payment gateway

# Installation

```bash
composer require jeishanul/nagad
```

# Setup

## 1 . vendor publish

```bash
php artisan vendor:publish --provider="Jeishanul\Nagad\NagadServiceProvider" --tag=config
```

## 2 . Nagad config setup

- `config/nagad.php`

```php
return [
    'sandbox_mode' => env('NAGAD_MODE'),
    'merchant_id' => env('NAGAD_MERCHANT_ID'),
    'merchant_number' => env('NAGAD_MERCHANT_NUMBER'),
    'callback_url' => env('NAGAD_CALLBACK_URL'),
    'public_key' => env('NAGAD_PUBLIC_KEY'),
    'private_key' => env('NAGAD_PRIVATE_KEY')
];
```

# .env setup

```bash
NAGAD_MERCHANT_ID=
NAGAD_MERCHANT_NUMBER=
NAGAD_CALLBACK_URL=
NAGAD_MODE=sandbox // sandbox or live
NAGAD_PUBLIC_KEY="" // sandbox <optional>
NAGAD_PRIVATE_KEY=""  // sandbox <optional>
```

# Usage

## Get callback url

```php
use Jeishanul\Nagad\Nagad;

$tnxID = 1;
$amount = 99;
$redirectUrl = Nagad::getRedirectUrl($tnxID,$amount);;
return redirect($redirectUrl);
```

## Verify payment

```php
use Jeishanul\Nagad\Nagad;
$verify = Nagad::verify();
if($verify->status === 'Success'){
    $order = json_decode($verify->additionalMerchantInfo);
    $order_id = $order->tnx_id;
    // your functional task with order_id
}
if ($verify->status === 'Aborted') {
    // redirect or other what you want
}

```

## How to enable nagad gateway on server

- Contact with nagad, provide your ip and support ID. Nagad will be white-listed your ip and approve your merchant. Now your nagad gateway work properly on server.

```php
use Jeishanul\Nagad\Nagad;
// It's provide you a "support ID"
$tnxID = 1;
$sid = Nagad::getSupportID($tnxID);
return $sid;
```
