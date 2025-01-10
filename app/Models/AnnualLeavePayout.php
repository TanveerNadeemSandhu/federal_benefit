<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnualLeavePayout extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'payout'
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
