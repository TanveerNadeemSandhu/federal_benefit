<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YosDollar extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'age',
        'value',
        'sick_leaves',
        'annual_leaves',
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
