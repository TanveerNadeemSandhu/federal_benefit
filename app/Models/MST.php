<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MST extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'military_service',
        'military_service_date_1',
        'military_service_date_2',
        'military_service_active_duty',
        'military_service_active_duty_date_1',
        'military_service_active_duty_date_2',
        'military_service_reserve',
        'military_service_reserve_date_1',
        'military_service_reserve_date_2',
        'military_service_academy',
        'military_service_academy_amount',
        'military_service_retire',
        'military_service_collecting',
        'military_service_reserves',
        'military_service_note',
        'military_service_amount',
    ];
    public function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
