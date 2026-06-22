<?php

require __DIR__ . "/../vendor/autoload.php";

use CheckEt\Config;
use CheckEt\CheckEtClient;

$config = new Config(
    apiKey: config("check-et.api_key"),
    appEnv: env("APP_ENV"),
);

// dd(env("APP_ENV"), config("check-et.api_key"), $config); // worked

$client = new CheckEtClient($config);

try {
    $response = $client->verify(
        bank: "cbe",
        transactionNumber: "FT25161234567",
    );

    if ($response->isSuccessful()) {
        echo "Transaction verified successfully\n";
    } else {
        echo "Failed: " . $response->message() . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
