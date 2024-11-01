<?php

namespace Aldeebhasan\LaSubscription\Concerns;

interface ContractUI
{
    public function getId(): int|string;

    public function getCode(): string;

    public function isRecurring(): bool;
}
