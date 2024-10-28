<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'birthday',
        'contact_no',
        'avatar',
        'profile_date_updated',
        
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
