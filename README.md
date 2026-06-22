# Check.et PHP SDK

Official PHP SDK for interacting with the Check.et API.

## Requirements

- PHP 8.2+

## Installation

```bash
composer require yohacodes/check.et
```

## Basic Usage

```php
use CheckEt\CheckEtClient;

$client = new CheckEtClient(
    apiKey: getenv('CHECK_ET_API_KEY'),
    appEnv: getenv('CHECK_ET_APP_ENV')
);

$response = $client->verify(
    bank: 'cbe',
    transactionNumber: 'FT25161234567'
);
```
