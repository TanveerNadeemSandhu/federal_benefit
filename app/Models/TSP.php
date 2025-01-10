<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TSP extends Model
{
    use HasFactory;
    protected $fillable = [
        'fed_case_id',
        'contribute',
        'contribute_pp',
        'contribute_pp_percentage',
        'contribute_tsp',
        'contribute_tsp_pp',
        'contribute_tsp_pp_percentage',
        'contribute_limit',
        'contribute_tsp_loan',
        'contribute_pay_pp',
        'contribute_pay_pp_value',
        'contribute_own_loan',
        'contribute_own_loan_2',
        'contribute_pay_date',
        'contribute_tsp_loan_gen',
        'contribute_tsp_res',
        'employee_not_sure',
        'employee_imd',
        'at_age',
        'employee_at_age',
        'employee_loss',
        'employee_income',
        'goal',
        'goal_amount',
        'goal_tsp',
        'goal_retirement',
        'goal_track',
        'goal_comfor',
        'goal_professional',
        'goal_why',
        'g_name',
        'g_value',
        'f_name',
        'f_value',
        'c_name',
        'c_value',
        's_name',
        's_value',
        'i_name',
        'i_value',
        'l_income',
        'l_income_value',
        'l_2025',
        'l_2025_value',
        'l_2030',
        'l_2030',
        'l_2030_value',
        'l_2035',
        'l_2035_value',
        'l_2040',
        'l_2040_value',
        'l_2045',
        'l_2045_value',
        'l_2050',
        'l_2050_value',
        'l_2055',
        'l_2055_value',
        'l_2060',
        'l_2060_value',
        'l_2065',
        'l_2065_value',
        'total_amount',
        'total_amount_percentage',
    ];
    public function fedCase(){
        return $this->belongsTo(FedCase::class);
    }
}
