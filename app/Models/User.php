<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'display_name',
        'phone',
        'birth_date',
        'country_code',
        'city',
        'postal_code',
        'address_line_1',
        'address_line_2',
        'avatar_url',
        'is_anonymized',
        'anonymized_at',
        'privacy_policy_accepted_at',
        'privacy_policy_version',
        'marketing_consent_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'role' => UserRole::class,
            'password' => 'hashed',
            'birth_date' => 'date',
            'is_anonymized' => 'boolean',
            'anonymized_at' => 'datetime',
            'privacy_policy_accepted_at' => 'datetime',
            'marketing_consent_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function builds(): HasMany
    {
        return $this->hasMany(Build::class);
    }

    public function ownedGames(): HasMany
    {
        return $this->hasMany(Game::class, 'owner_user_id');
    }

    public function gameDrafts(): HasMany
    {
        return $this->hasMany(GameDraft::class, 'owner_user_id');
    }
}
