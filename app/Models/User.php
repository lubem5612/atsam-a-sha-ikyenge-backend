<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\UlidHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UlidHelper, HasApiTokens;

    protected $guarded = ['id'];

    protected $with = [
        'latestSubscription:id,code,amount,status,reference'
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }
}
