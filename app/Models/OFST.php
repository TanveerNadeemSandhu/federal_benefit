<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OFST extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'employee_work',
        'empolyee_hours_work',
        'empolyee_multiple_date',
        'empolyee_multiple_date_to',
        'non_deduction_service',
        'non_deduction_service_date',
        'non_deduction_service_date_2',
        'non_deduction_service_deposit',
        'non_deduction_service_deposit_owned',
        'break_in_service',
        'break_in_service_date_1',
        'break_in_service_date_2',
        'break_in_service_return_date',
        'break_in_service_refund',
        'break_in_service_redeposite',
        'break_in_service_amount_redeposite',
    ];
    public function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
