<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighThree extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'value'
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
