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
        return $this->hasMany(Subscription::class);
    }

    public function productionAccesses(): HasMany
    {
        return $this->hasMany(ProductionAccess::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function artistSpecialties(): HasMany
    {
        return $this->hasMany(ArtistSpecialty::class);
    }

    public function primarySpecialty(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArtistSpecialty::class)->where('is_primary', true);
    }

    public function artistProfilePremium(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ArtistProfilePremium::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function analyticsEvents(): HasMany
    {
        return $this->hasMany(AnalyticsEvent::class, 'artist_id');
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

    public function activeSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
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
            ->whereHas('payment', fn($q) => $q->where('status', 'paid'))
            ->whereColumn('used_count', '<', 'bundle_size')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest()
            ->first();
    }
}
