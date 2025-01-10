<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialSecurity extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'amount'
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
