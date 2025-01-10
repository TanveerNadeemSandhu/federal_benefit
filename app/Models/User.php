<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function routeNotificationForMail($notification)
    {
        return $this->email;
    }

    
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

    public function subscription(){
        return $this->hasOne(Subscription::class);
    }

    public function paymentHistory(){
        return $this->hasOne(PaymentHistory::class);
    }

    public function fedCase(){
        return $this->hasMany(FedCase::class);
    }

    public function share(){
        return $this->hasMany(Share::class);
    }

    public function sharesAgency(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function profile(){
        return $this->hasOne(Profile::class);
    }
}
