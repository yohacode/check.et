<?php

declare(strict_types=1);

namespace CheckEt\Http;

interface HttpClientInterface
{
    public function post(
        string $url,
        array $headers = [],
        array $body = [],
    ): array;
}
