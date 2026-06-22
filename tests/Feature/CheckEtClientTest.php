<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use CheckEt\Config;
use CheckEt\CheckEtClient;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CheckEtClient::class)]
final class CheckEtClientTest extends TestCase
{
    public function test_client_can_be_created(): void
    {
        $client = new CheckEtClient(new Config(apiKey: "test_key"));

        $this->assertInstanceOf(CheckEtClient::class, $client);
    }
}
