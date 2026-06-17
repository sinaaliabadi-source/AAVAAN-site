<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    private string $merchantId;
    private bool $sandbox;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('payment.zarinpal.merchant_id');
        $this->sandbox = config('payment.zarinpal.sandbox', true);
        $this->baseUrl = $this->sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment'
            : 'https://api.zarinpal.com/pg/v4/payment';
    }

    public function request(int $amountTomans, string $description, string $callbackUrl): array
    {
        $response = Http::post($this->baseUrl . '/request.json', [
            'merchant_id' => $this->merchantId,
            'amount' => $amountTomans * 10,
            'description' => $description,
            'callback_url' => $callbackUrl,
        ]);

        $data = $response->json();

        if (!$response->successful() || ($data['data']['code'] ?? null) !== 100) {
            $error = $data['errors']['message'] ?? 'خطا در اتصال به درگاه پرداخت';
            throw new Exception($error);
        }

        $authority = $data['data']['authority'];
        $redirectUrl = $this->sandbox
            ? "https://sandbox.zarinpal.com/pg/StartPay/{$authority}"
            : "https://www.zarinpal.com/pg/StartPay/{$authority}";

        return ['authority' => $authority, 'redirect_url' => $redirectUrl];
    }

    public function verify(string $authority, int $amountTomans): string
    {
        $response = Http::post($this->baseUrl . '/verify.json', [
            'merchant_id' => $this->merchantId,
            'amount' => $amountTomans * 10,
            'authority' => $authority,
        ]);

        $data = $response->json();
        $code = $data['data']['code'] ?? null;

        if ($code === 101) {
            return (string) $data['data']['ref_id'];
        }

        if ($code !== 100) {
            $error = $data['errors']['message'] ?? 'پرداخت تأیید نشد';
            throw new Exception($error);
        }

        return (string) $data['data']['ref_id'];
    }
}
