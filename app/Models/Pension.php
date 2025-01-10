<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pension extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'amount',
        'first_year'
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
