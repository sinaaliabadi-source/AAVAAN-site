<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\ZarinPalGateway;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            $driver = config('payment.driver', 'zarinpal');

            return match ($driver) {
                'zarinpal' => new ZarinPalGateway(
                    merchantId: config('payment.zarinpal.merchant_id', ''),
                    sandbox: (bool) config('payment.zarinpal.sandbox', true),
                ),
                default => throw new \RuntimeException("Unsupported payment driver: {$driver}"),
            };
        });

        $this->app->singleton(PaymentService::class, fn($app) =>
            new PaymentService($app->make(PaymentGatewayInterface::class))
        );
    }
}
