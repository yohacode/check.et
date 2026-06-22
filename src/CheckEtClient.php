<?php

declare(strict_types=1);

namespace CheckEt;

use CheckEt\DTO\VerificationRequest;
use CheckEt\DTO\VerificationResponse;
use CheckEt\Http\CurlHttpClient;
use CheckEt\Http\HttpClientInterface;
use CheckEt\Services\VerificationService;

final class CheckEtClient
{
    private HttpClientInterface $http;
    private VerificationService $verification;

    public function __construct(
        private readonly Config $config,
        ?HttpClientInterface $http = null,
    ) {
        $this->http = $http ?? new CurlHttpClient($config);

        $this->verification = new VerificationService($config, $this->http);
    }

    public function verify(
        string $bank,
        string $transactionNumber,
    ): VerificationResponse {
        $request = new VerificationRequest(
            bank: $bank,
            transactionNumber: $transactionNumber,
        );

        return $this->verification->verify($request);
    }
}
