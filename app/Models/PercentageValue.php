<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageValue extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'annual_salary_increment',
        'cpiw',
        'csrs_cola',
        'fers_cola',
        'tsp_increment',
        'fehb_increment',
    ];
}
