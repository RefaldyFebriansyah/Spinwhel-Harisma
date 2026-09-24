<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpinHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'wheel_item_id',
        'item_title',
        'class_level',
        'executor_name',
        'spun_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'spun_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wheelItem(): BelongsTo
    {
        return $this->belongsTo(WheelItem::class);
    }
}
