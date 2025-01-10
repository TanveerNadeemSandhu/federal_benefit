<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualLeavePayout;
use App\Models\ASI;
use App\Models\COLA;
use App\Models\DentalAndVision;
use App\Models\FedCase;
use App\Models\FEGLI;
use App\Models\FEHBVP;
use App\Models\FLTCIP;
use App\Models\HighThree;
use App\Models\InsuranceCost;
use App\Models\InsurancePlan;
use App\Models\MST;
use App\Models\OFST;
use App\Models\PartTimePension;
use App\Models\Pension;
use App\Models\PercentageValue;
use App\Models\SickLeavesConversion;
use App\Models\SocialSecurity;
use App\Models\SRS;
use App\Models\State;
use App\Models\SurvivorBenefit;
use App\Models\TSP;
use App\Models\TSPCalculate;
use App\Models\User;
use App\Models\YosDollar;
use App\Models\YosE;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Carbon\Carbon;
use DateTime;
use Dompdf\Adapter\PDFLib;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PDF;

class FedCaseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role == 'super admin') {
            $cases = FedCase::get();
            $count = $cases->count();
        } else {
            $cases = FedCase::where("user_id", $user->id)->get();
            $count = $cases->count();
        }

        return view('admin.fed-case.index', compact('user', 'cases', 'count'));
    }

    public function create()
    {
        $states = State::orderBy('name', 'asc')->get();
        return view('admin.fed-case.create', compact('states'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                   => 'required',
            'dob'                    => 'required',
            'retirement_system'      => 'required',
            'employee_type'          => 'required',
            'lscd' => 'nullable|required_without_all:rscd,scd|date',
            'rscd' => 'nullable|required_without_all:lscd,scd|date',
            'scd'  => 'nullable|required_without_all:lscd,rscd|date',
            'retirement_type'        => 'required',
            'retirement_type_date'   => 'required',
        ]);
        if ($validator->passes()) {
            $user = Auth::user();

            // save data in this table before this(OTHER FEDERAL SERVICE TIME) section 
            $case = FedCase::create([
                'user_id'                           => $user->id,
                'status'                            => $request->status,
                'name'                              => $request->name,
                'dob'                               => $request->dob
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->dob)->format('Y-m-d'))
                    : null,
                'age'                               => $request->age,
                'spouse_name'                       => $request->spouse_name,
                'spouse_dob'                        => $request->spouse_dob
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->spouse_dob)->format('Y-m-d'))
                    : null,
                'spouse_age'                        => $request->spouse_age,
                'address'                           => $request->address,
                'city'                              => $request->city,
                'state'                             => $request->state,
                'zip'                               => $request->zip,
                'email'                             => $request->email,
                'phone'                             => $request->phone,
                'retirement_system'                 => $request->retirement_system,
                'retirement_system_csrs_offset'     => $request->retirement_system_csrs_offset
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_system_csrs_offset)->format('Y-m-d'))
                    : null,
                'retirement_system_fers_transfer'   => $request->retirement_system_fers_transfer
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_system_fers_transfer)->format('Y-m-d'))
                    : null,
                'employee_type'                     => $request->employee_type,
                'lscd'                              => $request->lscd,
                'rscd'                              => $request->rscd,
                'scd'                               => $request->scd,
                'retirement_type'                   => $request->retirement_type,
                'retirement_type_age'               => $request->retirement_type_age,
                'retirement_type_date'              => $request->retirement_type_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_type_date)->format('Y-m-d'))
                    : null,
                'retirement_type_voluntary'         => $request->retirement_type_voluntary
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_type_voluntary)->format('Y-m-d'))
                    : null,
                'annual_leave_hours'                => $request->annual_leave_hours,
                'sick_leave_hours'                  => $request->sick_leave_hours,
                'current_hours_option'              => $request->current_hours_option,
                'current_leave_option'              => $request->current_leave_option,
                'income_employee_option'            => $request->income_employee_option,
                'salary_1'                          => $request->salary_1,
                'salary_2'                          => $request->salary_2,
                'salary_3'                          => $request->salary_3,
                'salary_4'                          => $request->salary_4,
                'employee_spouse'                   => $request->employee_spouse,
                'survior_benefit_fers'              => $request->survior_benefit_fers,
                'survior_benefit_csrs'              => $request->survior_benefit_csrs,
                'employee_eligible'                 => $request->employee_eligible,
                'amount_1'                          => $request->amount_1,
                'amount_2'                          => $request->amount_2,
                'amount_3'                          => $request->amount_3,
            ]);

            // save data of OTHER FEDERAL SERVICE TIME section
            $ofst = OFST::create([
                'fed_case_id'                           => $case->id,
                'employee_work'                         => $request->employee_work,
                'empolyee_hours_work'                   => $request->empolyee_hours_work,
                'empolyee_multiple_date'                => $request->empolyee_multiple_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->empolyee_multiple_date)->format('Y-m-d'))
                    : null,
                'empolyee_multiple_date_to'             => $request->empolyee_multiple_date_to
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->empolyee_multiple_date_to)->format('Y-m-d'))
                    : null,
                'non_deduction_service'                 => $request->non_deduction_service,
                'non_deduction_service_date'            => $request->non_deduction_service_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->non_deduction_service_date)->format('Y-m-d'))
                    : null,
                'non_deduction_service_date_2'          => $request->non_deduction_service_date_2
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->non_deduction_service_date_2)->format('Y-m-d'))
                    : null,
                'non_deduction_service_deposit'         => $request->non_deduction_service_deposit,
                'non_deduction_service_deposit_owned'   => $request->non_deduction_service_deposit_owned,
                'break_in_service'                      => $request->break_in_service,
                'break_in_service_date_1'               => $request->break_in_service_date_1
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_date_1)->format('Y-m-d'))
                    : null,
                'break_in_service_date_2'               => $request->break_in_service_date_2
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_date_2)->format('Y-m-d'))
                    : null,
                'break_in_service_refund'               => $request->break_in_service_refund,
                'break_in_service_redeposite'           => $request->break_in_service_redeposite,
                'break_in_service_return_date'          => $request->break_in_service_return_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_return_date)->format('Y-m-d'))
                    : null,
                'break_in_service_amount_redeposite'    => $request->break_in_service_amount_redeposite,
            ]);

            // save data of MILITARY SERVICE TIME section
            $mst = MST::create([
                'fed_case_id'                           => $case->id,
                'military_service'                      => $request->military_service,
                'military_service_date_1'               => $request->military_service_date_1
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_date_1)->format('Y-m-d'))
                    : null,
                'military_service_date_2'               => $request->military_service_date_2
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_date_2)->format('Y-m-d'))
                    : null,
                'military_service_active_duty'          => $request->military_service_active_duty,
                'military_service_active_duty_date_1'   => $request->military_service_active_duty_date_1
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_active_duty_date_1)->format('Y-m-d'))
                    : null,
                'military_service_active_duty_date_2'   => $request->military_service_active_duty_date_2
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_active_duty_date_2)->format('Y-m-d'))
                    : null,
                'military_service_reserve'              => $request->military_service_reserve,
                'military_service_reserve_date_1'       => $request->military_service_reserve_date_1
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_reserve_date_1)->format('Y-m-d'))
                    : null,
                'military_service_reserve_date_2'       => $request->military_service_reserve_date_2
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_reserve_date_2)->format('Y-m-d'))
                    : null,
                'military_service_academy'              => $request->military_service_academy,
                'military_service_academy_amount'       => $request->military_service_academy_amount,
                'military_service_note'                 => $request->military_service_note,
                'military_service_retire'               => $request->military_service_retire,
                'military_service_collecting'           => $request->military_service_collecting,
                'military_service_reserves'             => $request->military_service_reserves,
                'military_service_amount'               => $request->military_service_amount,
            ]);

            // save data of THRIFT SAVINGS PLAN section
            $tsp = TSP::create([
                'fed_case_id'                           => $case->id,
                'contribute'                            => $request->contribute,
                'contribute_pp'                         => $request->contribute_pp,
                'contribute_pp_percentage'              => $request->contribute_pp_percentage,
                'contribute_tsp'                        => $request->contribute_tsp,
                'contribute_tsp_pp'                     => $request->contribute_tsp_pp,
                'contribute_tsp_pp_percentage'          => $request->contribute_tsp_pp_percentage,
                'contribute_limit'                      => $request->contribute_limit,
                'contribute_tsp_loan'                   => $request->contribute_tsp_loan,
                'contribute_tsp_res'                    => $request->contribute_tsp_res,
                'contribute_tsp_loan_gen'               => $request->contribute_tsp_loan_gen,
                'contribute_pay_pp'                     => $request->contribute_pay_pp,
                'contribute_pay_pp_value'               => $request->contribute_pay_pp_value,
                'contribute_own_loan'                   => $request->contribute_own_loan,
                'contribute_own_loan_2'                 => $request->contribute_own_loan_2,
                'contribute_pay_date'                   => $request->contribute_pay_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->contribute_pay_date)->format('Y-m-d'))
                    : null,
                'employee_not_sure'                     => $request->employee_not_sure,
                'employee_imd'                          => $request->employee_imd,
                'employee_at_age'                       => $request->employee_at_age,
                'employee_loss'                         => $request->employee_loss,
                'employee_income'                       => $request->employee_income,
                'goal'                                  => $request->goal,
                'goal_amount'                           => $request->goal_amount,
                'goal_tsp'                              => $request->goal_tsp,
                'goal_retirement'                       => $request->goal_retirement,
                'goal_track'                            => $request->goal_track,
                'goal_comfor'                           => $request->goal_comfor,
                'goal_professional'                     => $request->goal_professional,
                'goal_why'                              => $request->goal_why,
                'g_name'                                => $request->g_name,
                'g_value'                               => $request->g_value,
                'f_name'                                => $request->f_name,
                'f_value'                               => $request->f_value,
                'c_name'                                => $request->c_name,
                'c_value'                               => $request->c_value,
                's_name'                                => $request->s_name,
                's_value'                               => $request->s_value,
                'i_name'                                => $request->i_name,
                'i_value'                               => $request->i_value,
                'l_income'                              => $request->l_income,
                'l_income_value'                        => $request->l_income_value,
                'l_2025'                                => $request->l_2025,
                'l_2025_value'                          => $request->l_2025_value,
                'l_2030'                                => $request->l_2030,
                'l_2030_value'                          => $request->l_2030_value,
                'l_2035'                                => $request->l_2035,
                'l_2035_value'                          => $request->l_2035_value,
                'l_2040'                                => $request->l_2040,
                'l_2040_value'                          => $request->l_2040_value,
                'l_2045'                                => $request->l_2045,
                'l_2045_value'                          => $request->l_2045_value,
                'l_2050'                                => $request->l_2050,
                'l_2050_value'                          => $request->l_2050_value,
                'l_2055'                                => $request->l_2055,
                'l_2055_value'                          => $request->l_2055_value,
                'l_2060'                                => $request->l_2060,
                'l_2060_value'                          => $request->l_2060_value,
                'l_2065'                                => $request->l_2065,
                'l_2065_value'                          => $request->l_2065_value,
                'total_amount'                          => $request->total_amount,
                'total_amount_percentage'               => $request->total_amount_percentage,
            ]);

            // save data of INSURANCE PLAN section
            $insurancePlan = InsurancePlan::create([
                'fed_case_id'                           => $case->id,
                'insurance'                             => $request->insurance,
                'insurance_emloyee'                     => $request->insurance_emloyee,
                'insurance_retirement'                  => $request->insurance_retirement,
                'insurance_coverage'                    => $request->insurance_coverage,
                'insurance_employee_dependent'          => $request->insurance_employee_dependent,
                'insurance_coverage_basic_option'       => $request->insurance_coverage_basic_option,
                'basic_option_select'                   => $request->basic_option_select,
                'insurance_coverage_a_option'           => $request->insurance_coverage_a_option,
                'insurance_coverage_b_option'           => $request->insurance_coverage_b_option,
                'option_b_value'                        => $request->option_b_value,
                'insurance_coverage_c_option'           => $request->insurance_coverage_c_option,
                'insurance_employee_coverage_c'         => $request->insurance_employee_coverage_c,
                'insurance_employee_coverage_pp'        => $request->insurance_employee_coverage_pp,
                'insurance_employee_coverage_age'       => $request->insurance_employee_coverage_age,
                'insurance_employee_coverage_self_age'  => $request->insurance_employee_coverage_self_age,
                'insurance_analysis'                    => $request->insurance_analysis,
                'federal'                               => $request->federal,
                'plan_type'                             => $request->plan_type,
                'premium'                               => $request->premium,
                'coverage'                              => $request->coverage,
                'coverage_retirement'                   => $request->coverage_retirement,
                'coverage_retirement_dependent'         => $request->coverage_retirement_dependent,
                'coverage_retirement_insurance'         => $request->coverage_retirement_insurance,
                'coverage_retirement_insurance_why'     => $request->coverage_retirement_insurance_why,
                'coverage_retirement_insurance_who'     => $request->coverage_retirement_insurance_who,
                // 'coverage_retirement_insurance_spouse'                               => $request->coverage_retirement_insurance_spouse,
                // 'coverage_retirement_insurance_child'                                => $request->coverage_retirement_insurance_child,
                // 'coverage_retirement_insurance_both'                                 => $request->coverage_retirement_insurance_both,
                'dental'                                => $request->dental,
                'dental_retirement'                     => $request->dental_retirement,
                'dental_premium'                        => $request->dental_premium,
                'vision'                                => $request->vision,
                'vision_retirement'                     => $request->vision_retirement,
                'vision_premium'                        => $request->vision_premium,
                'vision_total_cost'                     => $request->vision_total_cost,
                'insurance_program'                     => $request->insurance_program,
                'insurance_age'                         => $request->insurance_age,
                'insurance_purchase_premium'            => $request->insurance_purchase_premium,
                'insurance_program_retirement'          => $request->insurance_program_retirement,
                'insurance_program_plan'                => $request->insurance_program_plan,
                'insurance_program_daily'               => $request->insurance_program_daily,
                'max_lifetime'                          => $request->max_lifetime,
                'notes'                                 => $request->notes,
                'insurance_purpose_coverage'            => $request->insurance_purpose_coverage,
                'insurance_program_purpose'             => $request->insurance_program_purpose,
            ]);

            // start tsp calculation
            if ($request->contribute == 'none') {
                $tspContribution = 0;
                $tspContributionPercentage = 0;
            } else {
                $tspContribution = $request->contribute_pp;
                $tspContributionPercentage = $request->contribute_pp_percentage;
            }

            if ($request->contribute_tsp == 'none') {
                $rothContribution = 0;
                $rothContributionPercentage = 0;
            } else {
                $rothContribution = $request->contribute_tsp_pp;
                $rothContributionPercentage = $request->contribute_tsp_pp_percentage;
            }

            $totalContribution = $tspContribution + $rothContribution;
            $totalContributionPercentage = $tspContributionPercentage + $rothContributionPercentage;

            if ($totalContributionPercentage == 0) {
                $matchingPercentage = 1 / 100;
            } else if ($totalContributionPercentage > 0 && $totalContributionPercentage <= 1) {
                $matchingPercentage = 2 / 100;
            } else if ($totalContributionPercentage > 1 && $totalContributionPercentage <= 2) {
                $matchingPercentage = 3 / 100;
            } else if ($totalContributionPercentage > 2 && $totalContributionPercentage <= 3) {
                $matchingPercentage = 4 / 100;
            } else if ($totalContributionPercentage > 3 && $totalContributionPercentage <= 4) {
                $matchingPercentage = 4.5 / 100;
            } else if ($totalContributionPercentage > 4 && $totalContributionPercentage <= 5) {
                $matchingPercentage = 5 / 100;
            } else if ($totalContributionPercentage > 5) {
                $matchingPercentage = 5 / 100;
            }

            $salary = str_replace(',', '', $request->salary_1);
            $salary = intval($salary);
            $totalMatching = $salary * $matchingPercentage;


            if (!empty($request->g_name)) {
                $gAverageRate = 1.025;
                $gFund = $request->g_name * $gAverageRate;
            } else {
                $gFund = 0;
            }

            if (!empty($request->f_name)) {
                $fAverageRate = 1.015;
                $fFund = $request->f_name * $fAverageRate;
            } else {
                $fFund = 0;
            }

            if (!empty($request->c_name)) {
                $cAverageRate = 1.13;
                $cFund = $request->c_name * $cAverageRate;
            } else {
                $cFund = 0;
            }

            if (!empty($request->s_name)) {
                $sAverageRate = 1.08;
                $sFund = $request->s_name * $sAverageRate;
            } else {
                $sFund = 0;
            }

            if (!empty($request->i_name)) {
                $iAverageRate = 1.05;
                $iFund = $request->i_name * $iAverageRate;
            } else {
                $iFund = 0;
            }

            if (!empty($request->l_income)) {
                $lAverageRate = 1.04;
                $lFund = $request->l_income * $lAverageRate;
            } else {
                $lFund = 0;
            }

            if (!empty($request->l_2025)) {
                $l2025AverageRate = 1.04;
                $l2025Fund = $request->l_2025 * $l2025AverageRate;
            } else {
                $l2025Fund = 0;
            }

            if (!empty($request->l_2030)) {
                $l2030AverageRate = 1.07;
                $l2030Fund = $request->l_2030 * $l2030AverageRate;
            } else {
                $l2030Fund = 0;
            }

            if (!empty($request->l_2035)) {
                $l2035AverageRate = 1.07;
                $l2035Fund = $request->l_2035 * $l2035AverageRate;
            } else {
                $l2035Fund = 0;
            }

            if (!empty($request->l_2040)) {
                $l2040AverageRate = 1.08;
                $l2040Fund = $request->l_2040 * $l2040AverageRate;
            } else {
                $l2040Fund = 0;
            }

            if (!empty($request->l_2045)) {
                $l2045AverageRate = 1.08;
                $l2045Fund = $request->l_2045 * $l2045AverageRate;
            } else {
                $l2045Fund = 0;
            }

            if (!empty($request->l_2050)) {
                $l2050AverageRate = 1.08;
                $l2050Fund = $request->l_2050 * $l2050AverageRate;
            } else {
                $l2050Fund = 0;
            }

            if (!empty($request->l_2055)) {
                $l2055AverageRate = 1.1;
                $l2055Fund = $request->l_2055 * $l2055AverageRate;
            } else {
                $l2055Fund = 0;
            }

            if (!empty($request->l_2060)) {
                $l2060AverageRate = 1.1;
                $l2060Fund = $request->l_2060 * $l2060AverageRate;
            } else {
                $l2060Fund = 0;
            }

            if (!empty($request->l_2065)) {
                $l2065AverageRate = 1.1;
                $l2065Fund = $request->l_2025 * $l2065AverageRate;
            } else {
                $l2065Fund = 0;
            }

            $totalTSPCalculate = $gFund + $fFund + $cFund + $sFund + $iFund + $lFund + $l2025Fund + $l2030Fund + $l2035Fund + $l2040Fund + $l2045Fund + $l2050Fund + $l2055Fund + $l2060Fund + $l2065Fund + $totalContribution + $totalMatching;
            if ($totalTSPCalculate > 0) {
                $tspCalculate = TSPCalculate::create([
                    'fed_case_id'        => $case->id,
                    'gFund'              => $gFund,
                    'fFund'              => $fFund,
                    'cFund'              => $cFund,
                    'sFund'              => $sFund,
                    'iFund'              => $iFund,
                    'lFund'              => $lFund,
                    'l2025Fund'          => $l2025Fund,
                    'l2030Fund'          => $l2030Fund,
                    'l2035Fund'          => $l2035Fund,
                    'l2040Fund'          => $l2040Fund,
                    'l2045Fund'          => $l2045Fund,
                    'l2050Fund'          => $l2050Fund,
                    'l2055Fund'          => $l2055Fund,
                    'l2060Fund'          => $l2060Fund,
                    'l2065Fund'          => $l2065Fund,
                    'totalContribution'  => $totalContribution,
                    'totalMatching'      => $totalMatching,
                    'totalTSPCalculate'  => $totalTSPCalculate,
                    'matchingPercentage' => $matchingPercentage,
                ]);
            }
            // end tsp calculation

            // start of social security calculation
            if ($request->employee_eligible == 'yes') {

                // $estimateBeginSS = $case->amount_2;
                if ($request->amount_2) {
                    if ($request->amount_2 == 63) {
                        $percentagePIA = 0.75;
                    } else if ($request->amount_2 == 64) {
                        $percentagePIA = 0.80;
                    } else if ($request->amount_2 == 65) {
                        $percentagePIA = 0.867;
                    } else if ($request->amount_2 == 66) {
                        $percentagePIA = 0.933;
                    } else if ($request->amount_2 == 67) {
                        $percentagePIA = 1;
                    } else if ($request->amount_2 == 68) {
                        $percentagePIA = 1.08;
                    } else if ($request->amount_2 == 69) {
                        $percentagePIA = 1.16;
                    } else if ($request->amount_2 == 70) {
                        $percentagePIA = 1.24;
                    } else {
                        $percentagePIA = 0.7;
                    }
                } else {
                    $percentagePIA = 0.7;
                }

                $amountOfSS = str_replace(',', '', $request->amount_1);
                $amountOfSS = intval($amountOfSS);
                $totalAmountOfSS = $amountOfSS * $percentagePIA * 12 / .7;
                // SRS calculations
                if (preg_match('/(\d+)\s*Y/', $request->retirement_type_age, $matches)) {
                    $ageYearAtRetirement = (int)$matches[1];
                    if ($ageYearAtRetirement < 62) {
                        preg_match('/(\d+)\s*Y/', $request->yosDollar, $matches);
                        $yosYear = (int)$matches[1];
                        $srsAmount = str_replace(',', '', $request->amount_1);
                        $srsAmount = intval($srsAmount);
                        $srsAmount = $yosYear * $srsAmount / 40;
                        $srsAmount = SRS::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $srsAmount,
                        ]);
                    }
                }
                $socialSecurity = SocialSecurity::create([
                    'fed_case_id' => $case->id,
                    'amount'      => $totalAmountOfSS
                ]);
            } else {
                $totalAmountOfSS = 0;
                $srsAmount = 0;
                $srsAmount = SRS::create([
                    'fed_case_id'    => $case->id,
                    'amount'         => $srsAmount,
                ]);
                $socialSecurity = SocialSecurity::create([
                    'fed_case_id' => $case->id,
                    'amount'      => $totalAmountOfSS
                ]);
            }
            // end of social security calculation

            // sick leave hour 
            if ($request->current_leave_option == 'yes') {
                $leaveHours = $request->input('sick_leave_hours');
                $baseHours = 2082;
                $years = 0;
                while ($leaveHours >= $baseHours) {
                    $leaveHours -= $baseHours;
                    $years++;
                }
                $remainingHours = $leaveHours;
                $hoursDurationArray = SickLeavesConversion::all();
                foreach ($hoursDurationArray as $item) {
                    $finalDuration = null;
                    if ($remainingHours <= $item['hours']) {
                        $finalDuration = $item['duration'];
                        break;
                    }
                }
                $months = 0;
                $days = 0;
                preg_match('/(\d+)\s*M/', $finalDuration, $monthsMatch);
                preg_match('/(\d+)\s*D/', $finalDuration, $daysMatch);

                if (!empty($monthsMatch)) {
                    $months = (int) $monthsMatch[1];
                }
                if (!empty($daysMatch)) {
                    $days = (int) $daysMatch[1];
                }
                $formattedSickLeaveDuration = "{$years} Y, {$months} M, {$days} D";
            } else {
                $formattedSickLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // annual leave hour 
            if ($request->current_hours_option == 'yes') {
                $leaveHours = $request->input('annual_leave_hours');
                $baseHours = 2082;
                $years = 0;
                while ($leaveHours >= $baseHours) {
                    $leaveHours -= $baseHours;
                    $years++;
                }
                $remainingHours = $leaveHours;
                $hoursDurationArray = SickLeavesConversion::all();
                foreach ($hoursDurationArray as $item) {
                    $finalDuration = null;
                    if ($remainingHours <= $item['hours']) {
                        $finalDuration = $item['duration'];
                        break;
                    }
                }
                $months = 0;
                $days = 0;
                preg_match('/(\d+)\s*M/', $finalDuration, $monthsMatch);
                preg_match('/(\d+)\s*D/', $finalDuration, $daysMatch);

                if (!empty($monthsMatch)) {
                    $months = (int) $monthsMatch[1];
                }
                if (!empty($daysMatch)) {
                    $days = (int) $daysMatch[1];
                }
                $formattedAnnualLeaveDuration = "{$years} Y, {$months} M, {$days} D";
            } else {
                $formattedAnnualLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // military service date difference calculate in days
            if ($request->military_service == 'yes') {
                $military_service_date_1 = Carbon::parse($request->military_service_date_1);
                $military_service_date_2 = Carbon::parse($request->military_service_date_2);
                $diff = $military_service_date_1->diff($military_service_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceDate = "0 Y, 0 M, 0 D";
            }

            // military service active duty date difference calculate in days
            if ($request->military_service_active_duty  == 'yes') {
                $military_service_active_duty_date_1 = Carbon::parse($request->military_service_active_duty_date_1);
                $military_service_active_duty_date_2 = Carbon::parse($request->military_service_active_duty_date_2);
                $diff = $military_service_active_duty_date_1->diff($military_service_active_duty_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceActiveDutyDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceActiveDutyDate = "0 Y, 0 M, 0 D";
            }

            // military service reserve date difference calculate in days
            if ($request->military_service_reserve  == 'yes') {
                $military_service_reserve_date_1 = Carbon::parse($request->military_service_reserve_date_1);
                $military_service_reserve_date_2 = Carbon::parse($request->military_service_reserve_date_2);
                $diff = $military_service_reserve_date_1->diff($military_service_reserve_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceReserveDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceReserveDate = "0 Y, 0 M, 0 D";
            }


            // total military time calculate
            $totalMilitaryServiceTime = [$militaryServiceDate, $militaryServiceActiveDutyDate, $militaryServiceReserveDate];
            $totalYears = 0;
            $totalMonths = 0;
            $totalDays = 0;
            foreach ($totalMilitaryServiceTime as $duration) {
                // Extract years, months, and days from the duration string
                preg_match('/(\d+)\s*Y/', $duration, $years);
                preg_match('/(\d+)\s*M/', $duration, $months);
                preg_match('/(\d+)\s*D/', $duration, $days);

                $totalYears += isset($years[1]) ? (int)$years[1] : 0;
                $totalMonths += isset($months[1]) ? (int)$months[1] : 0;
                $totalDays += isset($days[1]) ? (int)$days[1] : 0;
            }
            while ($totalDays >= 30) {
                $totalDays -= 30;
                $totalMonths += 1;
            }
            while ($totalMonths >= 12) {
                $totalMonths -= 12;
                $totalYears += 1;
            }
            $parts = [];
            if ($totalYears > 0) {
                $parts[] = "{$totalYears} Y";
            }
            if ($totalMonths > 0) {
                $parts[] = "{$totalMonths} M";
            }
            if ($totalDays > 0) {
                $parts[] = "{$totalDays} D";
            }
            $totalMilitaryDuration = implode(', ', $parts);


            // calculate all yos data
            $finalValue = [$formattedSickLeaveDuration, $formattedAnnualLeaveDuration, $totalMilitaryDuration, $request->yosDollar];
            $totalFinalYears = 0;
            $totalFinalMonths = 0;
            $totalFinalDays = 0;

            foreach ($finalValue as $value) {
                // Extract years, months, and days from the duration string
                preg_match('/(\d+)\s*Y/', $value, $finalYears);
                preg_match('/(\d+)\s*M/', $value, $finalMonths);
                preg_match('/(\d+)\s*D/', $value, $finalDays);

                $totalFinalYears += isset($finalYears[1]) ? (int)$finalYears[1] : 0;
                $totalFinalMonths += isset($finalMonths[1]) ? (int)$finalMonths[1] : 0;
                $totalFinalDays += isset($finalDays[1]) ? (int)$finalDays[1] : 0;
            }
            while ($totalFinalDays >= 30) {
                $totalFinalDays -= 30;
                $totalFinalMonths += 1;
            }
            while ($totalFinalMonths >= 12) {
                $totalFinalMonths -= 12;
                $totalFinalYears += 1;
            }
            $finalParts = [];
            if ($totalFinalYears > 0) {
                $finalParts[] = "{$totalFinalYears} Y";
            }
            if ($totalFinalMonths > 0) {
                $finalParts[] = "{$totalFinalMonths} M";
            }
            if ($totalFinalDays > 0) {
                $finalParts[] = "{$totalFinalDays} D";
            }

            $finalData = implode(', ', $finalParts);

            $numberYears = 0;
            $numberMonths = 0;
            $numberDays = 0;

            // Parse the duration string
            if (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M,\s*(\d+)\s*D/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = (int)$matches[3];
            } elseif (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = 0;
            } elseif (preg_match('/(\d+)\s*Y/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = 0;
                $numberDays = 0;
            }

            $monthsToYears = $numberMonths / 12;
            $daysToYears = $numberDays / 365;

            $totalYears = $numberYears + $monthsToYears + $daysToYears;

            $finalTotalYears = round($totalYears, 2);

            // save data in yos($)
            $yosDollar = YosDollar::create([
                'fed_case_id'   => $case->id,
                'age'           => $request->yosDollar,
                'value'         => $finalTotalYears,
                'sick_leaves'   => $formattedSickLeaveDuration,
                'annual_leaves' => $formattedAnnualLeaveDuration,
            ]);


            // calculate all yos(E) data
            $finalValue = [$totalMilitaryDuration, $request->yosDollar];
            $totalFinalYears = 0;
            $totalFinalMonths = 0;
            $totalFinalDays = 0;

            foreach ($finalValue as $value) {
                preg_match('/(\d+)\s*Y/', $value, $finalYears);
                preg_match('/(\d+)\s*M/', $value, $finalMonths);
                preg_match('/(\d+)\s*D/', $value, $finalDays);

                $totalFinalYears += isset($finalYears[1]) ? (int)$finalYears[1] : 0;
                $totalFinalMonths += isset($finalMonths[1]) ? (int)$finalMonths[1] : 0;
                $totalFinalDays += isset($finalDays[1]) ? (int)$finalDays[1] : 0;
            }
            while ($totalFinalDays >= 30) {
                $totalFinalDays -= 30;
                $totalFinalMonths += 1;
            }
            while ($totalFinalMonths >= 12) {
                $totalFinalMonths -= 12;
                $totalFinalYears += 1;
            }
            $finalParts = [];
            if ($totalFinalYears > 0) {
                $finalParts[] = "{$totalFinalYears} Y";
            }
            if ($totalFinalMonths > 0) {
                $finalParts[] = "{$totalFinalMonths} M";
            }
            if ($totalFinalDays > 0) {
                $finalParts[] = "{$totalFinalDays} D";
            }

            $finalData = implode(', ', $finalParts);

            $numberYears = 0;
            $numberMonths = 0;
            $numberDays = 0;

            // Parse the duration string
            if (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M,\s*(\d+)\s*D/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = (int)$matches[3];
            } elseif (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = 0;
            } elseif (preg_match('/(\d+)\s*Y/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = 0;
                $numberDays = 0;
            }
            $monthsToYears = $numberMonths / 12;
            $daysToYears = $numberDays / 365;
            $totalYears = $numberYears + $monthsToYears + $daysToYears;

            $finalTotalYears = round($totalYears, 2);
            // save data in yos(E)
            $yosE = YosE::create([
                'fed_case_id' => $case->id,
                'age'         => $request->yosDollar,
                'value'       => $finalTotalYears
            ]);

            // calculate high three
            $annualSalaryIncrement = PercentageValue::value('annual_salary_increment');
            $annualSalaryIncrement = $annualSalaryIncrement / 100;
            $annualSalaryDecrement = 1 + $annualSalaryIncrement;
            $current_salary = intval(preg_replace('/\D/', '', $request->salary_1));
            if ($request->input('income_employee_option') == 'yes') {
                $retirementDate = $request->retirement_type_date; // Format: 'YYYY-MM-DD'
                // Convert the retirement date from the request to a Carbon instance
                $retirementDate = Carbon::parse($retirementDate);

                $date = $request->retirement_type_date;
                $retirementDateTime = new DateTime($date);
                $retirementYear = $retirementDateTime->format('y');
                $retirementMonth = $retirementDateTime->format('m');
                // Get today's date
                $today = Carbon::now();
                $currentDateTime = new DateTime($today);
                $currentYear = $currentDateTime->format('y');

                // Calculate the difference in years
                $yearsRemaining = $retirementYear - $currentYear;
                $salaries = [];
                $realCurrentSalary = $current_salary;
                $currentMonthlySalary = $current_salary / 12;
                $fourthRemainingMonth = 12 - $retirementMonth;
                $fourthYearSalary = $fourthRemainingMonth * $currentMonthlySalary;
                if ($yearsRemaining > 0) {
                    for ($i = 1; $i <= $yearsRemaining; $i++) {
                        // Increment salary for each year
                        $current_salary += $current_salary * $annualSalaryIncrement;
                        $salaries[] = $current_salary;

                        // Handle specific cases
                        if ($yearsRemaining > 2) {
                            if ($yearsRemaining == 3 && $retirementMonth < 12) {
                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $current_salary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;

                                // Include second and third-year salaries
                                if ($i == 1) $secondYearSalary = $current_salary;
                                if ($i == 2) $thirdYearSalary = $current_salary;

                                if ($i == 3) {
                                    $average_salary = ($secondYearSalary + $thirdYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                                }
                            } elseif ($retirementMonth == 12) {
                                // Standard case for full years
                                rsort($salaries);
                                $topThreeSalaries = array_slice($salaries, 0, 3);
                                $average_salary = array_sum($topThreeSalaries) / count($topThreeSalaries);
                            } else if ($yearsRemaining > 3 && $retirementMonth < 12) {
                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $current_salary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;

                                // Include second and third-year salaries
                                if ($i == $yearsRemaining - 1) $secondYearSalary = $current_salary;
                                if ($i == $yearsRemaining - 2) $thirdYearSalary = $current_salary;
                                if ($i == $yearsRemaining - 3) {
                                    $fourthYearSalary = $current_salary;
                                    $fourthMonthlySalary = $fourthYearSalary / 12;
                                    $fourthYearSalary = $fourthMonthlySalary * $fourthRemainingMonth;
                                }
                                if ($i == $yearsRemaining) {
                                    $average_salary = ($secondYearSalary + $thirdYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                                }
                            }
                        } elseif ($yearsRemaining == 2) {
                            if ($retirementMonth == 12) {
                                // Handle two years remaining
                                $nextYearSalary = $current_salary;
                                $nextToNextYearSalary = $nextYearSalary + $nextYearSalary * $annualSalaryIncrement;
                                $average_salary = ($realCurrentSalary + $nextYearSalary + $nextToNextYearSalary) / 3;
                            } else {
                                // Handle two years remaining
                                $nextYearSalary = $current_salary;
                                $nextToNextYearSalary = $nextYearSalary + $nextYearSalary * $annualSalaryIncrement;

                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $nextToNextYearSalary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;
                                // Include second and third-year salaries
                                $fourthYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $fourthMonthlySalary = $fourthYearSalary / 12;
                                $fourthYearSalary = $fourthMonthlySalary * $fourthRemainingMonth;
                                $average_salary = ($realCurrentSalary + $nextYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                            }

                            break;
                        } elseif ($yearsRemaining == 1) {
                            if ($retirementMonth == 12) {
                                // Handle one year remaining
                                $lastYearSalary = $realCurrentSalary + $realCurrentSalary * $annualSalaryIncrement;
                                $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $average_salary = ($realCurrentSalary + $lastYearSalary + $previousYearSalary) / 3;
                                break;
                            } else {
                                $lastYearSalary = $realCurrentSalary + $realCurrentSalary * $annualSalaryIncrement;
                                $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $prePreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;

                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $lastYearSalary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;
                                // Include second and third-year salaries
                                $prePreviousMonthlySalary = $prePreviousYearSalary / 12;
                                $prePreviousYearSalary = $prePreviousMonthlySalary * $fourthRemainingMonth;
                                $average_salary = ($realCurrentSalary + $lastYearSalary + $previousYearSalary + $prePreviousYearSalary) / 3;
                            }
                        }
                    }
                } else {
                    if ($retirementMonth == 12) {
                        $realCurrentSalary = $realCurrentSalary;
                        $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                        $secondPreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;
                        $average_salary = $realCurrentSalary + $secondPreviousYearSalary + $previousYearSalary;
                        $average_salary = $average_salary / 3;
                    } else {
                        $realCurrentSalary = $realCurrentSalary;
                        $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                        $secondPreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;
                        $thirdPreviousYearSalary = $secondPreviousYearSalary / $annualSalaryDecrement;

                        $thirdPreviousMonthlySalary = $thirdPreviousYearSalary / 12;
                        $thirdPreviousYearSalary = $thirdPreviousMonthlySalary * $fourthRemainingMonth;

                        $currentMonthlySalary = $realCurrentSalary / 12;
                        $currentYearSalary = $currentMonthlySalary * $retirementMonth;

                        $average_salary = ($thirdPreviousYearSalary + $secondPreviousYearSalary + $previousYearSalary + $currentYearSalary) / 3;
                    }
                }
            } else {
                $salary_2 = intval(preg_replace('/\D/', '', $request->salary_2));
                $salary_3 = intval(preg_replace('/\D/', '', $request->salary_3));
                $salary_4 = intval(preg_replace('/\D/', '', $request->salary_4));
                $average_salary = ($salary_2 + $salary_3 + $salary_4) / 3;
            }
            $highThreeValue = HighThree::create([
                'fed_case_id' => $case->id,
                'value'       => $average_salary,
            ]);

            // Annual Leave Hours Payout calculate
            if ($request->input('income_employee_option') == 'yes') {
                $payout = $current_salary * $request->annual_leave_hours / 2080;
            } else {
                $salaries = [
                    $request->salary_2,
                    $request->salary_3,
                    $request->salary_4
                ];
                // Use the max function to find the highest salary
                $highestSalary = max($salaries);
                $highestSalary = intval(preg_replace('/\D/', '', $highestSalary));
                $payout = $highestSalary * $request->annual_leave_hours / 2080;
            }
            $annualLeavePayout = AnnualLeavePayout::create([
                'fed_case_id'  => $case->id,
                'payout'       => $payout,
            ]);

            // pension section
            $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
            if ($case->retirement_system) {
                $yosDollarAge = $yosDollar->value;
                // preg_match('/^(\d+)\s*Y/', $yosDollarAge, $matches);
                // if (isset($matches[1])) {
                //     $yosDollarAge = (int) $matches[1];
                // }
                if ($case->retirement_system == 'csrs') {
                    if ($case->employee_type == 'regular' || $case->employee_type == 'postal') {
                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    } else if (in_array($case->employee_type, $types)) {
                        $remainingAge = $yosDollarAge - 20;
                        $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 0.025 * 20) +
                            ($highThreeValue->value * 0.02 * $remainingAge);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    } else {
                        $pension = 0;
                    }
                } else if ($case->retirement_system == 'csrs_offset') {
                    // $date = $request->input('retirement_system_csrs_offset');
                    // $carbonDate = Carbon::parse($date);
                    // $year = $carbonDate->year;

                    // $retirmentDate = $request->input('retirement_type_date');
                    // $retirmentDate = Carbon::parse($retirmentDate);
                    // $retirmentYear = $retirmentDate->year;
                    // dd($retirmentYear - $year);
                    if ($case->employee_type == 'regular' || $case->employee_type == 'postal') {
                        // $yosDollarAge = $yosDollar->value;
                        // $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        // $pension = ($highThreeValue->value * 5 * 0.015) + 
                        //             ($highThreeValue->value * 5 * 0.0175) + 
                        //             ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);

                        // $pensionMonthly = $pension / 12;

                        // $retirement_type_date = $case->retirement_type_date;
                        // $retirement_type_date = Carbon::parse($retirement_type_date);
                        // $retirmentTypeDateYear = $retirement_type_date->year;

                        // $retirement_system_csrs_offset = $case->retirement_system_csrs_offset;
                        // $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        // $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;

                        // $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        // $csrsPension = $case->amount_1 * $csrfOffsetYears / 40;
                        // $pension = $pension - $csrsPension;
                        // $pension = $pension * 12;

                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    } else if (in_array($case->employee_type, $types)) {
                        // $yosDollarAge = $yosDollar->value;
                        // $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        // $pension = ($highThreeValue->value * 5 * 0.015) + 
                        //             ($highThreeValue->value * 5 * 0.0175) + 
                        //             ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);

                        // $pensionMonthly = $pension / 12;

                        // $retirement_type_date = $case->retirement_type_date;
                        // $retirement_type_date = Carbon::parse($retirement_type_date);
                        // $retirmentTypeDateYear = $retirement_type_date->year;

                        // $retirement_system_csrs_offset = $case->retirement_system_csrs_offset;
                        // $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        // $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;

                        // $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        // $csrsPension = $case->amount_1 * $csrfOffsetYears / 40;
                        // $pension = $pensionMonthly - $csrsPension;
                        // $pension = $pension * 12;
                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    } else {
                        $pension = 0;
                    }
                } else if ($case->retirement_system == 'fers' || $case->retirement_system == 'fers_rea' || $case->retirement_system == 'fers_frea') {
                    if ($case->employee_type == 'regular' || $case->employee_type == 'postal') {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];

                        if ($numericPart >= 62 && $yosDollarAge >= 20) {
                            $pension = $yosDollarAge * $highThreeValue->value * 0.011;
                            $pension = Pension::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $pension,
                            ]);
                        } else {
                            $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                    } else if (in_array($case->employee_type, $types)) {
                        $startDate = Carbon::parse($request->input('scd'));
                        $today = Carbon::now();
                        $yearsDifference = $startDate->diffInYears($today);
                        $yearsDifference = intval($yearsDifference);

                        if ($yearsDifference >= 20) {
                            $remainAgeYOS = $yosDollarAge - 20;
                            $pension1 = $remainAgeYOS * $highThreeValue->value * 0.01;
                            $firstYearPension = 20 * $highThreeValue->value * 0.017;
                            $pension = $pension1 + $firstYearPension;
                            $pension = Pension::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $pension,
                                'first_year'     => $firstYearPension,
                            ]);
                        } else {
                            $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                    } else {
                        $pension = 0;
                    }
                } else if ($case->retirement_system == 'fers_transfer') {
                    if ($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal') {
                        $retirement_type_date = $case->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;

                        $retirement_system_fers_transfer = $case->retirement_system_fers_transfer;
                        $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                        $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                        $rscd = $case->rscd;
                        $rscd = Carbon::parse($rscd);
                        $rscd = $rscd->year;

                        $csrsYear = $retirment_fers_transfer_year - $rscd;

                        $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                        $csrsPension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($csrsYear - 10) * 0.02);

                        $fersPension = $fersYear * $highThreeValue->value * 0.01;

                        $pension = $csrsPension + $fersPension;
                        $pension = Pension::create([
                            'fed_case_id' => $case->id,
                            'amount'      => $pension,
                        ]);
                    } else if (in_array($case->employee_type, $types)) {
                        $retirement_type_date = $case->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;

                        $retirement_system_fers_transfer = $case->retirement_system_fers_transfer;
                        $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                        $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                        $rscd = $case->rscd;
                        $rscd = Carbon::parse($rscd);
                        $rscd = $rscd->year;

                        $csrsYear = $retirment_fers_transfer_year - $rscd;

                        $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                        $csrsPension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($csrsYear - 10) * 0.02);

                        $fersPension = $fersYear * $highThreeValue->value * 0.01;

                        $pension = $csrsPension + $fersPension;
                        $pension = Pension::create([
                            'fed_case_id' => $case->id,
                            'amount'      => $pension,
                        ]);
                    } else {
                        $pension = 0;
                    }
                } else {
                    $pension = 0;
                }
            }


            if ($request->employee_spouse == 'yes') {
                if ($request->survior_benefit_fers != null) {
                    if ($request->survior_benefit_fers == 50) {
                        $pensionAmount = $pension->amount;
                        $survivorBenefitCost = $pensionAmount * 0.1;
                    } else if ($request->survior_benefit_fers == 25) {
                        $pensionAmount = $pension->amount;
                        $survivorBenefitCost = $pensionAmount * 0.05;
                    } else {
                        $survivorBenefitCost = 0;
                    }
                } else if ($request->survior_benefit_csrs != null) {
                    $pensionAmount = $pension->amount;
                    $totalLeavePension = $pensionAmount * $request->survior_benefit_csrs / 100;
                    if ($totalLeavePension <= 3600) {
                        $survivorBenefitCost = $totalLeavePension * 0.025;
                    } else {
                        $remainningLeavePension = $totalLeavePension - 3600;
                        $survivorBenefitFirst = 3600 * 0.025;
                        $survivorBenefitSecond = $remainningLeavePension * 0.1;
                        $survivorBenefitCost = $survivorBenefitFirst + $survivorBenefitSecond;
                    }
                } else {
                    $survivorBenefitCost = 0;
                }
                $survivorBenefit = SurvivorBenefit::create([
                    'fed_case_id'  => $case->id,
                    'cost'         => $survivorBenefitCost,
                ]);
            }

            // part time pension
            if ($request->employee_work == 'yes') {
                $empolyee_multiple_date = Carbon::parse($request->empolyee_multiple_date);
                $empolyee_multiple_date_to = Carbon::parse($request->empolyee_multiple_date_to);
                $employeePartTimeDays = $empolyee_multiple_date->diffInDays($empolyee_multiple_date_to);
                $employeePartTimeDays = $employeePartTimeDays * $case->empolyee_hours_work;
                $partTimePercentage = $case->empolyee_hours_work / 40;
                $partTimePensionAmount = $partTimePercentage * $employeePartTimeDays;
                $partTimePension = PartTimePension::create([
                    'fed_case_id'  => $case->id,
                    'amount'       => $partTimePensionAmount,
                ]);
            }


            // insurance section
            if (!empty($request->insurance)) {
                if ($request->insurance == 'yes') {
                    if ($request->insurance_emloyee == 'yes' && $request->insurance_retirement == 'yes') {
                        $client_age = $request->age;
                        $client_age = explode('Y', $client_age)[0];
                        if ($request->insurance_coverage_basic_option == 'basic_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = $salary / 1000;
                            $salary = intval($salary);


                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            $insuranceCostOptionBasic = $salary * 0.3467;
                            $insuranceCostOptionBasic = $insuranceCostOptionBasic * 12;
                        } else {
                            $insuranceCostOptionBasic = 0;
                        }
                        if ($request->insurance_coverage_a_option == 'a_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);
                            $salary =  $salary + 2000;
                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            if ($client_age < 35) {
                                $insuranceCostOptionA = $salary * 0.43;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionA = $salary * 0.43;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionA = $salary * 0.65;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionA = $salary * 1.30;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionA = $salary * 2.17;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionA = $salary * 3.90;
                            } else {
                                $insuranceCostOptionA = $salary * 13.00;
                            }
                        } else {
                            $insuranceCostOptionA = 0;
                        }
                        if ($request->insurance_coverage_b_option == 'b_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;

                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);

                            $annualPay = $salary * $request->option_b_value;
                            $monthlyBasic = $annualPay / 1000;

                            if ($client_age < 35) {
                                $insuranceCostOptionB = $monthlyBasic * 0.043;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionB = $monthlyBasic * 0.043;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionB = $monthlyBasic * 0.065;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionB = $monthlyBasic * 0.130;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionB = $monthlyBasic * 0.217;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionB = $monthlyBasic * 0.390;
                            } else if ($client_age >= 60 && $client_age <= 64) {
                                $insuranceCostOptionB = $monthlyBasic * 0.867;
                            } else if ($client_age >= 65 && $client_age <= 69) {
                                $insuranceCostOptionB = $monthlyBasic * 1.040;
                            } else if ($client_age >= 70 && $client_age <= 74) {
                                $insuranceCostOptionB = $monthlyBasic * 1.863;
                            } else if ($client_age >= 75 && $client_age <= 79) {
                                $insuranceCostOptionB = $monthlyBasic * 3.900;
                            } else {
                                $insuranceCostOptionB = $monthlyBasic * 6.240;
                            }
                        } else {
                            $insuranceCostOptionB = 0;
                        }
                        if ($request->insurance_coverage_c_option == 'c_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);
                            $value = $request->insurance_employee_coverage_c;
                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            if ($client_age < 35) {
                                $insuranceCostOptionC = $value * 0.43;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionC = $value * 0.52;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionC = $value * 0.80;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionC = $value * 1.15;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionC = $value * 1.80;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionC = $value * 2.88;
                            } else if ($client_age >= 60 && $client_age <= 64) {
                                $insuranceCostOptionC = $value * 5.27;
                            } else if ($client_age >= 65 && $client_age <= 69) {
                                $insuranceCostOptionC = $value * 6.13;
                            } else if ($client_age >= 70 && $client_age <= 74) {
                                $insuranceCostOptionC = $value * 8.30;
                            } else if ($client_age >= 75 && $client_age <= 79) {
                                $insuranceCostOptionC = $value * 12.48;
                            } else {
                                $insuranceCostOptionC = $value * 16.90;
                            }
                        } else {
                            $insuranceCostOptionC = 0;
                        }
                        $fegliInsuranceCost = FEGLI::create([
                            'fed_case_id'   => $case->id,
                            'basic'         => $insuranceCostOptionBasic,
                            'optionA'       => $insuranceCostOptionA,
                            'optionB'       => $insuranceCostOptionB,
                            'optionC'       => $insuranceCostOptionC,
                        ]);
                    }
                }
            }


            // start calculations DENTAL AND VISION
            $todayDate = Carbon::now();
            $retirementDate = Carbon::parse($request->retirement_type_date);
            $yearsUntilRetirement = $todayDate->diff($retirementDate);
            $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
            if (!empty($request->vision_total_cost)) {
                $dentalVisionCombine = str_replace(',', '', $request->vision_total_cost);
                $dentalVisionCombine = intval($dentalVisionCombine);
                $dentalVisionCombine = $dentalVisionCombine * 26;
                $dentalPremiumAmount = 0;
                $visionPremiumAmount = 0;
                $dentalAndVisionValue = DentalAndVision::create([
                    'fed_case_id'         => $case->id,
                    'dentalPremiumAmount' => $dentalPremiumAmount,
                    'visionPremiumAmount' => $visionPremiumAmount,
                    'dentalVisionCombine' => $dentalVisionCombine,
                ]);
            } else {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                if ($request->dental == 'yes' && $request->vision == 'no') {
                    $dentalPremiumAmount = $dentalPremiumAmount * 26;
                    $visionPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                } else if ($request->vision == 'yes' && $request->dental == 'no') {
                    $visionPremiumAmount = $visionPremiumAmount * 26;
                    $dentalPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                } else if ($request->dental == 'yes' && $request->vision == 'yes') {
                    $dentalPremiumAmount = $dentalPremiumAmount * 26;
                    $visionPremiumAmount = $visionPremiumAmount * 26;
                    $dentalVisionCombine = 0;
                } else {
                    $dentalPremiumAmount = 0;
                    $visionPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                }
                $dentalAndVisionValue = DentalAndVision::create([
                    'fed_case_id'         => $case->id,
                    'dentalPremiumAmount' => $dentalPremiumAmount,
                    'visionPremiumAmount' => $visionPremiumAmount,
                    'dentalVisionCombine' => $dentalVisionCombine,
                ]);
            }
            // end calculations DENTAL AND VISION


            // start FEHB VARIOUS PLANS calculations
            if ($request->coverage_retirement == 'yes') {
                $todayDate = Carbon::now();
                $retirementDate = Carbon::parse($request->retirement_type_date);
                $yearsUntilRetirement = $todayDate->diff($retirementDate);
                $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;

                $fehbPremiumAmount = str_replace(',', '', $request->premium);
                $fehbPremiumAmount = intval($fehbPremiumAmount);
                $fehbPremiumAmount = $fehbPremiumAmount * 26;
                // for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                // $fehbPremiumAmount += $fehbPremiumAmount * (5 / 100);
                // }
                $fehbVPValue = FEHBVP::create([
                    'fed_case_id'         => $case->id,
                    'fehbPremiumAmount'   => $fehbPremiumAmount,
                ]);
            } else {
                $fehbPremiumAmount = 0;
                $fehbVPValue = FEHBVP::create([
                    'fed_case_id'         => $case->id,
                    'fehbPremiumAmount'   => $fehbPremiumAmount,
                ]);
            }

            // end FEHB VARIOUS PLANS calculations

            // start FLTCIP calculations 
            if ($request->insurance_program == 'yes') {
                if ($request->insurance_program_retirement == 'yes') {
                    preg_match('/(\d+)\s*Y/', $request->age, $matches);
                    $age = (int)$matches[1];
                    $age = intval((95 - $age) / 5);

                    $insurancePurchasePremiumAmount = str_replace(',', '', $request->insurance_purchase_premium);
                    $insurancePurchasePremiumAmount = intval($insurancePurchasePremiumAmount);
                    $insurancePurchasePremiumAmount = $insurancePurchasePremiumAmount * 26;
                    $yearlyPremiumAmount = $insurancePurchasePremiumAmount;
                    for ($i = 1; $i <= $age; $i++) {
                        $insurancePurchasePremiumAmount += $insurancePurchasePremiumAmount * (85 / 100);
                    }
                } else {
                    $todayDate = Carbon::now();
                    $retirementDate = Carbon::parse($request->retirement_type_date);
                    $yearsUntilRetirement = $todayDate->diff($retirementDate);
                    $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
                    $yearsUntilRetirementCalculate = $yearsUntilRetirementCalculate / 5;

                    $insurancePurchasePremiumAmount = str_replace(',', '', $request->insurance_purchase_premium);
                    $insurancePurchasePremiumAmount = intval($insurancePurchasePremiumAmount);
                    $insurancePurchasePremiumAmount = $insurancePurchasePremiumAmount * 26;
                    $yearlyPremiumAmount = $insurancePurchasePremiumAmount;
                    for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                        $insurancePurchasePremiumAmount += $insurancePurchasePremiumAmount * (85 / 100);
                    }
                }
                $fltcipValue = FLTCIP::create([
                    'fed_case_id'                    => $case->id,
                    'yearlyPremiumAmount'            => $yearlyPremiumAmount,
                    'insurancePurchasePremiumAmount' => $insurancePurchasePremiumAmount,
                ]);
            }
            // end FLTCIP calculations 

            return response()->json([
                'status'  => true,
                'message' => 'Case added successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function show(FedCase $fedCase)
    {
        $highThree = HighThree::where('fed_case_id', $fedCase->id)->first();
        $yosDollar = YosDollar::where('fed_case_id', $fedCase->id)->first();
        $pension = Pension::where('fed_case_id', $fedCase->id)->first();
        $fehbVP = FEHBVP::where('fed_case_id', $fedCase->id)->first();
        $dentalAndVision = DentalAndVision::where('fed_case_id', $fedCase->id)->first();
        $insuranceCost = FEGLI::where('fed_case_id', $fedCase->id)->first();
        $tspCalculation = TSP::where('fed_case_id', $fedCase->id)->first();
        // $tspCalculation = TSPCalculate::where('fed_case_id',$fedCase->id)->first();
        $srsData = SRS::where('fed_case_id', $fedCase->id)->first();
        $socialSecurity = SocialSecurity::where('fed_case_id', $fedCase->id)->first();
        $survivorBenefit = SurvivorBenefit::where('fed_case_id', $fedCase->id)->first();
        $fltcip = FLTCIP::where('fed_case_id', $fedCase->id)->first();
        $incrementPercentageValues = PercentageValue::first();

        $annualSalaryIncrement = $incrementPercentageValues->annual_salary_increment;
        $annualSalaryIncrement = $annualSalaryIncrement / 100;
        if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_transfer' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
            $annualColaIncrement = $incrementPercentageValues->fers_cola;
        } else {
            $annualColaIncrement = $incrementPercentageValues->csrs_cola;
        }
        $annualColaIncrement = $annualColaIncrement / 100;

        // FEGLI
        if ($insuranceCost) {
            $fegliAmountTotal = $insuranceCost->basic + $insuranceCost->optionA + $insuranceCost->optionB + $insuranceCost->optionC;
            $fegliAmountArray = [];
        } else {
            $fegliAmountTotal = 0;
            $fegliAmountArray = [];
        }

        // Convert the premium text to an integer
        $premium = str_replace(',', '', $fedCase->InsurancePlan->premium);
        $premium = intval($premium);
        // Convert the current salary text to an integer
        $currentSalary = str_replace(',', '', $fedCase->salary_1);
        $currentSalary = intval($currentSalary);
        // Multiply the premium by 26
        $initialPremium = $premium * 26;

        // Employee's current age
        preg_match('/(\d+)\s*Y/', $fedCase->age, $matches);
        $currentAge = (int)$matches[1];

        // employee retirement age
        preg_match('/(\d+)\s*Y/', $fedCase->retirement_type_age, $matches);
        $retirementAge = (int)$matches[1];

        $pensionArray = [];
        if (!empty($pension->amount)) {
            $pensionAmount = $pension->amount;
        } else {
            $pensionAmount = 0;
        }

        // survivor benefit 
        $SurvivorBenefitArray = [];
        $survivorBenefitAmount = $survivorBenefit->cost;
        // Calculate the premiums for each year until age 90
        $premiums = [];
        $premiumAmount = $initialPremium;

        $gFundAmount = $tspCalculation->gFund;
        $fFundAmount = $tspCalculation->fFund;
        $cFundAmount = $tspCalculation->cFund;
        $sFundAmount = $tspCalculation->sFund;
        $iFundAmount = $tspCalculation->iFund;
        $lFundAmount = $tspCalculation->lFund;
        $l2025FundAmount = $tspCalculation->l2025Fund;
        $l2030FundAmount = $tspCalculation->l2030Fund;
        $l2035FundAmount = $tspCalculation->l2035Fund;
        $l2040FundAmount = $tspCalculation->l2040Fund;
        $l2045FundAmount = $tspCalculation->l2045Fund;
        $l2050FundAmount = $tspCalculation->l2050Fund;
        $l2055FundAmount = $tspCalculation->l2055Fund;
        $l2060FundAmount = $tspCalculation->l2060Fund;
        $l2065FundAmount = $tspCalculation->l2065Fund;

        $tspCalculationTotalArray = [];
        $tspCalculationTotal = $tspCalculation->totalTSPCalculate;
        $tspCalculationPercentage = $fedCase->tSP->contribute_pp_percentage;
        $rothCalculationPercentage = $fedCase->tSP->contribute_tsp_pp_percentage;
        $totalContributionPercentage = $tspCalculationPercentage + $rothCalculationPercentage;
        $MatchingPercentage = $tspCalculation->matchingPercentage;

        $totalContribution = $tspCalculation->totalContribution;
        $totalMatching = $tspCalculation->totalMatching;
        $totalConMatch = $totalContribution + $totalMatching;

        $gFundCon = $fedCase->g_value;
        $fFundCon = $fedCase->f_value;
        $cFundCon = $fedCase->c_value;
        $sFundCon = $fedCase->s_value;
        $iFundCon = $fedCase->i_value;
        $lFundCon = $fedCase->l_income_value;
        $l2025FundCon = $fedCase->l_2025_value;
        $l2030FundCon = $fedCase->l_2030_value;
        $l2035FundCon = $fedCase->l_2035_value;
        $l2040FundCon = $fedCase->l_2040_value;
        $l2045FundCon = $fedCase->l_2045_value;
        $l2050FundCon = $fedCase->l_2050_value;
        $l2055FundCon = $fedCase->l_2055_value;
        $l2060FundCon = $fedCase->l_2060_value;
        $l2065FundCon = $fedCase->l_2065_value;

        $totalIncome = [];
        $totalExpenses = [];

        // fehbVP
        preg_match('/(\d+)\s*Y/', $fedCase->age, $matches);
        $todayAge = (int)$matches[1];
        $caseDate = Carbon::parse($fedCase->updated_at);
        $currentYear = $caseDate->year;
        $retirementDate = Carbon::parse($fedCase->retirement_type_date);
        $retirementYear = $retirementDate->year;
        $diffRetirementCurrentYear = $retirementYear - $currentYear;
        // dd($todayAge);
        for ($age = $currentAge; $age <= 90; $age++) {
            if ($fedCase->InsurancePlan->coverage_retirement == 'no') {
                if ($fedCase->insurancePlan->federal == 'yes') {
                    if ($todayAge < $retirementAge) {
                        $premiums[] = intVal($premiumAmount);
                        $premiumAmount += $premiumAmount * $incrementPercentageValues->fehb_increment / 100;
                    } else {
                        $premiums[] = null; // when calculate only to retirement date after retirement value is null
                    }
                } else {
                    $premiums[] = null;
                }
                $todayAge++;
            } else {
                $premiums[] = intVal($premiumAmount);
                $premiumAmount += $premiumAmount * $incrementPercentageValues->fehb_increment / 100; // Increment by 5%
            }
        }

        // for SRS
        if ($srsData) {
            $srsArray = [];
            $srsAmount = $srsData->amount;
        } else {
            $srsArray = [];
            $srsAmount = 0;
        }

        // for SS
        $ssAmount = $socialSecurity->amount;
        $ssArray = [];
        $ssAmount62 = str_replace(',', '', $fedCase->amount_1);
        // for SS and SRS
        if ($fedCase->amount_2) {
            $srsAgeNumber = $fedCase->amount_2;
        } else {
            $srsAgeNumber = 62;
        }
        $dob = \Carbon\Carbon::parse($fedCase->dob);
        $dobMonth = $dob->month;
        $dobMonth = 12 - $dobMonth + 1;

        // dd($retirementAge);

        if($fedCase->employee_eligible == 'yes')
        {
            $dobYear = $dob->year;
            if($dobYear <= 1937)
            {
                $fullRetirementAgeMonths = "65Y, 0M";
            }
            else if($dobYear == 1938)
            {
                $fullRetirementAgeMonths = "65Y, 2M";
            }
            else if($dobYear == 1939)
            {
                $fullRetirementAgeMonths = "65Y, 4M";
            }
            else if($dobYear == 1940)
            {
                $fullRetirementAgeMonths = "65Y, 6M";
            }
            else if($dobYear == 1941)
            {
                $fullRetirementAgeMonths = "65Y, 8M";
            }
            else if($dobYear == 1942)
            {
                $fullRetirementAgeMonths = "65Y, 10M";
            }
            else if($dobYear >= 1943 && $dobYear <= 1954)
            {
                $fullRetirementAgeMonths = "66Y, 0M";
            }
            else if($dobYear == 1955)
            {
                $fullRetirementAgeMonths = "66Y, 2M";
            }
            else if($dobYear == 1956)
            {
                $fullRetirementAgeMonths = "66Y, 4M";
            }
            else if($dobYear == 1957)
            {
                $fullRetirementAgeMonths = "66Y, 6M";
            }
            else if($dobYear == 1958)
            {
                $fullRetirementAgeMonths = "66Y, 8M";
            }
            else if($dobYear == 1959)
            {
                $fullRetirementAgeMonths = "66Y, 10M";
            }
            else
            {
                $fullRetirementAgeMonths = "67Y, 0M";
            }

            if($fedCase->amount_2 > 62)
            {
                $drawAge = $fedCase->amount_2;
            }
            else
            {
                $drawAge = 62;
            }

            $fraBenefit = $ssAmount62 / 0.70;

            preg_match('/(\d+)\s*Y/', $fullRetirementAgeMonths, $matches);
            $fraAgeCheck = (int)$matches[1];
            preg_match('/(\d+)\s*M/', $fullRetirementAgeMonths, $matches);
            $fraAgeMonth = (int)$matches[1];

            if($fraAgeCheck == "65")
            {
                for($i = 62; $i<=70; $i++)
                {
                    if($i == 62)
                    {
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $ssAmount62,
                        ];
                    }
                    else if($i == 63)
                    {
                        $reduction = (24 + $fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 64)
                    {
                        $reduction = (12+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 65)
                    {
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $fraBenefit,
                        ];
                    }
                    else if($i == 66)
                    {
                        $reduction = 12*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 67)
                    {
                        $reduction = 24*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 68)
                    {
                        $reduction = 36*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 69)
                    {
                        $reduction = 48*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 70)
                    {
                        $reduction = 60*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                }
                
            }
            else if($fraAgeCheck == "66")
            {
                for($i = 62; $i<=70; $i++)
                {
                    if($i == 62)
                    {
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $ssAmount62,
                        ];
                    }
                    else if($i == 63)
                    {
                        $reduction = (36+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 64)
                    {
                        
                        $reduction = (24+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 65)
                    {
                        
                        $reduction = (12+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 66)
                    {
                        
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $fraBenefit,
                        ];
                    }
                    else if($i == 67)
                    {
                        $reduction = 12*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 68)
                    {
                        $reduction = 24*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 69)
                    {
                        $reduction = 36*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 70)
                    {
                        $reduction = 48*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                }
            }
            else
            {
                for($i = 62; $i<=70; $i++)
                {
                    if($i == 62)
                    {
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $ssAmount62,
                        ];
                    }
                    else if($i == 63)
                    {
                        $reduction = 36*5/9  +  (12 + $fraAgeMonth)* 5/12;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 64)
                    {
                        $reduction = (36+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 65)
                    {
                        $reduction = (24+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 66)
                    {
                        $reduction = (12+$fraAgeMonth)*5/9;
                        $monthlyBenefit = $fraBenefit - ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 67)
                    {
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $fraBenefit,
                        ];
                    }
                    else if($i == 68)
                    {
                        $reduction = 12*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 69)
                    {
                        $reduction = 24*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                    else if($i == 70)
                    {
                        $reduction = 36*2/3;
                        $monthlyBenefit = $fraBenefit + ($fraBenefit * $reduction/100);
                        $ageSsArray[] = [
                            'age' => $i,
                            'ssMonthlyBenefit' => $monthlyBenefit,
                        ];
                    }
                }
                // loop for get draw age amount
                foreach ($ageSsArray as $entry) {
                    if ($entry['age'] == $drawAge) {
                        $drawAmount = $entry['ssMonthlyBenefit'];
                        break;
                    }
                }
            }
        }
        else
        {
            $ssYearlyAmount = 0;
        }
        // dd($drawAmount);
        $ssYearlyAmount = $drawAmount * 12;
        $ssFirstYearAmount = $drawAmount * $dobMonth;
        // dd($ssFirstYearAmount);
        $ssRetireAge = $retirementAge;

        if($drawAge < $ssRetireAge)
        {
            for($j=$drawAge;$j<$ssRetireAge;$j++)
            {
                $ssYearlyAmount += $ssYearlyAmount * 0.025;
                $ssYearlyAmount = intVal($ssYearlyAmount);
            }
        }
        
        for ($age = $currentAge; $age <= 90; $age++) {
            // for SS
            if($age < $retirementAge)
            {
                $ssArray[] = null;
                $srsArray[] = null;
                $pensionArray[] = null;
                $SurvivorBenefitArray[] = null;
            }
            else
            {
                if ($currentAge <= $age) {
                    $pensionArray[] = $pensionAmount;
                    $pensionAmount += $pensionAmount * $annualColaIncrement;
                    $pensionAmount = intVal($pensionAmount);
                } else {
                    $pensionArray[] = null;
                }

                if ($age < $srsAgeNumber) {
                    $ssArray[] = null;
                } else {
                    if ($age == $srsAgeNumber && $dobMonth < 12) {
                        if ($currentAge <= $age) {
                            $ssArray[] = intVal($ssFirstYearAmount);
                            $ssYearlyAmount += $ssYearlyAmount * 0.025;
                            $ssYearlyAmount = intVal($ssYearlyAmount);
                        } else {
                            $ssArray[] = null;
                        }
                    } else {
                        if ($currentAge <= $age) {
                            if($ssYearlyAmount == 0)
                            {
                                $ssArray[] = null;
                            }
                            else
                            {
                                    $ssArray[] = intVal($ssYearlyAmount);
                                    $ssYearlyAmount += $ssYearlyAmount * 0.025;
                                    $ssYearlyAmount = intVal($ssYearlyAmount);
                            }
                        } else {
                            $ssArray[] = null;
                        }
                    }
                }
                // for SRS
                if ($age < 62) {
                    if ($currentAge <= $age) {
                        $retirementDate = Carbon::parse($fedCase->retirement_type_date);
                        $retirementMonth = $retirementDate->month;
                        $retirementMonth = 12 - $retirementMonth;
                        if ($age == $currentAge) {
                            $srsMonthAmount = $srsAmount / 12;
                            $srsFirstYearAmount = $srsMonthAmount * $retirementMonth;
                            $srsArray[] = intVal($srsFirstYearAmount);
                        } else if ($age == 61) {
                            $dobDate = Carbon::parse($fedCase->dob);
                            $dobMonth = $dobDate->month;
                            $dobMonth = $dobMonth - 1;
                            if ($dobMonth == 0) {
                                $dobMonth = 12;
                            }
                            $srsMonthAmount = $srsAmount / 12;
                            $srsLastYearAmount = $srsMonthAmount * $dobMonth;
                            $srsArray[] = intVal($srsLastYearAmount);
                        } else {
                            $srsArray[] = intVal($srsAmount);
                        }
                    } else {
                        $srsArray[] = null;
                    }
                } 
                else 
                {
                    $srsArray[] = null;
                }

                
                // Survivor benefit 
                
                if ($fedCase->employee_spouse == 'yes') 
                {
                    $SurvivorBenefitArray[] = intVal($survivorBenefitAmount);
                    if ($fedCase->survior_benefit_fers != null) {
                        if ($fedCase->survior_benefit_fers == 50) {
                            $survivorBenefitAmount = $pensionAmount * 0.1;
                        } else if ($fedCase->survior_benefit_fers == 25) {
                            $survivorBenefitAmount = $pensionAmount * 0.05;
                        } else {
                            $survivorBenefitAmount = 0;
                        }
                    } else if ($fedCase->survior_benefit_csrs != null) {
                        $totalLeavePension = $pensionAmount * $fedCase->survior_benefit_csrs / 100;
                        if ($totalLeavePension <= 3600) {
                            $survivorBenefitAmount = $totalLeavePension * 0.025;
                        } else {
                            $remainningLeavePension = $totalLeavePension - 3600;
                            $survivorBenefitFirst = 3600 * 0.025;
                            $survivorBenefitSecond = $remainningLeavePension * 0.1;
                            $survivorBenefitAmount = $survivorBenefitFirst + $survivorBenefitSecond;
                        }
                    } else {
                        $survivorBenefitAmount = 0;
                    }
                }
                else
                {
                    $SurvivorBenefitArray[] = null;
                }
            }
            
        }

        // dental and vision
        if ($fedCase->insurancePlan->vision_total_cost) {
        }
        $dentalValue = intval($dentalAndVision->dentalPremiumAmount);
        $visionValue = intval($dentalAndVision->visionPremiumAmount);
        $dentalAndVisionCombineValue = intval($dentalAndVision->dentalVisionCombine);
        $dentalValueArray = [];
        $visionValueArray = [];
        $dentalAndVisionCombineValueArray = [];
        $fegliSalary = $currentSalary;
        for ($age = $currentAge; $age <= 90; $age++) {
            $ageCount[] = $age;
            // FEGLI start 
            $fegliSalary = $fegliSalary * 1.03;
            $fegliSalaryRounded = intVal($fegliSalary / 1000);
            $fegliOptBVal = $fedCase->insurancePlan->option_b_value;
            $fegliOptCVal = $fedCase->insurancePlan->option_c_value;
            if($fedCase->insurancePlan->insurance == 'yes')
            {
                if($fedCase->insurancePlan->insurance_emloyee == 'yes' && $fedCase->insurancePlan->insurance_retirement == 'yes')
                {
                    // basic option start
                    if($fedCase->insurancePlan->insurance_coverage_basic_option == 'basic_option')
                    {
                        if($age <= 65)
                        {
                            if($fedCase->insurancePlan->basic_option_select == '75')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 0.3467 * 12;
                            }
                            else if($fedCase->insurancePlan->basic_option_select == '50')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 1.0967 * 12;
                            }
                            else if($fedCase->insurancePlan->basic_option_select == 'no')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 2.5967 * 12;
                            }
                            else
                            {
                                $fegliBasicAmount = null;
                            }
                        }
                        else
                        {
                            if($fedCase->insurancePlan->basic_option_select == '75')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 12;
                            }
                            else if($fedCase->insurancePlan->basic_option_select == '50')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 0.75 * 12;
                            }
                            else if($fedCase->insurancePlan->basic_option_select == 'no')
                            {
                                $fegliBasicAmount = $fegliSalaryRounded * 2.25 * 12;
                            }
                            else
                            {
                                $fegliBasicAmount = null;
                            }
                        }
                    }
                    else
                    {
                        $fegliBasicAmount = null;
                    }

                    // basic option end

                    // start option A calculations
                    if($fedCase->insurancePlan->insurance_coverage_a_option == 'a_option')
                    {
                        if($age >= $retirementAge)  //for retiree
                        {
                            if($age < 40)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.43 * 12;
                            }
                            else if($age >= 40 && $age < 45)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.65 * 12;
                            }
                            else if($age >= 45 && $age < 50)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 1.30 * 12;
                            }
                            else if($age >= 50 && $age < 55)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 2.17 * 12;
                            }
                            else if($age >= 55 && $age < 60)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 3.90 * 12;
                            }
                            else if($age >= 60 && $age < 65)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 13 * 12;
                            }
                            else
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 12;
                            }
                            
                        }
                        else            //for employee
                        {
                            if($age < 40)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.43 * 12;
                            }
                            else if($age >= 40 && $age < 45)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.65 * 12;
                            }
                            else if($age >= 45 && $age < 50)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 1.30 * 12;
                            }
                            else if($age >= 50 && $age < 55)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 2.17 * 12;
                            }
                            else if($age >= 55 && $age < 60)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 3.90 * 12;
                            }
                            else
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 13 * 12;
                            }
                        }
                    }
                    else
                    {
                        $fegliAOptAmount = null;
                    }
                    // end option A calculations

                    // option B start
                    if($fedCase->insurancePlan->insurance_coverage_b_option == 'b_option')
                    {
                        if($age < 40)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.043 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 40 && $age < 45)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.065 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 45 && $age < 50)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.130 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 50 && $age < 55)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.217 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 55 && $age < 60)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.390 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 60 && $age < 65)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 0.867 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 65 && $age < 70)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 1.040 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 70 && $age < 75)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 1.863 * 12 * $fegliOptBVal;
                        }
                        else if($age >= 75 && $age < 80)
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 3.9 * 12 * $fegliOptBVal;
                        }
                        else
                        {
                            $fegliBOptAmount = $fegliSalaryRounded * 6.240 * 12 * $fegliOptBVal;
                        }
                    }
                    else
                    {
                        $fegliBOptAmount = null;
                    }
                    // option B end

                    // option C start
                    if($fedCase->insurancePlan->insurance_coverage_c_option == 'c_option')
                    {
                        if($age < 35)
                        {
                            $fegliCOptAmount = 0.43 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 35 && $age < 40)
                        {
                            $fegliCOptAmount = 0.52 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 40 && $age < 45)
                        {
                            $fegliCOptAmount = 0.80 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 45 && $age < 50)
                        {
                            $fegliCOptAmount = 1.15 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 50 && $age < 55)
                        {
                            $fegliCOptAmount = 1.8 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 55 && $age < 60)
                        {
                            $fegliCOptAmount = 2.88 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 60 && $age < 65)
                        {
                            $fegliCOptAmount = 5.27 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 65 && $age < 70)
                        {
                            $fegliCOptAmount = 6.13 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 70 && $age < 75)
                        {
                            $fegliCOptAmount = 8.3 * 12 * $fegliOptCVal;
                        }
                        else if($age >= 75 && $age < 80)
                        {
                            $fegliCOptAmount = 12.48 * 12 * $fegliOptCVal;
                        }
                        else
                        {
                            $fegliCOptAmount = 16.9 * 12 * $fegliOptCVal;
                        }
                    }
                    else
                    {
                        $fegliCOptAmount = null;
                    }
                    // option C end
                }
                else
                {
                    if($age >= $retirementAge)
                    {
                        $fegliBasicAmount = null;
                        $fegliAOptAmount = null;
                        $fegliBOptAmount = null;
                        $fegliCOptAmount = null;
                    }
                    else
                    {
                        // basic option start
                        if($fedCase->insurancePlan->insurance_coverage_basic_option == 'basic_option')
                        {
                            if($age <= 65)
                            {
                                if($fedCase->insurancePlan->basic_option_select == '75')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 0.3467 * 12;
                                }
                                else if($fedCase->insurancePlan->basic_option_select == '50')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 1.0967 * 12;
                                }
                                else if($fedCase->insurancePlan->basic_option_select == 'no')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 2.5967 * 12;
                                }
                                else
                                {
                                    $fegliBasicAmount = null;
                                }
                            }
                            else
                            {
                                if($fedCase->insurancePlan->basic_option_select == '75')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 12;
                                }
                                else if($fedCase->insurancePlan->basic_option_select == '50')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 0.75 * 12;
                                }
                                else if($fedCase->insurancePlan->basic_option_select == 'no')
                                {
                                    $fegliBasicAmount = $fegliSalaryRounded * 2.25 * 12;
                                }
                                else
                                {
                                    $fegliBasicAmount = null;
                                }
                            }
                        }
                        else
                        {
                            $fegliBasicAmount = null;
                        }

                        // basic option end

                        // start option A calculations
                        if($fedCase->insurancePlan->insurance_coverage_a_option == 'a_option')
                        {
                            if($age < 40)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.43 * 12;
                            }
                            else if($age >= 40 && $age < 45)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 0.65 * 12;
                            }
                            else if($age >= 45 && $age < 50)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 1.30 * 12;
                            }
                            else if($age >= 50 && $age < 55)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 2.17 * 12;
                            }
                            else if($age >= 55 && $age < 60)
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 3.90 * 12;
                            }
                            else
                            {
                                $fegliAOptAmount = $fegliSalaryRounded * 13 * 12;
                            }
                        }
                        else
                        {
                            $fegliAOptAmount = null;
                        }
                        // end option A calculations

                        // option B start
                        if($fedCase->insurancePlan->insurance_coverage_b_option == 'b_option')
                        {
                            if($age < 40)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.043 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 40 && $age < 45)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.065 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 45 && $age < 50)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.130 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 50 && $age < 55)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.217 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 55 && $age < 60)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.390 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 60 && $age < 65)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 0.867 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 65 && $age < 70)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 1.040 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 70 && $age < 75)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 1.863 * 12 * $fegliOptBVal;
                            }
                            else if($age >= 75 && $age < 80)
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 3.9 * 12 * $fegliOptBVal;
                            }
                            else
                            {
                                $fegliBOptAmount = $fegliSalaryRounded * 6.240 * 12 * $fegliOptBVal;
                            }
                        }
                        else
                        {
                            $fegliBOptAmount = null;
                        }
                        // option B end

                        // option C start
                        if($fedCase->insurancePlan->insurance_coverage_c_option == 'c_option')
                        {
                            if($age < 35)
                            {
                                $fegliCOptAmount = 0.43 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 35 && $age < 40)
                            {
                                $fegliCOptAmount = 0.52 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 40 && $age < 45)
                            {
                                $fegliCOptAmount = 0.80 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 45 && $age < 50)
                            {
                                $fegliCOptAmount = 1.15 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 50 && $age < 55)
                            {
                                $fegliCOptAmount = 1.8 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 55 && $age < 60)
                            {
                                $fegliCOptAmount = 2.88 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 60 && $age < 65)
                            {
                                $fegliCOptAmount = 5.27 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 65 && $age < 70)
                            {
                                $fegliCOptAmount = 6.13 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 70 && $age < 75)
                            {
                                $fegliCOptAmount = 8.3 * 12 * $fegliOptCVal;
                            }
                            else if($age >= 75 && $age < 80)
                            {
                                $fegliCOptAmount = 12.48 * 12 * $fegliOptCVal;
                            }
                            else
                            {
                                $fegliCOptAmount = 16.9 * 12 * $fegliOptCVal;
                            }
                        }
                        else
                        {
                            $fegliCOptAmount = null;
                        }
                        // option C end

                    }
                }
                
            }
            else
            {
                $fegliBasicAmount = null;
                $fegliAOptAmount = null;
                $fegliBOptAmount = null;
                $fegliCOptAmount = null;
            }

            $fegliAmountArray[] = $fegliBasicAmount + $fegliAOptAmount + $fegliBOptAmount + $fegliCOptAmount;
            // fegli end
            
            
            // dental and vision
            if (!empty($dentalAndVisionCombineValue)) {
                $dentalAndVisionCombineValueArray[] = $dentalAndVisionCombineValue;
                $dentalAndVisionCombineValue += $dentalAndVisionCombineValue * 0.05;
                $dentalAndVisionCombineValue = intVal($dentalAndVisionCombineValue);
                $dentalValueArray[] = 0;
                $visionValueArray[] = 0;
            } else {
                if (!empty($dentalValue) && !empty($visionValue)) {
                    if ($fedCase->insurancePlan->dental_retirement == 'yes' && $fedCase->insurancePlan->vision_retirement == 'yes') {
                        $dentalAndVisionCombineValueArray[] = 0;
                        $dentalValueArray[] = $dentalValue;
                        $dentalValue += $dentalValue * 0.05;
                        $dentalValue = intVal($dentalValue);
                        $visionValueArray[] = $visionValue;
                        $visionValue += $visionValue * 0.05;
                        $visionValue = intVal($visionValue);
                    } else if ($fedCase->insurancePlan->dental_retirement == 'yes' && $fedCase->insurancePlan->vision_retirement == 'no') {
                        $dentalValueArray[] = $dentalValue;
                        $dentalValue += $dentalValue * 0.05;
                        $dentalValue = intVal($dentalValue);
                        $dentalAndVisionCombineValueArray[] = 0;
                        if ($currentAge > $age) {
                            $visionValueArray[] = $visionValue;
                            $visionValue += $visionValue * 0.05;
                            $visionValue = intVal($visionValue);
                        } else {
                            $visionValueArray[] = null;
                        }
                    } else if ($fedCase->insurancePlan->dental_retirement == 'no' && $fedCase->insurancePlan->vision_retirement == 'yes') {
                        $visionValueArray[] = $visionValue;
                        $visionValue += $visionValue * 0.05;
                        $visionValue = intVal($visionValue);
                        $dentalAndVisionCombineValueArray[] = 0;
                        if ($currentAge > $age) {
                            $dentalValueArray[] = $dentalValue;
                            $dentalValue += $dentalValue * 0.05;
                            $dentalValue = intVal($dentalValue);
                        } else {
                            $dentalValueArray[] = null;
                        }
                    } else {
                        if ($currentAge > $age) {
                            $visionValueArray[] = $visionValue;
                            $visionValue += $visionValue * 0.05;
                            $visionValue = intVal($visionValue);
                            $dentalValueArray[] = $dentalValue;
                            $dentalValue += $dentalValue * 0.05;
                            $dentalValue = intVal($dentalValue);
                            $dentalAndVisionCombineValueArray[] = 0;
                        } else {
                            $dentalValueArray[] = null;
                            $visionValueArray[] = null;
                            $dentalAndVisionCombineValueArray[] = null;
                        }
                    }
                } else if (!empty($dentalValue) && empty($visionValue)) {
                    if ($fedCase->insurancePlan->dental_retirement == 'yes') {
                        $dentalValueArray[] = $dentalValue;
                        $dentalValue += $dentalValue * 0.05;
                        $dentalValue = intVal($dentalValue);
                        $visionValueArray[] = $visionValue;
                        $visionValue += $visionValue * 0.05;
                        $visionValue = intVal($visionValue);
                        $dentalAndVisionCombineValueArray[] = 0;
                    } else {
                        if ($currentAge > $age) {
                            $visionValueArray[] = $visionValue;
                            $visionValue += $visionValue * 0.05;
                            $visionValue = intVal($visionValue);
                            $dentalValueArray[] = $dentalValue;
                            $dentalValue += $dentalValue * 0.05;
                            $dentalValue = intVal($dentalValue);
                            $dentalAndVisionCombineValueArray[] = 0;
                        } else {
                            $dentalValueArray[] = null;
                            $visionValueArray[] = null;
                            $dentalAndVisionCombineValueArray[] = null;
                        }
                    }
                } else if (empty($dentalValue) && !empty($visionValue)) {
                    if ($fedCase->insurancePlan->vision_retirement == 'yes') {
                        $dentalValueArray[] = $dentalValue;
                        $dentalValue += $dentalValue * 0.05;
                        $dentalValue = intVal($dentalValue);
                        $visionValueArray[] = $visionValue;
                        $visionValue += $visionValue * 0.05;
                        $visionValue = intVal($visionValue);
                        $dentalAndVisionCombineValueArray[] = 0;
                    } else {
                        if ($currentAge > $age) {
                            $visionValueArray[] = $visionValue;
                            $visionValue += $visionValue * 0.05;
                            $visionValue = intVal($visionValue);
                            $dentalValueArray[] = $dentalValue;
                            $dentalValue += $dentalValue * 0.05;
                            $dentalValue = intVal($dentalValue);
                            $dentalAndVisionCombineValueArray[] = 0;
                        } else {
                            $dentalValueArray[] = null;
                            $visionValueArray[] = null;
                            $dentalAndVisionCombineValueArray[] = null;
                        }
                    }
                } else {
                    $dentalValueArray[] = $dentalValue;
                    $dentalValue += $dentalValue * 0.05;
                    $dentalValue = intVal($dentalValue);
                    $visionValueArray[] = $visionValue;
                    $visionValue += $visionValue * 0.05;
                    $visionValue = intVal($visionValue);
                    $dentalAndVisionCombineValueArray[] = 0;
                }
            }

            // start tsp calculation
            $tspCalculationTotalArray[] = $tspCalculationTotal;

            $gFundAmount += $totalConMatch * $gFundCon;
            $gFundAmount += $gFundAmount * 0.025;

            $fFundAmount += $totalConMatch * $fFundCon;
            $fFundAmount += $fFundAmount * 0.015;

            $cFundAmount += $totalConMatch * $cFundCon;
            $cFundAmount += $cFundAmount * 0.13;

            $sFundAmount += $totalConMatch * $sFundCon;
            $sFundAmount += $sFundAmount * 0.08;

            $iFundAmount += $totalConMatch * $iFundCon;
            $iFundAmount += $iFundAmount * 0.05;

            $lFundAmount += $totalConMatch * $lFundCon;
            $lFundAmount += $lFundAmount * 0.04;

            $l2025FundAmount += $totalConMatch * $l2025FundCon;
            $l2025FundAmount += $l2025FundAmount * 0.04;

            $l2030FundAmount += $totalConMatch * $l2030FundCon;
            $l2030FundAmount += $l2030FundAmount * 0.07;

            $l2035FundAmount += $totalConMatch * $l2035FundCon;
            $l2035FundAmount += $l2035FundAmount * 0.07;

            $l2040FundAmount += $totalConMatch * $l2040FundCon;
            $l2040FundAmount += $l2040FundAmount * 0.08;

            $l2045FundAmount += $totalConMatch * $l2045FundCon;
            $l2045FundAmount += $l2045FundAmount * 0.08;

            $l2050FundAmount += $totalConMatch * $l2050FundCon;
            $l2050FundAmount += $l2050FundAmount * 0.08;

            $l2055FundAmount += $totalConMatch * $l2055FundCon;
            $l2055FundAmount += $l2055FundAmount * 0.1;

            $l2060FundAmount += $totalConMatch * $l2060FundCon;
            $l2060FundAmount += $l2060FundAmount * 0.1;

            $l2065FundAmount += $totalConMatch * $l2065FundCon;
            $l2065FundAmount += $l2065FundAmount * 0.1;

            $currentSalary  += $currentSalary * 0.02; // increment by 2%
            $totalContribution = $currentSalary * $totalContributionPercentage / 100;

            $totalMatching = $currentSalary * $MatchingPercentage;
            // dd($gFundAmount."f".$fFundAmount."c".$cFundAmount."s".$sFundAmount."i".$iFundAmount."L".$lFundAmount."2025".$l2025FundAmount."2030".$l2030FundAmount."2035".$l2035FundAmount."2040".$l2040FundAmount."2045".$l2045FundAmount."2050".$l2050FundAmount."2055".$l2055FundAmount."2060".$l2060FundAmount."2065".$l2065FundAmount."cont".$totalContribution."match".$totalMatching);

            $tspCalculationTotal = $gFundAmount + $fFundAmount + $cFundAmount + $sFundAmount + $iFundAmount + $lFundAmount + $l2025FundAmount + $l2030FundAmount + $l2035FundAmount + $l2040FundAmount + $l2045FundAmount + $l2050FundAmount + $l2055FundAmount + $l2060FundAmount + $l2065FundAmount + $totalContribution + $totalMatching;
            // end tsp calculation

            // code for FLTCIP
            // $ageFLTCIP = intval($currentAge / 5);
            // $fltcipArray = [];
            // $fltcipAmount = $fltcip->yearlyPremiumAmount;
            // $fltcipKeepInRetirement = $fedCase->insurancePlan->insurance_program_retirement;
            // if($fltcipKeepInRetirement == 'yes')
            // {
            //     for ($i = 1; $i <= $ageFLTCIP; $i++) {
            //         $fltcipArray[] = $fltcipAmount;
            //         $fltcipAmount = $fltcipAmount * (85 / 100);
            //     }
            // }
            // else
            // {
            //     $todayDate = Carbon::now();
            //     $retirementDate = Carbon::parse($fedCase->retirement_type_date);
            //     $yearsUntilRetirement = $todayDate->diff($retirementDate);
            //     $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
            //     $yearsUntilRetirementCalculate = $yearsUntilRetirementCalculate / 5;
            //     for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
            //         $fltcipArray[] = $fltcipAmount;
            //         $fltcipAmount += $fltcipAmount * (85 / 100);
            //     }
            // }



            $totalIncomeArray[] = $pensionAmount + $srsAmount + $ssAmount + $tspCalculationTotal;
            $totalExpensesArray[] = $premiumAmount + $dentalValue + $visionValue + $dentalAndVisionCombineValue + $survivorBenefitAmount + $fegliAmountTotal;
        }

        $totalIncomeSum = array_sum($totalIncomeArray);
        $totalExpensesSum = array_sum($totalExpensesArray);
        $totalDiffIncomeExpense = $totalIncomeSum - $totalExpensesSum;

        $fltcipArray = [];
        $fltcipAmount = $fltcip->yearlyPremiumAmount;
        $fltcipKeepInRetirement = $fedCase->insurancePlan->insurance_program_retirement;
        // dd($fedCase->retirement_type_age);
        if ($fltcipKeepInRetirement == 'yes') {
            $startLoop = $currentAge;
            $checkLoop = $startLoop + 4;
            for ($age = $currentAge - $diffRetirementCurrentYear; $age <= 90; $age++) {
                if($startLoop == $checkLoop)
                {
                    $fltcipAmount += $fltcipAmount * (85 / 100); // Apply increment
                    $fltcipArray[] = intval($fltcipAmount);
                    $checkLoop = $startLoop + 4;
                }
                else
                {
                    $fltcipArray[] = intval($fltcipAmount);
                }
                $startLoop++;
            }
        } 
        else {
            // dd($diffRetirementCurrentYear);
            // preg_match('/(\d+)\s*Y/', $fedCase->retirement_type_age, $matches);
            // $retirementAge = (int)$matches[1];
            $startLoop = $currentAge;
            $checkLoop = $startLoop + 4;
            for ($age = $currentAge - $diffRetirementCurrentYear; $age <= 90; $age++) {
                if($startLoop < $retirementAge)
                {
                    if($startLoop == $checkLoop)
                    {
                        $fltcipAmount += $fltcipAmount * (85 / 100); // Apply increment
                        $fltcipArray[] = intval($fltcipAmount);
                        $checkLoop = $startLoop + 4;
                    }
                    else
                    {
                        $fltcipArray[] = intval($fltcipAmount);
                    }
                    $startLoop++;
                }
                else
                {
                    $fltcipArray[] = 0;
                }
                
            }
        }

        $totalFLTCIPSum = array_sum($fltcipArray);
        // dd($ssArray);

        $data = [];
        $data['fedCase']                            = $fedCase;
        $data['highThree']                          = $highThree;
        $data['yosDollar']                          = $yosDollar;
        $data['pensionAmount']                      = $pensionAmount;
        $data['fehbVP']                             = $fehbVP;
        $data['dentalAndVision']                    = $dentalAndVision;
        $data['fegliAmountTotal']                   = $fegliAmountTotal;
        $data['fegliAmountArray']                   = $fegliAmountArray;
        $data['fltcipArray']                        = $fltcipArray;
        $data['premiumAmount']                      = $premiumAmount;
        $data['premiums']                           = $premiums;
        $data['ageCount']                           = $ageCount;
        $data['dentalAndVisionCombineValueArray']   = $dentalAndVisionCombineValueArray;
        $data['dentalValueArray']                   = $dentalValueArray;
        $data['visionValueArray']                   = $visionValueArray;
        $data['pensionArray']                       = $pensionArray;
        $data['tspCalculation']                     = $tspCalculation;
        $data['tspCalculationTotal']                = $tspCalculationTotal;
        $data['tspCalculationTotalArray']           = $tspCalculationTotalArray;
        $data['srsAmount']                          = $srsAmount;
        $data['srsArray']                           = $srsArray;
        $data['ssAmount']                           = $ssAmount;
        $data['ssArray']                            = $ssArray;
        $data['survivorBenefitAmount']              = $survivorBenefitAmount;
        $data['SurvivorBenefitArray']               = $SurvivorBenefitArray;
        $data['totalIncomeArray']                   = $totalIncomeArray;
        $data['totalExpensesArray']                 = $totalExpensesArray;
        $data['totalDiffIncomeExpense']             = $totalDiffIncomeExpense;
        $data['totalFLTCIPSum']                     = $totalFLTCIPSum;
        return view('admin.fed-case.show', $data);
    }
    public function print(FedCase $fedCase)
    {
        $highThree = HighThree::where('fed_case_id', $fedCase->id)->first();
        $pension = Pension::where('fed_case_id', $fedCase->id)->first();
        $fehbVP = FEHBVP::where('fed_case_id', $fedCase->id)->first();
        $dentalAndVision = DentalAndVision::where('fed_case_id', $fedCase->id)->first();
        $insuranceCost = FEGLI::where('fed_case_id', $fedCase->id)->first();
        $tspCalculation = TSPCalculate::where('fed_case_id', $fedCase->id)->first();
        $srsData = SRS::where('fed_case_id', $fedCase->id)->first();
        $socialSecurity = SocialSecurity::where('fed_case_id', $fedCase->id)->first();
        $survivorBenefit = SurvivorBenefit::where('fed_case_id', $fedCase->id)->first();
        $fltcip = FLTCIP::where('fed_case_id', $fedCase->id)->first();
        $annualSalaryIncrement = ASI::value('value');
        $annualSalaryIncrement = $annualSalaryIncrement / 100;
        if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_transfer' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
            $annualColaIncrement = COLA::value('fers_cola');
        } else {
            $annualColaIncrement = COLA::value('csrs_cola');
        }
        $annualColaIncrement = $annualColaIncrement / 100;
        // FEGLI
        if ($insuranceCost) {
            $fegliAmountTotal = $insuranceCost->basic + $insuranceCost->optionA + $insuranceCost->optionB + $insuranceCost->optionC;
            $fegliAmountArray = [];
        } else {
            $fegliAmountTotal = 0;
            $fegliAmountArray = [];
        }


        // Convert the premium text to an integer
        $premium = str_replace(',', '', $fedCase->InsurancePlan->premium);
        $premium = intval($premium);
        // Convert the current salary text to an integer
        $currentSalary = str_replace(',', '', $fedCase->salary_1);
        $currentSalary = intval($currentSalary);
        // Multiply the premium by 26
        $initialPremium = $premium * 26;

        // Employee's current age
        preg_match('/(\d+)\s*Y/', $fedCase->age, $matches);
        $currentAge = (int)$matches[1];

        $dentalAndVisionValue = intval($dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount);
        $dentalAndVisionValueArray = [];

        $pensionArray = [];
        if (!empty($pension->amount)) {
            $pensionAmount = $pension->amount;
        } else {
            $pensionAmount = 0;
        }

        // survivor benefit 
        $SurvivorBenefitArray = [];
        $survivorBenefitAmount = $survivorBenefit->cost;
        // Calculate the premiums for each year until age 90
        $premiums = [];
        $premiumAmount = $initialPremium;

        // for SRS

        if ($srsData) {
            $srsArray = [];
            $srsAmount = $srsData->amount;
        } else {
            $srsArray = [];
            $srsAmount = 0;
        }


        // for SS
        $ssAmount = $socialSecurity->amount;
        $ssArray = [];

        $gFundAmount = $tspCalculation->gFund;
        $fFundAmount = $tspCalculation->fFund;
        $cFundAmount = $tspCalculation->cFund;
        $sFundAmount = $tspCalculation->sFund;
        $iFundAmount = $tspCalculation->iFund;
        $lFundAmount = $tspCalculation->lFund;
        $l2025FundAmount = $tspCalculation->l2025Fund;
        $l2030FundAmount = $tspCalculation->l2030Fund;
        $l2035FundAmount = $tspCalculation->l2035Fund;
        $l2040FundAmount = $tspCalculation->l2040Fund;
        $l2045FundAmount = $tspCalculation->l2045Fund;
        $l2050FundAmount = $tspCalculation->l2050Fund;
        $l2055FundAmount = $tspCalculation->l2055Fund;
        $l2060FundAmount = $tspCalculation->l2060Fund;
        $l2065FundAmount = $tspCalculation->l2065Fund;

        $tspCalculationTotalArray = [];
        $tspCalculationTotal = $tspCalculation->totalTSPCalculate;
        $tspCalculationPercentage = $fedCase->tSP->contribute_pp_percentage;
        $rothCalculationPercentage = $fedCase->tSP->contribute_tsp_pp_percentage;
        $totalContributionPercentage = $tspCalculationPercentage + $rothCalculationPercentage;
        $MatchingPercentage = $tspCalculation->matchingPercentage;

        $totalContribution = $tspCalculation->totalContribution;
        $totalMatching = $tspCalculation->totalMatching;
        $totalConMatch = $totalContribution + $totalMatching;

        $gFundCon = $fedCase->g_value;
        $fFundCon = $fedCase->f_value;
        $cFundCon = $fedCase->c_value;
        $sFundCon = $fedCase->s_value;
        $iFundCon = $fedCase->i_value;
        $lFundCon = $fedCase->l_income_value;
        $l2025FundCon = $fedCase->l_2025_value;
        $l2030FundCon = $fedCase->l_2030_value;
        $l2035FundCon = $fedCase->l_2035_value;
        $l2040FundCon = $fedCase->l_2040_value;
        $l2045FundCon = $fedCase->l_2045_value;
        $l2050FundCon = $fedCase->l_2050_value;
        $l2055FundCon = $fedCase->l_2055_value;
        $l2060FundCon = $fedCase->l_2060_value;
        $l2065FundCon = $fedCase->l_2065_value;

        $totalIncome = [];
        $totalExpenses = [];
        for ($age = $currentAge; $age <= 90; $age++) {
            $premiumAmount += $premiumAmount * 0.05; // Increment by 5%
            $premiums[] = $premiumAmount;
            $ageCount[] = $age;


            $srsArray[] = $srsAmount; //for SRS

            $ssArray[] = $ssAmount; // for SS
            $ssAmount += $ssAmount * 0.025;

            // FEGLI
            $fegliAmountTotal += $fegliAmountTotal * 0.03;
            $fegliAmountArray[] = $fegliAmountTotal;


            $dentalAndVisionValueArray[] = $dentalAndVisionValue;
            $dentalAndVisionValue += $dentalAndVisionValue * 0.05;

            $pensionArray[] = $pensionAmount;
            $pensionAmount += $pensionAmount * $annualColaIncrement;

            // Survivor benefit 

            $SurvivorBenefitArray[] = $survivorBenefitAmount;
            if ($fedCase->employee_spouse == 'yes') {
                if ($fedCase->survior_benefit_fers != null) {
                    if ($fedCase->survior_benefit_fers == 50) {
                        $survivorBenefitAmount = $pensionAmount * 0.1;
                    } else if ($fedCase->survior_benefit_fers == 25) {
                        $survivorBenefitAmount = $pensionAmount * 0.05;
                    } else {
                        $survivorBenefitAmount = 0;
                    }
                } else if ($fedCase->survior_benefit_csrs != null) {
                    $totalLeavePension = $pensionAmount * $fedCase->survior_benefit_csrs / 100;
                    if ($totalLeavePension <= 3600) {
                        $survivorBenefitAmount = $totalLeavePension * 0.025;
                    } else {
                        $remainningLeavePension = $totalLeavePension - 3600;
                        $survivorBenefitFirst = 3600 * 0.025;
                        $survivorBenefitSecond = $remainningLeavePension * 0.1;
                        $survivorBenefitAmount = $survivorBenefitFirst + $survivorBenefitSecond;
                    }
                } else {
                    $survivorBenefitAmount = 0;
                }
            }

            // start tsp calculation
            $tspCalculationTotalArray[] = $tspCalculationTotal;

            $gFundAmount += $totalConMatch * $gFundCon;
            $gFundAmount += $gFundAmount * 0.025;

            $fFundAmount += $totalConMatch * $fFundCon;
            $fFundAmount += $fFundAmount * 0.015;

            $cFundAmount += $totalConMatch * $cFundCon;
            $cFundAmount += $cFundAmount * 0.13;

            $sFundAmount += $totalConMatch * $sFundCon;
            $sFundAmount += $sFundAmount * 0.08;

            $iFundAmount += $totalConMatch * $iFundCon;
            $iFundAmount += $iFundAmount * 0.05;

            $lFundAmount += $totalConMatch * $lFundCon;
            $lFundAmount += $lFundAmount * 0.04;

            $l2025FundAmount += $totalConMatch * $l2025FundCon;
            $l2025FundAmount += $l2025FundAmount * 0.04;

            $l2030FundAmount += $totalConMatch * $l2030FundCon;
            $l2030FundAmount += $l2030FundAmount * 0.07;

            $l2035FundAmount += $totalConMatch * $l2035FundCon;
            $l2035FundAmount += $l2035FundAmount * 0.07;

            $l2040FundAmount += $totalConMatch * $l2040FundCon;
            $l2040FundAmount += $l2040FundAmount * 0.08;

            $l2045FundAmount += $totalConMatch * $l2045FundCon;
            $l2045FundAmount += $l2045FundAmount * 0.08;

            $l2050FundAmount += $totalConMatch * $l2050FundCon;
            $l2050FundAmount += $l2050FundAmount * 0.08;

            $l2055FundAmount += $totalConMatch * $l2055FundCon;
            $l2055FundAmount += $l2055FundAmount * 0.1;

            $l2060FundAmount += $totalConMatch * $l2060FundCon;
            $l2060FundAmount += $l2060FundAmount * 0.1;

            $l2065FundAmount += $totalConMatch * $l2065FundCon;
            $l2065FundAmount += $l2065FundAmount * 0.1;


            $currentSalary  += $currentSalary * 0.02; // increment by 2%

            $totalContribution = $currentSalary * $totalContributionPercentage / 100;

            $totalMatching = $currentSalary * $MatchingPercentage;
            // dd($gFundAmount."f".$fFundAmount."c".$cFundAmount."s".$sFundAmount."i".$iFundAmount."L".$lFundAmount."2025".$l2025FundAmount."2030".$l2030FundAmount."2035".$l2035FundAmount."2040".$l2040FundAmount."2045".$l2045FundAmount."2050".$l2050FundAmount."2055".$l2055FundAmount."2060".$l2060FundAmount."2065".$l2065FundAmount."cont".$totalContribution."match".$totalMatching);

            $tspCalculationTotal = $gFundAmount + $fFundAmount + $cFundAmount + $sFundAmount + $iFundAmount + $lFundAmount + $l2025FundAmount + $l2030FundAmount + $l2035FundAmount + $l2040FundAmount + $l2045FundAmount + $l2050FundAmount + $l2055FundAmount + $l2060FundAmount + $l2065FundAmount + $totalContribution + $totalMatching;
            // end tsp calculation


            $totalIncomeArray[] = $pensionAmount + $srsAmount + $ssAmount + $tspCalculationTotal;
            $totalExpensesArray[] = $premiumAmount + $dentalAndVisionValue + $survivorBenefitAmount + $fegliAmountTotal;
        }
        $totalIncomeSum = array_sum($totalIncomeArray);
        $totalExpensesSum = array_sum($totalExpensesArray);
        $totalDiffIncomeExpense = $totalIncomeSum - $totalExpensesSum;

        // code for FLTCIP
        $ageFLTCIP = intval($currentAge / 5);
        $fltcipArray = [];
        $fltcipAmount = $fltcip->yearlyPremiumAmount;
        for ($i = 1; $i <= $ageFLTCIP; $i++) {
            $fltcipArray[] = $fltcipAmount;
            $fltcipAmount = $fltcipAmount * (85 / 100);
        }
        $totalFLTCIPSum = array_sum($fltcipArray);


        $data = [];
        $data['fedCase']                     = $fedCase;
        $data['highThree']                   = $highThree;
        $data['pensionAmount']               = $pensionAmount;
        $data['fehbVP']                      = $fehbVP;
        $data['dentalAndVision']             = $dentalAndVision;
        $data['fegliAmountTotal']            = $fegliAmountTotal;
        $data['fegliAmountArray']            = $fegliAmountArray;
        $data['premiumAmount']               = $premiumAmount;
        $data['premiums']                    = $premiums;
        $data['ageCount']                    = $ageCount;
        $data['dentalAndVisionValueArray']   = $dentalAndVisionValueArray;
        $data['pensionArray']                = $pensionArray;
        $data['tspCalculation']              = $tspCalculation;
        $data['tspCalculationTotalArray']    = $tspCalculationTotalArray;
        $data['srsAmount']                   = $srsAmount;
        $data['srsArray']                    = $srsArray;
        $data['ssAmount']                    = $ssAmount;
        $data['ssArray']                     = $ssArray;
        $data['survivorBenefitAmount']       = $survivorBenefitAmount;
        $data['SurvivorBenefitArray']        = $SurvivorBenefitArray;
        $data['totalIncomeArray']            = $totalIncomeArray;
        $data['totalExpensesArray']          = $totalExpensesArray;
        $data['totalDiffIncomeExpense']      = $totalDiffIncomeExpense;
        $data['totalFLTCIPSum']              = $totalFLTCIPSum;
        return view('admin.fed-case.print', $data);
    }

    public function edit(FedCase $fedCase)
    {
        $case = FedCase::find($fedCase->id);
        $caseOFST = OFST::where('fed_Case_id', $case->id)->first();
        $caseMST = MST::where('fed_Case_id', $case->id)->first();
        $caseTSP = TSP::where('fed_Case_id', $case->id)->first();
        $caseInsurancePlan = InsurancePlan::where('fed_Case_id', $case->id)->first();
        $yosDollar = YosDollar::where('fed_Case_id', $case->id)->first();
        $states = State::orderBy('name', 'asc')->get();
        $id = $fedCase->id;
        return view('admin.fed-case.edit', compact('case', 'id', 'yosDollar', 'caseOFST', 'caseMST', 'caseTSP', 'caseInsurancePlan', 'states'));
    }

    public function update(Request $request, FedCase $fedCase)
    {
        $validator = Validator::make($request->all(), [
            'name'                   => 'required',
            'dob'                    => 'required',
            'retirement_system'      => 'required',
            'employee_type'          => 'required',
            'lscd' => 'nullable|required_without_all:rscd,scd|date',
            'rscd' => 'nullable|required_without_all:lscd,scd|date',
            'scd'  => 'nullable|required_without_all:lscd,rscd|date',
            'retirement_type'        => 'required',
            'retirement_type_date'   => 'required',
        ]);
        if ($validator->passes()) {
            $user = Auth::user();
            $fedCase = FedCase::where('id', $fedCase->id)->first();
            $caseOFST = OFST::where('fed_Case_id', $fedCase->id)->first();
            $caseMST = MST::where('fed_Case_id', $fedCase->id)->first();
            $caseTSP = TSP::where('fed_Case_id', $fedCase->id)->first();
            $caseInsurancePlan = InsurancePlan::where('fed_Case_id', $fedCase->id)->first();
            $caseYosDollar = YosDollar::where('fed_Case_id', $fedCase->id)->first();
            $caseYosE = YosE::where('fed_Case_id', $fedCase->id)->first();
            $caseHighThree = HighThree::where('fed_Case_id', $fedCase->id)->first();
            $annualLeavePayout = AnnualLeavePayout::where('fed_Case_id', $fedCase->id)->first();
            $casePension = Pension::where('fed_Case_id', $fedCase->id)->first();
            $casepartTimePension = PartTimePension::where('fed_Case_id', $fedCase->id)->first();
            $caseFEGLUIInsuranceCost = FEGLI::where('fed_Case_id', $fedCase->id)->first();
            $caseSRS = SRS::where('fed_Case_id', $fedCase->id)->first();
            $caseDentalAndVision = DentalAndVision::where('fed_Case_id', $fedCase->id)->first();
            $caseFEHBVP = FEHBVP::where('fed_Case_id', $fedCase->id)->first();
            $caseFLTCIP = FLTCIP::where('fed_Case_id', $fedCase->id)->first();

            // update data in this table before this(OTHER FEDERAL SERVICE TIME) section
            $fedCase->update([
                'user_id'                           => $user->id,
                'status'                            => $request->status,
                'name'                              => $request->name,
                'dob'                               => $request->dob
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->dob)->format('Y-m-d'))
                    : null,
                'age'                               => $request->age,
                'spouse_name'                       => $request->spouse_name,
                'spouse_dob'                        => $request->spouse_dob
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->spouse_dob)->format('Y-m-d'))
                    : null,
                'spouse_age'                        => $request->spouse_age,
                'address'                           => $request->address,
                'city'                              => $request->city,
                'state'                             => $request->state,
                'zip'                               => $request->zip,
                'email'                             => $request->email,
                'phone'                             => $request->phone,
                'retirement_system'                 => $request->retirement_system,
                'retirement_system_csrs_offset'     => $request->retirement_system_csrs_offset
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_system_csrs_offset)->format('Y-m-d'))
                    : null,
                'retirement_system_fers_transfer'   => $request->retirement_system_fers_transfer
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_system_fers_transfer)->format('Y-m-d'))
                    : null,
                'employee_type'                     => $request->employee_type,
                'lscd'                              => $request->lscd,
                'rscd'                              => $request->rscd,
                'scd'                               => $request->scd,
                'retirement_type'                   => $request->retirement_type,
                'retirement_type_age'               => $request->retirement_type_age,
                'retirement_type_date'              => $request->retirement_type_date
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_type_date)->format('Y-m-d'))
                    : null,
                'retirement_type_voluntary'         => $request->retirement_type_voluntary
                    ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->retirement_type_voluntary)->format('Y-m-d'))
                    : null,
                'annual_leave_hours'                => $request->annual_leave_hours,
                'sick_leave_hours'                  => $request->sick_leave_hours,
                'current_hours_option'              => $request->current_hours_option,
                'current_leave_option'              => $request->current_leave_option,
                'income_employee_option'            => $request->income_employee_option,
                'salary_1'                          => $request->salary_1,
                'salary_2'                          => $request->salary_2,
                'salary_3'                          => $request->salary_3,
                'salary_4'                          => $request->salary_4,
                'employee_spouse'                   => $request->employee_spouse,
                'survior_benefit_fers'              => $request->survior_benefit_fers,
                'survior_benefit_csrs'              => $request->survior_benefit_csrs,
                'employee_eligible'                 => $request->employee_eligible,
                'amount_1'                          => $request->amount_1,
                'amount_2'                          => $request->amount_2,
                'amount_3'                          => $request->amount_3,


            ]);

            // save data of OTHER FEDERAL SERVICE TIME section
            $caseOFST = OFST::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'                           => $fedCase->id,
                    'employee_work'                         => $request->employee_work,
                    'empolyee_hours_work'                   => $request->empolyee_hours_work,
                    'empolyee_multiple_date'                => $request->empolyee_multiple_date
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->empolyee_multiple_date)->format('Y-m-d'))
                        : null,
                    'empolyee_multiple_date_to'             => $request->empolyee_multiple_date_to
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->empolyee_multiple_date_to)->format('Y-m-d'))
                        : null,
                    'non_deduction_service'                 => $request->non_deduction_service,
                    'non_deduction_service_date'            => $request->non_deduction_service_date
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->non_deduction_service_date)->format('Y-m-d'))
                        : null,
                    'non_deduction_service_date_2'          => $request->non_deduction_service_date_2
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->non_deduction_service_date_2)->format('Y-m-d'))
                        : null,
                    'non_deduction_service_deposit'         => $request->non_deduction_service_deposit,
                    'non_deduction_service_deposit_owned'   => $request->non_deduction_service_deposit_owned,
                    'break_in_service'                      => $request->break_in_service,
                    'break_in_service_date_1'               => $request->break_in_service_date_1
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_date_1)->format('Y-m-d'))
                        : null,
                    'break_in_service_date_2'               => $request->break_in_service_date_2
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_date_2)->format('Y-m-d'))
                        : null,
                    'break_in_service_refund'               => $request->break_in_service_refund,
                    'break_in_service_redeposite'           => $request->break_in_service_redeposite,
                    'break_in_service_return_date'          => $request->break_in_service_return_date
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->break_in_service_return_date)->format('Y-m-d'))
                        : null,
                    'break_in_service_amount_redeposite'    => $request->break_in_service_amount_redeposite,
                ]
            );

            // save data of MILITARY SERVICE TIME section
            $caseMST = MST::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'                           => $fedCase->id,
                    'military_service'                      => $request->military_service,
                    'military_service_date_1'               => $request->military_service_date_1
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_date_1)->format('Y-m-d'))
                        : null,
                    'military_service_date_2'               => $request->military_service_date_2
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_date_2)->format('Y-m-d'))
                        : null,
                    'military_service_active_duty'          => $request->military_service_active_duty,
                    'military_service_active_duty_date_1'   => $request->military_service_active_duty_date_1
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_active_duty_date_1)->format('Y-m-d'))
                        : null,
                    'military_service_active_duty_date_2'   => $request->military_service_active_duty_date_2
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_active_duty_date_2)->format('Y-m-d'))
                        : null,
                    'military_service_reserve'              => $request->military_service_reserve,
                    'military_service_reserve_date_1'       => $request->military_service_reserve_date_1
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_reserve_date_1)->format('Y-m-d'))
                        : null,
                    'military_service_reserve_date_2'       => $request->military_service_reserve_date_2
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->military_service_reserve_date_2)->format('Y-m-d'))
                        : null,
                    'military_service_academy'              => $request->military_service_academy,
                    'military_service_academy_amount'       => $request->military_service_academy_amount,
                    'military_service_note'                 => $request->military_service_note,
                    'military_service_retire'               => $request->military_service_retire,
                    'military_service_collecting'           => $request->military_service_collecting,
                    'military_service_reserves'             => $request->military_service_reserves,
                    'military_service_amount'               => $request->military_service_amount,
                ]
            );

            // save data of THRIFT SAVINGS PLAN section
            $caseTSP = TSP::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'                           => $fedCase->id,
                    'contribute'                            => $request->contribute,
                    'contribute_pp'                         => $request->contribute_pp,
                    'contribute_pp_percentage'              => $request->contribute_pp_percentage,
                    'contribute_tsp'                        => $request->contribute_tsp,
                    'contribute_tsp_pp'                     => $request->contribute_tsp_pp,
                    'contribute_tsp_pp_percentage'          => $request->contribute_tsp_pp_percentage,
                    'contribute_limit'                      => $request->contribute_limit,
                    'contribute_tsp_loan'                   => $request->contribute_tsp_loan,
                    'contribute_tsp_res'                    => $request->contribute_tsp_res,
                    'contribute_tsp_loan_gen'               => $request->contribute_tsp_loan_gen,
                    'contribute_pay_pp'                     => $request->contribute_pay_pp,
                    'contribute_pay_pp_value'               => $request->contribute_pay_pp_value,
                    'contribute_own_loan'                   => $request->contribute_own_loan,
                    'contribute_own_loan_2'                 => $request->contribute_own_loan_2,
                    'contribute_pay_date'                   => $request->contribute_pay_date
                        ? Carbon::createFromFormat('Y-m-d', Carbon::parse($request->contribute_pay_date)->format('Y-m-d'))
                        : null,
                    'employee_not_sure'                     => $request->employee_not_sure,
                    'employee_imd'                          => $request->employee_imd,
                    'employee_at_age'                       => $request->employee_at_age,
                    'employee_loss'                         => $request->employee_loss,
                    'employee_income'                       => $request->employee_income,
                    'goal'                                  => $request->goal,
                    'goal_amount'                           => $request->goal_amount,
                    'goal_tsp'                              => $request->goal_tsp,
                    'goal_retirement'                       => $request->goal_retirement,
                    'goal_track'                            => $request->goal_track,
                    'goal_comfor'                           => $request->goal_comfor,
                    'goal_professional'                     => $request->goal_professional,
                    'goal_why'                              => $request->goal_why,
                    'g_name'                                => $request->g_name,
                    'g_value'                               => $request->g_value,
                    'f_name'                                => $request->f_name,
                    'f_value'                               => $request->f_value,
                    'c_name'                                => $request->c_name,
                    'c_value'                               => $request->c_value,
                    's_name'                                => $request->s_name,
                    's_value'                               => $request->s_value,
                    'i_name'                                => $request->i_name,
                    'i_value'                               => $request->i_value,
                    'l_income'                              => $request->l_income,
                    'l_income_value'                        => $request->l_income_value,
                    'l_2025'                                => $request->l_2025,
                    'l_2025_value'                          => $request->l_2025_value,
                    'l_2030'                                => $request->l_2030,
                    'l_2030_value'                          => $request->l_2030_value,
                    'l_2035'                                => $request->l_2035,
                    'l_2035_value'                          => $request->l_2035_value,
                    'l_2040'                                => $request->l_2040,
                    'l_2040_value'                          => $request->l_2040_value,
                    'l_2045'                                => $request->l_2045,
                    'l_2045_value'                          => $request->l_2045_value,
                    'l_2050'                                => $request->l_2050,
                    'l_2050_value'                          => $request->l_2050_value,
                    'l_2055'                                => $request->l_2055,
                    'l_2055_value'                          => $request->l_2055_value,
                    'l_2060'                                => $request->l_2060,
                    'l_2060_value'                          => $request->l_2060_value,
                    'l_2065'                                => $request->l_2065,
                    'l_2065_value'                          => $request->l_2065_value,
                    'total_amount'                          => $request->total_amount,
                    'total_amount_percentage'               => $request->total_amount_percentage,
                ]
            );

            // save data of INSURANCE PLAN section
            $caseInsurancePlan = InsurancePlan::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'                           => $fedCase->id,
                    'insurance'                             => $request->insurance,
                    'insurance_emloyee'                     => $request->insurance_emloyee,
                    'insurance_retirement'                  => $request->insurance_retirement,
                    'insurance_coverage'                    => $request->insurance_coverage,
                    'insurance_employee_dependent'          => $request->insurance_employee_dependent,
                    'insurance_coverage_basic_option'       => $request->insurance_coverage_basic_option,
                    'basic_option_select'                   => $request->basic_option_select,
                    'insurance_coverage_a_option'           => $request->insurance_coverage_a_option,
                    'insurance_coverage_b_option'           => $request->insurance_coverage_b_option,
                    'option_b_value'                        => $request->option_b_value,
                    'insurance_coverage_c_option'           => $request->insurance_coverage_c_option,
                    'insurance_employee_coverage_c'         => $request->insurance_employee_coverage_c,
                    'insurance_employee_coverage_pp'        => $request->insurance_employee_coverage_pp,
                    'insurance_employee_coverage_age'       => $request->insurance_employee_coverage_age,
                    'insurance_employee_coverage_self_age'  => $request->insurance_employee_coverage_self_age,
                    'insurance_analysis'                    => $request->insurance_analysis,
                    'federal'                               => $request->federal,
                    'plan_type'                             => $request->plan_type,
                    'premium'                               => $request->premium,
                    'coverage'                              => $request->coverage,
                    'coverage_retirement'                   => $request->coverage_retirement,
                    'coverage_retirement_dependent'         => $request->coverage_retirement_dependent,
                    'coverage_retirement_insurance'         => $request->coverage_retirement_insurance,
                    'coverage_retirement_insurance_why'     => $request->coverage_retirement_insurance_why,
                    'coverage_retirement_insurance_who'     => $request->coverage_retirement_insurance_who,
                    // 'coverage_retirement_insurance_spouse'                               => $request->coverage_retirement_insurance_spouse,
                    // 'coverage_retirement_insurance_child'                                => $request->coverage_retirement_insurance_child,
                    // 'coverage_retirement_insurance_both'                                 => $request->coverage_retirement_insurance_both,
                    'dental'                                => $request->dental,
                    'dental_retirement'                     => $request->dental_retirement,
                    'dental_premium'                        => $request->dental_premium,
                    'vision'                                => $request->vision,
                    'vision_retirement'                     => $request->vision_retirement,
                    'vision_premium'                        => $request->vision_premium,
                    'vision_total_cost'                     => $request->vision_total_cost,
                    'insurance_program'                     => $request->insurance_program,
                    'insurance_age'                         => $request->insurance_age,
                    'insurance_purchase_premium'            => $request->insurance_purchase_premium,
                    'insurance_program_retirement'          => $request->insurance_program_retirement,
                    'insurance_program_plan'                => $request->insurance_program_plan,
                    'insurance_program_daily'               => $request->insurance_program_daily,
                    'max_lifetime'                          => $request->max_lifetime,
                    'notes'                                 => $request->notes,
                    'insurance_purpose_coverage'            => $request->insurance_purpose_coverage,
                    'insurance_program_purpose'             => $request->insurance_program_purpose,
                ]
            );


            // start tsp calculation
            if ($request->contribute == 'none') {
                $tspContribution = 0;
                $tspContributionPercentage = 0;
            } else {
                $tspContribution = $request->contribute_pp;
                $tspContributionPercentage = $request->contribute_pp_percentage;
            }

            if ($request->contribute_tsp == 'none') {
                $rothContribution = 0;
                $rothContributionPercentage = 0;
            } else {
                $rothContribution = $request->contribute_tsp_pp;
                $rothContributionPercentage = $request->contribute_tsp_pp_percentage;
            }

            $totalContribution = $tspContribution + $rothContribution;
            $totalContributionPercentage = $tspContributionPercentage + $rothContributionPercentage;

            if ($totalContributionPercentage == 0) {
                $matchingPercentage = 1 / 100;
            } else if ($totalContributionPercentage > 0 && $totalContributionPercentage <= 1) {
                $matchingPercentage = 2 / 100;
            } else if ($totalContributionPercentage > 1 && $totalContributionPercentage <= 2) {
                $matchingPercentage = 3 / 100;
            } else if ($totalContributionPercentage > 2 && $totalContributionPercentage <= 3) {
                $matchingPercentage = 4 / 100;
            } else if ($totalContributionPercentage > 3 && $totalContributionPercentage <= 4) {
                $matchingPercentage = 4.5 / 100;
            } else if ($totalContributionPercentage > 4 && $totalContributionPercentage <= 5) {
                $matchingPercentage = 5 / 100;
            } else if ($totalContributionPercentage > 5) {
                $matchingPercentage = 5 / 100;
            }

            $salary = str_replace(',', '', $request->salary_1);
            $salary = intval($salary);
            $totalMatching = $salary * $matchingPercentage;


            if (!empty($request->g_name)) {
                $gAverageRate = 1.025;
                $gFund = $request->g_name * $gAverageRate;
            } else {
                $gFund = 0;
            }

            if (!empty($request->f_name)) {
                $fAverageRate = 1.015;
                $fFund = $request->f_name * $fAverageRate;
            } else {
                $fFund = 0;
            }

            if (!empty($request->c_name)) {
                $cAverageRate = 1.13;
                $cFund = $request->c_name * $cAverageRate;
            } else {
                $cFund = 0;
            }

            if (!empty($request->s_name)) {
                $sAverageRate = 1.08;
                $sFund = $request->s_name * $sAverageRate;
            } else {
                $sFund = 0;
            }

            if (!empty($request->i_name)) {
                $iAverageRate = 1.05;
                $iFund = $request->i_name * $iAverageRate;
            } else {
                $iFund = 0;
            }

            if (!empty($request->l_income)) {
                $lAverageRate = 1.04;
                $lFund = $request->l_income * $lAverageRate;
            } else {
                $lFund = 0;
            }

            if (!empty($request->l_2025)) {
                $l2025AverageRate = 1.04;
                $l2025Fund = $request->l_2025 * $l2025AverageRate;
            } else {
                $l2025Fund = 0;
            }

            if (!empty($request->l_2030)) {
                $l2030AverageRate = 1.07;
                $l2030Fund = $request->l_2030 * $l2030AverageRate;
            } else {
                $l2030Fund = 0;
            }

            if (!empty($request->l_2035)) {
                $l2035AverageRate = 1.07;
                $l2035Fund = $request->l_2035 * $l2035AverageRate;
            } else {
                $l2035Fund = 0;
            }

            if (!empty($request->l_2040)) {
                $l2040AverageRate = 1.08;
                $l2040Fund = $request->l_2040 * $l2040AverageRate;
            } else {
                $l2040Fund = 0;
            }

            if (!empty($request->l_2045)) {
                $l2045AverageRate = 1.08;
                $l2045Fund = $request->l_2045 * $l2045AverageRate;
            } else {
                $l2045Fund = 0;
            }

            if (!empty($request->l_2050)) {
                $l2050AverageRate = 1.08;
                $l2050Fund = $request->l_2050 * $l2050AverageRate;
            } else {
                $l2050Fund = 0;
            }

            if (!empty($request->l_2055)) {
                $l2055AverageRate = 1.1;
                $l2055Fund = $request->l_2055 * $l2055AverageRate;
            } else {
                $l2055Fund = 0;
            }

            if (!empty($request->l_2060)) {
                $l2060AverageRate = 1.1;
                $l2060Fund = $request->l_2060 * $l2060AverageRate;
            } else {
                $l2060Fund = 0;
            }

            if (!empty($request->l_2065)) {
                $l2065AverageRate = 1.1;
                $l2065Fund = $request->l_2025 * $l2065AverageRate;
            } else {
                $l2065Fund = 0;
            }

            $totalTSPCalculate = $gFund + $fFund + $cFund + $sFund + $iFund + $lFund + $l2025Fund + $l2030Fund + $l2035Fund + $l2040Fund + $l2045Fund + $l2050Fund + $l2055Fund + $l2060Fund + $l2065Fund + $totalContribution + $totalMatching;
            if ($totalTSPCalculate > 0) {
                $tspCalculate = TSPCalculate::updateOrCreate(
                    ['fed_case_id'       => $fedCase->id],
                    [
                        'fed_case_id'        => $fedCase->id,
                        'gFund'              => $gFund,
                        'fFund'              => $fFund,
                        'cFund'              => $cFund,
                        'sFund'              => $sFund,
                        'iFund'              => $iFund,
                        'lFund'              => $lFund,
                        'l2025Fund'          => $l2025Fund,
                        'l2030Fund'          => $l2030Fund,
                        'l2035Fund'          => $l2035Fund,
                        'l2040Fund'          => $l2040Fund,
                        'l2045Fund'          => $l2045Fund,
                        'l2050Fund'          => $l2050Fund,
                        'l2055Fund'          => $l2055Fund,
                        'l2060Fund'          => $l2060Fund,
                        'l2065Fund'          => $l2065Fund,
                        'totalContribution'  => $totalContribution,
                        'totalMatching'      => $totalMatching,
                        'totalTSPCalculate'  => $totalTSPCalculate,
                        'matchingPercentage' => $matchingPercentage,
                    ]
                );
            }
            // end tsp calculation

            // start of social security calculation
            if ($request->employee_eligible == 'yes') {
                if ($request->amount_2) {
                    if ($request->amount_2 == 63) {
                        $percentagePIA = 0.75;
                    } else if ($request->amount_2 == 64) {
                        $percentagePIA = 0.80;
                    } else if ($request->amount_2 == 65) {
                        $percentagePIA = 0.867;
                    } else if ($request->amount_2 == 66) {
                        $percentagePIA = 0.933;
                    } else if ($request->amount_2 == 67) {
                        $percentagePIA = 1;
                    } else if ($request->amount_2 == 68) {
                        $percentagePIA = 1.08;
                    } else if ($request->amount_2 == 69) {
                        $percentagePIA = 1.16;
                    } else if ($request->amount_2 == 70) {
                        $percentagePIA = 1.24;
                    } else {
                        $percentagePIA = 0.7;
                    }
                } else {
                    $percentagePIA = 0.7;
                }
                $amountOfSS = str_replace(',', '', $request->amount_1);
                $amountOfSS = intval($amountOfSS);
                $totalAmountOfSS = $amountOfSS * $percentagePIA * 12 / .7;

                // SRS calculations
                if (preg_match('/(\d+)\s*Y/', $request->retirement_type_age, $matches)) {
                    $ageYearAtRetirement = (int)$matches[1];
                    if ($ageYearAtRetirement < 62) {
                        preg_match('/(\d+)\s*Y/', $request->yosDollar, $matches);
                        $yosYear = (int)$matches[1];
                        $srsAmount = str_replace(',', '', $request->amount_1);
                        $srsAmount = intval($srsAmount);
                        $srsAmount = $yosYear * $srsAmount / 40;
                        $srsAmount = SRS::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $srsAmount,
                            ]
                        );
                    }
                }

                $socialSecurity = SocialSecurity::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'  => $fedCase->id,
                        'amount'       => $totalAmountOfSS
                    ]
                );
            } else {
                $totalAmountOfSS = 0;
                $srsAmount = 0;
                $srsAmount = SRS::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'    => $fedCase->id,
                        'amount'         => $srsAmount,
                    ]
                );

                $socialSecurity = SocialSecurity::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'  => $fedCase->id,
                        'amount'       => $totalAmountOfSS
                    ]
                );
            }
            // end of social security calculation

            // sick leave hour 
            if ($request->current_leave_option == 'yes') {
                $leaveHours = $request->input('sick_leave_hours');
                $baseHours = 2082;
                $years = 0;
                while ($leaveHours >= $baseHours) {
                    $leaveHours -= $baseHours;
                    $years++;
                }
                $remainingHours = $leaveHours;
                $hoursDurationArray = SickLeavesConversion::all();
                foreach ($hoursDurationArray as $item) {
                    $finalDuration = null;
                    if ($remainingHours <= $item['hours']) {
                        $finalDuration = $item['duration'];
                        break;
                    }
                }
                $months = 0;
                $days = 0;
                preg_match('/(\d+)\s*M/', $finalDuration, $monthsMatch);
                preg_match('/(\d+)\s*D/', $finalDuration, $daysMatch);

                if (!empty($monthsMatch)) {
                    $months = (int) $monthsMatch[1];
                }
                if (!empty($daysMatch)) {
                    $days = (int) $daysMatch[1];
                }
                $formattedSickLeaveDuration = "{$years} Y, {$months} M, {$days} D";
            } else {
                $formattedSickLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // annual leave hour 
            if ($request->current_hours_option == 'yes') {
                $leaveHours = $request->input('annual_leave_hours');
                $baseHours = 2082;
                $years = 0;
                while ($leaveHours >= $baseHours) {
                    $leaveHours -= $baseHours;
                    $years++;
                }
                $remainingHours = $leaveHours;
                $hoursDurationArray = SickLeavesConversion::all();
                foreach ($hoursDurationArray as $item) {
                    $finalDuration = null;
                    if ($remainingHours <= $item['hours']) {
                        $finalDuration = $item['duration'];
                        break;
                    }
                }
                $months = 0;
                $days = 0;
                preg_match('/(\d+)\s*M/', $finalDuration, $monthsMatch);
                preg_match('/(\d+)\s*D/', $finalDuration, $daysMatch);

                if (!empty($monthsMatch)) {
                    $months = (int) $monthsMatch[1];
                }
                if (!empty($daysMatch)) {
                    $days = (int) $daysMatch[1];
                }
                $formattedAnnualLeaveDuration = "{$years} Y, {$months} M, {$days} D";
            } else {
                $formattedAnnualLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // military service date difference calculate in days
            if ($request->military_service == 'yes') {
                $military_service_date_1 = Carbon::parse($request->military_service_date_1);
                $military_service_date_2 = Carbon::parse($request->military_service_date_2);
                $diff = $military_service_date_1->diff($military_service_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceDate = "0 Y, 0 M, 0 D";
            }

            // military service active duty date difference calculate in days
            if ($request->military_service_active_duty  == 'yes') {
                $military_service_active_duty_date_1 = Carbon::parse($request->military_service_active_duty_date_1);
                $military_service_active_duty_date_2 = Carbon::parse($request->military_service_active_duty_date_2);
                $diff = $military_service_active_duty_date_1->diff($military_service_active_duty_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceActiveDutyDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceActiveDutyDate = "0 Y, 0 M, 0 D";
            }

            // military service reserve date difference calculate in days
            if ($request->military_service_reserve  == 'yes') {
                $military_service_reserve_date_1 = Carbon::parse($request->military_service_reserve_date_1);
                $military_service_reserve_date_2 = Carbon::parse($request->military_service_reserve_date_2);
                $diff = $military_service_reserve_date_1->diff($military_service_reserve_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceReserveDate = "{$years} Y, {$months} M, {$days} D";
            } else {
                $militaryServiceReserveDate = "0 Y, 0 M, 0 D";
            }


            // total military time calculate
            $totalMilitaryServiceTime = [$militaryServiceDate, $militaryServiceActiveDutyDate, $militaryServiceReserveDate];
            $totalYears = 0;
            $totalMonths = 0;
            $totalDays = 0;
            foreach ($totalMilitaryServiceTime as $duration) {
                // Extract years, months, and days from the duration string
                preg_match('/(\d+)\s*Y/', $duration, $years);
                preg_match('/(\d+)\s*M/', $duration, $months);
                preg_match('/(\d+)\s*D/', $duration, $days);

                $totalYears += isset($years[1]) ? (int)$years[1] : 0;
                $totalMonths += isset($months[1]) ? (int)$months[1] : 0;
                $totalDays += isset($days[1]) ? (int)$days[1] : 0;
            }
            while ($totalDays >= 30) {
                $totalDays -= 30;
                $totalMonths += 1;
            }
            while ($totalMonths >= 12) {
                $totalMonths -= 12;
                $totalYears += 1;
            }
            $parts = [];
            if ($totalYears > 0) {
                $parts[] = "{$totalYears} Y";
            }
            if ($totalMonths > 0) {
                $parts[] = "{$totalMonths} M";
            }
            if ($totalDays > 0) {
                $parts[] = "{$totalDays} D";
            }
            $totalMilitaryDuration = implode(', ', $parts);


            // calculate all yos data
            $finalValue = [$formattedSickLeaveDuration, $formattedAnnualLeaveDuration, $totalMilitaryDuration, $request->yosDollar];
            $totalFinalYears = 0;
            $totalFinalMonths = 0;
            $totalFinalDays = 0;

            foreach ($finalValue as $value) {
                // Extract years, months, and days from the duration string
                preg_match('/(\d+)\s*Y/', $value, $finalYears);
                preg_match('/(\d+)\s*M/', $value, $finalMonths);
                preg_match('/(\d+)\s*D/', $value, $finalDays);

                $totalFinalYears += isset($finalYears[1]) ? (int)$finalYears[1] : 0;
                $totalFinalMonths += isset($finalMonths[1]) ? (int)$finalMonths[1] : 0;
                $totalFinalDays += isset($finalDays[1]) ? (int)$finalDays[1] : 0;
            }
            while ($totalFinalDays >= 30) {
                $totalFinalDays -= 30;
                $totalFinalMonths += 1;
            }
            while ($totalFinalMonths >= 12) {
                $totalFinalMonths -= 12;
                $totalFinalYears += 1;
            }
            $finalParts = [];
            if ($totalFinalYears > 0) {
                $finalParts[] = "{$totalFinalYears} Y";
            }
            if ($totalFinalMonths > 0) {
                $finalParts[] = "{$totalFinalMonths} M";
            }
            if ($totalFinalDays > 0) {
                $finalParts[] = "{$totalFinalDays} D";
            }

            $finalData = implode(', ', $finalParts);

            $numberYears = 0;
            $numberMonths = 0;
            $numberDays = 0;

            // Parse the duration string
            if (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M,\s*(\d+)\s*D/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = (int)$matches[3];
            } elseif (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = 0;
            } elseif (preg_match('/(\d+)\s*Y/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = 0;
                $numberDays = 0;
            }

            $monthsToYears = $numberMonths / 12;
            $daysToYears = $numberDays / 365;

            $totalYears = $numberYears + $monthsToYears + $daysToYears;

            $finalTotalYears = round($totalYears, 2);

            // save data in yos($)
            $yosDollar = YosDollar::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'  => $fedCase->id,
                    'age'          => $request->yosDollar,
                    'value'        => $finalTotalYears,
                    'sick_leaves'   => $formattedSickLeaveDuration,
                    'annual_leaves' => $formattedAnnualLeaveDuration,
                ]
            );


            // calculate all yos(E) data
            $finalValue = [$totalMilitaryDuration, $request->yosDollar];
            $totalFinalYears = 0;
            $totalFinalMonths = 0;
            $totalFinalDays = 0;

            foreach ($finalValue as $value) {
                preg_match('/(\d+)\s*Y/', $value, $finalYears);
                preg_match('/(\d+)\s*M/', $value, $finalMonths);
                preg_match('/(\d+)\s*D/', $value, $finalDays);

                $totalFinalYears += isset($finalYears[1]) ? (int)$finalYears[1] : 0;
                $totalFinalMonths += isset($finalMonths[1]) ? (int)$finalMonths[1] : 0;
                $totalFinalDays += isset($finalDays[1]) ? (int)$finalDays[1] : 0;
            }
            while ($totalFinalDays >= 30) {
                $totalFinalDays -= 30;
                $totalFinalMonths += 1;
            }
            while ($totalFinalMonths >= 12) {
                $totalFinalMonths -= 12;
                $totalFinalYears += 1;
            }
            $finalParts = [];
            if ($totalFinalYears > 0) {
                $finalParts[] = "{$totalFinalYears} Y";
            }
            if ($totalFinalMonths > 0) {
                $finalParts[] = "{$totalFinalMonths} M";
            }
            if ($totalFinalDays > 0) {
                $finalParts[] = "{$totalFinalDays} D";
            }

            $finalData = implode(', ', $finalParts);

            $numberYears = 0;
            $numberMonths = 0;
            $numberDays = 0;

            // Parse the duration string
            if (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M,\s*(\d+)\s*D/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = (int)$matches[3];
            } elseif (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = (int)$matches[2];
                $numberDays = 0;
            } elseif (preg_match('/(\d+)\s*Y/', $finalData, $matches)) {
                $numberYears = (int)$matches[1];
                $numberMonths = 0;
                $numberDays = 0;
            }
            $monthsToYears = $numberMonths / 12;
            $daysToYears = $numberDays / 365;
            $totalYears = $numberYears + $monthsToYears + $daysToYears;

            $finalTotalYears = round($totalYears, 2);
            // save data in yos(E)
            $yosE = YosE::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'  => $fedCase->id,
                    'age'          => $request->yosDollar,
                    'value'        => $finalTotalYears
                ]
            );

            // calculate high three

            $annualSalaryIncrement = PercentageValue::value('annual_salary_increment');
            $annualSalaryIncrement = $annualSalaryIncrement / 100;
            $annualSalaryDecrement = 1 + $annualSalaryIncrement;
            $current_salary = intval(preg_replace('/\D/', '', $request->salary_1));
            if ($request->input('income_employee_option') == 'yes') {
                $retirementDate = $request->retirement_type_date; // Format: 'YYYY-MM-DD'
                // Convert the retirement date from the request to a Carbon instance
                $retirementDate = Carbon::parse($retirementDate);

                $date = $request->retirement_type_date;
                $retirementDateTime = new DateTime($date);
                $retirementYear = $retirementDateTime->format('y');
                $retirementMonth = $retirementDateTime->format('m');
                // Get today's date
                $today = Carbon::now();
                $currentDateTime = new DateTime($today);
                $currentYear = $currentDateTime->format('y');

                // Calculate the difference in years
                $yearsRemaining = $retirementYear - $currentYear;
                $salaries = [];
                $realCurrentSalary = $current_salary;
                $currentMonthlySalary = $current_salary / 12;
                $fourthRemainingMonth = 12 - $retirementMonth;
                $fourthYearSalary = $fourthRemainingMonth * $currentMonthlySalary;
                if ($yearsRemaining > 0) {
                    for ($i = 1; $i <= $yearsRemaining; $i++) {
                        // Increment salary for each year
                        $current_salary += $current_salary * $annualSalaryIncrement;
                        $salaries[] = $current_salary;

                        // Handle specific cases
                        if ($yearsRemaining > 2) {
                            if ($yearsRemaining == 3 && $retirementMonth < 12) {
                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $current_salary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;

                                // Include second and third-year salaries
                                if ($i == 1) $secondYearSalary = $current_salary;
                                if ($i == 2) $thirdYearSalary = $current_salary;

                                if ($i == 3) {
                                    $average_salary = ($secondYearSalary + $thirdYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                                }
                            } elseif ($retirementMonth == 12) {
                                // Standard case for full years
                                rsort($salaries);
                                $topThreeSalaries = array_slice($salaries, 0, 3);
                                $average_salary = array_sum($topThreeSalaries) / count($topThreeSalaries);
                            } else if ($yearsRemaining > 3 && $retirementMonth < 12) {
                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $current_salary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;

                                // Include second and third-year salaries
                                if ($i == $yearsRemaining - 1) $secondYearSalary = $current_salary;
                                if ($i == $yearsRemaining - 2) $thirdYearSalary = $current_salary;
                                if ($i == $yearsRemaining - 3) {
                                    $fourthYearSalary = $current_salary;
                                    $fourthMonthlySalary = $fourthYearSalary / 12;
                                    $fourthYearSalary = $fourthMonthlySalary * $fourthRemainingMonth;
                                }
                                if ($i == $yearsRemaining) {
                                    $average_salary = ($secondYearSalary + $thirdYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                                }
                            }
                        } elseif ($yearsRemaining == 2) {
                            if ($retirementMonth == 12) {
                                // Handle two years remaining
                                $nextYearSalary = $current_salary;
                                $nextToNextYearSalary = $nextYearSalary + $nextYearSalary * $annualSalaryIncrement;
                                $average_salary = ($realCurrentSalary + $nextYearSalary + $nextToNextYearSalary) / 3;
                            } else {
                                // Handle two years remaining
                                $nextYearSalary = $current_salary;
                                $nextToNextYearSalary = $nextYearSalary + $nextYearSalary * $annualSalaryIncrement;

                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $nextToNextYearSalary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;
                                // Include second and third-year salaries
                                $fourthYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $fourthMonthlySalary = $fourthYearSalary / 12;
                                $fourthYearSalary = $fourthMonthlySalary * $fourthRemainingMonth;
                                $average_salary = ($realCurrentSalary + $nextYearSalary + $lastYearSalary + $fourthYearSalary) / 3;
                            }

                            break;
                        } elseif ($yearsRemaining == 1) {
                            if ($retirementMonth == 12) {
                                // Handle one year remaining
                                $lastYearSalary = $realCurrentSalary + $realCurrentSalary * $annualSalaryIncrement;
                                $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $average_salary = ($realCurrentSalary + $lastYearSalary + $previousYearSalary) / 3;
                                break;
                            } else {
                                $lastYearSalary = $realCurrentSalary + $realCurrentSalary * $annualSalaryIncrement;
                                $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                                $prePreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;

                                // Calculate salary for incomplete last year
                                $lastYearMonthlySalary = $lastYearSalary / 12;
                                $lastYearSalary = $lastYearMonthlySalary * $retirementMonth;
                                // Include second and third-year salaries
                                $prePreviousMonthlySalary = $prePreviousYearSalary / 12;
                                $prePreviousYearSalary = $prePreviousMonthlySalary * $fourthRemainingMonth;
                                $average_salary = ($realCurrentSalary + $lastYearSalary + $previousYearSalary + $prePreviousYearSalary) / 3;
                            }
                        }
                    }
                } else {
                    if ($retirementMonth == 12) {
                        $realCurrentSalary = $realCurrentSalary;
                        $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                        $secondPreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;
                        $average_salary = $realCurrentSalary + $secondPreviousYearSalary + $previousYearSalary;
                        $average_salary = $average_salary / 3;
                    } else {
                        $realCurrentSalary = $realCurrentSalary;
                        $previousYearSalary = $realCurrentSalary / $annualSalaryDecrement;
                        $secondPreviousYearSalary = $previousYearSalary / $annualSalaryDecrement;
                        $thirdPreviousYearSalary = $secondPreviousYearSalary / $annualSalaryDecrement;

                        $thirdPreviousMonthlySalary = $thirdPreviousYearSalary / 12;
                        $thirdPreviousYearSalary = $thirdPreviousMonthlySalary * $fourthRemainingMonth;

                        $currentMonthlySalary = $realCurrentSalary / 12;
                        $currentYearSalary = $currentMonthlySalary * $retirementMonth;

                        $average_salary = ($thirdPreviousYearSalary + $secondPreviousYearSalary + $previousYearSalary + $currentYearSalary) / 3;
                    }
                }
            } else {
                $salary_2 = intval(preg_replace('/\D/', '', $request->salary_2));
                $salary_3 = intval(preg_replace('/\D/', '', $request->salary_3));
                $salary_4 = intval(preg_replace('/\D/', '', $request->salary_4));
                $average_salary = ($salary_2 + $salary_3 + $salary_4) / 3;
            }
            $highThreeValue = HighThree::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'  => $fedCase->id,
                    'value'        => $average_salary,
                ]
            );

            // Annual Leave Hours Payout calculate
            if ($request->input('income_employee_option') == 'yes') {
                $payout = $current_salary * $request->annual_leave_hours / 2080;
            } else {
                $salaries = [
                    $request->salary_2,
                    $request->salary_3,
                    $request->salary_4
                ];
                // Use the max function to find the highest salary
                $highestSalary = max($salaries);
                $highestSalary = intval(preg_replace('/\D/', '', $highestSalary));
                $payout = $highestSalary * $request->annual_leave_hours / 2080;
            }
            $annualLeavePayout = AnnualLeavePayout::updateOrCreate(
                ['fed_case_id' => $fedCase->id],
                [
                    'fed_case_id'  => $fedCase->id,
                    'payout'       => $payout,
                ]
            );

            // pension section
            $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
            if ($fedCase->retirement_system) {
                $yosDollarAge = $yosDollar->value;
                // preg_match('/^(\d+)\s*Y/', $yosDollarAge, $matches);
                // if (isset($matches[1])) {
                //     $yosDollarAge = (int) $matches[1];
                // }
                if ($fedCase->retirement_system == 'csrs') {
                    if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {

                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else if (in_array($fedCase->employee_type, $types)) {
                        $remainingAge = $yosDollarAge - 20;
                        $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 0.025 * 20) +
                            ($highThreeValue->value * 0.02 * $remainingAge);

                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else {
                        $pension = 0;
                    }
                } else if ($fedCase->retirement_system == 'csrs_offset') {
                    // $date = $request->input('retirement_system_csrs_offset');
                    // $carbonDate = Carbon::parse($date);
                    // $year = $carbonDate->year;

                    // $retirmentDate = $request->input('retirement_type_date');
                    // $retirmentDate = Carbon::parse($retirmentDate);
                    // $retirmentYear = $retirmentDate->year;
                    // dd($retirmentYear - $year);
                    if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {
                        // $yosDollarAge = $yosDollar->value;
                        // $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        // $pension = ($highThreeValue->value * 5 * 0.015) + 
                        //             ($highThreeValue->value * 5 * 0.0175) + 
                        //             ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);

                        // $pensionMonthly = $pension / 12;

                        // $retirement_type_date = $fedCase->retirement_type_date;
                        // $retirement_type_date = Carbon::parse($retirement_type_date);
                        // $retirmentTypeDateYear = $retirement_type_date->year;

                        // $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                        // $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        // $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;

                        // $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        // $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                        // $pension = $pension - $csrsPension;
                        // $pension = $pension * 12;

                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else if (in_array($fedCase->employee_type, $types)) {
                        // $yosDollarAge = $yosDollar->value;
                        // $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                        // $pension = ($highThreeValue->value * 5 * 0.015) + 
                        //             ($highThreeValue->value * 5 * 0.0175) + 
                        //             ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);

                        // $pensionMonthly = $pension / 12;

                        // $retirement_type_date = $fedCase->retirement_type_date;
                        // $retirement_type_date = Carbon::parse($retirement_type_date);
                        // $retirmentTypeDateYear = $retirement_type_date->year;

                        // $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                        // $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        // $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;

                        // $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        // $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                        // $pension = $pensionMonthly - $csrsPension;
                        // $pension = $pension * 12;
                        $pension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($yosDollarAge - 10) * 0.02);
                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else {
                        $pension = 0;
                    }
                } else if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
                    if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {
                        $retirement_type_age = $fedCase->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];

                        if ($numericPart >= 62 && $yosDollarAge >= 20) {
                            $numericPart = trim($numericPart);
                            $numericValue = floatval($numericPart);
                            $pension = $yosDollarAge * $highThreeValue->value * 0.011;
                            $pension = Pension::updateOrCreate(
                                ['fed_case_id' => $fedCase->id],
                                [
                                    'fed_case_id'  => $fedCase->id,
                                    'amount'       => $pension,
                                ]
                            );
                        } else {
                            $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                            $pension = Pension::updateOrCreate(
                                ['fed_case_id' => $fedCase->id],
                                [
                                    'fed_case_id'  => $fedCase->id,
                                    'amount'       => $pension,
                                ]
                            );
                        }
                    } else if (in_array($fedCase->employee_type, $types)) {
                        $startDate = Carbon::parse($request->input('scd'));
                        $today = Carbon::now();
                        $yearsDifference = $startDate->diffInYears($today);
                        $yearsDifference = intval($yearsDifference);

                        if ($yearsDifference >= 20) {
                            $remainAgeYOS = $yosDollarAge - 20;
                            $pension1 = $remainAgeYOS * $highThreeValue->value * 0.01;
                            $firstYearPension = 20 * $highThreeValue->value * 0.017;
                            $pension = $pension1 + $firstYearPension;
                            $pension = Pension::updateOrCreate(
                                ['fed_case_id' => $fedCase->id],
                                [
                                    'fed_case_id'  => $fedCase->id,
                                    'amount'       => $pension,
                                    'first_year'   => $firstYearPension,
                                ]
                            );
                        } else {
                            $pension = $yosDollarAge * $highThreeValue->value * 0.01;
                            $pension = Pension::updateOrCreate(
                                ['fed_case_id' => $fedCase->id],
                                [
                                    'fed_case_id'  => $fedCase->id,
                                    'amount'       => $pension,
                                ]
                            );
                        }
                    } else {
                        $pension = 0;
                    }
                } else if ($fedCase->retirement_system == 'fers_transfer') {
                    if ($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal') {
                        $retirement_type_date = $fedCase->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;

                        $retirement_system_fers_transfer = $fedCase->retirement_system_fers_transfer;
                        $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                        $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                        $rscd = $fedCase->rscd;
                        $rscd = Carbon::parse($rscd);
                        $rscd = $rscd->year;

                        $csrsYear = $retirment_fers_transfer_year - $rscd;

                        $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                        $csrsPension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($csrsYear - 10) * 0.02);

                        $fersPension = $fersYear * $highThreeValue->value * 0.01;

                        $pension = $csrsPension + $fersPension;
                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else if (in_array($fedCase->employee_type, $types)) {
                        $retirement_type_date = $fedCase->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;

                        $retirement_system_fers_transfer = $fedCase->retirement_system_fers_transfer;
                        $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                        $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                        $rscd = $fedCase->rscd;
                        $rscd = Carbon::parse($rscd);
                        $rscd = $rscd->year;

                        $csrsYear = $retirment_fers_transfer_year - $rscd;

                        $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                        $csrsPension = ($highThreeValue->value * 5 * 0.015) +
                            ($highThreeValue->value * 5 * 0.0175) +
                            ($highThreeValue->value * ($csrsYear - 10) * 0.02);

                        $fersPension = $fersYear * $highThreeValue->value * 0.01;

                        $pension = $csrsPension + $fersPension;
                        $pension = Pension::updateOrCreate(
                            ['fed_case_id' => $fedCase->id],
                            [
                                'fed_case_id'  => $fedCase->id,
                                'amount'       => $pension,
                            ]
                        );
                    } else {
                        $pension = 0;
                    }
                } else {
                    $pension = 0;
                }
            }

            if ($request->employee_spouse == 'yes') {
                if ($request->survior_benefit_fers != null) {
                    if ($request->survior_benefit_fers == 50) {
                        $pensionAmount = $pension->amount;
                        $survivorBenefitCost = $pensionAmount * 0.1;
                    } else if ($request->survior_benefit_fers == 25) {
                        $pensionAmount = $pension->amount;
                        $survivorBenefitCost = $pensionAmount * 0.05;
                    } else {
                        $survivorBenefitCost = 0;
                    }
                } else if ($request->survior_benefit_csrs != null) {
                    $pensionAmount = $pension->amount;
                    $totalLeavePension = $pensionAmount * $request->survior_benefit_csrs / 100;
                    if ($totalLeavePension <= 3600) {
                        $survivorBenefitCost = $totalLeavePension * 0.025;
                    } else {
                        $remainningLeavePension = $totalLeavePension - 3600;
                        $survivorBenefitFirst = 3600 * 0.025;
                        $survivorBenefitSecond = $remainningLeavePension * 0.1;
                        $survivorBenefitCost = $survivorBenefitFirst + $survivorBenefitSecond;
                    }
                } else {
                    $survivorBenefitCost = 0;
                }
                $survivorBenefit = SurvivorBenefit::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'  => $fedCase->id,
                        'cost'         => $survivorBenefitCost,
                    ]
                );
            }

            // part time pension
            if ($request->employee_work == 'yes') {
                $empolyee_multiple_date = Carbon::parse($request->empolyee_multiple_date);
                $empolyee_multiple_date_to = Carbon::parse($request->empolyee_multiple_date_to);
                $employeePartTimeDays = $empolyee_multiple_date->diffInDays($empolyee_multiple_date_to);
                $employeePartTimeDays = $employeePartTimeDays * $fedCase->empolyee_hours_work;
                $partTimePercentage = $fedCase->empolyee_hours_work / 40;
                $partTimePensionAmount = $partTimePercentage * $employeePartTimeDays;
                $partTimePension = PartTimePension::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'  => $fedCase->id,
                        'amount'       => $partTimePensionAmount,
                    ]
                );
            }


            // insurance section
            if (!empty($request->insurance)) {
                if ($request->insurance == 'yes') {
                    if ($request->insurance_emloyee == 'yes' && $request->insurance_retirement == 'yes') {
                        $client_age = $request->age;
                        $client_age = explode('Y', $client_age)[0];
                        if ($request->insurance_coverage_basic_option == 'basic_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = $salary / 1000;
                            $salary = intval($salary);


                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            $insuranceCostOptionBasic = $salary * 0.3467;
                            $insuranceCostOptionBasic = $insuranceCostOptionBasic * 12;
                        } else {
                            $insuranceCostOptionBasic = 0;
                        }
                        if ($request->insurance_coverage_a_option == 'a_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);
                            $salary =  $salary + 2000;
                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            if ($client_age < 35) {
                                $insuranceCostOptionA = $salary * 0.43;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionA = $salary * 0.43;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionA = $salary * 0.65;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionA = $salary * 1.30;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionA = $salary * 2.17;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionA = $salary * 3.90;
                            } else {
                                $insuranceCostOptionA = $salary * 13.00;
                            }
                        } else {
                            $insuranceCostOptionA = 0;
                        }
                        if ($request->insurance_coverage_b_option == 'b_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;

                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);

                            $annualPay = $salary * $request->option_b_value;
                            $monthlyBasic = $annualPay / 1000;

                            if ($client_age < 35) {
                                $insuranceCostOptionB = $monthlyBasic * 0.043;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionB = $monthlyBasic * 0.043;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionB = $monthlyBasic * 0.065;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionB = $monthlyBasic * 0.130;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionB = $monthlyBasic * 0.217;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionB = $monthlyBasic * 0.390;
                            } else if ($client_age >= 60 && $client_age <= 64) {
                                $insuranceCostOptionB = $monthlyBasic * 0.867;
                            } else if ($client_age >= 65 && $client_age <= 69) {
                                $insuranceCostOptionB = $monthlyBasic * 1.040;
                            } else if ($client_age >= 70 && $client_age <= 74) {
                                $insuranceCostOptionB = $monthlyBasic * 1.863;
                            } else if ($client_age >= 75 && $client_age <= 79) {
                                $insuranceCostOptionB = $monthlyBasic * 3.900;
                            } else {
                                $insuranceCostOptionB = $monthlyBasic * 6.240;
                            }
                        } else {
                            $insuranceCostOptionB = 0;
                        }
                        if ($request->insurance_coverage_c_option == 'c_option') {
                            $incrementRate = 0.03;
                            $currentSalary = $request->salary_1;
                            // $currentYear = Carbon::now()->year;
                            // $retirement_type_date = $request->retirement_type_date;
                            // $retirement_type_date = Carbon::parse($retirement_type_date);
                            // $retirementYear = $retirement_type_date->year;
                            // $currentYear = intval($currentYear);
                            // $retirementYear = intval($retirementYear);
                            // $remainingYear = $retirementYear - $currentYear;
                            $salary = str_replace(',', '', $currentSalary);
                            $salary = intval($salary);
                            $value = $request->insurance_employee_coverage_c;
                            // for ($year = 1; $year <= $remainingYear; $year++) {
                            //     $salary *= (1 + $incrementRate);
                            // }
                            if ($client_age < 35) {
                                $insuranceCostOptionC = $value * 0.43;
                            } else if ($client_age >= 35 && $client_age <= 39) {
                                $insuranceCostOptionC = $value * 0.52;
                            } else if ($client_age >= 40 && $client_age <= 44) {
                                $insuranceCostOptionC = $value * 0.80;
                            } else if ($client_age >= 45 && $client_age <= 49) {
                                $insuranceCostOptionC = $value * 1.15;
                            } else if ($client_age >= 50 && $client_age <= 54) {
                                $insuranceCostOptionC = $value * 1.80;
                            } else if ($client_age >= 55 && $client_age <= 59) {
                                $insuranceCostOptionC = $value * 2.88;
                            } else if ($client_age >= 60 && $client_age <= 64) {
                                $insuranceCostOptionC = $value * 5.27;
                            } else if ($client_age >= 65 && $client_age <= 69) {
                                $insuranceCostOptionC = $value * 6.13;
                            } else if ($client_age >= 70 && $client_age <= 74) {
                                $insuranceCostOptionC = $value * 8.30;
                            } else if ($client_age >= 75 && $client_age <= 79) {
                                $insuranceCostOptionC = $value * 12.48;
                            } else {
                                $insuranceCostOptionC = $value * 16.90;
                            }
                        } else {
                            $insuranceCostOptionC = 0;
                        }
                        $fegliInsuranceCost = FEGLI::updateOrCreate(
                            ['fed_case_id'  => $fedCase->id],
                            [
                                'fed_case_id'   => $fedCase->id,
                                'basic'         => $insuranceCostOptionBasic,
                                'optionA'       => $insuranceCostOptionA,
                                'optionB'       => $insuranceCostOptionB,
                                'optionC'       => $insuranceCostOptionC,
                            ]
                        );
                    }
                }
            }


            // start calculations DENTAL AND VISION
            $todayDate = Carbon::now();
            $retirementDate = Carbon::parse($request->retirement_type_date);
            $yearsUntilRetirement = $todayDate->diff($retirementDate);
            $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
            if (!empty($request->vision_total_cost)) {
                $dentalVisionCombine = str_replace(',', '', $request->vision_total_cost);
                $dentalVisionCombine = intval($dentalVisionCombine);
                $dentalVisionCombine = $dentalVisionCombine * 26;
                $dentalPremiumAmount = 0;
                $visionPremiumAmount = 0;
                $dentalAndVisionValue = DentalAndVision::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id'         => $fedCase->id,
                        'dentalPremiumAmount' => $dentalPremiumAmount,
                        'visionPremiumAmount' => $visionPremiumAmount,
                        'dentalVisionCombine' => $dentalVisionCombine,
                    ]
                );
            } else {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                if ($request->dental == 'yes' && $request->vision == 'no') {
                    $dentalPremiumAmount = $dentalPremiumAmount * 26;
                    $visionPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                } else if ($request->vision == 'yes' && $request->dental == 'no') {
                    $visionPremiumAmount = $visionPremiumAmount * 26;
                    $dentalPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                } else if ($request->dental == 'yes' && $request->vision == 'yes') {
                    $dentalPremiumAmount = $dentalPremiumAmount * 26;
                    $visionPremiumAmount = $visionPremiumAmount * 26;
                    $dentalVisionCombine = 0;
                } else {
                    $dentalPremiumAmount = 0;
                    $visionPremiumAmount = 0;
                    $dentalVisionCombine = 0;
                }
                $dentalAndVisionValue = DentalAndVision::updateOrCreate(
                    ['fed_case_id' => $fedCase->id],
                    [
                        'fed_case_id' => $fedCase->id,
                        'dentalPremiumAmount' => $dentalPremiumAmount,
                        'visionPremiumAmount' => $visionPremiumAmount,
                        'dentalVisionCombine' => $dentalVisionCombine,
                    ]
                );
            }
            // end calculations DENTAL AND VISION


            // start FEHB VARIOUS PLANS calculations
            if ($request->coverage_retirement == 'yes') {
                $todayDate = Carbon::now();
                $retirementDate = Carbon::parse($request->retirement_type_date);
                $yearsUntilRetirement = $todayDate->diff($retirementDate);
                $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;

                $fehbPremiumAmount = str_replace(',', '', $request->premium);
                $fehbPremiumAmount = intval($fehbPremiumAmount);
                $fehbPremiumAmount = $fehbPremiumAmount * 26;
                // for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                // $fehbPremiumAmount += $fehbPremiumAmount * (5 / 100);
                // }
                $fehbVPValue = FEHBVP::updateOrCreate(
                    ['fed_case_id'      => $fedCase->id],
                    [
                        'fed_case_id'       => $fedCase->id,
                        'fehbPremiumAmount' => $fehbPremiumAmount,
                    ]
                );
            } else {
                $fehbPremiumAmount = 0;
                $fehbVPValue = FEHBVP::updateOrCreate(
                    ['fed_case_id'      => $fedCase->id],
                    [
                        'fed_case_id'       => $fedCase->id,
                        'fehbPremiumAmount' => $fehbPremiumAmount,
                    ]
                );
            }

            // end FEHB VARIOUS PLANS calculations

            // start FLTCIP calculations 
            if ($request->insurance_program == 'yes') {
                if ($request->insurance_program_retirement == 'yes') {
                    preg_match('/(\d+)\s*Y/', $request->age, $matches);
                    $age = (int)$matches[1];
                    $age = intval((95 - $age) / 5);

                    $insurancePurchasePremiumAmount = str_replace(',', '', $request->insurance_purchase_premium);
                    $insurancePurchasePremiumAmount = intval($insurancePurchasePremiumAmount);
                    $insurancePurchasePremiumAmount = $insurancePurchasePremiumAmount * 26;
                    $yearlyPremiumAmount = $insurancePurchasePremiumAmount;
                    for ($i = 1; $i <= $age; $i++) {
                        $insurancePurchasePremiumAmount += $insurancePurchasePremiumAmount * (85 / 100);
                    }
                } else {
                    $todayDate = Carbon::now();
                    $retirementDate = Carbon::parse($request->retirement_type_date);
                    $yearsUntilRetirement = $todayDate->diff($retirementDate);
                    $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
                    $yearsUntilRetirementCalculate = $yearsUntilRetirementCalculate / 5;

                    $insurancePurchasePremiumAmount = str_replace(',', '', $request->insurance_purchase_premium);
                    $insurancePurchasePremiumAmount = intval($insurancePurchasePremiumAmount);
                    $insurancePurchasePremiumAmount = $insurancePurchasePremiumAmount * 26;
                    $yearlyPremiumAmount = $insurancePurchasePremiumAmount;
                    for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                        $insurancePurchasePremiumAmount += $insurancePurchasePremiumAmount * (85 / 100);
                    }
                }
                $fltcipValue = FLTCIP::updateOrCreate(
                    ['fed_case_id'                   => $fedCase->id],
                    [
                        'fed_case_id'                    => $fedCase->id,
                        'yearlyPremiumAmount'            => $yearlyPremiumAmount,
                        'insurancePurchasePremiumAmount' => $insurancePurchasePremiumAmount,
                    ]
                );
            }
            // end FLTCIP calculations

            return response()->json([
                'status'  => true,
                'message' => 'case updated successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function destroy(FedCase $fedCase)
    {
        $fedCase = FedCase::where('id', $fedCase->id)->first();
        $fedCase->delete();
        return response()->json([
            'status'   => true,
            'message'  => 'Case Deleted Successfully'
        ]);
    }

    public function downloadPDF(FedCase $fedCase)
    {
        $user = Auth::user();
        $userData = User::where('id', $user->id)->first();
        $profile = $userData->profile;

        $highThree = HighThree::where('fed_case_id', $fedCase->id)->first();
        $pension = Pension::where('fed_case_id', $fedCase->id)->first();
        $fehbVP = FEHBVP::where('fed_case_id', $fedCase->id)->first();
        $dentalAndVision = DentalAndVision::where('fed_case_id', $fedCase->id)->first();
        $insuranceCost = FEGLI::where('fed_case_id', $fedCase->id)->first();
        $tspCalculation = TSPCalculate::where('fed_case_id', $fedCase->id)->first();
        $srsData = SRS::where('fed_case_id', $fedCase->id)->first();
        $socialSecurity = SocialSecurity::where('fed_case_id', $fedCase->id)->first();
        $survivorBenefit = SurvivorBenefit::where('fed_case_id', $fedCase->id)->first();
        $fltcip = FLTCIP::where('fed_case_id', $fedCase->id)->first();
        $annualSalaryIncrement = ASI::value('value');
        $annualSalaryIncrement = $annualSalaryIncrement / 100;
        if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_transfer' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
            $annualColaIncrement = COLA::value('fers_cola');
        } else {
            $annualColaIncrement = COLA::value('csrs_cola');
        }
        $annualColaIncrement = $annualColaIncrement / 100;
        // FEGLI
        if ($insuranceCost) {
            $fegliAmountTotal = $insuranceCost->basic + $insuranceCost->optionA + $insuranceCost->optionB + $insuranceCost->optionC;
            $fegliAmountArray = [];
        } else {
            $fegliAmountTotal = 0;
            $fegliAmountArray = [];
        }


        // Convert the premium text to an integer
        $premium = str_replace(',', '', $fedCase->InsurancePlan->premium);
        $premium = intval($premium);
        // Convert the current salary text to an integer
        $currentSalary = str_replace(',', '', $fedCase->salary_1);
        $currentSalary = intval($currentSalary);
        // Multiply the premium by 26
        $initialPremium = $premium * 26;

        // Employee's current age
        preg_match('/(\d+)\s*Y/', $fedCase->retirement_type_age, $matches);
        $currentAge = (int)$matches[1];

        $dentalAndVisionValue = intval($dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount);
        $dentalAndVisionValueArray = [];

        $pensionArray = [];
        if (!empty($pension->amount)) {
            $pensionAmount = $pension->amount;
        } else {
            $pensionAmount = 0;
        }

        // survivor benefit 
        $SurvivorBenefitArray = [];
        $survivorBenefitAmount = $survivorBenefit->cost;
        // Calculate the premiums for each year until age 90
        $premiums = [];
        $premiumAmount = $initialPremium;

        // for SRS

        if ($srsData) {
            $srsArray = [];
            $srsAmount = $srsData->amount;
        } else {
            $srsArray = [];
            $srsAmount = 0;
        }


        // for SS
        $ssAmount = $socialSecurity->amount;
        $ssArray = [];
        if ($tspCalculation) {
            $gFundAmount = $tspCalculation->gFund;
            $fFundAmount = $tspCalculation->fFund;
            $cFundAmount = $tspCalculation->cFund;
            $sFundAmount = $tspCalculation->sFund;
            $iFundAmount = $tspCalculation->iFund;
            $lFundAmount = $tspCalculation->lFund;
            $l2025FundAmount = $tspCalculation->l2025Fund;
            $l2030FundAmount = $tspCalculation->l2030Fund;
            $l2035FundAmount = $tspCalculation->l2035Fund;
            $l2040FundAmount = $tspCalculation->l2040Fund;
            $l2045FundAmount = $tspCalculation->l2045Fund;
            $l2050FundAmount = $tspCalculation->l2050Fund;
            $l2055FundAmount = $tspCalculation->l2055Fund;
            $l2060FundAmount = $tspCalculation->l2060Fund;
            $l2065FundAmount = $tspCalculation->l2065Fund;

            $tspCalculationTotalArray = [];
            $tspCalculationTotal = $tspCalculation->totalTSPCalculate;
            $tspCalculationPercentage = $fedCase->tSP->contribute_pp_percentage;
            $rothCalculationPercentage = $fedCase->tSP->contribute_tsp_pp_percentage;
            $totalContributionPercentage = $tspCalculationPercentage + $rothCalculationPercentage;
            $MatchingPercentage = $tspCalculation->matchingPercentage;

            $totalContribution = $tspCalculation->totalContribution;
            $totalMatching = $tspCalculation->totalMatching;
            $totalConMatch = $totalContribution + $totalMatching;
        } else {
            $gFundAmount = 0;
            $fFundAmount = 0;
            $cFundAmount = 0;
            $sFundAmount = 0;
            $iFundAmount = 0;
            $lFundAmount = 0;
            $l2025FundAmount = 0;
            $l2030FundAmount = 0;
            $l2035FundAmount = 0;
            $l2040FundAmount = 0;
            $l2045FundAmount = 0;
            $l2050FundAmount = 0;
            $l2055FundAmount = 0;
            $l2060FundAmount = 0;
            $l2065FundAmount = 0;
            $tspCalculationTotal = 0;
            $totalConMatch = 0;
            $totalContributionPercentage = 1;
            $MatchingPercentage = 1;
        }
        $gFundCon = $fedCase->g_value;
        $fFundCon = $fedCase->f_value;
        $cFundCon = $fedCase->c_value;
        $sFundCon = $fedCase->s_value;
        $iFundCon = $fedCase->i_value;
        $lFundCon = $fedCase->l_income_value;
        $l2025FundCon = $fedCase->l_2025_value;
        $l2030FundCon = $fedCase->l_2030_value;
        $l2035FundCon = $fedCase->l_2035_value;
        $l2040FundCon = $fedCase->l_2040_value;
        $l2045FundCon = $fedCase->l_2045_value;
        $l2050FundCon = $fedCase->l_2050_value;
        $l2055FundCon = $fedCase->l_2055_value;
        $l2060FundCon = $fedCase->l_2060_value;
        $l2065FundCon = $fedCase->l_2065_value;

        $totalIncome = [];
        $totalExpenses = [];
        for ($age = $currentAge; $age <= 90; $age++) {
            $premiumAmount += $premiumAmount * 0.05; // Increment by 5%
            $premiums[] = $premiumAmount;
            $ageCount[] = $age;


            $srsArray[] = $srsAmount; //for SRS

            $ssArray[] = $ssAmount; // for SS
            $ssAmount += $ssAmount * 0.025;

            // FEGLI
            $fegliAmountTotal += $fegliAmountTotal * 0.03;
            $fegliAmountArray[] = $fegliAmountTotal;


            $dentalAndVisionValueArray[] = $dentalAndVisionValue;
            $dentalAndVisionValue += $dentalAndVisionValue * 0.05;

            $pensionArray[] = $pensionAmount;
            $pensionAmount += $pensionAmount * $annualColaIncrement;

            // Survivor benefit 

            $SurvivorBenefitArray[] = $survivorBenefitAmount;
            if ($fedCase->employee_spouse == 'yes') {
                if ($fedCase->survior_benefit_fers != null) {
                    if ($fedCase->survior_benefit_fers == 50) {
                        $survivorBenefitAmount = $pensionAmount * 0.1;
                    } else if ($fedCase->survior_benefit_fers == 25) {
                        $survivorBenefitAmount = $pensionAmount * 0.05;
                    } else {
                        $survivorBenefitAmount = 0;
                    }
                } else if ($fedCase->survior_benefit_csrs != null) {
                    $totalLeavePension = $pensionAmount * $fedCase->survior_benefit_csrs / 100;
                    if ($totalLeavePension <= 3600) {
                        $survivorBenefitAmount = $totalLeavePension * 0.025;
                    } else {
                        $remainningLeavePension = $totalLeavePension - 3600;
                        $survivorBenefitFirst = 3600 * 0.025;
                        $survivorBenefitSecond = $remainningLeavePension * 0.1;
                        $survivorBenefitAmount = $survivorBenefitFirst + $survivorBenefitSecond;
                    }
                } else {
                    $survivorBenefitAmount = 0;
                }
            }

            // start tsp calculation
            if ($tspCalculationTotal) {
                $tspCalculationTotalArray[] = $tspCalculationTotal;
            } else {
                $tspCalculationTotalArray[] = 0;
            }
            $gFundAmount += $totalConMatch * $gFundCon;
            $gFundAmount += $gFundAmount * 0.025;

            $fFundAmount += $totalConMatch * $fFundCon;
            $fFundAmount += $fFundAmount * 0.015;

            $cFundAmount += $totalConMatch * $cFundCon;
            $cFundAmount += $cFundAmount * 0.13;

            $sFundAmount += $totalConMatch * $sFundCon;
            $sFundAmount += $sFundAmount * 0.08;

            $iFundAmount += $totalConMatch * $iFundCon;
            $iFundAmount += $iFundAmount * 0.05;

            $lFundAmount += $totalConMatch * $lFundCon;
            $lFundAmount += $lFundAmount * 0.04;

            $l2025FundAmount += $totalConMatch * $l2025FundCon;
            $l2025FundAmount += $l2025FundAmount * 0.04;

            $l2030FundAmount += $totalConMatch * $l2030FundCon;
            $l2030FundAmount += $l2030FundAmount * 0.07;

            $l2035FundAmount += $totalConMatch * $l2035FundCon;
            $l2035FundAmount += $l2035FundAmount * 0.07;

            $l2040FundAmount += $totalConMatch * $l2040FundCon;
            $l2040FundAmount += $l2040FundAmount * 0.08;

            $l2045FundAmount += $totalConMatch * $l2045FundCon;
            $l2045FundAmount += $l2045FundAmount * 0.08;

            $l2050FundAmount += $totalConMatch * $l2050FundCon;
            $l2050FundAmount += $l2050FundAmount * 0.08;

            $l2055FundAmount += $totalConMatch * $l2055FundCon;
            $l2055FundAmount += $l2055FundAmount * 0.1;

            $l2060FundAmount += $totalConMatch * $l2060FundCon;
            $l2060FundAmount += $l2060FundAmount * 0.1;

            $l2065FundAmount += $totalConMatch * $l2065FundCon;
            $l2065FundAmount += $l2065FundAmount * 0.1;


            $currentSalary  += $currentSalary * 0.02; // increment by 2%

            $totalContribution = $currentSalary * $totalContributionPercentage / 100;

            $totalMatching = $currentSalary * $MatchingPercentage;
            // dd($gFundAmount."f".$fFundAmount."c".$cFundAmount."s".$sFundAmount."i".$iFundAmount."L".$lFundAmount."2025".$l2025FundAmount."2030".$l2030FundAmount."2035".$l2035FundAmount."2040".$l2040FundAmount."2045".$l2045FundAmount."2050".$l2050FundAmount."2055".$l2055FundAmount."2060".$l2060FundAmount."2065".$l2065FundAmount."cont".$totalContribution."match".$totalMatching);

            $tspCalculationTotal = $gFundAmount + $fFundAmount + $cFundAmount + $sFundAmount + $iFundAmount + $lFundAmount + $l2025FundAmount + $l2030FundAmount + $l2035FundAmount + $l2040FundAmount + $l2045FundAmount + $l2050FundAmount + $l2055FundAmount + $l2060FundAmount + $l2065FundAmount + $totalContribution + $totalMatching;
            // end tsp calculation


            $totalIncomeArray[] = $pensionAmount + $srsAmount + $ssAmount + $tspCalculationTotal;
            $totalExpensesArray[] = $premiumAmount + $dentalAndVisionValue + $survivorBenefitAmount + $fegliAmountTotal;
        }
        $totalIncomeSum = array_sum($totalIncomeArray);
        $totalExpensesSum = array_sum($totalExpensesArray);
        $totalDiffIncomeExpense = $totalIncomeSum - $totalExpensesSum;

        // code for FLTCIP
        $ageFLTCIP = intval($currentAge / 5);
        $fltcipArray = [];
        $fltcipAmount = $fltcip->yearlyPremiumAmount;
        for ($i = 1; $i <= $ageFLTCIP; $i++) {
            $fltcipArray[] = $fltcipAmount;
            $fltcipAmount = $fltcipAmount * (85 / 100);
        }
        $totalFLTCIPSum = array_sum($fltcipArray);

        // code for calculate service today
        $todayDate = date('Y-m-d');
        $rscdDate = $fedCase->rscd;

        if (!$todayDate || !$rscdDate) {
            $differenceString = '';
        } else {
            $todayDateObj = new \DateTime($todayDate);
            $rscdDateObj = new \DateTime($rscdDate);
            $interval = $todayDateObj->diff($rscdDateObj);

            $years = $interval->y;
            $months = $interval->m;
            $days = $interval->d;

            $differenceString = '';
            if ($years > 0) {
                $differenceString .= $years . ' Y';
            }
            if ($months > 0) {
                $differenceString .= ($differenceString ? ', ' : '') . $months . ' M';
            }
            if ($days > 0) {
                $differenceString .= ($differenceString ? ', ' : '') . $days . ' D';
            }
        }
        // end today service calculation

        // Dynamic data pass kar rahein
        $data = [
            'title' => 'My Custom PDF',
            'content' => 'Yah ek dynamic content hai jo PDF mein aa raha hai.',
            'fedCase' => $fedCase,
            'userData' => $userData,
            'profile' => $profile,

            'fedCase'                     => $fedCase,
            'highThree'                   => $highThree,
            'todayYOS'                    => $differenceString,
            'pensionAmount'               => $pensionAmount,
            'fehbVP'                      => $fehbVP,
            'dentalAndVision'             => $dentalAndVision,
            'fegliAmountTotal'            => $fegliAmountTotal,
            'fegliAmountArray'            => $fegliAmountArray,
            'premiumAmount'               => $premiumAmount,
            'premiums'                    => $premiums,
            'ageCount'                    => $ageCount,
            'dentalAndVisionValueArray'   => $dentalAndVisionValueArray,
            'pensionArray'                => $pensionArray,
            'tspCalculation'              => $tspCalculation,
            'tspCalculationTotalArray'    => $tspCalculationTotalArray,
            'srsAmount'                   => $srsAmount,
            'srsArray'                    => $srsArray,
            'ssAmount'                    => $ssAmount,
            'ssArray'                     => $ssArray,
            'survivorBenefitAmount'       => $survivorBenefitAmount,
            'SurvivorBenefitArray'        => $SurvivorBenefitArray,
            'totalIncomeArray'            => $totalIncomeArray,
            'totalExpensesArray'          => $totalExpensesArray,
            'totalDiffIncomeExpense'      => $totalDiffIncomeExpense,
            'totalFLTCIPSum'              => $totalFLTCIPSum,
        ];



        // PDF view ko render karna
        $pdf = FacadePdf::loadView('admin.fed-case.pdf', $data);

        // PDF ko download karwana
        return $pdf->download($fedCase->name . '.pdf');
    }

    public function sideBar(Request $request, FedCase $fedCase)
    {
        $highThree = $request->highThree;
        $highThree = preg_replace('/[\$,]/', '', $highThree);
        $highThree = intval($highThree);

        $retirement_type_date = $request->retirement_type_date;
        $retirement_type_age = $request->retirement_type_age;

        $yosDollar = $request->yosDollar;
        $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
        if ($fedCase->retirement_system) {
            $yosDollarAge = $yosDollar;
            preg_match('/^(\d+)\s*Y/', $yosDollarAge, $matches);
            if (isset($matches[1])) {
                $yosDollarAge = (int) $matches[1];
            }
            if ($fedCase->retirement_system == 'csrs') {
                if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {
                    $pension = ($highThree * 5 * 0.015) +
                        ($highThree * 5 * 0.0175) +
                        ($highThree * ($yosDollarAge - 10) * 0.02);
                } else if (in_array($fedCase->employee_type, $types)) {
                    $remainingAge = $yosDollarAge - 20;
                    $pension = $yosDollarAge * $highThree * 0.01;
                    $pension = ($highThree * 0.025 * 20) +
                        ($highThree * 0.02 * $remainingAge);
                } else {
                    $pension = 0;
                }
            } else if ($fedCase->retirement_system == 'csrs_offset') {
                if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {
                    $pension = ($highThree * 5 * 0.015) +
                        ($highThree * 5 * 0.0175) +
                        ($highThree * ($yosDollarAge - 10) * 0.02);
                } else if (in_array($fedCase->employee_type, $types)) {
                    $pension = ($highThree * 5 * 0.015) +
                        ($highThree * 5 * 0.0175) +
                        ($highThree * ($yosDollarAge - 10) * 0.02);
                } else {
                    $pension = 0;
                }
            } else if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
                if ($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal') {
                    $retirement_type_age = $retirement_type_age;
                    $numericPart = explode('Y', $retirement_type_age)[0];

                    if ($numericPart >= 62 && $yosDollarAge >= 20) {
                        $numericPart = trim($numericPart);
                        $numericValue = floatval($numericPart);
                        $pension = $yosDollarAge * $highThree * 0.011;
                    } else {
                        $pension = $yosDollarAge * $highThree * 0.01;
                    }
                } else if (in_array($fedCase->employee_type, $types)) {
                    $startDate = Carbon::parse($request->input('scd'));
                    $today = Carbon::now();
                    $yearsDifference = $startDate->diffInYears($today);
                    $yearsDifference = intval($yearsDifference);

                    if ($yearsDifference >= 20) {
                        $remainAgeYOS = $yosDollarAge - 20;
                        $pension1 = $remainAgeYOS * $highThree * 0.01;
                        $firstYearPension = 20 * $highThree * 0.017;
                        $pension = $pension1 + $firstYearPension;
                    } else {
                        $pension = $yosDollarAge * $highThree * 0.01;
                    }
                } else {
                    $pension = 0;
                }
            } else if ($fedCase->retirement_system == 'fers_transfer') {
                if ($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal') {
                    $retirement_type_date = $retirement_type_date;
                    $retirement_type_date = Carbon::parse($retirement_type_date);
                    $retirmentTypeDateYear = $retirement_type_date->year;

                    $retirement_system_fers_transfer = $fedCase->retirement_system_fers_transfer;
                    $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                    $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                    $rscd = $fedCase->rscd;
                    $rscd = Carbon::parse($rscd);
                    $rscd = $rscd->year;

                    $csrsYear = $retirment_fers_transfer_year - $rscd;

                    $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                    $csrsPension = ($highThree * 5 * 0.015) +
                        ($highThree * 5 * 0.0175) +
                        ($highThree * ($csrsYear - 10) * 0.02);

                    $fersPension = $fersYear * $highThree * 0.01;

                    $pension = $csrsPension + $fersPension;
                } else if (in_array($fedCase->employee_type, $types)) {
                    $retirement_type_date = $retirement_type_date;
                    $retirement_type_date = Carbon::parse($retirement_type_date);
                    $retirmentTypeDateYear = $retirement_type_date->year;

                    $retirement_system_fers_transfer = $fedCase->retirement_system_fers_transfer;
                    $retirement_system_fers_transfer = Carbon::parse($retirement_system_fers_transfer);
                    $retirment_fers_transfer_year = $retirement_system_fers_transfer->year;

                    $rscd = $fedCase->rscd;
                    $rscd = Carbon::parse($rscd);
                    $rscd = $rscd->year;

                    $csrsYear = $retirment_fers_transfer_year - $rscd;

                    $fersYear =  $retirmentTypeDateYear - $retirment_fers_transfer_year;

                    $csrsPension = ($highThree * 5 * 0.015) +
                        ($highThree * 5 * 0.0175) +
                        ($highThree * ($csrsYear - 10) * 0.02);

                    $fersPension = $fersYear * $highThree * 0.01;

                    $pension = $csrsPension + $fersPension;
                } else {
                    $pension = 0;
                }
            } else {
                $pension = 0;
            }
        }

        $fehbVP = $request->fehb;
        $fehbVP = preg_replace('/[\$,]/', '', $fehbVP);
        $fehbVP = intval($fehbVP);

        $dentalAndVision = DentalAndVision::where('fed_case_id', $fedCase->id)->first();
        $insuranceCost = $request->fegli;
        $insuranceCost = preg_replace('/[\$,]/', '', $insuranceCost);
        $insuranceCost = intval($insuranceCost);
        $tspCalculation = TSP::where('fed_case_id', $fedCase->id)->first();
        // $tspCalculation = TSPCalculate::where('fed_case_id',$fedCase->id)->first();
        $srsData = SRS::where('fed_case_id', $fedCase->id)->first();
        $socialSecurity = SocialSecurity::where('fed_case_id', $fedCase->id)->first();

        $survivorBenefit = $request->sb;
        $survivorBenefit = preg_replace('/[\$,]/', '', $survivorBenefit);
        $survivorBenefit = intval($survivorBenefit);

        $fltcip = FLTCIP::where('fed_case_id', $fedCase->id)->first();
        $annualSalaryIncrement = ASI::value('value');
        $annualSalaryIncrement = $annualSalaryIncrement / 100;
        if ($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_transfer' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea') {
            $annualColaIncrement = COLA::value('fers_cola');
        } else {
            $annualColaIncrement = COLA::value('csrs_cola');
        }
        $annualColaIncrement = $annualColaIncrement / 100;

        // FEGLI
        if ($insuranceCost) {
            $fegliAmountTotal = $insuranceCost;
            $fegliAmountArray = [];
        } else {
            $fegliAmountTotal = 0;
            $fegliAmountArray = [];
        }


        // Convert the premium text to an integer
        $premium = $fehbVP;
        // Convert the current salary text to an integer
        $currentSalary = str_replace(',', '', $fedCase->salary_1);
        $currentSalary = intval($currentSalary);
        // Multiply the premium by 26
        $initialPremium = $premium * 26;

        // Employee's current age
        preg_match('/(\d+)\s*Y/', $retirement_type_age, $matches);
        $currentAge = (int)$matches[1];

        $dentalAndVisionValue = intval($dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount);
        $dentalAndVisionValueArray = [];

        $pensionArray = [];
        if (!empty($pension)) {
            $pensionAmount = $pension;
        } else {
            $pensionAmount = 0;
        }
        // survivor benefit 
        $SurvivorBenefitArray = [];
        $survivorBenefitAmount = $survivorBenefit;
        // Calculate the premiums for each year until age 90
        $premiums = [];
        $premiumAmount = $initialPremium;

        // for SRS

        if ($srsData) {
            $srsArray = [];
            $srsAmount = $srsData->amount;
        } else {
            $srsArray = [];
            $srsAmount = 0;
        }


        // for SS
        $ssAmount = $socialSecurity->amount;
        $ssArray = [];

        $gFundAmount = $tspCalculation->gFund;
        $fFundAmount = $tspCalculation->fFund;
        $cFundAmount = $tspCalculation->cFund;
        $sFundAmount = $tspCalculation->sFund;
        $iFundAmount = $tspCalculation->iFund;
        $lFundAmount = $tspCalculation->lFund;
        $l2025FundAmount = $tspCalculation->l2025Fund;
        $l2030FundAmount = $tspCalculation->l2030Fund;
        $l2035FundAmount = $tspCalculation->l2035Fund;
        $l2040FundAmount = $tspCalculation->l2040Fund;
        $l2045FundAmount = $tspCalculation->l2045Fund;
        $l2050FundAmount = $tspCalculation->l2050Fund;
        $l2055FundAmount = $tspCalculation->l2055Fund;
        $l2060FundAmount = $tspCalculation->l2060Fund;
        $l2065FundAmount = $tspCalculation->l2065Fund;

        $tspCalculationTotalArray = [];
        $tspCalculationTotal = $tspCalculation->totalTSPCalculate;
        $tspCalculationPercentage = $fedCase->tSP->contribute_pp_percentage;
        $rothCalculationPercentage = $fedCase->tSP->contribute_tsp_pp_percentage;
        $totalContributionPercentage = $tspCalculationPercentage + $rothCalculationPercentage;
        $MatchingPercentage = $tspCalculation->matchingPercentage;

        $totalContribution = $tspCalculation->totalContribution;
        $totalMatching = $tspCalculation->totalMatching;
        $totalConMatch = $totalContribution + $totalMatching;

        $gFundCon = $fedCase->g_value;
        $fFundCon = $fedCase->f_value;
        $cFundCon = $fedCase->c_value;
        $sFundCon = $fedCase->s_value;
        $iFundCon = $fedCase->i_value;
        $lFundCon = $fedCase->l_income_value;
        $l2025FundCon = $fedCase->l_2025_value;
        $l2030FundCon = $fedCase->l_2030_value;
        $l2035FundCon = $fedCase->l_2035_value;
        $l2040FundCon = $fedCase->l_2040_value;
        $l2045FundCon = $fedCase->l_2045_value;
        $l2050FundCon = $fedCase->l_2050_value;
        $l2055FundCon = $fedCase->l_2055_value;
        $l2060FundCon = $fedCase->l_2060_value;
        $l2065FundCon = $fedCase->l_2065_value;

        $totalIncome = [];
        $totalExpenses = [];
        for ($age = $currentAge; $age <= 90; $age++) {
            $premiumAmount += $premiumAmount * 0.05; // Increment by 5%
            $premiums[] = $premiumAmount;
            $ageCount[] = $age;


            $srsArray[] = $srsAmount; //for SRS

            $ssArray[] = $ssAmount; // for SS
            $ssAmount += $ssAmount * 0.025;

            // FEGLI
            $fegliAmountTotal += $fegliAmountTotal * 0.03;
            $fegliAmountArray[] = $fegliAmountTotal;


            $dentalAndVisionValueArray[] = $dentalAndVisionValue;
            $dentalAndVisionValue += $dentalAndVisionValue * 0.05;

            $pensionArray[] = $pensionAmount;
            $pensionAmount += $pensionAmount * $annualColaIncrement;

            // Survivor benefit 
            $SBP = $request->sb;
            $SBP = preg_replace('/[\$,]/', '', $SBP);
            $SBP = intval($SBP);
            $SurvivorBenefitArray[] = $survivorBenefitAmount;
            if ($fedCase->employee_spouse == 'yes') {
                if ($fedCase->survior_benefit_fers != null) {
                    if ($fedCase->survior_benefit_fers == 50) {
                        $survivorBenefitAmount = $pensionAmount * 0.1;
                    } else if ($fedCase->survior_benefit_fers == 25) {
                        $survivorBenefitAmount = $pensionAmount * 0.05;
                    } else {
                        $survivorBenefitAmount = 0;
                    }
                } else if ($fedCase->survior_benefit_csrs != null) {
                    $totalLeavePension = $pensionAmount * $fedCase->survior_benefit_csrs / 100;
                    if ($totalLeavePension <= 3600) {
                        $survivorBenefitAmount = $totalLeavePension * 0.025;
                    } else {
                        $remainningLeavePension = $totalLeavePension - 3600;
                        $survivorBenefitFirst = 3600 * 0.025;
                        $survivorBenefitSecond = $remainningLeavePension * 0.1;
                        $survivorBenefitAmount = $survivorBenefitFirst + $survivorBenefitSecond;
                    }
                } else {
                    $survivorBenefitAmount = 0;
                }
            }

            // start tsp calculation
            $tspCalculationTotalArray[] = $tspCalculationTotal;

            $gFundAmount += $totalConMatch * $gFundCon;
            $gFundAmount += $gFundAmount * 0.025;

            $fFundAmount += $totalConMatch * $fFundCon;
            $fFundAmount += $fFundAmount * 0.015;

            $cFundAmount += $totalConMatch * $cFundCon;
            $cFundAmount += $cFundAmount * 0.13;

            $sFundAmount += $totalConMatch * $sFundCon;
            $sFundAmount += $sFundAmount * 0.08;

            $iFundAmount += $totalConMatch * $iFundCon;
            $iFundAmount += $iFundAmount * 0.05;

            $lFundAmount += $totalConMatch * $lFundCon;
            $lFundAmount += $lFundAmount * 0.04;

            $l2025FundAmount += $totalConMatch * $l2025FundCon;
            $l2025FundAmount += $l2025FundAmount * 0.04;

            $l2030FundAmount += $totalConMatch * $l2030FundCon;
            $l2030FundAmount += $l2030FundAmount * 0.07;

            $l2035FundAmount += $totalConMatch * $l2035FundCon;
            $l2035FundAmount += $l2035FundAmount * 0.07;

            $l2040FundAmount += $totalConMatch * $l2040FundCon;
            $l2040FundAmount += $l2040FundAmount * 0.08;

            $l2045FundAmount += $totalConMatch * $l2045FundCon;
            $l2045FundAmount += $l2045FundAmount * 0.08;

            $l2050FundAmount += $totalConMatch * $l2050FundCon;
            $l2050FundAmount += $l2050FundAmount * 0.08;

            $l2055FundAmount += $totalConMatch * $l2055FundCon;
            $l2055FundAmount += $l2055FundAmount * 0.1;

            $l2060FundAmount += $totalConMatch * $l2060FundCon;
            $l2060FundAmount += $l2060FundAmount * 0.1;

            $l2065FundAmount += $totalConMatch * $l2065FundCon;
            $l2065FundAmount += $l2065FundAmount * 0.1;


            $currentSalary  += $currentSalary * 0.02; // increment by 2%

            $totalContribution = $currentSalary * $totalContributionPercentage / 100;

            $totalMatching = $currentSalary * $MatchingPercentage;
            // dd($gFundAmount."f".$fFundAmount."c".$cFundAmount."s".$sFundAmount."i".$iFundAmount."L".$lFundAmount."2025".$l2025FundAmount."2030".$l2030FundAmount."2035".$l2035FundAmount."2040".$l2040FundAmount."2045".$l2045FundAmount."2050".$l2050FundAmount."2055".$l2055FundAmount."2060".$l2060FundAmount."2065".$l2065FundAmount."cont".$totalContribution."match".$totalMatching);

            $tspCalculationTotal = $gFundAmount + $fFundAmount + $cFundAmount + $sFundAmount + $iFundAmount + $lFundAmount + $l2025FundAmount + $l2030FundAmount + $l2035FundAmount + $l2040FundAmount + $l2045FundAmount + $l2050FundAmount + $l2055FundAmount + $l2060FundAmount + $l2065FundAmount + $totalContribution + $totalMatching;
            // end tsp calculation


            $totalIncomeArray[] = $pensionAmount + $srsAmount + $ssAmount + $tspCalculationTotal;
            $totalExpensesArray[] = $premiumAmount + $dentalAndVisionValue + $survivorBenefitAmount + $fegliAmountTotal;
        }
        $totalIncomeSum = array_sum($totalIncomeArray);
        $totalExpensesSum = array_sum($totalExpensesArray);
        $totalDiffIncomeExpense = $totalIncomeSum - $totalExpensesSum;

        // code for FLTCIP
        $ageFLTCIP = intval($currentAge / 5);
        $fltcipArray = [];
        $fltcipAmount = $fltcip->yearlyPremiumAmount;
        for ($i = 1; $i <= $ageFLTCIP; $i++) {
            $fltcipArray[] = $fltcipAmount;
            $fltcipAmount = $fltcipAmount * (85 / 100);
        }
        $totalFLTCIPSum = array_sum($fltcipArray);
        $data = [];
        $data['fedCase']                     = $fedCase;
        $data['highThree']                   = $highThree;
        $data['yosDollar']                   = $yosDollar;
        $data['retirement_type_date']        = $retirement_type_date;
        $data['retirement_type_age']         = $retirement_type_age;
        $data['pensionAmount']               = $pensionAmount;
        $data['fehbVP']                      = $fehbVP;
        $data['dentalAndVision']             = $dentalAndVision;
        $data['fegliAmountTotal']            = $fegliAmountTotal;
        $data['fegliAmountArray']            = $fegliAmountArray;
        $data['fegli']                       = $insuranceCost;
        $data['premiumAmount']               = $premiumAmount;
        $data['premiums']                    = $premiums;
        $data['ageCount']                    = $ageCount;
        $data['dentalAndVisionValueArray']   = $dentalAndVisionValueArray;
        $data['pensionArray']                = $pensionArray;
        $data['tspCalculation']              = $tspCalculation;
        $data['tspCalculationTotal']         = $tspCalculationTotal;
        $data['tspCalculationTotalArray']    = $tspCalculationTotalArray;
        $data['srsAmount']                   = $srsAmount;
        $data['srsArray']                    = $srsArray;
        $data['ssAmount']                    = $ssAmount;
        $data['ssArray']                     = $ssArray;
        $data['survivorBenefitAmount']       = $survivorBenefitAmount;
        $data['SBP']                         = $SBP;
        $data['SurvivorBenefitArray']        = $SurvivorBenefitArray;
        $data['totalIncomeArray']            = $totalIncomeArray;
        $data['totalExpensesArray']          = $totalExpensesArray;
        $data['totalDiffIncomeExpense']      = $totalDiffIncomeExpense;
        $data['totalFLTCIPSum']              = $totalFLTCIPSum;
        return view('admin.fed-case.sideBar', $data);
    }
}
