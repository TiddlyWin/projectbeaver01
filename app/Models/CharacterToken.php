<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CharacterToken extends Model
{
    protected $fillable = [
        'character_id',
        'access_token',
        'refresh_token',
        'expires_in',
        'scopes',
        'owner_hash',
    ];

    protected function casts(): array
    {
        return [
            'expires_in' => 'int',
        ];
    }

    public function character(): BelongsTo
    {
        return $this->belongsTo(Character::class);
    }

    public function getDecryptedAccessTokenAttribute(): string
    {
        return decrypt($this->access_token);
    }

    public function getDecryptedRefreshTokenAttribute(): string
    {
        return decrypt($this->refresh_token);
    }
}
