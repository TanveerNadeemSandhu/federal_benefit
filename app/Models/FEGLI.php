<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FEGLI extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'basic',
        'optionA',
        'optionB',
        'optionC',
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
