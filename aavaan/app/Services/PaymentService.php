<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;

class PaymentService
{
    public function __construct(private readonly PaymentGatewayInterface $gateway) {}

    /** @throws PaymentException */
    public function initiate(int $amountTomans, string $description, string $callbackUrl): array
    {
        return $this->gateway->initiate($amountTomans, $description, $callbackUrl);
    }

    /** @throws PaymentException */
    public function verify(string $authority, int $amountTomans): string
    {
        return $this->gateway->verify($authority, $amountTomans);
    }
}
