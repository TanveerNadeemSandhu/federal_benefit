<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YosE extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'age',
        'value'
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
