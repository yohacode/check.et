<?php

declare(strict_types=1);

namespace CheckEt\DTO;

final readonly class VerificationResponse
{
    public function __construct(
        public bool $success,
        public ?string $message,
        public array $raw,
    ) {}

    public static function fromArray(array $data): self
    {
        if (!array_key_exists("success", $data)) {
            throw new \CheckEt\Exceptions\CheckEtException(
                "Malformed API response: missing success field",
            );
        }

        return new self(
            success: (bool) $data["success"],
            message: $data["message"] ?? null,
            raw: $data,
        );
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }

    public function message(): ?string
    {
        return $this->message;
    }
}
