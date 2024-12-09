<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthenticationCode extends BaseModel
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];

    protected $hidden = ['pivot'];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}