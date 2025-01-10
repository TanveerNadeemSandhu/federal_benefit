<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FedCase extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'share_user_id',
        'name',
        'dob',
        'age',
        'spouse_name',
        'spouse_dob',
        'spouse_age',
        'address',
        'city',
        'state',
        'zip',
        'email',
        'phone',
        'retirement_system',
        'retirement_system_csrs_offset',
        'retirement_system_fers_transfer',
        'employee_type',
        'lscd',
        'rscd',
        'scd',
        'retirement_type',
        'retirement_type_age',
        'retirement_type_date',
        'retirement_type_voluntary',
        'current_hours_option',
        'current_leave_option',
        'annual_leave_hours',
        'sick_leave_hours',
        'income_employee_option',
        'salary_1',
        'salary_2',
        'salary_3',
        'salary_4',
        'employee_spouse',
        'survior_benefit_fers',
        'survior_benefit_csrs',
        'employee_eligible',
        'amount_1',
        'amount_2',
        'amount_3',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function mST(){
        return $this->hasOne(MST::class);
    }

    public function insurancePlan(){
        return $this->hasOne(InsurancePlan::class);
    }
    public function oFST(){
        return $this->hasOne(OFST::class, 'foreign_key');
    }

    public function tSP(){
        return $this->hasOne(TSP::class);
    }

    public function yosDollar(){
        return $this->hasOne(YosDollar::class);
    }

    public function yosE(){
        return $this->hasOne(YosE::class);
    }

    public function highThree(){
        return $this->hasOne(HighThree::class);
    }

    public function pension(){
        return $this->hasOne(Pension::class);
    }

    public function partTimePension(){
        return $this->hasOne(PartTimePension::class);
    }

    public function FEGLI(){
        return $this->hasOne(FEGLI::class);
    }

    public function annualLeavePayout(){
        return $this->hasOne(AnnualLeavePayout::class);
    }

    public function srs(){
        return $this->hasOne(SRS::class);
    }

    public function dentalAndVision(){
        return $this->hasOne(DentalAndVision::class);
    }

    public function fehbVP(){
        return $this->hasOne(FEHBVP::class);
    }

    public function fltcip(){
        return $this->hasOne(FLTCIP::class);
    }

    public function tspCalculate(){
        return $this->hasOne(TSPCalculate::class);
    }

    public function socialSecurity(){
        return $this->hasOne(SocialSecurity::class);
    }

    public function survivorBenefit(){
        return $this->hasOne(SurvivorBenefit::class);
    }

}
