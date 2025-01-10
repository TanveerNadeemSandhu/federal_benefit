@extends('layouts.app')
@section('title', 'Edit case | Fed Benefit Anaylzer')

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

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <form name="fedForm" id="fedForm">
        @csrf
        <div class='row justify-content-between mb-4 printHeader'>
            <div class="col-sm-3 col">
                <div class="position-relative d-flex align-items-center ">
                    <a href="{{ route('fed-case.index') }}">
                        <img class="group-icon me-2" alt="" src="{{ asset('images/accountagency/group-1442.svg') }}">
                    </a>
                    <div class="dropdown showStatus">
                        {{-- <a class="btn dropdown-toggle" role="button" id="showStatus" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Status
                        </a> --}}


                        <select class="form-select showStatus text-dark fs-6" aria-label="Default select example"
                            id="status" name="status">
                            <option value="New" {{ $case->status === 'New' ? 'selected' : '' }}>New</option>
                            <option value="For bos review" {{ $case->status === 'For bos review' ? 'selected' : '' }}>For
                                bos
                                review</option>
                            <option value="Need information" {{ $case->status === 'Need information' ? 'selected' : '' }}>
                                Need
                                information</option>
                            <option value="Completed" {{ $case->status === 'Completed' ? 'selected' : '' }}>Completed
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col actionButton text-center">
                <button><a href="{{ route('fed-case.print', $case->id) }}"><img
                            src="{{ asset('images/dashboard/black3.svg') }}" /> Print</a></button>
                <iframe id="pageAFrame" style="display:none;"></iframe>
                {{-- <button><a href="{{ route('fed-case.show', $case->id) }}"><img src="{{ asset('images/dashboard/vector.svg')}}" />Present</a></button> --}}
                <a href="{{ route('calculation.show', $case->id) }}">Calculate</a>
            </div>
            <div class="col-sm-3 col text-end">
                <!-- <span class="position-relative">
                            <img class="noti-icon" alt="" src="/images/accountagency/notification-1@2x.png">
                            <span class="noti-number">1</span>
                        </span> -->
                <button class="case" onclick="saveCase()" style="display: block">Save & Exit</button>

            </div>
        </div>
        <!-- section one  -->
        <div class="row present">
            <div class="col-12">
                <table id="table-one" class="table">
                    @if (!empty($userId))
                        <input type="hidden" name="id" value="{{ $userId }}" />
                    @endif
                    <input type="hidden" name="case_id" value="{{ $id }}">
                    <tr>
                        <td colspan="12" class="tableTitle">
                            EMPLOYEE INFORMATION
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td colspan="3">Name: <input type="text" name="name" id="name" class="tableInput w-75"
                                value="{{ $case->name }}">
                            <p></p>
                        </td>
                        <td>D.O.B: <input type="date" name="dob" id="dob" class="tableInput w-50"
                                onchange="calculateAge(); calculateRetirementAge(); calculateYoSDollar()"
                                value="{{ $case->dob }}">
                            <p></p>
                        </td>
                        <td>Age: <input type="text" name="age" id="age" class="tableInput w-50"
                                value="{{ $case->age }}"></td>

                    </tr>
                    <tr class="dataTr">
                        <td colspan="3">Spouse Name: <input type="text" name="spouse_name" id="spouse_name"
                                class="tableInput w-75" value="{{ $case->spouse_name }}"></td>
                        <td>D.O.B: <input type="date" name="spouse_dob" id="spouse_dob" onchange="calculateSpouseAge()"
                                class="tableInput w-50" value="{{ $case->spouse_dob }}"></td>
                        <td>Age: <input type="text" name="spouse_age" id="spouse_age" class="tableInput w-50"
                                value="{{ $case->spouse_age }}"></td>
                    </tr>
                    <tr class="dataTr">
                        <td>Address: <input type="text" name="address" id="address" class="tableInput w-75"
                                value="{{ $case->address }}"></td>
                        <td>City: <input type="text" name="city" id="city" class="tableInput w-50"
                                value="{{ $case->city }}"></td>
                        <td></td>
                        <td>
                            <select name="state" id="state" class="form-select w-50">
                                <option value="">select a state</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->name }}"
                                        {{ $case->state == $state->name ? 'selected' : '' }}>{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>Zip: <input type="text" name="zip" id="zip" class="tableInput w-50"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" value="{{ $case->zip }}">

                    </tr>
                    <tr class="dataTr">
                        <td colspan="4">Email:
                            <input type="email" name="email" id="email" class="tableInput w-75"
                                placeholder="xxxxx@xxxxxxxxx.xxx" value="{{ $case->email }}">
                            <span id="emailError" style="color:red; display:none;">Please enter a valid email
                                address.</span>
                        </td>
                        <td>Phone:
                            <input type="text" name="phone" id="phone" placeholder="(555) 555-1234"
                                class="tableInput w-50" value="{{ $case->phone }}">
                            <span id="phoneError" style="color:red; display:none;">Please enter a valid phone
                                number.</span>
                        </td>
                    </tr>

                </table>
            </div>

            <div class="col-sm-12 col-md-6">
                <table id="table-two retirement_system" class="table">
                    <span id="retirementSystemError" style="color:red;"></span>
                    <tr>
                        <td colspan="4" class="tableTitle">
                            RETIREMENT SYSTEM
                        </td>
                    </tr>
                    <tr class="dataTr">

                        <td>
                            <input type="radio" id="csrs" name="retirement_system" value="csrs"
                                {{ $case->retirement_system == 'csrs' ? 'checked' : '' }}>
                        </td>

                        <td colspan="3"><label for="csrs">CSRS</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td>
                            <input type="radio" id="csof" value="csrs_offset" name="retirement_system"
                                {{ $case->retirement_system == 'csrs_offset' ? 'checked' : '' }}>
                        </td>
                        <td colspan="3">
                            <label for="csof">CSRS OFFSET</label>
                            <input type="date" name="retirement_system_csrs_offset" id="retirement_system_csrs_offset"
                                class="tableInput w-50"
                                value="{{ $case->retirement_system_csrs_offset ? date('Y-m-d', strtotime($case->retirement_system_csrs_offset)) : '' }}">
                        </td>
                    </tr>

                    <tr class="dataTr">
                        <td><input type="radio" id="fers" name="retirement_system" value="fers"
                                {{ $case->retirement_system == 'fers' ? 'checked' : '' }}></td>
                        <td colspan="3"><label for="fers">FERS</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="ft" name="retirement_system" value="fers_transfer"
                                {{ $case->retirement_system == 'fers_transfer' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="ft">FERS TRANSFER</label>

                            <input type="date" name="retirement_system_fers_transfer"
                                id="retirement_system_fers_transfer" class="tableInput w-50"
                                value="{{ date('Y-m-d', strtotime($case->retirement_system_fers_transfer)) }}">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="fr" name="retirement_system" value="fers_rea"
                                {{ $case->retirement_system == 'fers_rea' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="fr">FERS RAE(Hired in the year of 2013)</label>
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="frh" name="retirement_system" value="fers_frea"
                                {{ $case->retirement_system == 'fers_frea' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="frh">FERS FRAE(Hired after 01-01-2014)</label>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12 col-md-6">
                <table id="table-three employee_type" class="table">
                    <span id="employeeTypeError" style="color:red;"></span>
                    <tr>
                        <td colspan="4" class="tableTitle">
                            EMPLOYEE TYPE
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="rg" name="employee_type" value="regular"
                                {{ $case->employee_type == 'regular' ? 'checked' : '' }}></td>
                        <td colspan="3"><label for="rg">REGULAR</label></td>
                        <td><input type="radio" id="pst" name="employee_type" value="postal"
                                {{ $case->employee_type == 'postal' ? 'checked' : '' }}></td>
                        <td><label for="pst">POSTAL</label></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="leo" name="employee_type" value="leo"
                                {{ $case->employee_type == 'leo' ? 'checked' : '' }}></td>
                        <td colspan="3"><label for="leo">LEO(Law Enforcement Offer)</label></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="htc" name="employee_type" value="atc"
                                {{ $case->employee_type == 'atc' ? 'checked' : '' }}></td>
                        <td colspan="3"><label for="htc">ATC(Air Traffic Control)</label></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="fff" name="employee_type" value="fff"
                                {{ $case->employee_type == 'fff' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="fff">FF(Fire Fighter)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="mrt" name="employee_type" value="mrt"
                                {{ $case->employee_type == 'mrt' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="mrt">MRT(Military Reserve Technician)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                    <tr class="dataTr">
                        <td><input type="radio" id="cbpo" name="employee_type" value="cbpo"
                                {{ $case->employee_type == 'cbpo' ? 'checked' : '' }}></td>
                        <td colspan="3">
                            <label for="cbpo">CBPO(Customs and Border Protection Officer)</label>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                </table>
            </div>
            <div class="col-12">
                <table id="table-four" class="table">
                    <span id="PEDError" style="color:red;"></span>
                    <tr>
                        <td colspan="4" class="tableTitle">
                            PENSION AND ELIGIBILITY
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td style="vertical-align:middle" class="text-center">What are the employee service computation
                            dates(SCD’s)?</td>
                        <td colspan="3">
                            LSCD(Leave SCD-from paystub / LES) :<input type="date" name="lscd" id="lscd"
                                class="tableInput w-50" value="{{ $case->lscd }}"><br>
                            RSCD(Retirement SCD) :<input type="date" name="rscd" id="rscd"
                                class="tableInput w-50" value="{{ $case->rscd }}"><br>
                            <span id="dateError" class="error" style="display: none;">Joinning date must be greater than
                                Date of Birth date</span><br>
                            6C SCD(Date begin as LEO , FF , ATC , CBPO) :<input type="date" name="scd"
                                class="tableInput w-25" value="{{ $case->scd }}">

                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-12">
                <table id="table-five" class="table">
                    <span id="retirementTypeError" style="color:red;"></span><br>
                    <span id="retirementtypedateError" style="color:red;"></span>
                    <tr>
                        <td colspan="12" class="tableTitle">
                            RETIREMENT TYPE
                        </td>
                    </tr>
                    <tr class="dataTr">

                        <td>
                            <input type="radio" id="fed" name="retirement_type"
                                value="fully_eligible_disability"
                                {{ $case->retirement_type == 'fully_eligible_disability' ? 'checked' : '' }}>
                            <label for="fed">Fully Eligible/Disability</label>
                        </td>
                        <td> <label for="date"> Desired Retirement Date:</label> <input type="date"
                                name="retirement_type_date" id="retirement_type_date"
                                onchange="calculateRetirmentAge(); calculateYoSDollar()" class="tableInput w-50"
                                value={{ $case->retirement_type_date }}>
                            <br><span id="retireDateError" class="error" style="display: none;">Retirement date must be
                                greater
                                than Joinning date</span><br>
                        </td>
                        <input type="text" hidden name="yosDollar" id="yosDollar" value="{{ $yosDollar->age }}"
                            class="tableInput w-50">

                        <td> <label for="age">At Age:</label> <input type="text" name="retirement_type_age"
                                id="retirement_type_age" class="tableInput w-50"
                                value="{{ $case->retirement_type_age }}">
                        </td>
                    </tr>
                    <tr class="dataTr">
                        <td><input value="first_eligible" type="radio" id="fem" name="retirement_type"
                                {{ $case->retirement_type == 'first_eligible' ? 'checked' : '' }}> <label
                                for="fem">FIRST
                                Eligible (MRA+10
                                Retirement)</label></td>
                        <td colspan="1"> </td>
                        <td><input value="voluntary" type="radio" name="retirement_type" id="ve"
                                {{ $case->retirement_type == 'voluntary' ? 'checked' : '' }}>
                            <label for="ve">Voluntary (EARLY OUT)–Offer
                                Date:</label> <input type="date" name="retirement_type_voluntary"
                                class="tableInput w-25" value="{{ $case->retirement_type_voluntary }}">
                        </td>
                        <!-- <td> </td> -->
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

                                <select id="current_leave_option" name="current_leave_option" class="form-select">
                                    <option value="yes" {{ $case->current_leave_option == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no" {{ $case->current_leave_option == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </td>

                        <!-- <td><input type="text" name="name" class="tableInput "></td> -->
                        <td>Sick Leave Hours</td>
                        <td class="">
                            <input type="number" name="sick_leave_hours" class="tableInput w-50"
                                value="{{ $case->sick_leave_hours }}">
                        </td>
                        <!-- <td><input type="text" name="name" class="tableInput "> </td> -->
                    </tr>
                    <tr class="dataTr">
                        <td class="text-center">Will the employee be collecting a lump sum payout for their annual leave
                            hours?</td>
                        <td class="">
                            <div class="dropdown">
                                <select id="current_hours_option" name="current_hours_option" class="form-select">
                                    <option value="yes" {{ $case->current_hours_option == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no" {{ $case->current_hours_option == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </td>
                        <!-- <td><input type="text" name="name" class="tableInput "></td> -->
                        <td> Annual Leave Hours</td>
                        <td class="">
                            <input type="number" name="annual_leave_hours" class="tableInput w-50"
                                value={{ $case->annual_leave_hours }}>
                        </td>

                        <!-- <td><input type="text" name="name" class="tableInput "> </td> -->
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
                                    <option value="yes" {{ $case->income_employee_option == 'yes' ? 'selected' : '' }}>
                                        Yes
                                    </option>
                                    <option value="no" {{ $case->income_employee_option == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>

                            </div>
                        </td>
                        <td> Current Salary Amount $ <input type="text" name="salary_1" id="salary_1"
                                class="tableInput w-25 numberInput" value="{{ $case->salary_1 }}"> </td>
                    </tr>
                    <tr class="dataTr">
                        <td>If NOT, what are the salary amounts to be used? $<input type="text" name="salary_2"
                                class="tableInput w-25 numberInput" value="{{ $case->salary_2 }}"> $<input
                                type="text" name="salary_3" class="tableInput w-25 numberInput"
                                value="{{ $case->salary_3 }}"></td>
                        <td>$<input type="text" name="salary_4" class="tableInput w-50 numberInput"
                                value="{{ $case->salary_4 }}">
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
                                    <option value="yes" {{ $case->employee_spouse === 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $case->employee_spouse === 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>

                            <div class="dropdown">

                                <select id="survior_benefit_fers" name="survior_benefit_fers" class="form-select">
                                    <option value="0" {{ $case->survior_benefit_fers == '0' ? 'selected' : '' }}>0%
                                    </option>
                                    <option value="25" {{ $case->survior_benefit_fers == '25' ? 'selected' : '' }}>25%
                                    </option>
                                    <option value="50" {{ $case->survior_benefit_fers == '50' ? 'selected' : '' }}>50%
                                    </option>
                                </select>



                                </ul>
                            </div>
                            <div>
                                <input type="text" name="survior_benefit_csrs" id="survior_benefit_csrs"
                                    value="{{ $case->survior_benefit_csrs ?? 0 }}" class="tableInput w-50 numberInput"
                                    placeholder="0">
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
                                {{-- <a class="btn dropdown-toggle" role="button" id="employeeEligible" data-bs-toggle="dropdown" aria-expanded="false">
                                    Please Select
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="employeeEligible">
                                    <li><a class="dropdown-item" href="#"  data-value="yes">Yes</a></li>
                                    <li><a class="dropdown-item" href="#"  data-value="no">No</a></li>
                                    <li><a class="dropdown-item" href="#"  data-value="net yet">Net yet</a></li>
                                </ul> --}}

                                <select id="employee_eligible" name="employee_eligible" class="form-select">
                                    <option value="yes" {{ $case->employee_eligible == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $case->employee_eligible == 'no' ? 'selected' : '' }}>No
                                    </option>
                                    <option value="net yet" {{ $case->employee_eligible == 'net yet' ? 'selected' : '' }}>
                                        Net
                                        yet</option>

                                </select>

                            </div>
                        </td>
                        <td>Amount of SS at 62 $ <input type="text" name="amount_1"
                                class="tableInput w-50 numberInput" value="{{ $case->amount_1 }}"> </td>
                    </tr>
                    <tr class="dataTr">
                        <td>What age does the employee estimate begin SS Income? <input type="text" name="amount_2" id="amount_2"
                                class="tableInput w-25 numberInput" value="{{ $case->amount_2 }}" oninput="validateMinValue(this)">
                                <span id="srsAgeError" style="color:red; display:none;">Please enter above 62.</span>
                            </td>
                        <td>Amount to receive $ <input type="text" name="amount_3" class="tableInput w-50 numberInput"
                                value="{{ $case->amount_3 }}"></td>
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
                                {{-- <a class="btn dropdown-toggle" role="button" id="empolyeeWork" data-bs-toggle="dropdown" aria-expanded="false">
                                    Please Select
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="empolyeeWork">
                                    <li><a class="dropdown-item" href="#"  data-value="yes">Yes</a></li>
                                    <li><a class="dropdown-item" href="#" data-value="no">No</a></li>
                                </ul> --}}

                                <select id="employee_work" name="employee_work" class="form-select">
                                    <option value="yes" {{ $caseOFST->employee_work == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $caseOFST->employee_work === 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </td>
                        <!-- <td></td> -->
                        <td colspan="3">Hours worked per week: <input type="text" name="empolyee_hours_work"
                                class="tableInput w-50" value="{{ $caseOFST->empolyee_hours_work }}"></td>
                        <!-- <td> </td> -->
                    </tr>
                    <tr class="dataTr">
                        <td>IF the employee has multiple dates of part-time service please list ALL dates in NOTES section
                        </td>
                        <td class="text-center">Dates: <input type="date" name="empolyee_multiple_date"
                                class="tableInput w-50"value="{{ $caseOFST->empolyee_multiple_date }}">
                        </td>
                        <!-- <td></td> -->
                        <td class="text-center">to </td>
                        <td><input type="date" name="empolyee_multiple_date_to" class="tableInput"
                                value="{{ $caseOFST->empolyee_multiple_date_to }}"> </td>

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
                                    <option value="yes"
                                        {{ $caseOFST->non_deduction_service === 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseOFST->non_deduction_service === 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>

                        </td>
                        <td> What were the dates of service? <input type="text" name="non_deduction_service_date"
                                class="tableInput w-25" value="{{ $caseOFST->non_deduction_service_date }}"> to <input
                                type="text" name="non_deduction_service_date_2" class="tableInput w-25"
                                value="{{ $caseOFST->non_deduction_service_date_2 }}"> <br>
                            <div class='d-flex align-items-center'>Was a deposit paid?
                                <div class="dropdown">


                                    <select id="non_deduction_service_deposit" name="non_deduction_service_deposit"
                                        class="form-select">
                                        <option value="yes"
                                            {{ $caseOFST->non_deduction_service_deposit === 'yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="no"
                                            {{ $caseOFST->non_deduction_service_deposit === 'no' ? 'selected' : '' }}>No
                                        </option>
                                    </select>

                                </div>
                            </div>
            </div>What was the deposit owed? $<input type="text" name="non_deduction_service_deposit_owned"
                class="tableInput w-25 numberInput" value="{{ $caseOFST->non_deduction_service_deposit_owned }}">
            </td>
            </tr>
            </table>
        </div>
        <div class="col-12">
            <table id="table-twelve" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        BREAK IN SERVICE / REFUNDED SERVICE
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style='display: flex;align-items: baseline;'> Did the employee ever have a Break in Service of
                        more than 3 days?


                        <select id="break_in_service" name="break_in_service" class="form-select">
                            <option value="yes" {{ $caseOFST->break_in_service === 'yes' ? 'selected' : '' }}>Yes
                            </option>
                            <option value="no" {{ $caseOFST->break_in_service === 'no' ? 'selected' : '' }}>No
                            </option>
                        </select>

        </div>
        </td>
        <td> Dates of service PRIOR to the BREAK <input type="date" name="break_in_service_date_1"
                class="tableInput w-25" value="{{ $caseOFST->break_in_service_date_1 }}"> to <input type="date"
                name="break_in_service_date_2" class="tableInput w-25" value="{{ $caseOFST->break_in_service_date_2 }}">
            Date employee RETURNED to federal
            service? <input type="date" name="break_in_service_return_date" class="tableInput w-25"
                value="{{ $caseOFST->break_in_service_return_date }}"></td>
        </tr>
        <tr class="dataTr">

            <td style="display: flex;align-items: baseline;"> Did the employee take a Refund of their retirement
                contributions
                during their break of service?
                <div class="dropdown">


                    <select id="break_in_service_refund" name="break_in_service_refund" class="form-select">
                        <option value="yes" {{ $caseOFST->break_in_service_refund === 'yes' ? 'selected' : '' }}>Yes
                        </option>
                        <option value="no" {{ $caseOFST->break_in_service_refund === 'no' ? 'selected' : '' }}>No
                        </option>
                    </select>

                </div>
            </td>
            <td> Was a Redeposit made after returning to federal service? <input type="text"
                    name="break_in_service_redeposite" class="tableInput w-25"
                    value="{{ $caseOFST->break_in_service_redeposite }}"> Amount of Redeposit $ <input type="text"
                    name="break_in_service_amount_redeposite" class="tableInput w-25 numberInput"
                    value="{{ $caseOFST->break_in_service_amount_redeposite }}">
            </td>
        </tr>

        </table>
        </div>
        <hr class="solid my-3">
        <div class="col-12">
            <p class="my-2 text-center"><b>MILITARY SERVICE TIME </b>(Select Yes or No and fill in the rest of the
                information)</p>
            <table id="table-thirteen" class="table">
                <tr>
                    <td colspan="12" class="tableTitle">
                        ACTIVE-DUTY / RESERVE / ACADEMY SERVICE
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style='display: flex;align-items: baseline;'>Has the employee ever had Military service?
                        <div class="dropdown">


                            <select id="military_service" name="military_service" class="form-select">
                                <option value="yes" {{ $caseMST->military_service === 'yes' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="no" {{ $caseMST->military_service === 'no' ? 'selected' : '' }}>No
                                </option>
                            </select>

                        </div>
                    </td>
                    <td>
                        Dates of service? <input type="date" name="military_service_date_1" class="tableInput w-25"
                            value="{{ $caseMST->military_service_date_1 }}"> to <input type="date"
                            name="military_service_date_2" class="tableInput w-25"
                            value="{{ $caseMST->military_service_date_2 }}">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style="display: flex;align-items: baseline;"><input type="checkbox" id="add"
                            class='me-1' {{ $caseMST->military_service_active_duty ? 'checked' : '' }}> <label
                            for="add"> Active-Duty -
                            Deposit
                            paid?</label>
                        <div class="dropdown">


                            <select id="military_service_active_duty" name="military_service_active_duty"
                                class="form-select">
                                <option value="yes"
                                    {{ $caseMST->military_service_active_duty === 'yes' ? 'selected' : '' }}>
                                    Yes</option>
                                <option value="no"
                                    {{ $caseMST->military_service_active_duty === 'no' ? 'selected' : '' }}>
                                    No</option>
                            </select>

                        </div>
                    </td>

                    <td>
                        Dates of service? <input type="date" name="military_service_active_duty_date_1"
                            class="tableInput w-25" value="{{ $caseMST->military_service_active_duty_date_1 }}"> to
                        <input type="date" name="military_service_active_duty_date_2" class="tableInput w-25"
                            value="{{ $caseMST->military_service_active_duty_date_2 }}">
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style="display: flex;align-items: baseline;"><input type="checkbox"
                            {{ $caseMST->military_service_reserve ? 'checked' : '' }} id="military_service_reserve_check"
                            class='me-1'> <label for="add"> Reserve - Deposit
                            paid?</label>
                        <div class="dropdown">


                            <select id="military_service_reserve" name="military_service_reserve" class="form-select">
                                <option value="yes"
                                    {{ $caseMST->military_service_reserve === 'yes' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="no"
                                    {{ $caseMST->military_service_reserve === 'no' ? 'selected' : '' }}>No
                                </option>
                            </select>
                        </div>
                    </td>
                    <td>
                        Dates of service? <input type="date" name="military_service_reserve_date_1"
                            class="tableInput w-25" value="{{ $caseMST->military_service_reserve_date_1 }}"> to <input
                            type="date" name="military_service_reserve_date_2" class="tableInput w-25"
                            value={{ $caseMST->military_service_reserve_date_2 }}>
                    </td>
                </tr>
                <tr class="dataTr">

                    <td style="display: flex;align-items: baseline;"> <input type="checkbox"
                            {{ $caseMST->military_service_academy ? 'checked' : '' }} id="add" class='me-1'>
                        <label for="add"> Academy - Deposit
                            paid?</label>
                        <div class="dropdown">


                            <select id="military_service_academy" name="military_service_academy" class="form-select">
                                <option value="yes"
                                    {{ $caseMST->military_service_academy == 'yes' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="no"
                                    {{ $caseMST->military_service_academy == 'no' ? 'selected' : '' }}>No
                                </option>
                            </select>
                        </div>
                    </td>
                    <td>
                        Amount of deposit $<input type="text" name="military_service_academy_amount"
                            value="{{ $caseMST->military_service_academy_amount }}" class="tableInput w-50 numberInput">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td style='display: flex;align-items: baseline;'>Did the employee Retire from Military service?
                        <div class="dropdown">

                            <select id="military_service_retire" name="military_service_retire" class="form-select">
                                <option value="yes"
                                    {{ $caseMST->military_service_retire == 'yes' ? 'selected' : '' }}>Yes
                                </option>
                                <option value="no" {{ $caseMST->military_service_retire == 'no' ? 'selected' : '' }}>
                                    No
                                </option>
                            </select>
                        </div>
                    </td>
                    <td>
                        Is the employee collecting VA pay?<input type="text" name="military_service_collecting"
                            class="tableInput w-50" value="{{ $caseMST->military_service_collecting }}">
                    </td>
                </tr>
                <tr class="dataTr">
                    <td>
                        <input type="checkbox" id="ady" name="military_service_active_duty"
                            {{ $caseMST->military_service_active_duty ? 'checked' : '' }}> <label
                            for="ady">Active-Duty</label> <input type="checkbox" id="revse"
                            name="military_service_reserves" {{ $caseMST->military_service_reserves ? 'checked' : '' }}>
                        <label for="revse">Reserves</label>
                    </td>
                    <td>
                        Amount of VA pay $<input type="text" name="military_service_amount"
                            class="tableInput w-50 numberInput" value="{{ $caseMST->military_service_amount }}"> /month
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <textarea name="military_service_note" class="w-100 tableInput" cols="30" rows="10">{{ $caseMST->military_service_note }}</textarea>
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
                    <td><input type="checkbox" id="none1" name="contribute" value ="none"
                            {{ $caseTSP->contribute ? 'checked' : '' }}> <label for="none1">None</label></td>
                    <td colspan="2">$<input type="text" name="contribute_pp" id="contribute_pp"
                            class="tableInput w-50 " value="{{ $caseTSP->contribute_pp }}">/PP </td>
                    <td colspan="2"><input type="text" name="contribute_pp_percentage"
                            id="contribute_pp_percentage" class="tableInput  w-50"
                            value="{{ $caseTSP->contribute_pp_percentage }}">% /PP </td>
                </tr>
                <tr class="dataTr">
                    <td>How much does the employee contribute to Roth TSP? </td>
                    <td><input type="checkbox" id="none2" name="contribute_tsp" value="none"
                            {{ $caseTSP->contribute_tsp ? 'checked' : '' }}> <label for="none2">None</label></td>
                    <td colspan="2">$<input type="text" name="contribute_tsp_pp" id="contribute_tsp_pp"
                            class="tableInput  w-50 " value="{{ $caseTSP->contribute_tsp_pp }}"> /PP </td>
                    <td colspan="2"><input type="text" name="contribute_tsp_pp_percentage"
                            id="contribute_tsp_pp_percentage" class="tableInput  w-50"
                            value="{{ $caseTSP->contribute_tsp_pp_percentage }}">% /PP </td>
                </tr>
                <tr class="dataTr">
                    <td>How did the employee decide their contribution limit shown in box 20?</td>
                    <td colspan="4"><input type="text" name="contribute_limit" class="tableInput "
                            value="{{ $caseTSP->contribute_limit }}"></td>
                </tr>
            </table>
            <table id="table-fifteen" class="table">
                <tr class="dataTr">
                    <td width="50%" class=''>
                        <div class='d-flex align-items-baseline'>Does the employee have any TSP loan(s)? <div
                                class="dropdown">

                                <select id="contribute_tsp_loan" name="contribute_tsp_loan" class="form-select">
                                    <option value="yes"
                                        {{ $caseTSP->contribute_tsp_loan == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $caseTSP->contribute_tsp_loan == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <br><br>
                        <input type="checkbox" id="g" name="contribute_tsp_loan_gen" value="general"
                            {{ $caseTSP->contribute_tsp_loan_gen ? 'checked' : '' }}> <label
                            for="g">General</label>
                        <br>
                        <input type="checkbox" id="r" name="contribute_tsp_res" value="residential"
                            {{ $caseTSP->contribute_tsp_res ? 'checked' : '' }}> <label
                            for="r">Residential</label>
                    </td>
                    <td></td>
                    <td width="50%">
                        How much does the employee pay for their loan(s)?<input type="text" name="contribute_pay_pp"
                            class="tableInput" style="width: 10%;" value="{{ $caseTSP->contribute_pay_pp }}">/PP
                        $<input type="text" name="contribute_pay_pp_value" class="tableInput numberInput"
                            style="width: 10%;" value="{{ $caseTSP->contribute_pay_pp_value }}">/PP <br>
                        How much is owed for each loan? $<input type="text" name="contribute_own_loan"
                            class="tableInput w-25 numberInput" value="{{ $caseTSP->contribute_own_loan }}"> $<input
                            type="text" name="contribute_own_loan_2" class="tableInput w-25 numberInput"
                            value="{{ $caseTSP->contribute_own_loan_2 }}"><br>
                        What is the estimated pay off dates(s)?<input type="date" name="contribute_pay_date"
                            class="tableInput w-25" value="{{ $caseTSP->contribute_pay_date }}">
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
                        <input type="checkbox" name="employee_not_sure" value="not sure"
                            {{ $caseTSP->employee_not_sure ? 'checked' : '' }} id="ns"> <label for="ns">Not
                            Sure</label>
                        <br>
                        <input type="checkbox" name="employee_imd" value="immediately"
                            {{ $caseTSP->employee_imd ? 'checked' : '' }} id="imd"> <label
                            for="imd">Immediately</label> <br>
                        <input type="checkbox" name="" id="atage"
                            {{ $caseTSP->employee_at_age ? 'checked' : '' }}>
                        <label for="atage">At
                            age</label><input type="number" name="employee_at_age" class="tableInput w-25"
                            value="{{ $caseTSP->employee_at_age }}"><br>
                        <div class='d-flex align-items-baseline'>Is protecting their $$ from market loss important?
                            <div class="dropdown">


                                <select id="employee_loss" name="employee_loss" class="form-select">
                                    <option value="yes" {{ $caseTSP->employee_loss == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $caseTSP->employee_loss == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>Will the TSP be the only source of additional income?
                            <div class="dropdown">
                                <select id="employee_income" name="employee_income" class="form-select">
                                    <option value="yes" {{ $caseTSP->employee_income == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $caseTSP->employee_income == 'no' ? 'selected' : '' }}>No
                                    </option>
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
                                    <option value="yes" {{ $caseTSP->goal == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no" {{ $caseTSP->goal == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>

                            </div>
                        </div>

                        <br>
                        If so, what is that amount? $<input type="text" name="goal_amount"
                            value="{{ $caseTSP->goal_amount }}" class="tableInput w-25 numberInput"><br>
                        What is the purpose for the TSP?<input type="text" name="goal_tsp"
                            value="{{ $caseTSP->goal_tsp }}" class="tableInput w-25"><br>
                        What would the employee like to do after they retire?<input type="text" name="goal_retirement"
                            value="{{ $caseTSP->goal_retirement }}" class="tableInput w-25"><br>
                        Does the employee feel on track to reach their retirement goals? <input type="text"
                            name="goal_track" value="{{ $caseTSP->goal_track }}" class="tableInput w-100"><br>
                        How much does the employee need to live comfortably?<input type="text" name="goal_comfor"
                            value="{{ $caseTSP->goal_comfor }}" class="tableInput w-25"><br>
                        <div class='d-flex align-items-baseline'>
                            Is the employee currently working with a financial professional?
                            <div class="dropdown">
                                <select id="goal_professional" name="goal_professional" class="form-select">
                                    <option value="yes" {{ $caseTSP->goal_professional == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no" {{ $caseTSP->goal_professional == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>


                            </div>
                        </div>
                        <br>
                        Why or why not?<input type="text" name="goal_why" value="{{ $caseTSP->goal_why }}"
                            class="tableInput w-25"><br>
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
                    <td><input type="number" name="g_name" id="g_name" value="{{ $caseTSP->g_name }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="g_value" id="g_value" value="{{ $caseTSP->g_value }}"
                            class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>F</td>
                    <td><input type="number" name="f_name" id="f_name" value="{{ $caseTSP->f_name }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="f_value" id="f_value" value="{{ $caseTSP->f_value }}"
                            class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>C</td>
                    <td><input type="number" name="c_name" id="c_name" value="{{ $caseTSP->c_name }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="c_value" id="c_value" value="{{ $caseTSP->c_value }}"
                            class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>S</td>
                    <td><input type="number" name="s_name" id="s_name" value="{{ $caseTSP->s_name }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="s_value" id="s_value" value="{{ $caseTSP->s_value }}"
                            class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>I</td>
                    <td><input type="number" name="i_name" id="i_name" value="{{ $caseTSP->i_name }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="i_value" id="i_value" value="{{ $caseTSP->i_value }}"
                            class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L Income</td>
                    <td><input type="number" name="l_income" id="l_income" value="{{ $caseTSP->l_income }}"
                            class="tableInput w-100 number-input"></td>
                    <td><input type="number" name="l_income_value" id="l_income_value"
                            value="{{ $caseTSP->l_income_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2025</td>
                    <td><input type="number" name="l_2025" id="l_2025" value="{{ $caseTSP->l_2025 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2025_value" id="l_2025_value"
                            value="{{ $caseTSP->l_2025_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2030</td>
                    <td><input type="number" name="l_2030" id="l_2030" value="{{ $caseTSP->l_2030 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2030_value" id="l_2030_value"
                            value="{{ $caseTSP->l_2030_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2035</td>
                    <td><input type="number" name="l_2035" id="l_2035" value="{{ $caseTSP->l_2035 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2035_value" id="l_2035_value"
                            value="{{ $caseTSP->l_2035_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2040</td>
                    <td><input type="number" name="l_2040" id="l_2040" value="{{ $caseTSP->l_2040 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2040_value" id="l_2040_value"
                            value="{{ $caseTSP->l_2040_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2045</td>
                    <td><input type="number" name="l_2045" id="l_2045" value="{{ $caseTSP->l_2045 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2045_value" id="l_2045_value"
                            value="{{ $caseTSP->l_2045_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2050</td>
                    <td><input type="number" name="l_2050" id="l_2050" value="{{ $caseTSP->l_2050 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2050_value" id="l_2050_value"
                            value="{{ $caseTSP->l_2050_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2055</td>
                    <td><input type="number" name="l_2055" id="l_2055" value="{{ $caseTSP->l_2055 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2055_value" name="l_2055_value"
                            value="{{ $caseTSP->l_2055_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2060</td>
                    <td><input type="number" name="l_2060" id="l_2060" value="{{ $caseTSP->l_2060 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2060_value" id="l_2060_value"
                            value="{{ $caseTSP->l_2060_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>L 2065</td>
                    <td><input type="number" name="l_2065" id="l_2065" value="{{ $caseTSP->l_2065 }}"
                            class="tableInput w-100 number-input">
                    </td>
                    <td><input type="number" name="l_2065_value" id="l_2065_value"
                            value="{{ $caseTSP->l_2065_value }}" class="tableInput w-75 percentage-input"> %</td>
                </tr>
                <tr class="dataTr">
                    <td>Total</td>
                    <td><input type="number" name="total_amount" id="total_amount"
                            value="{{ $caseTSP->total_amount }}" class="tableInput w-100">$0.00</td>
                    <td><input type="number" name="total_amount_percentage" id="total_amount_percentage"
                            value="{{ $caseTSP->total_amount_percentage }}" class="tableInput w-75 "> %
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
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>


                            </div>
                        </div>
                        <br>

                        <div class='d-flex align-items-baseline'>
                            Will the employee have coverage 5 years proceeding
                            retirement?
                            <div class="dropdown">
                                <select id="insurance_emloyee" name="insurance_emloyee" class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_emloyee == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_emloyee == 'no' ? 'selected' : '' }}>No</option>
                                </select>


                            </div>
                        </div>
                        <br>

                        <div class='d-flex align-items-baseline'>
                            Will the employee keep FEGLI in retirement?
                            <div class="dropdown">
                                <select id="insurance_retirement" name="insurance_retirement" class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_retirement == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_retirement == 'no' ? 'selected' : '' }}>No
                                    </option>
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
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_employee_dependent == 'yes' ? 'selected' : '' }}>
                                        Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_employee_dependent == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>


                            </div>
                        </div>
                    </td>
                    <td>What coverage does the employee have?<br>
                        <div>
                            <input type="checkbox" name="insurance_coverage_basic_option"
                                {{ $caseInsurancePlan->insurance_coverage_basic_option == 'basic_option' ? 'checked' : '' }}
                                value="basic_option" class="me-2" id="bs">
                            <label for="bs">Basic</label>
                            <select id="basic_option_select" name="basic_option_select">
                                <option value="75"
                                    {{ $caseInsurancePlan->basic_option_select == '75' ? 'selected' : '' }}>
                                    75%
                                </option>
                                <option value="50"
                                    {{ $caseInsurancePlan->basic_option_select == '50' ? 'selected' : '' }}>
                                    50%
                                </option>
                                <option value="no"
                                    {{ $caseInsurancePlan->basic_option_select == 'no' ? 'selected' : '' }}>
                                    No reduction 
                                </option>
                            </select>
                        </div>
                        <div>
                            <input type="checkbox" name="insurance_coverage_a_option"
                                {{ $caseInsurancePlan->insurance_coverage_a_option == 'a_option' ? 'checked' : '' }}
                                value="a_option" class="me-2" id="opa">
                            <label for="opa">Option A</label>
                        </div>
                        <div>
                            <input type="checkbox" name="insurance_coverage_b_option"
                                {{ $caseInsurancePlan->insurance_coverage_b_option == 'b_option' ? 'checked' : '' }}
                                value="b_option" class="me-2" id="obx">
                            <label for="obx">Option B x</label><input type="number"
                                value="{{ $caseInsurancePlan->option_b_value }}" name="option_b_value"
                                min="1" max="5" class="tableInput w-25">
                        </div>
                        <div>
                            <input type="checkbox" name="insurance_coverage_c_option"
                                {{ $caseInsurancePlan->insurance_coverage_c_option == 'c_option' ? 'checked' : '' }}
                                value="c_option" class="me-2" id="ocx">
                            <label for="ocx">Option C x</label> <input type="number" min="1"
                                max="5" name="insurance_employee_coverage_c"
                                value="{{ $caseInsurancePlan->insurance_employee_coverage_c }}"
                                class="tableInput w-25"><br>
                        </div>

                        What is the FEGLI premium? $
                        <input type="text" name="insurance_employee_coverage_pp"
                            value="{{ $caseInsurancePlan->insurance_employee_coverage_pp }}"
                            class="tableInput w-25 numberInput">/PP <br>
                        Age(s) of dependent child(ren)<input type="text" name="insurance_employee_coverage_age"
                            value="{{ $caseInsurancePlan->insurance_employee_coverage_age }}"
                            class="tableInput w-25"><br>
                        Age of child(ren) incapable of self-support<input type="text"
                            name="insurance_employee_coverage_self_age"
                            value="{{ $caseInsurancePlan->insurance_employee_coverage_self_age }}"
                            class="tableInput w-25"><br>
                        <div class='d-flex align-items-baseline'>
                            Have you ever had a needs analysis completed?
                            <div class="dropdown">
                                <select id="insurance_analysis" name="insurance_analysis" class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_analysis == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_analysis == 'no' ? 'selected' : '' }}>No</option>
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
                                    <option value="yes" {{ $caseInsurancePlan->federal == 'yes' ? 'selected' : '' }}>
                                        Yes
                                    </option>
                                    <option value="no" {{ $caseInsurancePlan->federal == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>


                            </div>
                            Type of plan
                            <div class="dropdown">
                                <select id="plan_type" name="plan_type" class="form-select">
                                    <option value="self"
                                        {{ $caseInsurancePlan->plan_type == 'self' ? 'selected' : '' }}>
                                        Self</option>
                                    <option value="self+1"
                                        {{ $caseInsurancePlan->plan_type == 'self+1' ? 'selected' : '' }}>
                                        Self+1</option>
                                    <option value="self+family"
                                        {{ $caseInsurancePlan->plan_type == 'self+family' ? 'selected' : '' }}>Self+Family
                                    </option>
                                </select>


                            </div>
                            FEHB premium $<input type="text" name="premium"
                                value="{{ $caseInsurancePlan->premium }}" class="tableInput numberInput"
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
                                    <option value="yes"
                                        {{ $caseInsurancePlan->coverage == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ $caseInsurancePlan->coverage == 'no' ? 'selected' : '' }}>
                                        No</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>
                            Will the employee keep FEHB in retirement?
                            <div class="dropdown">
                                <select id="coverage_retirement" name="coverage_retirement" class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->coverage_retirement == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->coverage_retirement == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>


                            </div>
                        </div>
                        <br>
                        <div class='d-flex align-items-baseline'>
                            Will any family members be dependent on FEHB coverage in retirement?
                            <div class="dropdown">
                                <select id="coverage_retirement_dependent" name="coverage_retirement_dependent"
                                    class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->coverage_retirement_dependent == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->coverage_retirement_dependent == 'no' ? 'selected' : '' }}>
                                        No</option>
                                </select>


                            </div>
                        </div>
                    </td>
                    <td>
                        If No, what other coverage does the employee intend to have for Health Insurance in retirement?
                        <input type="text" name="coverage_retirement_insurance"
                            value="{{ $caseInsurancePlan->coverage_retirement_insurance }}"
                            class="tableInput w-25"><br><br>
                        Why? <input type="text" name="coverage_retirement_insurance_why"
                            value="{{ $caseInsurancePlan->coverage_retirement_insurance_why }}"
                            class="tableInput w-25"><br><br>
                        Who ?<input type="checkbox" name="coverage_retirement_insurance_spouse"
                            value="{{ $caseInsurancePlan->coverage_retirement_insurance_spouse }}" value="spouse"
                            class="mx-2" id="spu"> <label for="spu">Spouse</label>
                        <input type="checkbox" name="coverage_retirement_insurance_child"
                            value="{{ $caseInsurancePlan->coverage_retirement_insurance_child }}" value="child(ren)"
                            class="me-2" id="chl"> <label for="chl">Child(ren)</label>
                        <input type="checkbox" name="coverage_retirement_insurance_both"
                            value="{{ $caseInsurancePlan->coverage_retirement_insurance_both }}" value="both"
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
                                    <option value="yes" {{ $caseInsurancePlan->dental == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no" {{ $caseInsurancePlan->dental == 'no' ? 'selected' : '' }}>No
                                    </option>
                                </select>


                            </div>

                            <div class='d-flex align-items-baseline'>
                                Keeping in retirement?
                                <div class="dropdown">
                                    <select id="dental_retirement" name="dental_retirement" class="form-select">
                                        <option value="yes"
                                            {{ $caseInsurancePlan->dental_retirement == 'yes' ? 'selected' : '' }}>Yes
                                        </option>
                                        <option value="no"
                                            {{ $caseInsurancePlan->dental_retirement == 'no' ? 'selected' : '' }}>No
                                        </option>
                                    </select>

                                </div>
                                Dental premium $<input type="text" name="dental_premium"
                                    value="{{ $caseInsurancePlan->dental_premium }}" class="tableInput numberInput"
                                    style="width: 18%;"> /PP
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
                            <option value="yes" {{ $caseInsurancePlan->vision == 'yes' ? 'selected' : '' }}>Yes
                            </option>
                            <option value="no" {{ $caseInsurancePlan->vision == 'no' ? 'selected' : '' }}>No
                            </option>
                        </select>



                    </div>
                    <div class='d-flex align-items-baseline'>
                        Keeping in retirement?
                        <div class="dropdown">
                            <select id="vision_retirement" name="vision_retirement" class="form-select">
                                <option value="yes"
                                    {{ $caseInsurancePlan->vision_retirement == 'yes' ? 'selected' : '' }}>Yes</option>
                                <option value="no"
                                    {{ $caseInsurancePlan->vision_retirement == 'no' ? 'selected' : '' }}>No</option>
                            </select>

                        </div>
                        Vision premium $<input type="text" name="vision_premium"
                            value="{{ $caseInsurancePlan->vision_premium }}" class="tableInput numberInput"
                            style="width: 18%;">/PP
                    </div>
                </div>

                </div>
            </td>
        </tr>

        <tr class="dataTr">
            <td colspan="12">FYI: If there is a combined premium for Dental and Vision, we will only be able
                to show the total cost. <span class="ms-2">Dental/Vision $</span><input type="text"
                    name="vision_total_cost" value="{{ $caseInsurancePlan->vision_total_cost }}"
                    class="tableInput w-25 numberInput">/PP
            </td>
        </tr>
        </table>
        </div>
        <div class="col-12">
            <table id="table-two" class="table">
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
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_program == 'yes' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_program == 'no' ? 'selected' : '' }}>No</option>
                                </select>



                            </div>
                            Purchase age <input type="number" name="insurance_age"
                                value="{{ $caseInsurancePlan->insurance_age }}" class="tableInput"
                                style="width: 18%;">Premium $<input type="text" name="insurance_purchase_premium"
                                value="{{ $caseInsurancePlan->insurance_purchase_premium }}"
                                class="tableInput numberInput" style="width: 18%;">/PP
                        </div>
                    </td>
                </tr>
                <tr class="dataTr">
                    <td colspan="12">
                        <div class="d-flex align-items-baseline">
                            Plans to keep in retirement?
                            <div class="dropdown">
                                <select id="insurance_program_retirement" name="insurance_program_retirement"
                                    class="form-select">
                                    <option value="yes"
                                        {{ $caseInsurancePlan->insurance_program_retirement == 'yes' ? 'selected' : '' }}>
                                        Yes</option>
                                    <option value="no"
                                        {{ $caseInsurancePlan->insurance_program_retirement == 'no' ? 'selected' : '' }}>
                                        No
                                    </option>
                                </select>


                            </div>
                            Plans to keep in retirement? <input type="text" name="insurance_program_plan"
                                value="{{ $caseInsurancePlan->insurance_program_plan }}" class="tableInput"
                                style="width: 15%;">Daily Benefit Amount<input type="text"
                                name="insurance_program_daily"
                                value="{{ $caseInsurancePlan->insurance_program_daily }}" class="tableInput"
                                style="width: 15%;">'day
                    </td>
        </div>
        </tr>
        <tr class="dataTr">
            <td colspan="12">
                <div class="d-flex align-items-baseline">

                    Purpose of coverage <input type="text" name="insurance_purpose_covergae"
                        value="{{ $caseInsurancePlan->insurance_purpose_covergae }}" class="tableInput"
                        style="width: 21%;">Inflation
                    protection
                    <div class="dropdown">


                        <select id="insurance_program_purpose" name="insurance_program_purpose" class="form-select">
                            <option value="ACI 4%"
                                {{ $caseInsurancePlan->insurance_program_purpose == 'ACI 4%' ? 'selected' : '' }}>ACI 4%
                            </option>
                            <option value="ACI 5%"
                                {{ $caseInsurancePlan->insurance_program_purpose == 'ACI 5%' ? 'selected' : '' }}>ACI 5%
                            </option>
                            <option value="FPO"
                                {{ $caseInsurancePlan->insurance_program_purpose == 'FPO' ? 'selected' : '' }}>FPO
                            </option>
                        </select>
                    </div>
                    Maximum Lifetime Benefit $<input type="text" id="max_lifetime"
                        value="{{ $caseInsurancePlan->max_lifetime }}" name="max_lifetime"
                        class="tableInput numberInput" style="width: 15%;">
                </div>
            </td>
        </tr>
        <tr class="dataTr">
            <td>
                <textarea name="notes" class="w-100" cols="30" rows="10">{{ $caseInsurancePlan->notes }}</textarea>
            </td>
        </tr>
        </table>
        </div>
        </div>
    </form>

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        
        function validateMinValue(input) {
            const srsAgeError = document.getElementById('srsAgeError');
        var minValue = 63;
        if (parseInt(input.value) < minValue && input.value !== "") {
            srsAgeError.style.display = 'block';
        } else {
            srsAgeError.style.display = 'none';
        }
}
    </script>

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

        function calculateRetirmentAge() {
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

        $("#fedForm").submit(function(event) {
            event.preventDefault();

            $("button[type=submit]").prop('disabled', true);
            jQuery.ajax({
                url: "{{ route('fed-case.update', $case->id) }}",
                type: 'put',
                data: jQuery('#fedForm').serializeArray(),
                dataType: 'json',
                success: function(response) {
                    $("button[type=submit]").prop('disabled', false);
                    if (response["status"] == true) {
                        window.location.href = "{{ route('fed-case.index') }}";
                        $("#name").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");

                        $("#dob").removeClass('is-invalid').siblings('p')
                            .removeClass('invalid-feedback').html("");


                    } else {
                        var errors = response['errors'];
                        if (errors['name']) {
                            $("#name").addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(errors['name']);
                        } else {
                            $("#name").removeClass('is-invalid').siblings('p')
                                .removeClass('invalid-feedback').html("");

                        }

                        if (errors['dob']) {
                            $("#dob").addClass('is-invalid').siblings('p')
                                .addClass('invalid-feedback').html(errors['dob']);
                        } else {
                            $("#dob").removeClass('is-invalid').siblings('p')
                                .removeClass('invalid-feedback').html("");

                        }
                        if (errors.retirement_system) {
                            $('#retirementSystemError').text(errors.retirement_system[0]);
                        } else {
                            $('#retirementSystemError').text('');
                        }

                        if (errors.employee_type) {
                            $('#employeeTypeError').text(errors.employee_type[0]);
                        } else {
                            $('#employeeTypeError').text('');
                        }

                        if (errors['lscd']) {
                            $("#PEDError").html(errors['lscd']);
                        } else {
                            $("#PEDError").html("");
                        }

                        if (errors['rscd']) {
                            $("#PEDError").html(errors['rscd']);
                        } else {
                            $("#PEDError").html("");
                        }

                        if (errors['scd']) {
                            $("#PEDError").html(errors['scd']);
                        } else {
                            $("#PEDError").html("");
                        }

                        if (errors['retirement_type']) {
                            $("#retirementTypeError").html(errors['retirement_type']);
                        } else {
                            $("#retirementTypeError").html("");
                        }

                        if (errors['retirement_type_date']) {
                            $("#retirementtypedateError").html(errors['retirement_type_date']);
                        } else {
                            $("#retirementtypedateError").html("");
                        }
                    }


                },
                error: function(jqXHR, exception) {
                    console.log("something went wrong");
                    // window.location.href="{{ route('fed-case.index') }}";
                }
            })
        });
    </script>



@endsection
