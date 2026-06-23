<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCodeUse extends Model {
    public $timestamps = false;
    protected $fillable = ['discount_code_id','user_id','subscription_id','used_at'];
    protected $casts = ['used_at' => 'datetime'];

    public function discountCode(): BelongsTo { return $this->belongsTo(DiscountCode::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
