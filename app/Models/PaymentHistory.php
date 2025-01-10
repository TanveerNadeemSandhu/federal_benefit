<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'subscription_id',
        'card_name',
        'card_number',
        'month',
        'year',
        'csv',
        'price',
        'payment_date'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }


}
