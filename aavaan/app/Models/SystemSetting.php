<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model {
    public $timestamps = false;
    protected $fillable = ['key', 'value', 'label_fa', 'group', 'updated_at', 'updated_by'];
    protected $dates = ['updated_at'];

    public static function get(string $key, mixed $default = null): mixed {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void {
        static::where('key', $key)->update([
            'value'      => (string) $value,
            'updated_at' => now(),
            'updated_by' => auth()->id(),
        ]);
    }
}
