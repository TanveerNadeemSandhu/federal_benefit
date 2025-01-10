@extends('layouts.app')
@section('title', 'Add case | Fed Benefit Anaylzer')

@section('content')
    <style>
        .dashboard-content {
            margin-top: 0 !important;
        }

        .error {
            color: red;
            font-size: 12px;
        }

        .invalid {
            border-color: red;
        }
    </style>

    <head>
        <!-- ... other head elements ... -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <form name="fedForm" id="fedForm">
        @csrf
        <div class='row justify-content-between mb-4 printHeader'>
            <div class="col-sm-3 col">
                <div class="position-relative d-flex align-items-center ">
                    <a href="{{ route('fed-case.index') }}">
                        <img class="group-icon me-2" alt="" src="{{ asset('images/accountagency/group-1442.svg')}}">
                    </a>
                    <select class="form-select showStatus text-dark fs-6" aria-label="Default select example" name="status"
                        id="status">
                        <option selected value="New">New</option>
                        <option value="For bos review">For bos review</option>
                        <option value="Need information">Need information</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4 col actionButton text-center">
                <button onclick="window.print()"><img src="{{ asset('images/dashboard/black3.svg')}}" /> Print</button>
                <button><img src="{{ asset('images/dashboard/vector.svg')}}" />Present</button>
            </div>
            <div class="col-sm-3 col text-end">
                <!-- <span class="position-relative">
                        <img class="noti-icon" alt="" src="/images/accountagency/notification-1@2x.png">
                        <span class="noti-number">1</span>
                    </span> -->
                <button class="case" onclick="saveCase()">Save</button>

            </div>
        </div>
        <!-- section one  -->
        <div class="row present">
            <div class="col-12">
                <table id="table-one" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            EMPLOYEE INFORMATION
                        </td>
                    </tr>

                    <tr class="dataTr">
                        @if(!empty($shareId))
                            <input type="hidden" name="id" value="{{$shareId}}" />
                        @endif
                        @if(!empty($userId))
                            <input type="hidden" name="userId" value="{{$userId}}" />
                        @endif
                        <td colspan="3">Name: <input type="text" name="name" id="name"
                                class="tableInput w-75">
                            <p></p>
                        </td>

                        <td>D.O.B: <input type="date" name="dob" id="dob" class="tableInput w-50"
                                onchange="calculateAge()">
                            <p></p>
                        </td>

                        <td>Age: <input type="text" name="age" id="age" class="tableInput w-50">
                        </td>

                    </tr>
                    <tr class="dataTr">
                        <td colspan="3">Spouse Name: <input type="text" name="spouse_name" id="spouse_name"
                                class="tableInput w-75">
                        </td>
                        <td>D.O.B: <input type="date" name="spouse_dob" id="spouse_dob" onchange="calculateSpouseAge()"
                                class="tableInput w-50">
                        </td>
                        <td>Age: <input type="text" name="spouse_age" id="spouse_age" class="tableInput w-50">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td>Address: <input type="text" name="address" id="address" class="tableInput w-75">
                        </td>
                        <td>City: <input type="text" name="city" id="city" class="tableInput w-50">
                        </td>
                        <td></td>
                        <td>
                            <select name="state" id="state" class="form-select w-50">
                                <option value="">select a state</option>
                                @foreach ($states as $state)
                                    <option value="{{$state->name}}">{{$state->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>Zip: <input type="text" name="zip" id="zip" class="tableInput w-50"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');">

                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td colspan="4">Email: <input type="email" name="email" id="email"
                                class="tableInput w-75" placeholder="xxxxx@xxxxxxxxx.xxx">
                            <span id="emailError" style="color:red; display:none;">Please enter a valid email
                                address.</span>
                        </td>

                        <td>Phone: <input type="text" name="phone" id="phone" placeholder="(555) 555-1234"
                                class="tableInput w-50">
                            <span id="phoneError" style="color:red; display:none;">Please enter a valid phone
                                number.</span>
                        </td>
                    </tr>

                </table>
            </div>

            <div class="col-sm-12 col-md-6">
                <table id="table-two retirement_system" class="table">
                    <tr>
                        <td colspan="4" class="tableTitle">
                            RETIREMENT SYSTEM
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="csrs" name="retirement_system" value="csrs"></td>
                        <td colspan="3"><label for="csrs">CSRS</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="csof" name="retirement_system" value="csrs_offset"></td>
                        <td colspan="3"><label for="csof">CSRS OFFSET</label>
                            <input type="date" name="retirement_system_csrs_offset" id="retirement_system_csrs_offset"
                                class="tableInput w-50">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="fers" name="retirement_system" value="fers"></td>
                        <td colspan="3"><label for="fers">FERS</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="ft" name="retirement_system" value="fers_transfer"></td>
                        <td colspan="3">
                            <label for="ft">FERS TRANSFER</label>
                            <input type="date" name="retirement_system_fers_transfer"
                                id="retirement_system_fers_transfer" class="tableInput w-50">

                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="fr" name="retirement_system" value="fers_rea"></td>
                        <td colspan="3">
                            <label for="fr">FERS RAE(Hired in the year of 2013)</label>
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="frh" name="retirement_system" value="fers_frea"></td>
                        <td colspan="3">
                            <label for="frh">FERS FRAE(Hired after 01-01-2014)</label>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12 col-md-6">
                <table id="table-three employee_type" class="table">
                    <tr>
                        <td colspan="4" class="tableTitle">
                            EMPLOYEE TYPE
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="rg" name="employee_type" value="regular"></td>
                        <td colspan="3"><label for="rg">REGULAR</label></td>
                        <td><input type="radio" id="pst" name="employee_type" value="postal"></td>
                        <td><label for="pst">POSTAL</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="leo" name="employee_type" value="leo"></td>
                        <td colspan="3"><label for="leo">LEO(Law Enforcement Offer)</label></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="htc" name="employee_type" value="atc"></td>
                        <td colspan="3"><label for="htc">ATC(Air Traffic Control)</label></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="fff" name="employee_type" value="fff"></td>
                        <td colspan="3">
                            <label for="fff">FF(Fire Fighter)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="mrt" name="employee_type" value="mrt"></td>
                        <td colspan="3">
                            <label for="mrt">MRT(Military Reserve Technician)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="cbpo" name="employee_type" value="cbpo"></td>
                        <td colspan="3">
                            <label for="cbpo">CBPO(Customs and Border Protection Officer)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                </table>
            </div>
            <div class="col-12">
                <table id="table-four" class="table">
                    <tr>
                        <td colspan="4" class="tableTitle">
                            PENSION AND ELIGIBILITY
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td style="vertical-align:middle" class="text-center">What are the employee service computation
                            dates(SCD’s)?</td>
                        <td colspan="3">
                            LSCD(Leave SCD-from paystub / LES) :<input type="text" name="lscd"
                                class="tableInput w-25"><br>
                            RSCD(Retirement SCD) :<input type="date" name="rscd" id="rscd"
                                class="tableInput w-50"><br>
                            <span id="dateError" class="error" style="display: none;">Joinning date must be greater than
                                Date of Birth date</span><br>
                            6C SCD(Date begin as LEO , FF , ATC , CBPO) :<input type="date" name="scd"
                                class="tableInput w-25">

                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-five" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            RETIREMENT TYPE
                        </td>
                    </tr>
                    <tr class="dataTr">

                        <td>

                            <input type="radio" id="fed" name="retirement_type"
                                value="fully_eligible_disability">
                            <label for="fed">Fully Eligible/Disability</label>
                        </td>

                        <td> <label for="date"> Date:</label> <input type="date" name="retirement_type_date"
                                id="retirement_type_date" onchange="calculateRetirementAge(); calculateYoSDollar()"
                                class="tableInput w-50">
                            <br><span id="retireDateError" class="error" style="display: none;">Retirement date must be
                                greater than Joinning date</span><br>

                        </td>
                        <input type="text" hidden name="yosDollar" id="yosDollar" class="tableInput w-50">
                        <td> <label for="age">At Age:</label> <input type="text" name="retirement_type_age"
                                id="retirement_type_age" class="tableInput w-50"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input value="first_eligible" type="radio" id="fem" name="retirement_type"> <label
                                for="fem">FIRST
                                Eligible (MRA+10
                                Retirement)</label></td>
                        <td colspan="1"> </td>
                        <td><input type="radio" value="voluntary" name="retirement_type" id="ve"> <label
                                for="ve">Voluntary (EARLY OUT)–Offer
                                Date:</label> <input type="date" name="retirement_type_voluntary"
                                class="tableInput w-25"></td>
                    </tr>

                </table>
            </div>
            <div class="col-12">
                <table id="table-six" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            CURRENT LEAVE HOURS
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td class="text-center">
                            Does the employee intend to use their sick leave hours towards their pension?
                        </td>
                        <td class="">
                            <div class="dropdown">

                                <select id="current_hours_option" name="current_hours_option" class="form-select">
                                    <option value="">select an option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>


                            </div>
                        </td>
                        <td> Annual Leave Hours</td>
                        <td class="">
                            <input type="number" name="annual_leave_hours" class="tableInput w-50">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td class="text-center">Will the employee be collecting a lump sum payout for their annual leave
                            hours?</td>
                        <td class="">
                            <div class="dropdown">

                                <select id="current_leave_option" name="current_leave_option" class="form-select">
                                    <option value="">select an option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>

                            </div>
                        </td>
                        <td>Sick Leave Hours</td>
                        <td class="">
                            <input type="number" name="sick_leave_hours" class="tableInput w-50">
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-seven" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            INCOME AND PENSION VALUES
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td class='d-flex align-items-center'>Will the employee’s High-3 be based on their CURRENT salary?
                            <div class="dropdown">
                                <select id="income_employee_option" name="income_employee_option" class="form-select">
                                    <option value="">select an option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </td>
                        <td> Current Salary Amount $ <input type="text" name="salary_1" id="salary_1"
                                class="tableInput w-25 numberInput" placeholder="Enter salary">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td>If NOT, what are the salary amounts to be used? $<input type="text" id="salary_2"
                                name="salary_2" class="tableInput w-25 numberInput" placeholder="0"> $<input
                                type="text" id="salary_3" name="salary_3" class="tableInput w-25 numberInput"
                                placeholder="0"></td>
                        <td>$<input type="text" name="salary_4" id="salary_4" class="tableInput w-50 numberInput"
                                placeholder="0">
                            <input type="text" hidden id="years_until_retirement" name="years_until_retirement"
                                class="form-control" readonly>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-eight" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            SURVIVOR BENEFIT
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td style="display: flex;align-items: baseline;">Is the employee’s Spouse and/or Former Spouse be
                            entitled to the Survivor Benefit?

                            <div class="dropdown">

                                <select id="employee_spouse" name="employee_spouse" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>

                            </div>

                            <div class="dropdown">


                                <select id="employee_spouse_status" name="employee_spouse_status" class="form-select">
                                    <option value="current spouse">Current spouse</option>
                                    <option value="former spouse">Former spouse</option>
                                </select>



                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td colspan="12">IF the FORMER Spouse is entitled to a portion of the Survivor Benefit, PLEASE
                            send a
                            Certified Copy of the Divorce Decree or COAP to OPM ASAP: <br><b>U.S. Office of Personnel
                                Management
                                Court Ordered Benefits Branch P.O. Box 17 Washington, DC 20044-0017</b></td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-nine" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            SOCIAL SECURITY INCOME AND SPECIAL RETIREMENT SUPPLEMENT (SRS)
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td style="display: flex;align-items: baseline;"> Is the employee ELIGIBLE for Social Security at
                            age
                            62?
                            <div class="dropdown">

                                <select id="employee_eligible" name="employee_eligible" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                    <option value="net yet">Net yet</option>

                                </select>

                            </div>
                        </td>
                        <td>Amount of SS at 62 $ <input type="text" name="amount_1"
                                class="tableInput w-50 numberInput"> </td>
                    </tr>
                    <tr class="dataTr">
                        <td>What age does the employee estimate begin SS Income? <input type="text" name="amount_2"
                                class="tableInput w-25 numberInput"></td>
                        <td>Amount to receive $ <input type="text" name="amount_3"
                                class="tableInput w-50 numberInput"></td>
                        <!-- <td> </td> -->
                    </tr>
                </table>
            </div>
            <hr class="solid my-3">
            <div class="col-12">
                <p class="my-2 text-center">OTHER FEDERAL SERVICE TIME <b>(Select Yes or No and fill in the rest of the
                        information)</b></p>
                <table id="table-ten" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            PART TIME SERVICE
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td class='d-flex align-items-center'>
                            Has the employee worked <b>Part-Time while contributing to CSRS or FERS</b>?
                            <div class="dropdown">

                                <select id="employee_work" name="employee_work" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>

                            </div>
                        </td>
                        <td colspan="3">Hours worked per week: <input type="text" name="empolyee_hours_work"
                                class="tableInput w-50"></td>
                    </tr>
                    <tr class="dataTr">
                        <td>IF the employee has multiple dates of part-time service please list ALL dates in NOTES section
                        </td>
                        <td class="text-center">Dates: <input type="date" name="empolyee_multiple_date"
                                class="tableInput w-50">
                        </td>
                        <td class="text-center">to </td>
                        <td><input type="date" name="empolyee_multiple_date_to" class="tableInput"> </td>

                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-eleven" class="table">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            NON-DEDUCTION SERVICE
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td style="display: flex;align-items: center;border:0;height:6rem">
                            <p>Has the employee <b>ever</b> worked in a <b>Non-Deduction
                                    SStatus (temporary, intern, student)</b> when CSRS
                                for FERS contributions were NOT taken from their pay?</p>
                            <div class="dropdown">

                                <select id="non_deduction_service" name="non_deduction_service" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>

                            </div>

                        </td>
                        <td> What were the dates of service? <input type="text" name="non_deduction_service_date"
                                class="tableInput w-25"> to <input type="text" name="non_deduction_service_date_2"
                                class="tableInput w-25"> <br>
                            <div class='d-flex align-items-center'>Was a deposit paid?
                                <div class="dropdown">

                                    <select id="non_deduction_service_deposit" name="non_deduction_service_deposit"
                                        class="form-select">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>

                                </div>
                            </div>
            </div>What was the deposit owed? $<input type="text" name="non_deduction_service_deposit_owned"
                class="tableInput w-25 numberInput">
            </td>
            </tr>
            </table>
        </div>
        <div class="col-12">
            <table id="table-tweleve" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        BREAK IN SERVICE / REFUNDED SERVICE
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style='display: flex;align-items: baseline;'> Did the employee ever have a Break in Service of
                        more than 3 days?
                        <div class="dropdown">

                            <select id="break_in_service" name="break_in_service" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>

                        </div>
                    </td>
                    <td> Dates of service PRIOR to the BREAK <input type="date" name="break_in_service_date_1"
                            class="tableInput w-25"> to <input type="date" name="break_in_service_date_2"
                            class="tableInput w-25"> Date employee RETURNED to federal service? <input type="date"
                            name="break_in_service_return_date" class="tableInput w-25"></td>
                </tr>
                <tr class="dataTr">

                    <td style="display: flex;align-items: baseline;"> Did the employee take a Refund of their retirement
                        contributions during their break of service?
                        <div class="dropdown">

                            <select id="break_in_service_refund" name="break_in_service_refund" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>

                        </div>
                    </td>
                    <td> Was a Redeposit made after returning to federal service? <input type="text"
                            name="break_in_service_redeposite" class="tableInput w-25"><br> Amount of Redeposit $ <input
                            type="text" name="break_in_service_amount_redeposite" class="tableInput w-25 numberInput">
                    </td>
                </tr>

            </table>
        </div>
        <hr class="solid my-3">
        <div class="col-12">
            <p class="my-2 text-center"><b>MILITARY SERVICE TIME </b>(Select Yes or No and fill in the rest of the
                information)</p>
            <table id="table-thrteen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        ACTIVE-DUTY / RESERVE / ACADEMY SERVICE
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style='display: flex;align-items: baseline;'>Has the employee ever had Military service?
                        <div class="dropdown">

                            <select id="military_service" name="military_service" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>

                        </div>
                    </td>
                    <td>
                        Dates of service? <input type="date" name="military_service_date_1" class="tableInput w-25">
                        to <input type="date" name="military_service_date_2" class="tableInput w-25">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style="display: flex;align-items: baseline;"><input type="checkbox" id="add"
                            class='me-1'> <label for="add"> Active-Duty - Deposit
                            paid?</label>
                        <div class="dropdown">

                            <select id="military_service_active_duty" name="military_service_active_duty"
                                class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>

                        </div>
                    </td>

                    <td>
                        Dates of service? <input type="date" name="military_service_active_duty_date_1"
                            class="tableInput w-25">
                        to <input type="date" name="military_service_active_duty_date_2" class="tableInput w-25">
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style="display: flex;align-items: baseline;"><input type="checkbox"
                            id="military_service_reserve_check" class='me-1'> <label for="add"> Reserve - Deposit
                            paid?</label>
                        <div class="dropdown">

                            <select id="military_service_reserve" name="military_service_reserve" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        Dates of service? <input type="date" name="military_service_reserve_date_1"
                            class="tableInput w-25"> to
                        <input type="date" name="military_service_reserve_date_2" class="tableInput w-25">
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style="display: flex;align-items: baseline;"> <input type="checkbox" id="add"
                            class='me-1'> <label for="add"> Academy - Deposit
                            paid?</label>
                        <div class="dropdown">

                            <select id="military_service_academy" name="military_service_academy" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        Amount of deposit $<input type="text" name="military_service_academy_amount"
                            class="tableInput w-50 numberInput">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style='display: flex;align-items: baseline;'>Did the employee Retire from Military service?
                        <div class="dropdown">

                            <select id="military_service_retire" name="military_service_retire" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                    </td>

                    <td>
                        Is the employee collecting VA pay?<input type="text" name="military_service_collecting"
                            class="tableInput w-50">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>
                        <input type="checkbox" id="ady" name="revse"> <label for="ady">Active-Duty</label>
                        <input type="checkbox" id="revse" name="military_service_reserves">
                        <label for="revse">Reserves</label>
                    </td>
                    <td>
                        Amount of VA pay $<input type="text" name="military_service_amount"
                            class="tableInput w-50 numberInput"> /month
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <textarea name="military_service_note" class="w-100 tableInput" cols="30" rows="10" placeholder="Note:"></textarea>
                    </td>
                </tr>

            </table>
        </div>
        <hr class="solid my-3">
        <div class="col-12">
            <p class="my-2 text-center"><b>THRIFT SAVINGS PLAN (TSP)</b></p>
            <table id="table-fourteen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        CONTRIBUTIONS / LOAN(S)
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>How much does the employee contribute to Traditional TSP? </td>
                    <td><input type="checkbox" id="none1" name="contribute" value="none"> <label
                            for="none1">None</label></td>
                    <td colspan="2">$<input type="text" id="contribute_pp" name="contribute_pp"
                            class="tableInput w-50 numberInput">/PP </td>
                    <td colspan="2"><input type="text" name="contribute_pp_percentage" id="contribute_pp_percentage" class="tableInput w-50">%
                        /PP </td>
                </tr>
                <tr class="dataTr">
                    <td>How much does the employee contribute to Roth TSP? </td>
                    <td><input type="checkbox" id="none2" name="contribute_tsp" value="none"> <label
                            for="none2">None</label>
                    </td>
                    <td colspan="2">$<input type="text" name="contribute_tsp_pp"
                            class="tableInput  w-50 numberInput">/PP </td>
                    <td colspan="2"><input type="text" name="contribute_tsp_pp_percentage"
                            class="tableInput  w-50">% /PP </td>
                </tr>
                <tr class="dataTr">
                    <td>How did the employee decide their contribution limit shown in box 20?</td>
                    <td colspan="4"><input type="text" name="contribute_limit" class="tableInput "></td>
                </tr>
            </table>
            <table id="table-fifteen" class="table">
                <tr class="dataTr">
                    <td width="50%" class=''>
                        <div class='d-flex align-items-baseline'>Does the employee have any TSP loan(s)? <div
                                class="dropdown">

                                <select id="contribute_tsp_loan" name="contribute_tsp_loan" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br><br>
                        <input type="checkbox" id="g" name="contribute_tsp_loan_gen" value="general"> <label
                            for="g">General</label>
                        <br>
                        <input type="checkbox" id="r" name="contribute_tsp_res" name="residential"> <label
                            for="r">Residential</label>
                    </td>
                    <td></td>
                    <td width="50%">
                        How much does the employee pay for their loan(s)?<input type="text" name="contribute_pay_pp"
                            class="tableInput" style="width: 10%;">/PP $<input type="text"
                            name="contribute_pay_pp_value" class="tableInput numberInput" style="width: 10%;">/PP <br>
                        How much is owed for each loan? $<input type="text" name="contribute_own_loan"
                            class="tableInput w-25 numberInput">
                        $<input type="text" name="contribute_own_loan_2" class="tableInput w-25 numberInput"><br>
                        What is the estimated pay off dates(s)?<input type="date" name="contribute_pay_date"
                            class="tableInput w-25">
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-12 col-md-6">
            <table id="table-sixteen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        IN RETIREMENT
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>When does the employee need access to the TSP? <br>
                        <input type="checkbox" name="employee_not_sure" value="not sure" id="ns"> <label
                            for="ns">Not
                            Sure</label>
                        <br>
                        <input type="checkbox" name="employee_imd" value="immediately" id="imd"> <label
                            for="imd">Immediately</label> <br>
                        <input type="checkbox" name="" id="atage"> <label for="atage">At
                            age</label><input type="number" name="employee_at_age" class="tableInput w-25"><br>
                        <div class='d-flex align-items-baseline'>Is protecting their $$ from market loss important?
                            <div class="dropdown">
                                <select id="employee_loss" name="employee_loss" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>Will the TSP be the only source of additional income?
                            <div class="dropdown">

                                <select id="employee_income" name="employee_income" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>

                        <br>
                        *List other accounts and their balances in the NOTES section.
                    </td>
                </tr>
            </table>
            <table id="table-seventeen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        GOALS
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>
                        <div class='d-flex align-items-baseline'>Is there a balance the employee would like to attain by
                            retirement?
                            <div class="dropdown">
                                <select id="goal" name="goal" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>

                        <br>
                        If so, what is that amount? $<input type="text" name="goal_amount"
                            class="tableInput w-25 numberInput"><br>
                        What is the purpose for the TSP?<input type="text" name="goal_tsp"
                            class="tableInput w-25"><br>
                        What would the employee like to do after they retire?<input type="text" name="goal_retirement"
                            class="tableInput w-25"><br>
                        Does the employee feel on track to reach their retirement goals? <input type="text"
                            name="goal_track" class="tableInput w-100"><br>
                        How much does the employee need to live comfortably?<input type="text" name="goal_comfor"
                            class="tableInput w-25"><br>
                        <div class='d-flex align-items-baseline'>
                            Is the employee currently working with a financial professional?
                            <div class="dropdown">

                                <select id="goal_professional" name="goal_professional" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        Why or why not?<input type="text" name="goal_why" class="tableInput w-25"><br>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-sm-12 col-md-6">
            <table id="table-eighteen" class="table">
                <tr>
                    <td class="tableTitle" style="font-size: 12px">
                        FUNDS CHOICES
                    </td>
                    <td class="tableTitle" style="font-size: 12px">
                        CURRENT BALANCES
                    </td>
                    <td class="tableTitle" style="font-size: 12px">
                        FUTURE ALLOCATION
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>G</td>
                    <td><input type="number" name="g_name" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="g_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>F</td>
                    <td><input type="number" name="f_name" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="f_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>C</td>
                    <td><input type="number" name="c_name" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="c_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>S</td>
                    <td><input type="number" name="s_name" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="s_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>I</td>
                    <td><input type="number" name="i_name" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="i_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L Income</td>
                    <td><input type="number" name="l_income" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_income_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2025</td>
                    <td><input type="number" name="l_2025" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2025_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2030</td>
                    <td><input type="number" name="l_2030" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2030_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2035</td>
                    <td><input type="number" name="l_2035" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2035_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2040</td>
                    <td><input type="number" name="l_2040" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2040_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2045</td>
                    <td><input type="number" name="l_2045" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2045_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2050</td>
                    <td><input type="number" name="l_2050" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2050_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2055</td>
                    <td><input type="number" name="l_2055" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2055_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2060</td>
                    <td><input type="number" name="l_2060" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2060_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2065</td>
                    <td><input type="number" name="l_2065" class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_2065_value" class="tableInput w-75 percentage-input" placeholder=""> %</td>
                </tr>
                <tr class="dataTr">
                    <td>Total</td>
                    <td><input type="number" name="total_amount" id="total_amount" class="tableInput w-100">$0.00</td>
                    <td><input type="number" name="total_amount_percentage" id="total_amount_percentage" class="tableInput w-75 " placeholder="0"> %
                        <small id="error-message" style="color: red; display: none;"></small>
                    </td>
                </tr>
            </table>
        </div>
        <hr class="solid my-3">
        <div class="col-12">
            <p class="my-2 text-center"><b>INSURANCE PLANS</b></p>
            <table id="table-nineteen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        FEDERAL EMPLOYEE GROUP LIFE INSURANCE (FEGLI) METLIFE
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>
                        <div class='d-flex align-items-baseline'>
                            Does the employee currently have FEGLI coverage?
                            <div class="dropdown">

                                <select id="insurance" name="insurance" class="form-select">
                                    <option value="">Select a Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>

                        <div class='d-flex align-items-baseline'>
                            Will the employee have coverage 5 years proceeding
                            retirement?
                            <div class="dropdown">

                                <select id="insurance_emloyee" name="insurance_emloyee" class="form-select">
                                    <option value="">Select a Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>

                        <div class='d-flex align-items-baseline'>
                            Will the employee keep FEGLI in retirement?
                            <div class="dropdown">


                                <select id="insurance_retirement" name="insurance_retirement" class="form-select">
                                    <option value="">Select a Option</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        Purpose of FEGLI coverage?<input type="text" name="insurance_coverage"
                            class="tableInput w-25"><br>

                        <div class='d-flex align-items-baseline'>
                            Does the employee have dependents?
                            <div class="dropdown">

                                <select id="insurance_employee_dependent" name="insurance_employee_dependent"
                                    class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>What coverage does the employee have?<br>
                        <div>
                            <input type="radio" name="insurance_coverage_value" value="basic_option" class="me-2"
                                id="bs">
                            <label for="bs">Basic</label>
                        </div>
                        <div>
                            <input type="radio" name="insurance_coverage_value" value="a_option" class="me-2"
                                id="opa">
                            <label for="opa">Option A</label>
                        </div>
                        <div>
                            <input type="radio" name="insurance_coverage_value" value="b_option" class="me-2"
                                id="obx">
                            <label for="obx">Option B x</label> <input type="number" name="option_b_value"
                                min="1" max="5" class="tableInput w-25">
                        </div>
                        <div>
                            <input type="radio" name="insurance_coverage_value" value="c_option" class="me-2"
                                id="ocx">
                            <label for="ocx">Option C x</label><input type="number" min="1" max="5"
                                name="insurance_employee_coverage_c" class="tableInput w-25"><br>
                        </div>

                        What is the FEGLI premium? $
                        <input type="text" name="insurance_employee_coverage_pp"
                            class="tableInput w-25 numberInput">/PP <br>
                        Age(s) of dependent child(ren)<input type="text" name="insurance_employee_coverage_age"
                            class="tableInput w-25"><br>
                        Age of child(ren) incapable of self-support<input type="text"
                            name="insurance_employee_coverage_self_age" class="tableInput w-25"><br>
                        <div class='d-flex align-items-baseline'>
                            Have you ever had a needs analysis completed?
                            <div class="dropdown">
                                <select id="insurance_analysis" name="insurance_analysis" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>

                        <br>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-12">
            <table id="table-twenty" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        FEDERAL EMPLOYEE HEALTH BENEFITS (FEHB) VARIOUS PLANS
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <div class="d-flex align-items-baseline">
                            Does the employee have FEHB coverage?
                            <div class="dropdown">

                                <select id="federal" name="federal" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            Type of plan
                            <div class="dropdown">

                                <select id="plan_type" name="plan_type" class="form-select">
                                    <option value="self">Self</option>
                                    <option value="self+1">Self+1</option>
                                    <option value="self+family">Self+Family</option>
                                </select>
                            </div>
                            FEHB premium $<input type="text" name="premium" class="tableInput numberInput"
                                style="width: 20%;">
                            /pp
                        </div>
                    </td>
                </tr>
                <tr class="dataTr" style='vertical-align:baseline'>
                    <td>

                        <div class='d-flex align-items-baseline'>
                            Will the employee have FEHB coverage for at least 5 years immediately preceding
                            retirement?
                            <div class="dropdown">

                                <select id="coverage" name="coverage" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>
                            Will the employee keep FEHB in retirement?
                            <div class="dropdown">
                                <select id="coverage_retirement" name="coverage_retirement" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>
                            Will any family members be dependent on FEHB coverage in retirement?
                            <div class="dropdown">

                                <select id="coverage_retirement_dependent" name="coverage_retirement_dependent"
                                    class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </td>
                    <td>
                        If No, what other coverage does the employee intend to have for Health Insurance in retirement?
                        <input type="text" name="coverage_retirement_insurance" class="tableInput w-25"><br><br>
                        Why? <input type="text" name="coverage_retirement_insurance_why"
                            class="tableInput w-25"><br><br>
                        Who ?<input type="checkbox" name="coverage_retirement_insurance_spouse" value="spouse"
                            class="mx-2" id="spu"> <label for="spu">Spouse</label>
                        <input type="checkbox" name="coverage_retirement_insurance_child" value="child(ren)"
                            class="me-2" id="chl"> <label for="chl">Child(ren)</label>
                        <input type="checkbox" name="coverage_retirement_insurance_both" value="both"
                            class="me-2" id="bth">
                        <label for="bth">Both</label>
                    </td>

                </tr>
            </table>
        </div>
        <div class="col-12">
            <table id="table-twenty-one" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        FEDERAL DENTAL AND VISION INSURANCE PLAN (FEDVIP) VARIOUS PLANS
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <div class="d-flex align-items-baseline">
                            Does the employee have Dental coverage?
                            <div class="dropdown">

                                <select id="dental" name="dental" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>

                            <div class='d-flex align-items-baseline'>
                                Keeping in retirement?
                                <div class="dropdown">

                                    <select id="dental_retirement" name="dental_retirement" class="form-select">
                                        <option value="yes">Yes</option>
                                        <option value="no">No</option>
                                    </select>
                                </div>
                                Dental premium $<input type="text" name="dental_premium"
                                    class="tableInput numberInput" style="width: 18%;">/PP
                            </div>
                        </div>

        </div>
        </td>
        </tr>
        <tr class="dataTr">
            <td colspan="12">
                <div class="d-flex align-items-baseline">
                    Does the employee have Vision coverage?
                    <div class="dropdown">

                        <select id="vision" name="vision" class="form-select">
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div class='d-flex align-items-baseline'>
                        Keeping in retirement?
                        <div class="dropdown">

                            <select id="vision_retirement" name="vision_retirement" class="form-select">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        Vision premium $<input type="text" name="vision_premium" class="tableInput numberInput"
                            style="width: 18%;">/PP
                    </div>
                </div>

                </div>
            </td>
        </tr>

        <tr class="dataTr">
            <td colspan="12">FYI: If there is a combined premium for Dental and Vision, we will only be able
                to show the total cost. <span class="ms-2">Dental/Vision $</span><input type="text"
                    name="vision_total_cost" class="tableInput w-25 numberInput">/PP
            </td>
        </tr>
        </table>
        </div>
        <div class="col-12">
            <table id="table-twenty-two" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        FEDERAL LONG TERM CARE INSURANCE PROGRAM (FLTCIP) JOHN HANCOCK
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <div class="d-flex align-items-baseline">
                            Does the employee have FLTCIP?
                            <div class="dropdown">


                                <select id="insurance_program" name="insurance_program" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            Purchase age <input type="number" name="insurance_age" class="tableInput"
                                style="width: 18%;">Premium $<input type="text" name="insurance_purchase_premium"
                                class="tableInput numberInput" style="width: 18%;">/PP
                        </div>
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <div class="d-flex align-items-baseline">
                            Plans to keep in retirement?
                            <div class="dropdown">

                                <select id="insurance_program_retirement" name="insurance_program_retirement" class="form-select">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            Plans to keep in retirement? <input type="text" name="insurance_program_plan"
                                class="tableInput" style="width: 15%;">Daily Benefit Amount<input type="text"
                                name="insurance_program_daily" class="tableInput" style="width: 15%;">'day
                    </td>
        </div>
        </tr>
        <tr class="dataTr">
            <td colspan="12">
                <div class="d-flex align-items-baseline">

                    Purpose of coverage <input type="text" name="insurance_purpose_coverage" class="tableInput"
                        style="width: 21%;">Inflation protection
                    <div class="dropdown">

                        <select id="insurance_program_purpose" name="insurance_program_purpose" class="form-select">
                            <option value="ACI 4%">ACI 4%</option>
                            <option value="ACI 5%">ACI 5%</option>
                            <option value="FPO">FPO</option>
                        </select>
                    </div>
                    Maximum Lifetime Benefit $<input type="text" id="max_lifetime" name="max_lifetime"
                        class="tableInput numberInput" style="width: 15%;">
                </div>
            </td>
        </tr>
        <tr class="dataTr">
            <td>
                <textarea name="notes" class="w-100" cols="30" rows="10" placeholder="Notes:"></textarea>
            </td>
        </tr>
        </table>
        </div>
        </div>
    </form>

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    {{-- number format --}}
    <script>
        // Add event listeners to all elements with class 'salaryInput'
        const numberInputs = document.querySelectorAll('.numberInput');

        numberInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                let value = e.target.value;

                // Remove any non-numeric characters, except for the decimal point
                value = value.replace(/[^0-9.]/g, '');

                // Split the input into integer and decimal parts
                const parts = value.split('.');

                // Format the integer part with commas
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                // Join the parts back together
                e.target.value = parts.join('.');
            });
        });
    </script>
    {{-- end number format --}}


    {{-- validation phone number format --}}
    <script>
        const phoneInput = document.getElementById('phone');
        const phoneError = document.getElementById('phoneError');
        const phonePattern = /^\(\d{3}\) \d{3}-\d{4}$/;

        phoneInput.addEventListener('input', function() {
            let value = phoneInput.value.replace(/\D/g, '');

            if (value.length > 0) {
                value = '(' + value;
            }
            if (value.length > 4) {
                value = value.slice(0, 4) + ') ' + value.slice(4);
            }
            if (value.length > 9) {
                value = value.slice(0, 9) + '-' + value.slice(9, 13);
            }

            phoneInput.value = value;

            if (phonePattern.test(phoneInput.value)) {
                phoneInput.classList.remove('invalid');
                phoneError.style.display = 'none';
            } else {
                phoneInput.classList.add('invalid');
                phoneError.style.display = 'block';
            }
        });

        phoneInput.addEventListener('blur', function() {
            if (!phonePattern.test(phoneInput.value)) {
                phoneError.style.display = 'block';
            } else {
                phoneError.style.display = 'none';
            }
        });
    </script>
    {{-- end validation phone number format --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('dob');
            const endDateInput = document.getElementById('rscd');
            const dateError = document.getElementById('dateError');

            function validateDates() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDateInput.value && endDateInput.value && startDate >= endDate) {
                    dateError.style.display = 'inline';
                } else {
                    dateError.style.display = 'none';
                }
            }

            startDateInput.addEventListener('change', validateDates);
            endDateInput.addEventListener('change', validateDates);
        });
    </script>

    

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('rscd');
            const endDateInput = document.getElementById('retirement_type_date');
            const retireDateError = document.getElementById('retireDateError');

            function validateDates() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);

                if (startDateInput.value && endDateInput.value && startDate >= endDate) {
                    retireDateError.style.display = 'inline';
                } else {
                    retireDateError.style.display = 'none';
                }
            }

            startDateInput.addEventListener('change', validateDates);
            endDateInput.addEventListener('change', validateDates);
        });
    </script>

    <script>
        // email input validations
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        emailInput.addEventListener('input', function() {
            if (emailPattern.test(emailInput.value)) {
                emailInput.classList.remove('invalid');
                emailError.style.display = 'none';
            } else {
                emailInput.classList.add('invalid');
                emailError.style.display = 'block';
            }
        });
        // end email validations
        function calculateAge() {
            const dob = document.getElementById('dob').value;
            const dobDate = new Date(dob);
            const currentDate = new Date();

            let years = currentDate.getFullYear() - dobDate.getFullYear();
            let months = currentDate.getMonth() - dobDate.getMonth();
            let days = currentDate.getDate() - dobDate.getDate();

            // Adjust days and months if necessary
            if (days < 0) {
                months--;
                // Get the number of days in the previous month
                const lastMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
                days += lastMonthDate.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            // Convert days to months if 30 or more days
            if (days >= 30) {
                months += Math.floor(days / 30);
                days = days % 30;
            }

            // Convert months to years if 12 or more months
            if (months >= 12) {
                years += Math.floor(months / 12);
                months = months % 12;
            }

            let ageString = '';

            if (years > 0) {
                ageString += years + ' Y';
            }
            if (months > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += months + ' M';
            }
            if (days > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += days + ' D';
            }

            document.getElementById('age').value = ageString;
        }




        function calculateSpouseAge() {
            const spouse_dob = document.getElementById('spouse_dob').value;
            const spouseDobDate = new Date(spouse_dob);
            const currentDate = new Date();

            let years = currentDate.getFullYear() - spouseDobDate.getFullYear();
            let months = currentDate.getMonth() - spouseDobDate.getMonth();
            let days = currentDate.getDate() - spouseDobDate.getDate();

            // Adjust days and months if necessary
            if (days < 0) {
                months--;
                // Get the number of days in the previous month
                const lastMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), 0);
                days += lastMonthDate.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            // Convert days to months if 30 or more days
            if (days >= 30) {
                months += Math.floor(days / 30);
                days = days % 30;
            }

            // Convert months to years if 12 or more months
            if (months >= 12) {
                years += Math.floor(months / 12);
                months = months % 12;
            }

            let ageString = '';

            if (years > 0) {
                ageString += years + ' Y';
            }
            if (months > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += months + ' M';
            }
            if (days > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += days + ' D';
            }

            document.getElementById('spouse_age').value = ageString;
        }

        function calculateRetirementAge() {
            const dob = document.getElementById('dob').value;
            const retirementDate = document.getElementById('retirement_type_date').value;

            if (!dob || !retirementDate) {
                document.getElementById('retirement_type_date').value = '';
                return;
            }

            const dobDate = new Date(dob);
            const retirementDateObj = new Date(retirementDate);

            let years = retirementDateObj.getFullYear() - dobDate.getFullYear();
            let months = retirementDateObj.getMonth() - dobDate.getMonth();
            let days = retirementDateObj.getDate() - dobDate.getDate();

            // Adjust days and months if necessary
            if (days < 0) {
                months--;
                const lastMonthDate = new Date(retirementDateObj.getFullYear(), retirementDateObj.getMonth(), 0);
                days += lastMonthDate.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            let ageString = '';

            if (years > 0) {
                ageString += years + ' Y';
            }
            if (months > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += months + ' M';
            }
            if (days > 0) {
                if (ageString !== '') {
                    ageString += ', ';
                }
                ageString += days + ' D';
            }

            document.getElementById('retirement_type_age').value = ageString;
        }


        function calculateYoSDollar() {
            const retirementDate = document.getElementById('retirement_type_date').value;
            const rscdDate = document.getElementById('rscd').value;

            if (!retirementDate || !rscdDate) {
                document.getElementById('yosDollar').value = '';
                return;
            }

            const retirementDateObj = new Date(retirementDate);
            const rscdDateObj = new Date(rscdDate);

            let years = retirementDateObj.getFullYear() - rscdDateObj.getFullYear();
            let months = retirementDateObj.getMonth() - rscdDateObj.getMonth();
            let days = retirementDateObj.getDate() - rscdDateObj.getDate();

            // Adjust days and months if necessary
            if (days < 0) {
                months--;
                const lastMonthDate = new Date(retirementDateObj.getFullYear(), retirementDateObj.getMonth(), 0);
                days += lastMonthDate.getDate();
            }

            if (months < 0) {
                years--;
                months += 12;
            }

            let differenceString = '';

            if (years > 0) {
                differenceString += years + ' Y';
            }
            if (months > 0) {
                if (differenceString !== '') {
                    differenceString += ', ';
                }
                differenceString += months + ' M';
            }
            if (days > 0) {
                if (differenceString !== '') {
                    differenceString += ', ';
                }
                differenceString += days + ' D';
            }

            document.getElementById('yosDollar').value = differenceString;
        }

        $("#fedForm").submit(function(event){
        event.preventDefault();

        $("button[type=submit]").prop('disabled',true);
        jQuery.ajax({
            url:"{{route('share.caseStore')}}",
            type:'post',
            data: jQuery('#fedForm').serializeArray(),
            dataType:'json',
            success:function(response){
                $("button[type=submit]").prop('disabled',false);
                if(response["status"]== true)
                {
                    window.location.href="{{route('share.caseList', ['userId' => $userId, 'shareId' => $shareId])}}";
                    $("#name").removeClass('is-invalid').siblings('p')
                    .removeClass('invalid-feedback').html("");

                    $("#dob").removeClass('is-invalid').siblings('p')
                    .removeClass('invalid-feedback').html("");


                }else{
                    var errors = response['errors'];
                if(errors['name'])
                {
                    $("#name").addClass('is-invalid').siblings('p')
                    .addClass('invalid-feedback').html(errors['name']);
                }else{
                    $("#name").removeClass('is-invalid').siblings('p')
                    .removeClass('invalid-feedback').html("");
                    
                }

                if(errors['dob'])
                {
                    $("#dob").addClass('is-invalid').siblings('p')
                    .addClass('invalid-feedback').html(errors['dob']);
                }else{
                    $("#dob").removeClass('is-invalid').siblings('p')
                    .removeClass('invalid-feedback').html("");
                    
                }
                }
                

            }, error : function(jqXHR , exception){
                // console.log("something went wrong");
                window.location.href="{{route('share.caseList', ['userId' => $userId, 'shareId' => $shareId])}}";
            }
        })
    });

    </script>

@endsection
