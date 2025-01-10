<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Mail\ShareMail;
use App\Models\AnnualLeavePayout;
use App\Models\ChMessage;
use App\Models\DentalAndVision;
use App\Models\FedCase;
use App\Models\FEHBVP;
use App\Models\FLTCIP;
use App\Models\HighThree;
use App\Models\InsuranceCost;
use App\Models\InsurancePlan;
use App\Models\MST;
use App\Models\OFST;
use App\Models\PartTimePension;
use App\Models\Pension;
use App\Models\Share;
use App\Models\SickLeavesConversion;
use App\Models\SRS;
use App\Models\State;
use App\Models\TSP;
use App\Models\User;
use App\Models\YosDollar;
use App\Models\YosE;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ShareController extends Controller
{
    public function processShare()
    {
        return view('admin.share.process-share');
    }

    public function share(Request $request)
    {
        // dd($request->all());
        $loginUser = Auth::user();
        $user = User::where('email', $request->email)->first();
        if(!empty($user))
        {
            $userRole = $user->role;
        }
        if (!$user) {
            return redirect()->route('process.share')->with('error', ' Sorry, the user not found! or Please check the user type.');
        }
        $alreadyShare = Share::where('user_id', $loginUser->id)
            ->where('share_id', $user->id)
            ->where('share_role', $request->role)
            ->first();


        if ($alreadyShare) {
            return redirect()->route('process.share')->with('error', 'Sorry, user already has access! Please check in the Shared Users section.');
        }
        $alreadyShareExit = Share::where('share_id', $loginUser->id)
            ->where('user_id', $user->id)
            ->where('share_role', $request->role)
            ->first();

        if ($alreadyShareExit) {
            return redirect()->route('process.share')->with('error', 'Sorry, user already has access! Please check in the Shared Users section.');
        }

        if (!$user) {

            return redirect()->route('process.share')->with('error', ' Sorry, the user not found! or Please check the user type.');
        } elseif ($user) {
            // $userRole = $user->hasRole(['back office', 'support']);
            if (($loginUser->role == "office" && $request->role == "office") || ($loginUser->role == "admin" && $request->role == "admin")) {
                $userRole = $user->role;
            }


            if (($loginUser->role == "agency" && $request->role == "office") || ($loginUser->role == "personal" && $request->role == "office")) {
                $userRole = $user->role;
            }

            if (($loginUser->role == "agency" && $request->role == "admin") || ($loginUser->role == "personal" && $request->role == "admin")) {
                $userRole = $user->role;
            }
            // if ($loginUser->roles->first() == "back office" || $loginUser->roles->first() == "support") {
            //     $userRole = $user->hasRole(['agency', 'personal']);
            // } elseif ($loginUser->roles->first() == "agency" || $loginUser->roles->first() == "personal") {
            //     $userRole = $user->hasRole(['back office', 'support']);
            // }
            if (!$userRole) {
                return redirect()->route('process.share')->with('error', ' Sorry, the user not found! or Please check the user type.');
            }
            else if(($user->role == "office" && $request->role == "admin") || ($user->role == "admin" && $request->role == "office"))
            {
                return redirect()->route('process.share')->with('error', " Sorry, Access haven't granted please check the user type.");
            }
            else {
                // when admin share access to sub admin
                if($loginUser->role == 'agency' || $loginUser->role == 'personal' )
                {
                    Share::create([
                        'user_id' => $user->id,
                        'share_id' => $loginUser->id,
                        'share_role' => $request->role,
                        'status' => in_array($loginUser->role, ["office", "admin"]) ? 'inactive' : 'active',
    
                    ]);
                    // for default message
                    ChMessage::create([
                        'from_id' => $loginUser->id,
                        'to_id'   => $user->id,
                        'body'    => 'You are now connected to each other',
    
                    ]);
                }
                // when sub admin want to access
                else
                {
                    Share::create([
                        'user_id' => $loginUser->id,
                        'share_id' => $user->id,
                        'share_role' => $request->role,
                        'status' => in_array($loginUser->role, ["office", "admin"]) ? 'inactive' : 'active',
    
                    ]);
                    // for default message
                    ChMessage::create([
                        'from_id' => $loginUser->id,
                        'to_id'   => $user->id,
                        'body'    => 'You are now connected to each other',
    
                    ]);
                }
                
                $details = [
                    'loginUser' => $loginUser,
                    'role' => $loginUser->role,
                    'requestRole' => $request->role
                ];
                Mail::to($user->email)->send(new ShareMail($details));
                $email = $request->email;
                // $maskedEmail = str_repeat('*', strlen($email) - 14) . substr($email, -14);
                $maskedEmail = $email;
                $alertMessage = [
                    'role' => ($request->role == 'admin') ?  'Administrative Support access has been requested successfully from ' . $maskedEmail : 'BOS access has been requested successfully from ' . $maskedEmail,
                ];
                if ($loginUser->role == 'agency' || $loginUser->role == 'personal') {
                    $alertMessage = [
                        'role' => ($request->role == 'admin') ?  'Administrative Support access has been granted successfully to ' . $maskedEmail : 'BOS access has been granted successfully to ' . $maskedEmail,
                    ];
                }
                return redirect()->route('process.share')->with('alertMessage', $alertMessage)->with('success', $alertMessage['role']);
            }
        }
    }

    public function shareList()
    {
        $loggedInUser = Auth::user();
        $shares = Share::with('shareUsers')->where("share_id", $loggedInUser->id)->get();
        return view("admin.share.list", compact('shares'));
    }

    public function shareDelete($id)
    {
        $share = Share::find($id);
        
        if (!$share) {
            // Handle case where the user with the given ID is not found
            return response()->json([
                'status'  => false,
                'message' => 'Data not found'
            ]);
        }
        $share->delete();
        // Redirect to the index page or wherever you want
        return response()->json([
            'status'  => true,
            'message' => 'Data Deleted Successfully'
        ]);
    }

    public function statusChange($id)
    {
        $share = Share::find($id);
        $newStatus = $share->status == 'active' ? 'inactive' : 'active';
        $share->status = $newStatus;
        $share->save();
        return redirect()->route('share.list')->with('success', 'Status change successfully');
    }

    public function shareAgencyList()
    {
        $loggedInUser = Auth::user();
        $shareAgency = Share::with('users')->where("user_id", $loggedInUser->id)->where("status", "active")->get();
        // $shares = Share::with('shareUsers')->where("share_id", $loggedInUser->id)->where("status", "active")->get();
        // dd($shareAgency);
        return view("admin.share.agency-list", compact('shareAgency'));
    }

    public function shareCaseList(Request $request, $userId, $shareId)
    {
        $user = Auth::user();
        $userId = $user->id;
        $userData = Share::where('user_id', $userId)->where('share_id', $shareId)->first();
        $userId = $userData->user_id;
        $shareUserId = $userData->share_id;
        $shareUser = User::where('id', $shareUserId)->first();
        $cases = FedCase::where("user_id", $shareUserId)->get();
        $count = $cases->count();
        return view('admin.share.case-list', compact('cases', 'userId', 'count', 'shareUser'));
    }
    public function shareCaseAdd(Request $request)
    {
        $userId =  $request->id;
        $shareId = $request->shareUserId;
        $shareData = Share::where('user_id', $userId)->where('share_id',$shareId)->first();
        $states = State::orderBy('name', 'asc')->get();
        return view('admin.share.case-create', compact('userId', 'shareId', 'states'));
    }

    public function shareCaseStore(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'dob'  => 'required'
        ]);
        if($validator->passes()){
            $user = Auth::user();

            // save data in this table before this(OTHER FEDERAL SERVICE TIME) section 
            $case = FedCase::create([
                'user_id'                           => $request->id,
                'share_user_id'                     => $user->id,
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
                'employee_spouse_status'            => $request->employee_spouse_status,
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
                'insurance_coverage_value'              => $request->insurance_coverage_value,
                'option_b_value'                        => $request->option_b_value,
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

            // create Yos

            // sick leave hour 
            if($request->current_leave_option == 'yes')
            {
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
            }
            else
            {
                $formattedSickLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // military service date difference calculate in days
            if($request->military_service == 'yes')
            {
                $military_service_date_1 = Carbon::parse($request->military_service_date_1);
                $military_service_date_2 = Carbon::parse($request->military_service_date_2);
                $diff = $military_service_date_1->diff($military_service_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceDate = "0 Y, 0 M, 0 D";
            }

            // military service active duty date difference calculate in days
            if($request->military_service_active_duty  == 'yes')
            {
                $military_service_active_duty_date_1 = Carbon::parse($request->military_service_active_duty_date_1);
                $military_service_active_duty_date_2 = Carbon::parse($request->military_service_active_duty_date_2);
                $diff = $military_service_active_duty_date_1->diff($military_service_active_duty_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceActiveDutyDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceActiveDutyDate = "0 Y, 0 M, 0 D";
            }

            // military service reserve date difference calculate in days
            if($request->military_service_reserve  == 'yes')
            {
                $military_service_reserve_date_1 = Carbon::parse($request->military_service_reserve_date_1);
                $military_service_reserve_date_2 = Carbon::parse($request->military_service_reserve_date_2);
                $diff = $military_service_reserve_date_1->diff($military_service_reserve_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceReserveDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceReserveDate = "0 Y, 0 M, 0 D";
            }

            // total military time calculate
            $totalMilitaryServiceTime = [$militaryServiceDate , $militaryServiceActiveDutyDate , $militaryServiceReserveDate];
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
            $finalValue = [$formattedSickLeaveDuration , $totalMilitaryDuration , $request->yosDollar];
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
                'fed_case_id' => $case->id,
                'age'         => $request->yosDollar,
                'value'       => $finalTotalYears
            ]);


            // calculate all yos(E) data
            $finalValue = [$totalMilitaryDuration , $request->yosDollar];
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
            $current_salary = intval(preg_replace('/\D/', '', $request->salary_1));
            if($request->input('income_employee_option') == 'yes')
            {
                $retirementDate = $request->retirement_type_date; // Format: 'YYYY-MM-DD'
                // Convert the retirement date from the request to a Carbon instance
                $retirementDate = Carbon::parse($retirementDate);
                // Get today's date
                $today = Carbon::now();
                // Calculate the difference in years
                $yearsRemaining = intval($today->diffInYears($retirementDate));
                $salaries = [];
                for ($i = 0; $i <= $yearsRemaining; $i++) {
                    $current_salary += $current_salary * 0.03;
                    $salaries[] = $current_salary;
                }
                rsort($salaries);
                // Get the top 3 values
                $topThreeSalaries = array_slice($salaries, 0, 3);
                // Calculate the average of the top 3 salaries
                $average_salary = array_sum($topThreeSalaries) / count($topThreeSalaries);

            }else{
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
            if($request->input('income_employee_option') == 'yes')
            {
                $payout = $current_salary * $request->annual_leave_hours / 2080 ;
            }
            else
            {
                $salaries = [
                    $request->salary_2,
                    $request->salary_3,
                    $request->salary_4
                ];
                // Use the max function to find the highest salary
                $highestSalary = max($salaries);
                $highestSalary = intval(preg_replace('/\D/', '', $highestSalary));
                $payout = $highestSalary * $request->annual_leave_hours / 2080 ;
            }
            $annualLeavePayout = AnnualLeavePayout::create([
                'fed_case_id'  => $case->id,
                'payout'       => $payout,
            ]);
    
            // pension section
            $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
            if($case->retirement_system)
            {
                if($case->retirement_system == 'csrs')
                {
                    if($case->employee_type == 'regular' || $case->employee_type == 'postal')
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        $pension = trim($numericPart) * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 5 * 0.015) + 
                                    ($highThreeValue->value * 5 * 0.0175) + 
                                    ($highThreeValue->value * ($numericPart - 10) * 0.02);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                        
                    }
                    else if(in_array($case->employee_type, $types))
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        $remainingAge = $yosDollar->value - 20;
                        $pension = trim($numericPart) * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 0.025 * 20) + 
                                    ($highThreeValue->value * 0.02 * $remainingAge);
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                        
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $case->id]);
                    }
                }
                else if($case->retirement_system == 'csrs_offset')
                {
                    // $date = $request->input('retirement_system_csrs_offset');
                    // $carbonDate = Carbon::parse($date);
                    // $year = $carbonDate->year;
        
                    // $retirmentDate = $request->input('retirement_type_date');
                    // $retirmentDate = Carbon::parse($retirmentDate);
                    // $retirmentYear = $retirmentDate->year;
                    // dd($retirmentYear - $year);
                    if($case->employee_type == 'regular' || $case->employee_type == 'postal')
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        $pension = trim($numericPart) * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 5 * 0.015) + 
                                    ($highThreeValue->value * 5 * 0.0175) + 
                                    ($highThreeValue->value * ($numericPart - 10) * 0.02);
        
                        $pensionMonthly = $pension / 12;
        
                        $retirement_type_date = $case->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;
        
                        $retirement_system_csrs_offset = $case->retirement_system_csrs_offset;
                        $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
        
                        $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        $csrsPension = $case->amount_1 * $csrfOffsetYears / 40;
                        $pension = $pension - $csrsPension;
                        $pension = $pension * 12;
        
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    }
                    else if(in_array($case->employee_type, $types))
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        $pension = trim($numericPart) * $highThreeValue->value * 0.01;
                        $pension = ($highThreeValue->value * 5 * 0.015) + 
                                    ($highThreeValue->value * 5 * 0.0175) + 
                                    ($highThreeValue->value * ($numericPart - 10) * 0.02);
        
                        $pensionMonthly = $pension / 12;
        
                        $retirement_type_date = $case->retirement_type_date;
                        $retirement_type_date = Carbon::parse($retirement_type_date);
                        $retirmentTypeDateYear = $retirement_type_date->year;
        
                        $retirement_system_csrs_offset = $case->retirement_system_csrs_offset;
                        $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                        $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
        
                        $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                        $csrsPension = $case->amount_1 * $csrfOffsetYears / 40;
                        $pension = $pensionMonthly - $csrsPension;
                        $pension = $pension * 12;
                        $pension = Pension::create([
                            'fed_case_id'    => $case->id,
                            'amount'         => $pension,
                        ]);
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $case->id]);
                    }
                }
                else if($case->retirement_system == 'fers' || $case->retirement_system == 'fers_rea' || $case->retirement_system == 'fers_frea')
                {
                    if($case->employee_type == 'regular' || $case->employee_type == 'postal')
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        
                        $yosDollarAge = $yosDollar->value;
                        if($numericPart >= 62 && $yosDollarAge >=20)
                        {
                            $numericPart = trim($numericPart);
                            $numericValue = floatval($numericPart);
                            $pension = $numericValue * $highThreeValue->value * 0.01;
                            $pension = Pension::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $pension,
                            ]);
                        }
                        else if($numericPart < 62 && $yosDollarAge <20)
                        {
                            $numericPart = trim($numericPart);
                            $numericValue = floatval($numericPart);
                            $pension = $numericValue * $highThreeValue->value * 0.011;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                        else
                        {
                            $pension = trim($numericPart) * $highThreeValue->value * 0.011;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                        
                    }
                    else if(in_array($case->employee_type, $types))
                    {
                        $retirement_type_age = $case->retirement_type_age;
                        $numericPart = explode('Y', $retirement_type_age)[0];
                        $yosDollarAge = $yosDollar->value;
                        if($numericPart >= 62 && $yosDollarAge >=20)
                        {
                            $numericPart = trim($numericPart);
                            $numericValue = floatval($numericPart);
                            $pension = $numericValue * $highThreeValue->value * 0.01;
                            $firstYearPension = trim($numericPart) * $highThreeValue->value * 0.017;
                            $pension = Pension::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $pension,
                                'first_year'     => $firstYearPension,
                            ]);
                        }
                        else if($numericPart < 62 && $yosDollarAge <20)
                        {
                            $numericPart = trim($numericPart);
                            $numericValue = floatval($numericPart);
                            $pension = $numericValue * $highThreeValue->value * 0.011;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                        else
                        {
                            $pension = trim($numericPart) * $highThreeValue->value * 0.011;
                            $pension = Pension::create([
                                'fed_case_id' => $case->id,
                                'amount'      => $pension,
                            ]);
                        }
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $case->id]);
                    }
                }
                else if($case->retirement_system == 'fers_transfer')
                {
                    if($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal')
                    {
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
        
                    }
                    
                    else if(in_array($case->employee_type, $types))
                    {
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
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $case->id]);
                    }
                }
                else
                {
                    Log::info('Employee type not in the specified types', ['fed_case_id' => $case->id]);
                }
            }

            // part time pension
            if($case->employee_work == 'yes')
            {
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
            if(!empty($insurancePlan->insurance))
            {
                if($insurancePlan->insurance == 'yes')
                {
                    if($insurancePlan->insurance_emloyee == 'yes' && $insurancePlan->insurance_retirement == 'yes')
                    {
                        $client_age = $case->age;
                        $client_age = explode('Y', $client_age)[0];
                        if($insurancePlan->insurance_coverage_value == 'basic_option')
                        {
                            $incrementRate = 0.03;
                            $currentSalary = $case->salary_1;
                            $currentYear = Carbon::now()->year;
                            $retirement_type_date = $case->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirementYear = $retirement_type_date->year;
                            $currentYear = intval($currentYear);
                            $retirementYear = intval($retirementYear);
                            $remainingYear = $retirementYear - $currentYear;
                            $salary = $currentSalary;
                            $salary = str_replace(',', '', $salary);
                            $salary = intval($salary);
                            for ($year = 1; $year <= $remainingYear; $year++) {
                                $salary *= (1 + $incrementRate);
                            }
                            $insuranceCost = $salary * 0.3467;
                            $insuranceCost = InsuranceCost::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $insuranceCost,
                            ]);
        
                        }
                        else if($insurancePlan->insurance_coverage_value == 'a_option')
                        {
                            $incrementRate = 0.03;
                            $currentSalary = $case->salary_1;
                            $currentYear = Carbon::now()->year;
                            $retirement_type_date = $case->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirementYear = $retirement_type_date->year;
                            $currentYear = intval($currentYear);
                            $retirementYear = intval($retirementYear);
                            $remainingYear = $retirementYear - $currentYear;
                            $salary = $currentSalary;
                            $salary = str_replace(',', '', $salary);
                            $salary = intval($salary);
                            $salary =  $salary + 2000;
                            for ($year = 1; $year <= $remainingYear; $year++) {
                                $salary *= (1 + $incrementRate);
                            }
                            if($client_age < 35)
                            {
                                $insuranceCost = $salary * 0.43;
                            }
                            else if($client_age >= 35 && $client_age <= 39)
                            {
                                $insuranceCost = $salary * 0.43;
                            }
                            else if($client_age >= 40 && $client_age <= 44)
                            {
                                $insuranceCost = $salary * 0.65;
                            }
                            else if($client_age >= 45 && $client_age <= 49)
                            {
                                $insuranceCost = $salary * 1.30;
                            }
                            else if($client_age >= 50 && $client_age <= 54)
                            {
                                $insuranceCost = $salary * 2.17;
                            }
                            else if($client_age >= 55 && $client_age <= 59)
                            {
                                $insuranceCost = $salary * 3.90;
                            }
                            else
                            {
                                $insuranceCost = $salary * 13.00;
                            }
                            $insuranceCost = InsuranceCost::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $insuranceCost,
                            ]);
                        }
                        else if($insurancePlan->insurance_coverage_value == 'b_option')
                        {
                            $incrementRate = 0.03;
                            $currentSalary = $case->salary_1;
                            $currentYear = Carbon::now()->year;
                            $retirement_type_date = $case->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirementYear = $retirement_type_date->year;
                            $currentYear = intval($currentYear);
                            $retirementYear = intval($retirementYear);
                            $remainingYear = $retirementYear - $currentYear;
                            $salary = $currentSalary;
                            
                            $salary = str_replace(',', '', $salary);
                            $salary = intval($salary);
                            
                            $annualPay = $salary * $insurancePlan->option_b_value;
                            $monthlyBasic = $annualPay / 1000;
        
                            if($client_age < 35)
                            {
                                $insuranceCost = $monthlyBasic * 0.043;
                            }
                            else if($client_age >= 35 && $client_age <= 39)
                            {
                                $insuranceCost = $monthlyBasic * 0.043;
                            }
                            else if($client_age >= 40 && $client_age <= 44)
                            {
                                $insuranceCost = $monthlyBasic * 0.065;
                            }
                            else if($client_age >= 45 && $client_age <= 49)
                            {
                                $insuranceCost = $monthlyBasic * 0.130;
                            }
                            else if($client_age >= 50 && $client_age <= 54)
                            {
                                $insuranceCost = $monthlyBasic * 0.217;
                            }
                            else if($client_age >= 55 && $client_age <= 59)
                            {
                                $insuranceCost = $monthlyBasic * 0.390;
                            }
                            else if($client_age >= 60 && $client_age <= 64)
                            {
                                $insuranceCost = $monthlyBasic * 0.867;
                            }
                            else if($client_age >= 65 && $client_age <= 69)
                            {
                                $insuranceCost = $monthlyBasic * 1.040;
                            }
                            else if($client_age >= 70 && $client_age <= 74)
                            {
                                $insuranceCost = $monthlyBasic * 1.863;
                            }
                            else if($client_age >= 75 && $client_age <= 79)
                            {
                                $insuranceCost = $monthlyBasic * 3.900;
                            }
                            else
                            {
                                $insuranceCost = $monthlyBasic * 6.240;
                            }
                            $insuranceCost = InsuranceCost::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $insuranceCost,
                            ]);
                        }
                        else if($insurancePlan->insurance_coverage_value == 'c_option')
                        {
                            $incrementRate = 0.03;
                            $currentSalary = $case->salary_1;
                            $currentYear = Carbon::now()->year;
                            $retirement_type_date = $case->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirementYear = $retirement_type_date->year;
                            $currentYear = intval($currentYear);
                            $retirementYear = intval($retirementYear);
                            $remainingYear = $retirementYear - $currentYear;
                            $salary = $currentSalary;
                            $salary = str_replace(',', '', $salary);
                            $salary = intval($salary);
                            $value = $insurancePlan->insurance_employee_coverage_c;
                            for ($year = 1; $year <= $remainingYear; $year++) {
                                $salary *= (1 + $incrementRate);
                            }
                            if($client_age < 35)
                            {
                                $insuranceCost = $value * 0.43;
                            }
                            else if($client_age >= 35 && $client_age <= 39)
                            {
                                $insuranceCost = $value * 0.52;
                            }
                            else if($client_age >= 40 && $client_age <= 44)
                            {
                                $insuranceCost = $value * 0.80;
                            }
                            else if($client_age >= 45 && $client_age <= 49)
                            {
                                $insuranceCost = $value * 1.15;
                            }
                            else if($client_age >= 50 && $client_age <= 54)
                            {
                                $insuranceCost = $value * 1.80;
                            }
                            else if($client_age >= 55 && $client_age <= 59)
                            {
                                $insuranceCost = $value * 2.88;
                            }
                            else if($client_age >= 60 && $client_age <= 64)
                            {
                                $insuranceCost = $value * 5.27;
                            }
                            else if($client_age >= 65 && $client_age <= 69)
                            {
                                $insuranceCost = $value * 6.13;
                            }
                            else if($client_age >= 70 && $client_age <= 74)
                            {
                                $insuranceCost = $value * 8.30;
                            }
                            else if($client_age >= 75 && $client_age <= 79)
                            {
                                $insuranceCost = $value * 12.48;
                            }
                            else
                            {
                                $insuranceCost = $value * 16.90;
                            }
                            $insuranceCost = InsuranceCost::create([
                                'fed_case_id'    => $case->id,
                                'amount'         => $insuranceCost,
                            ]);
                        }
                        else
                        {
                            Log::info('Select option not correct', ['fed_case_id' => $case->id]);
                        }
                    }
                    else
                    {
                        Log::info('Select option not correct', ['fed_case_id' => $case->id]);
                    }
                }
                else
                {
                    Log::info('Select option not correct', ['fed_case_id' => $case->id]);
                }
            }

            // SRS calculations
            if (preg_match('/(\d+)\s*Y/', $request->retirement_type_age, $matches)) {
                $ageYearAtRetirement = (int)$matches[1];
                if($ageYearAtRetirement < 62 )
                {
                    preg_match('/(\d+)\s*Y/', $request->yosDollar, $matches);
                    $yosYear = (int)$matches[1];
                    $srsAmount = $yosYear * $request->amount_1 / 40;
                    $srsAmount = SRS::create([
                        'fed_case_id'    => $case->id,
                        'amount'         => $srsAmount,
                    ]);
                }
            }

            // start calculations DENTAL AND VISION
            $todayDate = Carbon::now();
            $retirementDate = Carbon::parse($request->retirement_type_date);
            $yearsUntilRetirement = $todayDate->diff($retirementDate);
            $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
            if($request->dental == 'yes')
            {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $dentalPremiumAmount = $dentalPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $dentalPremiumAmount += $dentalPremiumAmount * (5 / 100);
                }
                $visionPremiumAmount = 0;
            }
            else if($request->vision == 'yes')
            {
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                $visionPremiumAmount = $visionPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $visionPremiumAmount += $visionPremiumAmount * (5 / 100);
                }
                $dentalPremiumAmount = 0;
            }
            else if(!empty($request->vision_total_cost))
            {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $dentalPremiumAmount = $dentalPremiumAmount * 26;
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                $visionPremiumAmount = $visionPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $dentalPremiumAmount += $dentalPremiumAmount * (5 / 100);
                    $visionPremiumAmount += $visionPremiumAmount * (5 / 100);
                }
            }
            else
            {
                $dentalPremiumAmount = 0;
                $visionPremiumAmount = 0;
            }

            $dentalAndVisionValue = DentalAndVision::create([
                'fed_case_id'         => $case->id,
                'dentalPremiumAmount' => $dentalPremiumAmount,
                'visionPremiumAmount' => $visionPremiumAmount,
            ]);
            // end calculations DENTAL AND VISION

            // start FEHB VARIOUS PLANS calculations
            if($request->coverage_retirement == 'yes')
            {
                $todayDate = Carbon::now();
                $retirementDate = Carbon::parse($request->retirement_type_date);
                $yearsUntilRetirement = $todayDate->diff($retirementDate);
                $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;

                $fehbPremiumAmount = str_replace(',', '', $request->premium);
                $fehbPremiumAmount = intval($fehbPremiumAmount);
                $fehbPremiumAmount = $fehbPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $fehbPremiumAmount += $fehbPremiumAmount * (5 / 100);
                }
            }
            else
            {
                $fehbPremiumAmount = 0;
            }
            $fehbVPValue = FEHBVP::create([
                'fed_case_id'         => $case->id,
                'fehbPremiumAmount' => $fehbPremiumAmount,
            ]); 
            // end FEHB VARIOUS PLANS calculations

            // start FLTCIP calculations 
            if($request->insurance_program == 'yes')
            {
                if($request->insurance_program_retirement == 'yes')
                {
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
                }
                else
                {
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
                'status' => true,
                'message' => 'Case added successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function shareCaseEdit($id)
    {
        $case = FedCase::find($id);
        $caseOFST = OFST::where('fed_Case_id', $case->id)->first();
        $caseMST = MST::where('fed_Case_id', $case->id)->first();
        $caseTSP = TSP::where('fed_Case_id', $case->id)->first();
        $caseInsurancePlan = InsurancePlan::where('fed_Case_id', $case->id)->first();
        $yosDollar = YosDollar::where('fed_Case_id', $case->id)->first();
        $id = $case->id;
        $shareData = Share::where('share_id', $case->user_id)->first();
        $userId = $shareData->user_id;
        $shareId = $shareData->share_id;
        $states = State::orderBy('name', 'asc')->get();
        return view('admin.share.case-edit', compact('case', 'id', 'shareId' , 'userId', 'yosDollar', 'caseOFST', 'caseMST', 'caseTSP', 'caseInsurancePlan', 'states'));
    }

    public function shareCaseUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'dob'  => 'required'
        ]);
        if($validator->passes()){
            $user = Auth::user();
            $fedCase = FedCase::where('id', $id)->first();
            $caseOFST = OFST::where('fed_Case_id', $fedCase->id)->first();
            $caseMST = MST::where('fed_Case_id', $fedCase->id)->first();
            $caseTSP = TSP::where('fed_Case_id', $fedCase->id)->first();
            $caseInsurancePlan = InsurancePlan::where('fed_Case_id', $fedCase->id)->first();
            $caseYosDollar = YosDollar::where('fed_Case_id', $fedCase->id)->first();
            $caseHighThree = HighThree::where('fed_Case_id', $fedCase->id)->first();
            $casePension = Pension::where('fed_Case_id', $fedCase->id)->first();
            $caseInsuranceCost = InsuranceCost::where('fed_Case_id', $fedCase->id)->first();
            $caseYosE = YosE::where('fed_Case_id', $fedCase->id)->first();
            $annualLeavePayout = AnnualLeavePayout::where('fed_Case_id', $fedCase->id)->first();
            $casepartTimePension = PartTimePension::where('fed_Case_id', $fedCase->id)->first();
            $caseSRS = SRS::where('fed_Case_id', $fedCase->id)->first();
            $caseDentalAndVision = DentalAndVision::where('fed_Case_id', $fedCase->id)->first();
            $caseFEHBVP = FEHBVP::where('fed_Case_id', $fedCase->id)->first();
            $caseFLTCIP = FLTCIP::where('fed_Case_id', $fedCase->id)->first();

            // update data in this table before this(OTHER FEDERAL SERVICE TIME) section
            $fedCase->update([
                'share_user_id'                     => $user->id,
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
                'employee_spouse_status'            => $request->employee_spouse_status,
                'employee_eligible'                 => $request->employee_eligible,
                'amount_1'                          => $request->amount_1,
                'amount_2'                          => $request->amount_2,
                'amount_3'                          => $request->amount_3,
            ]);

            // update data of OTHER FEDERAL SERVICE TIME section
            if($caseOFST)
            {
                $caseOFST->update([
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
                ]);
            }
            else
            {
                $caseOFST = OFST::create([
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
                ]);
            }
            
            // update data of MILITARY SERVICE TIME section
            if($caseMST)
            {
                $caseMST->update([
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
                ]);
            }
            else
            {
                $caseMST = MST::create([
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
                ]);
            }

            // update data of THRIFT SAVINGS PLAN section
            if($caseTSP)
            {
                $caseTSP->update([
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
                ]);
            }
            else
            {
                $caseTSP = TSP::create([
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
                ]);
            }

            // update data of INSURANCE PLAN section
            if($caseInsurancePlan)
            {
                $caseInsurancePlan->update([
                    'fed_case_id'                           => $fedCase->id,
                    'insurance'                             => $request->insurance,
                    'insurance_emloyee'                     => $request->insurance_emloyee,
                    'insurance_retirement'                  => $request->insurance_retirement,
                    'insurance_coverage'                    => $request->insurance_coverage,
                    'insurance_employee_dependent'          => $request->insurance_employee_dependent,
                    'insurance_coverage_value'              => $request->insurance_coverage_value,
                    'option_b_value'                        => $request->option_b_value,
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
            }
            else
            {
                $caseInsurancePlan = InsurancePlan::create([
                    'fed_case_id'                           => $fedCase->id,
                    'insurance'                             => $request->insurance,
                    'insurance_emloyee'                     => $request->insurance_emloyee,
                    'insurance_retirement'                  => $request->insurance_retirement,
                    'insurance_coverage'                    => $request->insurance_coverage,
                    'insurance_employee_dependent'          => $request->insurance_employee_dependent,
                    'insurance_coverage_value'              => $request->insurance_coverage_value,
                    'option_b_value'                        => $request->option_b_value,
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
            }

            // update Yos
            if($request->current_leave_option == 'yes')
            {
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
            }
            else
            {
                $formattedSickLeaveDuration = "0 Y, 0 M, 0 D";
            }

            // military service date difference calculate in days
            if($request->military_service == 'yes')
            {
                $military_service_date_1 = Carbon::parse($request->military_service_date_1);
                $military_service_date_2 = Carbon::parse($request->military_service_date_2);
                $diff = $military_service_date_1->diff($military_service_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceDate = "0 Y, 0 M, 0 D";
            }

            // military service active duty date difference calculate in days
            if($request->military_service_active_duty  == 'yes')
            {
                $military_service_active_duty_date_1 = Carbon::parse($request->military_service_active_duty_date_1);
                $military_service_active_duty_date_2 = Carbon::parse($request->military_service_active_duty_date_2);
                $diff = $military_service_active_duty_date_1->diff($military_service_active_duty_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceActiveDutyDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceActiveDutyDate = "0 Y, 0 M, 0 D";
            }

            // military service reserve date difference calculate in days
            if($request->military_service_reserve  == 'yes')
            {
                $military_service_reserve_date_1 = Carbon::parse($request->military_service_reserve_date_1);
                $military_service_reserve_date_2 = Carbon::parse($request->military_service_reserve_date_2);
                $diff = $military_service_reserve_date_1->diff($military_service_reserve_date_2);
                $years = $diff->y;
                $months = $diff->m;
                $days = $diff->d;
                $militaryServiceReserveDate = "{$years} Y, {$months} M, {$days} D";
            }
            else
            {
                $militaryServiceReserveDate = "0 Y, 0 M, 0 D";
            }

            // total military time calculate
            $totalMilitaryServiceTime = [$militaryServiceDate , $militaryServiceActiveDutyDate , $militaryServiceReserveDate];
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
            $finalValue = [$formattedSickLeaveDuration , $totalMilitaryDuration , $request->yosDollar];
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
            
            


            // calculate all yos(E) data
            $finalValueE = [$totalMilitaryDuration , $request->yosDollar];
            $totalFinalYearsE = 0;
            $totalFinalMonthsE = 0;
            $totalFinalDaysE = 0;
        
            foreach ($finalValueE as $value) {
                preg_match('/(\d+)\s*Y/', $value, $finalYearsE);
                preg_match('/(\d+)\s*M/', $value, $finalMonthsE);
                preg_match('/(\d+)\s*D/', $value, $finalDaysE);
        
                $totalFinalYearsE += isset($finalYearsE[1]) ? (int)$finalYearsE[1] : 0;
                $totalFinalMonthsE += isset($finalMonthsE[1]) ? (int)$finalMonthsE[1] : 0;
                $totalFinalDaysE += isset($finalDaysE[1]) ? (int)$finalDaysE[1] : 0;
            }
            while ($totalFinalDaysE >= 30) {
                $totalFinalDaysE -= 30;
                $totalFinalMonthsE += 1;
            }
            while ($totalFinalMonthsE >= 12) {
                $totalFinalMonthsE -= 12;
                $totalFinalYearsE += 1;
            }
            $finalPartsE = [];
            if ($totalFinalYearsE > 0) {
                $finalPartsE[] = "{$totalFinalYearsE} Y";
            }
            if ($totalFinalMonthsE > 0) {
                $finalPartsE[] = "{$totalFinalMonthsE} M";
            }
            if ($totalFinalDaysE > 0) {
                $finalPartsE[] = "{$totalFinalDaysE} D";
            }
        
            $finalDataE = implode(', ', $finalPartsE);

            $numberYearsE = 0;
            $numberMonthsE = 0;
            $numberDaysE = 0;

            // Parse the duration string
            if (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M,\s*(\d+)\s*D/', $finalDataE, $matchesE)) {
                $numberYearsE = (int)$matchesE[1];
                $numberMonthsE = (int)$matchesE[2];
                $numberDaysE = (int)$matchesE[3];
            } elseif (preg_match('/(\d+)\s*Y,\s*(\d+)\s*M/', $finalDataE, $matchesE)) {
                $numberYearsE = (int)$matchesE[1];
                $numberMonthsE = (int)$matchesE[2];
                $numberDaysE = 0;
            } elseif (preg_match('/(\d+)\s*Y/', $finalDataE, $matchesE)) {
                $numberYearsE = (int)$matchesE[1];
                $numberMonthsE = 0;
                $numberDaysE = 0;
            }
            $monthsToYearsE = $numberMonthsE / 12;
            $daysToYearsE = $numberDaysE / 365;
            $totalYearsE = $numberYearsE + $monthsToYearsE + $daysToYearsE;

            $finalTotalYearsE = round($totalYearsE, 2);
            if($caseYosDollar)
            {
                $caseYosDollar->update([
                    'fed_case_id' => $fedCase->id,
                    'age'         => $request->yosDollar,
                    'value'       => $finalTotalYears
                ]);
                // save data in yos(E)
                $caseYosE->update([
                    'fed_case_id' => $fedCase->id,
                    'age'         => $request->yosDollar,
                    'value'       => $finalTotalYearsE
                ]);
            }
            else
            {
                // save data in yos($)
                $yosDollar = YosDollar::create([
                    'fed_case_id' => $fedCase->id,
                    'age'         => $request->yosDollar,
                    'value'       => $finalTotalYears
                ]);
                // save data in yos(E)
                $yosE = YosE::create([
                    'fed_case_id' => $fedCase->id,
                    'age'         => $request->yosDollar,
                    'value'       => $finalTotalYearsE
                ]);
            }
            

            // update high three
            if($caseHighThree)
            {
                $current_salary = intval(preg_replace('/\D/', '', $request->salary_1));
                if($request->input('income_employee_option') == 'yes')
                {
                    $retirementDate = $request->retirement_type_date; // Format: 'YYYY-MM-DD'
                    // Convert the retirement date from the request to a Carbon instance
                    $retirementDate = Carbon::parse($retirementDate);
                    // Get today's date
                    $today = Carbon::now();
                    // Calculate the difference in years
                    $yearsRemaining = intval($today->diffInYears($retirementDate));
                    $salaries = [];
                    for ($i = 0; $i <= $yearsRemaining; $i++) {
                        $current_salary += $current_salary * 0.03;
                        $salaries[] = $current_salary;
                    }
                    rsort($salaries);
                    // dd($salaries);
                    // Get the top 3 values
                    $topThreeSalaries = array_slice($salaries, 0, 3);
                    // Calculate the average of the top 3 salaries
                    $average_salary = array_sum($topThreeSalaries) / count($topThreeSalaries);
                }else{
                    $salary_2 = intval(preg_replace('/\D/', '', $request->salary_2));
                    $salary_3 = intval(preg_replace('/\D/', '', $request->salary_3));
                    $salary_4 = intval(preg_replace('/\D/', '', $request->salary_4));
                    $average_salary = ($salary_2 + $salary_3 + $salary_4) / 3; 
                }
                $caseHighThree->update([
                    'fed_case_id' => $fedCase->id,
                    'value'       => $average_salary,
                ]);
            }
            else
            {
                $current_salary = intval(preg_replace('/\D/', '', $request->salary_1));
                if($request->input('income_employee_option') == 'yes')
                {
                    $retirementDate = $request->retirement_type_date; // Format: 'YYYY-MM-DD'
                    // Convert the retirement date from the request to a Carbon instance
                    $retirementDate = Carbon::parse($retirementDate);
                    // Get today's date
                    $today = Carbon::now();
                    // Calculate the difference in years
                    $yearsRemaining = intval($today->diffInYears($retirementDate));
                    $salaries = [];
                    for ($i = 0; $i <= $yearsRemaining; $i++) {
                        $current_salary += $current_salary * 0.03;
                        $salaries[] = $current_salary;
                    }
                    rsort($salaries);
                    // dd($salaries);
                    // Get the top 3 values
                    $topThreeSalaries = array_slice($salaries, 0, 3);
                    // Calculate the average of the top 3 salaries
                    $average_salary = array_sum($topThreeSalaries) / count($topThreeSalaries);
                }else{
                    $salary_2 = intval(preg_replace('/\D/', '', $request->salary_2));
                    $salary_3 = intval(preg_replace('/\D/', '', $request->salary_3));
                    $salary_4 = intval(preg_replace('/\D/', '', $request->salary_4));
                    $average_salary = ($salary_2 + $salary_3 + $salary_4) / 3; 
                }
                $caseHighThree = HighThree::create([
                    'fed_case_id' => $fedCase->id,
                    'value'       => $average_salary,
                ]);
            }

            // update Annual Leave Hours Payout calculate
            if($annualLeavePayout)
            {
                if($request->input('income_employee_option') == 'yes')
                {
                    $payout = $current_salary * $request->annual_leave_hours / 2080 ;
                }
                else
                {
                    $salaries = [
                        $request->salary_2,
                        $request->salary_3,
                        $request->salary_4
                    ];
                    // Use the max function to find the highest salary
                    $highestSalary = max($salaries);
                    $highestSalary = intval(preg_replace('/\D/', '', $highestSalary));
                    $payout = $highestSalary * $request->annual_leave_hours / 2080 ;
                }
                $annualLeavePayout->update([
                    'fed_case_id'  => $fedCase->id,
                    'payout'       => $payout,
                ]);
            }
            else
            {
                if($request->input('income_employee_option') == 'yes')
                {
                    $payout = $current_salary * $request->annual_leave_hours / 2080 ;
                }
                else
                {
                    $salaries = [
                        $request->salary_2,
                        $request->salary_3,
                        $request->salary_4
                    ];
                    // Use the max function to find the highest salary
                    $highestSalary = max($salaries);
                    $highestSalary = intval(preg_replace('/\D/', '', $highestSalary));
                    $payout = $highestSalary * $request->annual_leave_hours / 2080 ;
                }
                $annualLeavePayout = AnnualLeavePayout::create([
                    'fed_case_id'  => $fedCase->id,
                    'payout'       => $payout,
                ]);
            }

            // update pension
            if($casePension)
            {
                $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
                if(!empty($fedCase->retirement_system))
                {
                    if($fedCase->retirement_system == 'csrs')
                    {
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $casePension->update([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                            
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $remainingAge = $caseYosDollar->value - 20;
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 0.025 * 20) + 
                                        ($caseHighThree->value * 0.02 * $remainingAge);
                            $casePension->update([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                            
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'csrs_offset')
                    {
                        // $date = $request->input('retirement_system_csrs_offset');
                        // $carbonDate = Carbon::parse($date);
                        // $year = $carbonDate->year;
            
                        // $retirmentDate = $request->input('retirement_type_date');
                        // $retirmentDate = Carbon::parse($retirmentDate);
                        // $retirmentYear = $retirmentDate->year;
                        // dd($retirmentYear - $year);
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $pensionMonthly = $pension / 12;
            
                            $retirement_type_date = $fedCase->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirmentTypeDateYear = $retirement_type_date->year;
            
                            $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                            $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                            $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
            
                            $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                            $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                            $pension = $pension - $csrsPension;
                            $pension = $pension * 12;
            
            
                            $casePension->update([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $pensionMonthly = $pension / 12;
            
                            $retirement_type_date = $fedCase->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirmentTypeDateYear = $retirement_type_date->year;
            
                            $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                            $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                            $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
            
                            $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                            $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                            $pension = $pensionMonthly - $csrsPension;
                            $pension = $pension * 12;
            
                            $casePension->update([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea')
                    {
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $caseYosDollarAge = $caseYosDollar->value;
                            if($numericPart >= 62 && $caseYosDollarAge >=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                                $casePension->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $pension,
                                ]);
                            }
                            else if($numericPart <= 62 && $caseYosDollarAge <=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension->update([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                            else
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension->update([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                            
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $caseYosDollarAge = $caseYosDollar->value;
                            if($numericPart >= 62 && $caseYosDollarAge >=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                                $firstYearPension = trim($numericPart) * $caseHighThree->value * 0.017;
                                $casePension->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $pension,
                                    'first_year'     => $firstYearPension,
                                ]);
                            }
                            else if($numericPart <= 62 && $caseYosDollarAge <=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension->update([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                            else
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension->update([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'fers_transfer')
                    {
                        if($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal')
                        {
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
                            
                            $csrsPension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($csrsYear - 10) * 0.02);
            
                            $fersPension = $fersYear * $caseHighThree->value * 0.01;
            
                            $pension = $csrsPension + $fersPension;
                            $casePension->update([
                                'fed_case_id' => $fedCase->id,
                                'amount'      => $pension,
                            ]);
            
                        }
                        
                        else if(in_array($fedCase->employee_type, $types))
                        {
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
                            
                            $csrsPension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($csrsYear - 10) * 0.02);
            
                            $fersPension = $fersYear * $caseHighThree->value * 0.01;
            
                            $pension = $csrsPension + $fersPension;
                            $casePension->update([
                                'fed_case_id' => $fedCase->id,
                                'amount'      => $pension,
                            ]);
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                    }
                }
            }
            else
            {
                $types = ['leo', 'atc', 'fff', 'mrt', 'cbpo'];
                if(!empty($fedCase->retirement_system))
                {
                    if($fedCase->retirement_system == 'csrs')
                    {
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $casePension = Pension::create([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                            
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $remainingAge = $caseYosDollar->value - 20;
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 0.025 * 20) + 
                                        ($caseHighThree->value * 0.02 * $remainingAge);
                            $casePension = Pension::create([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                            
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'csrs_offset')
                    {
                        // $date = $request->input('retirement_system_csrs_offset');
                        // $carbonDate = Carbon::parse($date);
                        // $year = $carbonDate->year;
            
                        // $retirmentDate = $request->input('retirement_type_date');
                        // $retirmentDate = Carbon::parse($retirmentDate);
                        // $retirmentYear = $retirmentDate->year;
                        // dd($retirmentYear - $year);
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $pensionMonthly = $pension / 12;
            
                            $retirement_type_date = $fedCase->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirmentTypeDateYear = $retirement_type_date->year;
            
                            $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                            $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                            $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
            
                            $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                            $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                            $pension = $pension - $csrsPension;
                            $pension = $pension * 12;
            
            
                            $casePension = Pension::create([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                            $pension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($numericPart - 10) * 0.02);
            
                            $pensionMonthly = $pension / 12;
            
                            $retirement_type_date = $fedCase->retirement_type_date;
                            $retirement_type_date = Carbon::parse($retirement_type_date);
                            $retirmentTypeDateYear = $retirement_type_date->year;
            
                            $retirement_system_csrs_offset = $fedCase->retirement_system_csrs_offset;
                            $retirement_system_csrs_offset = Carbon::parse($retirement_system_csrs_offset);
                            $retirment_csrs_offset_year = $retirement_system_csrs_offset->year;
            
                            $csrfOffsetYears = $retirment_csrs_offset_year - $retirmentTypeDateYear;
                            $csrsPension = $fedCase->amount_1 * $csrfOffsetYears / 40;
                            $pension = $pensionMonthly - $csrsPension;
                            $pension = $pension * 12;
            
                            $casePension = Pension::create([
                                'fed_case_id'    => $fedCase->id,
                                'amount'         => $pension,
                            ]);
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'fers' || $fedCase->retirement_system == 'fers_rea' || $fedCase->retirement_system == 'fers_frea')
                    {
                        if($fedCase->employee_type == 'regular' || $fedCase->employee_type == 'postal')
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $caseYosDollarAge = $caseYosDollar->value;
                            if($numericPart >= 62 && $caseYosDollarAge >=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                                $pension = Pension::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $pension,
                                ]);
                            }
                            else if($numericPart <= 62 && $caseYosDollarAge <=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension = Pension::create([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                            
                        }
                        else if(in_array($fedCase->employee_type, $types))
                        {
                            $retirement_type_age = $fedCase->retirement_type_age;
                            $numericPart = explode('Y', $retirement_type_age)[0];
                            $caseYosDollarAge = $caseYosDollar->value;
                            if($numericPart >= 62 && $caseYosDollarAge >=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.01;
                                $firstYearPension = trim($numericPart) * $caseHighThree->value * 0.017;
                                $pension = Pension::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $pension,
                                    'first_year'     => $firstYearPension,
                                ]);
                            }
                            else if($numericPart <= 62 && $caseYosDollarAge <=20)
                            {
                                $pension = trim($numericPart) * $caseHighThree->value * 0.011;
                                $casePension = Pension::create([
                                    'fed_case_id' => $fedCase->id,
                                    'amount'      => $pension,
                                ]);
                            }
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else if($fedCase->retirement_system == 'fers_transfer')
                    {
                        if($request->input('employee_type') == 'regular' || $request->input('employee_type') == 'postal')
                        {
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
                            
                            $csrsPension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($csrsYear - 10) * 0.02);
            
                            $fersPension = $fersYear * $caseHighThree->value * 0.01;
            
                            $pension = $csrsPension + $fersPension;
                            $casePension = Pension::create([
                                'fed_case_id' => $fedCase->id,
                                'amount'      => $pension,
                            ]);
            
                        }
                        
                        else if(in_array($fedCase->employee_type, $types))
                        {
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
                            
                            $csrsPension = ($caseHighThree->value * 5 * 0.015) + 
                                        ($caseHighThree->value * 5 * 0.0175) + 
                                        ($caseHighThree->value * ($csrsYear - 10) * 0.02);
            
                            $fersPension = $fersYear * $caseHighThree->value * 0.01;
            
                            $pension = $csrsPension + $fersPension;
                            $casePension = Pension::create([
                                'fed_case_id' => $fedCase->id,
                                'amount'      => $pension,
                            ]);
                        }
                        else
                        {
                            Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else
                    {
                        Log::info('Employee type not in the specified types', ['fed_case_id' => $fedCase->id]);
                    }
                }
            }

            // update part time pension
            if($fedCase->employee_work == 'yes')
            {
                if($casepartTimePension)
                {
                    $empolyee_multiple_date = Carbon::parse($request->empolyee_multiple_date);
                    $empolyee_multiple_date_to = Carbon::parse($request->empolyee_multiple_date_to);
                    $employeePartTimeDays = $empolyee_multiple_date->diffInDays($empolyee_multiple_date_to);
                    $employeePartTimeDays = $employeePartTimeDays * $fedCase->empolyee_hours_work;
                    $partTimePercentage = $fedCase->empolyee_hours_work / 40;
                    $partTimePensionAmount = $partTimePercentage * $employeePartTimeDays;
                    $casepartTimePension->update([
                        'fed_case_id'  => $fedCase->id,
                        'amount'       => $partTimePensionAmount,
                    ]);
                }
                else
                {
                    $empolyee_multiple_date = Carbon::parse($request->empolyee_multiple_date);
                    $empolyee_multiple_date_to = Carbon::parse($request->empolyee_multiple_date_to);
                    $employeePartTimeDays = $empolyee_multiple_date->diffInDays($empolyee_multiple_date_to);
                    $employeePartTimeDays = $employeePartTimeDays * $fedCase->empolyee_hours_work;
                    $partTimePercentage = $fedCase->empolyee_hours_work / 40;
                    $partTimePensionAmount = $partTimePercentage * $employeePartTimeDays;
                    $partTimePension = PartTimePension::create([
                        'fed_case_id'  => $fedCase->id,
                        'amount'       => $partTimePensionAmount,
                    ]);
                }
            }

            // update insurance cost
            if($caseInsuranceCost)
            {
                if(!empty($caseInsurancePlan->insurance))
                {
                    if($caseInsurancePlan->insurance == 'yes')
                    {
                        if($caseInsurancePlan->insurance_emloyee == 'yes' && $caseInsurancePlan->insurance_retirement == 'yes')
                        {
                            $client_age = $fedCase->age;
                            $client_age = explode('Y', $client_age)[0];
                            if($caseInsurancePlan->insurance_coverage_value == 'basic_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                $insuranceCost = $salary * 0.3467;
            
                                $caseInsuranceCost->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
            
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'a_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $salary =  $salary + 2000;
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                if($client_age < 35)
                                {
                                    $insuranceCost = $salary * 0.43;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $salary * 0.43;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $salary * 0.65;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $salary * 1.30;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $salary * 2.17;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $salary * 3.90;
                                }
                                else
                                {
                                    $insuranceCost = $salary * 13.00;
                                }
                                $caseInsuranceCost->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'b_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $annualPay = $salary * $caseInsurancePlan->option_b_value;
                                $monthlyBasic = $annualPay / 1000;
            
                                if($client_age < 35)
                                {
                                    $insuranceCost = $monthlyBasic * 0.043;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $monthlyBasic * 0.043;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $monthlyBasic * 0.065;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $monthlyBasic * 0.130;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $monthlyBasic * 0.217;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $monthlyBasic * 0.390;
                                }
                                else if($client_age >= 60 && $client_age <= 64)
                                {
                                    $insuranceCost = $monthlyBasic * 0.867;
                                }
                                else if($client_age >= 65 && $client_age <= 69)
                                {
                                    $insuranceCost = $monthlyBasic * 1.040;
                                }
                                else if($client_age >= 70 && $client_age <= 74)
                                {
                                    $insuranceCost = $monthlyBasic * 1.863;
                                }
                                else if($client_age >= 75 && $client_age <= 79)
                                {
                                    $insuranceCost = $monthlyBasic * 3.900;
                                }
                                else
                                {
                                    $insuranceCost = $monthlyBasic * 6.240;
                                }
                                $caseInsuranceCost->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'c_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $value = $caseInsurancePlan->insurance_employee_coverage_c;
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                if($client_age < 35)
                                {
                                    $insuranceCost = $value * 0.43;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $value * 0.52;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $value * 0.80;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $value * 1.15;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $value * 1.80;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $value * 2.88;
                                }
                                else if($client_age >= 60 && $client_age <= 64)
                                {
                                    $insuranceCost = $value * 5.27;
                                }
                                else if($client_age >= 65 && $client_age <= 69)
                                {
                                    $insuranceCost = $value * 6.13;
                                }
                                else if($client_age >= 70 && $client_age <= 74)
                                {
                                    $insuranceCost = $value * 8.30;
                                }
                                else if($client_age >= 75 && $client_age <= 79)
                                {
                                    $insuranceCost = $value * 12.48;
                                }
                                else
                                {
                                    $insuranceCost = $value * 16.90;
                                }
                                $caseInsuranceCost->update([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else
                            {
                                Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                            }
                        }
                        else
                        {
                            Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else
                    {
                        Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                    }
                }
            }
            else
            {
                if(!empty($caseInsurancePlan->insurance))
                {
                    if($caseInsurancePlan->insurance == 'yes')
                    {
                        if($caseInsurancePlan->insurance_emloyee == 'yes' && $caseInsurancePlan->insurance_retirement == 'yes')
                        {
                            $client_age = $fedCase->age;
                            $client_age = explode('Y', $client_age)[0];
                            if($caseInsurancePlan->insurance_coverage_value == 'basic_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                $insuranceCost = $salary * 0.3467;
            
                                $caseInsuranceCost = InsuranceCost::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
            
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'a_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $salary =  $salary + 2000;
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                if($client_age < 35)
                                {
                                    $insuranceCost = $salary * 0.43;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $salary * 0.43;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $salary * 0.65;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $salary * 1.30;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $salary * 2.17;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $salary * 3.90;
                                }
                                else
                                {
                                    $insuranceCost = $salary * 13.00;
                                }
                                $caseInsuranceCost = InsuranceCost::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'b_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $annualPay = $salary * $caseInsurancePlan->option_b_value;
                                $monthlyBasic = $annualPay / 1000;
            
                                if($client_age < 35)
                                {
                                    $insuranceCost = $monthlyBasic * 0.043;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $monthlyBasic * 0.043;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $monthlyBasic * 0.065;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $monthlyBasic * 0.130;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $monthlyBasic * 0.217;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $monthlyBasic * 0.390;
                                }
                                else if($client_age >= 60 && $client_age <= 64)
                                {
                                    $insuranceCost = $monthlyBasic * 0.867;
                                }
                                else if($client_age >= 65 && $client_age <= 69)
                                {
                                    $insuranceCost = $monthlyBasic * 1.040;
                                }
                                else if($client_age >= 70 && $client_age <= 74)
                                {
                                    $insuranceCost = $monthlyBasic * 1.863;
                                }
                                else if($client_age >= 75 && $client_age <= 79)
                                {
                                    $insuranceCost = $monthlyBasic * 3.900;
                                }
                                else
                                {
                                    $insuranceCost = $monthlyBasic * 6.240;
                                }
                                $caseInsuranceCost = InsuranceCost::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else if($caseInsurancePlan->insurance_coverage_value == 'c_option')
                            {
                                $incrementRate = 0.03;
                                $currentSalary = $fedCase->salary_1;
                                $currentYear = Carbon::now()->year;
                                $retirement_type_date = $fedCase->retirement_type_date;
                                $retirement_type_date = Carbon::parse($retirement_type_date);
                                $retirementYear = $retirement_type_date->year;
                                $currentYear = intval($currentYear);
                                $retirementYear = intval($retirementYear);
                                $remainingYear = $retirementYear - $currentYear;
                                $salary = $currentSalary;
                                $salary = str_replace(',', '', $salary);
                                $salary = intval($salary);
                                $value = $caseInsurancePlan->insurance_employee_coverage_c;
                                for ($year = 1; $year <= $remainingYear; $year++) {
                                    $salary *= (1 + $incrementRate);
                                }
                                if($client_age < 35)
                                {
                                    $insuranceCost = $value * 0.43;
                                }
                                else if($client_age >= 35 && $client_age <= 39)
                                {
                                    $insuranceCost = $value * 0.52;
                                }
                                else if($client_age >= 40 && $client_age <= 44)
                                {
                                    $insuranceCost = $value * 0.80;
                                }
                                else if($client_age >= 45 && $client_age <= 49)
                                {
                                    $insuranceCost = $value * 1.15;
                                }
                                else if($client_age >= 50 && $client_age <= 54)
                                {
                                    $insuranceCost = $value * 1.80;
                                }
                                else if($client_age >= 55 && $client_age <= 59)
                                {
                                    $insuranceCost = $value * 2.88;
                                }
                                else if($client_age >= 60 && $client_age <= 64)
                                {
                                    $insuranceCost = $value * 5.27;
                                }
                                else if($client_age >= 65 && $client_age <= 69)
                                {
                                    $insuranceCost = $value * 6.13;
                                }
                                else if($client_age >= 70 && $client_age <= 74)
                                {
                                    $insuranceCost = $value * 8.30;
                                }
                                else if($client_age >= 75 && $client_age <= 79)
                                {
                                    $insuranceCost = $value * 12.48;
                                }
                                else
                                {
                                    $insuranceCost = $value * 16.90;
                                }
                                $caseInsuranceCost = InsuranceCost::create([
                                    'fed_case_id'    => $fedCase->id,
                                    'amount'         => $insuranceCost,
                                ]);
                            }
                            else
                            {
                                Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                            }
                        }
                        else
                        {
                            Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                        }
                    }
                    else
                    {
                        Log::info('Not selected correct insurance plan', ['fed_case_id' => $fedCase->id]);
                    }
                }
            }

            // update SRS
            if($caseSRS)
            {
                // SRS calculations
                if (preg_match('/(\d+)\s*Y/', $request->retirement_type_age, $matches)) {
                    $ageYearAtRetirement = (int)$matches[1];
                    if($ageYearAtRetirement < 62 )
                    {
                        preg_match('/(\d+)\s*Y/', $request->yosDollar, $matches);
                        $yosYear = (int)$matches[1];
                        $srsAmount = $yosYear * $request->amount_1 / 40;
                        $caseSRS->update([
                            'fed_case_id'    => $fedCase->id,
                            'amount'         => $srsAmount,
                        ]);
                    }
                }
            }
            else
            {
                // SRS calculations
                if (preg_match('/(\d+)\s*Y/', $request->retirement_type_age, $matches)) {
                    $ageYearAtRetirement = (int)$matches[1];
                    if($ageYearAtRetirement < 62 )
                    {
                        preg_match('/(\d+)\s*Y/', $request->yosDollar, $matches);
                        $yosYear = (int)$matches[1];
                        $srsAmount = $yosYear * $request->amount_1 / 40;
                        $srsAmount = SRS::create([
                            'fed_case_id'    => $fedCase->id,
                            'amount'         => $srsAmount,
                        ]);
                    }
                }
            }

            // start calculations DENTAL AND VISION
            $todayDate = Carbon::now();
            $retirementDate = Carbon::parse($request->retirement_type_date);
            $yearsUntilRetirement = $todayDate->diff($retirementDate);
            $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;
            if($request->dental == 'yes')
            {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $dentalPremiumAmount = $dentalPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $dentalPremiumAmount += $dentalPremiumAmount * (5 / 100);
                }
                $visionPremiumAmount = 0;
            }
            else if($request->vision == 'yes')
            {
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                $visionPremiumAmount = $visionPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $visionPremiumAmount += $visionPremiumAmount * (5 / 100);
                }
                $dentalPremiumAmount = 0;
            }
            else if(!empty($request->vision_total_cost))
            {
                $dentalPremiumAmount = str_replace(',', '', $request->dental_premium);
                $dentalPremiumAmount = intval($dentalPremiumAmount);
                $dentalPremiumAmount = $dentalPremiumAmount * 26;
                $visionPremiumAmount = str_replace(',', '', $request->vision_premium);
                $visionPremiumAmount = intval($visionPremiumAmount);
                $visionPremiumAmount = $visionPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $dentalPremiumAmount += $dentalPremiumAmount * (5 / 100);
                    $visionPremiumAmount += $visionPremiumAmount * (5 / 100);
                }
            }
            else
            {
                $dentalPremiumAmount = 0;
                $visionPremiumAmount = 0;
            }

            if($caseDentalAndVision)
            {
                $caseDentalAndVision->update([
                    'fed_case_id'         => $fedCase->id,
                    'dentalPremiumAmount' => $dentalPremiumAmount,
                    'visionPremiumAmount' => $visionPremiumAmount,
                ]);
            }
            else
            {
                $dentalAndVisionValue = DentalAndVision::create([
                    'fed_case_id'         => $fedCase->id,
                    'dentalPremiumAmount' => $dentalPremiumAmount,
                    'visionPremiumAmount' => $visionPremiumAmount,
                ]);
            }
            // end calculations DENTAL AND VISION

            // start FEHB VARIOUS PLANS calculations
            if($request->coverage_retirement == 'yes')
            {
                $todayDate = Carbon::now();
                $retirementDate = Carbon::parse($request->retirement_type_date);
                $yearsUntilRetirement = $todayDate->diff($retirementDate);
                $yearsUntilRetirementCalculate = $yearsUntilRetirement->y;

                $fehbPremiumAmount = str_replace(',', '', $request->premium);
                $fehbPremiumAmount = intval($fehbPremiumAmount);
                $fehbPremiumAmount = $fehbPremiumAmount * 26;
                for ($i = 1; $i <= $yearsUntilRetirementCalculate; $i++) {
                    $fehbPremiumAmount += $fehbPremiumAmount * (5 / 100);
                }
            }
            else
            {
                $fehbPremiumAmount = 0;
            }
            if($caseFEHBVP)
            {
                $caseFEHBVP->update([
                    'fed_case_id'         => $fedCase->id,
                    'fehbPremiumAmount' => $fehbPremiumAmount,
                ]);
            }
            else
            {
                $fehbVPValue = FEHBVP::create([
                    'fed_case_id'         => $fedCase->id,
                    'fehbPremiumAmount' => $fehbPremiumAmount,
                ]);
            }
            // end FEHB VARIOUS PLANS calculations

            // start FLTCIP calculations 
            if($request->insurance_program == 'yes')
            {
                if($request->insurance_program_retirement == 'yes')
                {
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
                }
                else
                {
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
                if($caseFLTCIP)
                {
                    $caseFLTCIP->update([
                        'fed_case_id'                    => $fedCase->id,
                        'yearlyPremiumAmount'            => $yearlyPremiumAmount,
                        'insurancePurchasePremiumAmount' => $insurancePurchasePremiumAmount,
                    ]);
                }
                else
                {
                    $fltcipValue = FLTCIP::create([
                        'fed_case_id'                    => $fedCase->id,
                        'yearlyPremiumAmount'            => $yearlyPremiumAmount,
                        'insurancePurchasePremiumAmount' => $insurancePurchasePremiumAmount,
                    ]);
                }
            }
            // end FLTCIP calculations



            return response()->json([
                'status'  => true,
                'message' => 'case updated successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
