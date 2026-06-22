<?php

declare(strict_types=1);

namespace CheckEt\Http;

use CheckEt\Config;
use CheckEt\Exceptions\AuthenticationException;
use CheckEt\Exceptions\CheckEtException;
use CheckEt\Exceptions\NetworkException;
use CheckEt\Exceptions\RateLimitException;
use CheckEt\Exceptions\ValidationException;

final class CurlHttpClient implements HttpClientInterface
{
    public function __construct(private readonly Config $config) {}

    public function post(
        string $url,
        array $headers = [],
        array $body = [],
    ): array {
        $attempt = 0;

        start:

        $attempt++;

        $ch = curl_init();

        $jsonBody = json_encode($body, JSON_THROW_ON_ERROR);

        $isLocal = $this->config->isLocal() || $this->config->isDev();

        // dd($isLocal);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,

            // SSL (safe toggle via config)
            CURLOPT_SSL_VERIFYPEER => !$isLocal,
            CURLOPT_SSL_VERIFYHOST => $isLocal ? 0 : 2,

            CURLOPT_HTTPHEADER => array_merge(
                ["Content-Type: application/json", "Accept: application/json"],
                $headers,
            ),

            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonBody,
            CURLOPT_TIMEOUT => $this->config->timeout,
            CURLOPT_CONNECTTIMEOUT => $this->config->connectTimeout,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);

            if ($this->shouldRetry(0, $attempt)) {
                $this->backoff($attempt);
                goto start;
            }

            throw new NetworkException("Network error: {$error}");
        }

        if (!is_string($response)) {
            throw new CheckEtException("Invalid response type from API");
        }

        try {
            $decoded = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new CheckEtException(
                "Invalid JSON response: " . $e->getMessage(),
            );
        }

        // SUCCESS
        if ($httpCode >= 200 && $httpCode < 300) {
            return $decoded;
        }

        // RETRY LOGIC
        if ($this->shouldRetry($httpCode, $attempt)) {
            $this->backoff($attempt);
            goto start;
        }

        $this->handleErrorResponse($httpCode, $decoded);
    }

    private function handleErrorResponse(int $code, array $data): void
    {
        $message = $data["message"] ?? "Unknown API error";

        match ($code) {
            401, 403 => throw new AuthenticationException($message),

            422 => throw new ValidationException($message),

            429 => throw new RateLimitException($message),

            500, 502, 503, 504 => throw new NetworkException(
                "Server error: {$message}",
            ),

            default => throw new CheckEtException(
                "API Error ({$code}): {$message}",
            ),
        };
    }

    private function shouldRetry(int $httpCode, int $attempt): bool
    {
        if ($attempt >= $this->config->retries) {
            return false;
        }

        return in_array($httpCode, [429, 500, 502, 503, 504], true);
    }

    private function backoff(int $attempt): void
    {
        $delay = 500 * 2 ** ($attempt - 1);
        usleep($delay * 1000);
    }
}
