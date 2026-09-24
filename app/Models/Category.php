<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function wheelItems(): HasMany
    {
        return $this->hasMany(WheelItem::class);
    }

    public function activeWheelItems(): HasMany
    {
        return $this->hasMany(WheelItem::class)->where('is_active', true);
    }

    public function spinHistories(): HasMany
    {
        return $this->hasMany(SpinHistory::class);
    }
}
