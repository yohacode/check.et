<?php

declare(strict_types=1);

namespace CheckEt\Services;

use CheckEt\Config;
use CheckEt\DTO\VerificationRequest;
use CheckEt\DTO\VerificationResponse;
use CheckEt\Http\HttpClientInterface;

final class VerificationService
{
    public function __construct(
        private readonly Config $config,
        private readonly HttpClientInterface $http,
    ) {}

    public function verify(VerificationRequest $request): VerificationResponse
    {
        $url = $this->config->baseUrl . "/verify";

        $response = $this->http->post(
            url: $url,
            headers: ["Authorization: Bearer " . $this->config->apiKey],
            body: $request->toArray(),
        );

        return VerificationResponse::fromArray($response);
    }
}
