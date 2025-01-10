<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DentalAndVision extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'dentalPremiumAmount',
        'visionPremiumAmount',
        'dentalVisionCombine',
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
