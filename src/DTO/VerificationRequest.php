<?php

declare(strict_types=1);

namespace CheckEt\DTO;

final readonly class VerificationRequest
{
    public function __construct(
        public string $bank,
        public string $transactionNumber,
    ) {}

    public function toArray(): array
    {
        return [
            "bank" => $this->bank,
            "transaction_number" => $this->transactionNumber,
        ];
    }
}
