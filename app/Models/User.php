<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'staff_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'transaction_limit',
        'two_factor_enabled',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_code',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'      => 'datetime',
            'two_factor_expires_at'  => 'datetime',
            'last_login_at'          => 'datetime',
            'password'               => 'hashed',
            'two_factor_enabled'     => 'boolean',
            'transaction_limit'      => 'decimal:2',
        ];
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
