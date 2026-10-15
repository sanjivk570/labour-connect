<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_LABOUR = 'labour';

    protected $fillable = [
        'mobile',
        'role',
        'preferred_locale',
        'mobile_verified_at',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function isLabour(): bool
    {
        return $this->role === self::ROLE_LABOUR;
    }

    public function isCustomer(): bool
    {
        return $this->role === self::ROLE_CUSTOMER;
    }

    public function customerDetail(): HasOne
    {
        return $this->hasOne(CustomerDetail::class);
    }

    public function labourDetail(): HasOne
    {
        return $this->hasOne(LabourDetail::class);
    }
}
