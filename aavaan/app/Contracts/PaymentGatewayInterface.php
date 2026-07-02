<?php

namespace App\Contracts;

use App\Exceptions\PaymentException;

interface PaymentGatewayInterface
{
    /**
     * Initiate a payment request.
     *
     * @return array{authority: string, redirect_url: string}
     * @throws PaymentException
     */
    public function initiate(int $amountTomans, string $description, string $callbackUrl): array;

    /**
     * Verify a completed payment and return the ref_id.
     *
     * @throws PaymentException
     */
    public function verify(string $authority, int $amountTomans): string;
}
