<?php

declare(strict_types=1);

namespace CheckEt;

final class CheckEtClient
{
    public function __construct(private readonly Config $config) {}

    public function config(): Config
    {
        return $this->config;
    }
}
