<?php

declare(strict_types=1);

namespace CheckEt;

use CheckEt\Exceptions\InvalidConfigurationException;

final readonly class Config
{
    private const DEFAULT_BASE_URL = "https://api.check.et/api/v1";
    private const DEFAULT_TIMEOUT = 30;
    private const DEFAULT_CONNECT_TIMEOUT = 5;
    private const DEFAULT_RETRIES = 3;
    private const DEFAULT_ENV = "production";

    public string $baseUrl;
    public int $timeout;
    public int $connectTimeout;
    public int $retries;
    public string $appEnv;

    public function __construct(
        public string $apiKey,
        ?string $appEnv = null,
        ?string $baseUrl = null,
        ?int $timeout = null,
        ?int $connectTimeout = null,
        ?int $retries = null,
    ) {
        $this->appEnv = $appEnv ?? self::DEFAULT_ENV;

        $this->baseUrl = rtrim($baseUrl ?? self::DEFAULT_BASE_URL, "/");

        $this->timeout = $timeout ?? self::DEFAULT_TIMEOUT;
        $this->connectTimeout =
            $connectTimeout ?? self::DEFAULT_CONNECT_TIMEOUT;
        $this->retries = $retries ?? self::DEFAULT_RETRIES;

        $this->validate();
    }

    public function isLocal(): bool
    {
        return $this->appEnv === "local";
    }

    public function isDev(): bool
    {
        return $this->appEnv === "dev";
    }

    public function isProduction(): bool
    {
        return $this->appEnv === "production";
    }

    private function validate(): void
    {
        if (empty(trim($this->apiKey))) {
            throw new InvalidConfigurationException("API key cannot be empty.");
        }

        if (!str_starts_with($this->baseUrl, "https://")) {
            throw new InvalidConfigurationException(
                "Only HTTPS endpoints are allowed.",
            );
        }

        if ($this->timeout < 1) {
            throw new InvalidConfigurationException(
                "Timeout must be greater than 0.",
            );
        }

        if ($this->connectTimeout < 1) {
            throw new InvalidConfigurationException(
                "Connect timeout must be greater than 0.",
            );
        }

        if ($this->retries < 0 || $this->retries > 5) {
            throw new InvalidConfigurationException(
                "Retries must be between 0 and 5.",
            );
        }

        if (!in_array($this->appEnv, ["local", "production"], true)) {
            throw new InvalidConfigurationException(
                "appEnv must be 'local' or 'production'.",
            );
        }
    }
}
