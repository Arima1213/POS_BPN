<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function role(): BelongsTo
    {
        return $this->belongsTo(Roles::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $user = Auth::user();
        $roles = $user->role->name;
        if ($panel->getId() === 'admin' && $roles === ('Admin')) {
            return true;
        } elseif ($panel->getId() === 'owner' && $roles === ('Owner')) {
            return true;
        } elseif ($panel->getId() === 'cashier' && $roles === ('Cashier')) {
            return true;
        } else {
            return false;
        }
    }
}