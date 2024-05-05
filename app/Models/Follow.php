<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    use HasFactory;

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id', 'id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id', 'id');
    }

    protected $fillable = [
        'follower_id',
        'following_id',
        'is_accepted',
    ];

    protected $casts = [
        'is_accepted' => 'boolean',
    ];
}
