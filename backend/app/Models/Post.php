<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subcategory_id',
        'type',
        'incident_location',
        'incident_date',
        'finish_transaction_date',
        'expiration_date',
        'status',
    ];

    protected $casts = [
        'status' => PostStatus::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
