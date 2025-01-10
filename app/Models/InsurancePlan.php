<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsurancePlan extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'insurance',
        'insurance_emloyee',
        'insurance_retirement',
        'insurance_coverage',
        'insurance_employee_dependent',
        'insurance_coverage_basic_option',
        'basic_option_select',
        'insurance_coverage_a_option',
        'insurance_coverage_b_option',
        'option_b_value',
        'insurance_coverage_c_option',
        'insurance_employee_coverage_c',
        'insurance_employee_coverage_pp',
        'insurance_employee_coverage_age',
        'insurance_employee_coverage_self_age',
        'insurance_analysis',
        'federal',
        'plan_type',
        'premium',
        'coverage',
        'coverage_retirement',
        'coverage_retirement_dependent',
        'coverage_retirement_insurance',
        'coverage_retirement_insurance_why',
        'coverage_retirement_insurance_who',
        'dental',
        'dental_retirement',
        'dental_premium',
        'vision',
        'vision_retirement',
        'vision_premium',
        'vision_total_cost',
        'insurance_program',
        'insurance_age',
        'insurance_purchase_premium',
        'insurance_program_retirement',
        'insurance_program_plan',
        'insurance_program_daily',
        'insurance_purpose_covergae',
        'max_lifetime',
        'notes',
    ];
    public function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
