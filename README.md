# Check.et PHP SDK

Official PHP SDK for interacting with the Check.et API.

## Requirements

- PHP 8.2+

## Installation
# Not available on Packagist yet.

```bash
composer require yohacodes/check-et-php
```

## Basic Usage

```php
use CheckEt\CheckEtClient;

$client = new CheckEtClient(
    apiKey: getenv('CHECK_ET_API_KEY')
);

$response = $client->verify(
    bank: 'cbe',
    transactionNumber: 'FT25161234567'
);
```
