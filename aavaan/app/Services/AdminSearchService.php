<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;

class AdminSearchService
{
    /**
     * Each provider is a closure returning ['label' => ..., 'url' => ..., 'meta' => ...].
     * Add new providers here in future phases without touching the search logic.
     */
    protected array $providers;

    public function __construct()
    {
        $this->providers = [
            'users'    => fn(string $q) => $this->searchUsers($q),
            'payments' => fn(string $q) => $this->searchPayments($q),
        ];
    }

    public function search(string $query): array
    {
        if (strlen(trim($query)) < 2) {
            return [];
        }

        $results = [];
        foreach ($this->providers as $key => $provider) {
            $found = $provider($query);
            if (!empty($found)) {
                $results[$key] = $found;
            }
        }

        return $results;
    }

    /** Register an additional search provider (for future phases). */
    public function addProvider(string $key, callable $provider): void
    {
        $this->providers[$key] = $provider;
    }

    private function searchUsers(string $q): array
    {
        return User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
            })
            ->limit(10)
            ->get()
            ->map(fn(User $u) => [
                'label' => $u->name . ' — ' . $u->email,
                'url'   => route('admin.users.show', $u->id),
                'meta'  => $u->role,
            ])
            ->toArray();
    }

    private function searchPayments(string $q): array
    {
        if (!ctype_digit($q)) {
            return [];
        }

        return Payment::where('id', $q)
            ->orWhere('ref_id', $q)
            ->limit(5)
            ->get()
            ->map(fn(Payment $p) => [
                'label' => 'پرداخت #' . $p->id . ' — ' . number_format($p->amount) . ' ریال',
                'url'   => route('admin.payments.index') . '?search=' . $q,
                'meta'  => $p->status,
            ])
            ->toArray();
    }
}
