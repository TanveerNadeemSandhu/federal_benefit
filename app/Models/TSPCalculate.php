<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSPCalculate extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'gFund',
        'fFund',
        'cFund',
        'sFund',
        'iFund',
        'lFund',
        'l2025Fund',
        'l2030Fund',
        'l2035Fund',
        'l2040Fund',
        'l2045Fund',
        'l2050Fund',
        'l2055Fund',
        'l2060Fund',
        'l2065Fund',
        'totalContribution',
        'totalMatching',
        'totalTSPCalculate',
        'matchingPercentage',
    ];

    protected function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
