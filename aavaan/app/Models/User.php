<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function artistProfile(): HasOne
    {
        return $this->hasOne(ArtistProfile::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(ArtistSubscription::class);
    }

    public function productionAccesses(): HasMany
    {
        return $this->hasMany(ProductionAccess::class);
    }

    public function isArtist(): bool
    {
        return $this->role === 'artist';
    }

    public function isProduction(): bool
    {
        return $this->role === 'production';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function activeSubscription(): ?ArtistSubscription
    {
        return $this->subscriptions()
            ->where('payment_status', 'paid')
            ->where('expires_at', '>', now())
            ->latest('expires_at')
            ->first();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    public function availableProductionAccess(): ?ProductionAccess
    {
        return $this->productionAccesses()
            ->where('payment_status', 'paid')
            ->whereColumn('used_count', '<', 'bundle_size')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }
}
