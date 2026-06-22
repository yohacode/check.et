<?php

declare(strict_types=1);

namespace CheckEt\Http;

use CheckEt\Config;
use CheckEt\Exceptions\CheckEtException;
use CheckEt\Exceptions\NetworkException;

final class CurlHttpClient implements HttpClientInterface
{
    public function __construct(private readonly Config $config) {}

    public function post(
        string $url,
        array $headers = [],
        array $body = [],
    ): array {
        $ch = curl_init();

        $jsonBody = json_encode($body, JSON_THROW_ON_ERROR);

        $defaultHeaders = [
            "Content-Type: application/json",
            "Accept: application/json",
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,

            // SECURITY: SSL always enabled
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,

            CURLOPT_HTTPHEADER => $allHeaders,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonBody,

            CURLOPT_TIMEOUT => $this->config->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->config->connectTimeout,
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new NetworkException("Network error: {$error}");
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $this->handleResponse($httpCode, $response);
    }

    private function handleResponse(int $httpCode, string $response): array
    {
        $decoded = json_decode($response, true);

        if (!is_array($decoded)) {
            throw new CheckEtException("Invalid JSON response from API.");
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return $decoded;
        }

        $this->handleErrorResponse($httpCode, $decoded);
    }

    private function handleErrorResponse(int $code, array $data): void
    {
        $message = $data["message"] ?? "Unknown API error";

        match ($code) {
            401 => throw new CheckEtException("Unauthorized: {$message}"),
            403 => throw new CheckEtException("Forbidden: {$message}"),
            422 => throw new CheckEtException("Validation error: {$message}"),
            429 => throw new CheckEtException("Rate limit: {$message}"),
            default => throw new CheckEtException(
                "API Error ({$code}): {$message}",
            ),
        };
    }
}
