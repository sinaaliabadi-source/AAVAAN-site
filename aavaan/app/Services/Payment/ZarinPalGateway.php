<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\PaymentException;
use Illuminate\Support\Facades\Http;

class ZarinPalGateway implements PaymentGatewayInterface
{
    private string $requestUrl;
    private string $verifyUrl;
    private string $startPayUrl;

    public function __construct(
        private readonly string $merchantId,
        private readonly bool   $sandbox = true,
    ) {
        $base = $sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment'
            : 'https://api.zarinpal.com/pg/v4/payment';

        $this->requestUrl  = $base . '/request.json';
        $this->verifyUrl   = $base . '/verify.json';
        $this->startPayUrl = $sandbox
            ? 'https://sandbox.zarinpal.com/pg/StartPay'
            : 'https://www.zarinpal.com/pg/StartPay';
    }

    public function initiate(int $amountTomans, string $description, string $callbackUrl): array
    {
        $response = Http::post($this->requestUrl, [
            'merchant_id'  => $this->merchantId,
            'amount'       => $amountTomans * 10,
            'description'  => $description,
            'callback_url' => $callbackUrl,
        ]);

        $data = $response->json();
        $code = $data['data']['code'] ?? null;

        if (!$response->successful() || $code !== 100) {
            throw new PaymentException($data['errors']['message'] ?? 'خطا در اتصال به درگاه پرداخت');
        }

        $authority = $data['data']['authority'];

        return [
            'authority'    => $authority,
            'redirect_url' => $this->startPayUrl . '/' . $authority,
        ];
    }

    public function verify(string $authority, int $amountTomans): string
    {
        $response = Http::post($this->verifyUrl, [
            'merchant_id' => $this->merchantId,
            'amount'      => $amountTomans * 10,
            'authority'   => $authority,
        ]);

        $data = $response->json();
        $code = $data['data']['code'] ?? null;

        // 101 = already verified — safe to treat as success
        if ($code === 100 || $code === 101) {
            return (string) $data['data']['ref_id'];
        }

        throw new PaymentException($data['errors']['message'] ?? 'پرداخت تأیید نشد');
    }
}
