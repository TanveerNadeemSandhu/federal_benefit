<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .headBlue
        {
            color: blue;
            text-align: center;
        }
        .textCenter
        {
            text-align: center;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div>
        <h1 class="mb-0 headBlue">My Federal Retirement</h1>
        <h1 class="mb-0 headBlue">Benefits Planning Report</h1>
        <p class="textCenter">Printed ({{ \Carbon\Carbon::now()->format('d M Y') }})</p>
        {{-- <h1 class="mb-0 newClass">{{ $title }}</h1> --}}
        <p class="textCenter" style="padding-top: 60px;">For</p>
        <h1 class="mb-0 headBlue">{{$fedCase->name}}</h1>
        {{-- <h1 class="mb-0">{{ $content }}</h1> --}}
        <p class="textCenter" style="padding-top: 150px;">Presented by</p>
        <p class="textCenter">{{$userData->first_name}} {{$userData->last_name}}</p>
        <p class="textCenter">{{$profile->company_name}}</p>
        @if($profile->image)
            <img alt="Image" style="width: 200px; height: auto;" src="{{ public_path('upload/profile/' . $profile->image) }}">
        @else
            <img alt="Image" style="width: 200px; height: auto;" src="{{ public_path('images/profile/default-profile-image.JPG') }}">
        @endif
        <p class="textCenter">{{$profile->address}}</p>
        <p class="textCenter">{{$profile->phone_1}}</p>
        <p class="textCenter">{{$userData->email}}</p>
    </div>
    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">DISCLAIMER</h4>
        @if($profile->statement)
            <p class="textCenter">{{$profile->statement}}</p>
        @else
            <p class="textCenter">
                This report illustrates estimates of cost and benefits for the Civil Service Retirement System (CSRS) 
                and/or the Federal Employees Retirement System (FERS), Federal Employees Group Life Insurance (FEGLI), 
                Federal Employees Health Benefits Program (FEHB), Long Term Care (LTC) Insurance, Social Security System benefits, 
                and the Thrift Savings Plan (TSP). Some estimates are based on assumptions, which may affect the results, and may 
                differ from actual experience. Since future costs and benefits cannot be estimated with absolute certainty, you should 
                not base your financial decisions solely on the estimates of this report, and it is recommended to consult with your 
                personnel office or the Office of Personnel Management (OPM), Retirement Information Office 1-888-767-6738. {{$profile->company_name}} 
                cannot provide retirement analysis and decision information to you. The analysis is provided 'AS IS' without warranties of 
                any kind (including the implied warranties of merchantability and fitness for a particular purpose). No oral or written 
                information or advice provided by {{$profile->company_name}} and its agents or employees shall create a warranty of any kind regarding 
                this analysis, and you may not rely upon such information or advice. Neither {{$profile->company_name}} nor anyone else who has been 
                involved in the creation, production, or delivery of this analysis shall be liable for any direct, indirect, consequential, 
                or incidental damages (including, but not limited to, damages for loss of business or personal profits, business or personal 
                interruption, and loss of business or personal information) arising from the use of (or inability to use) this analysis.
            </p>
        @endif
    </div>
    <div class="page-break"></div>
    <div id="rscd" data-value="{{ $fedCase->rscd }}"></div>
    <div>
        <h4 class="textCenter">Summary of Federal Retirement Benefits for</h4>
        <h3 class="textCenter">{{$fedCase->name}}</h3>
        <h6 class="textCenter">DOB: ({{$fedCase->dob}}) AGE: ({{$fedCase->age}})</h6>
        <table border="1" style="border-collapse: collapse; margin-right: 50px; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th colspan="2" class="headBlue">EMPLOYMENT (Today)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Service Computation Date</td>
                    <td>{{$fedCase->rscd}}</td>
                </tr>
                <tr>
                    <td>Annual Salary</td>
                    <td>{{$fedCase->salary_1}}</td>
                </tr>
                <tr>
                    <td>Hourly Salary</td>
                    <td>(Hourly Salary)</td>
                </tr>
                <tr>
                    <td>Annual Salary Increase (Est.)</td>
                    <td>1.3%</td>
                </tr>
                <tr>
                    <td>Creditable Service</td>
                    <td>{{$todayYOS}}</td>
                </tr>
                <tr>
                    <td>Sick Leave</td>
                    <td>(X) years
                        (X) months
                        (X) days</td>
                </tr>
                <tr>
                    <td>Annual Leave</td>
                    <td>(X) years
                        (X) months
                        (X) days</td>
                </tr>
            </tbody>
        </table>
        <table border="1" style="border-collapse: collapse; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th  colspan="2" class="headBlue">AT RETIREMENT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Planned Retirement Date</td>
                    <td>{{$fedCase->retirement_type_date}}</td>
                </tr>
                <tr>
                    <td>Annual Salary at retirement</td>
                    <td>(Salary)</td>
                </tr>
                <tr>
                    <td>High-3 Average Salary</td>
                    <td>{{$fedCase->highThree->value}}</td>
                </tr>
                <tr>
                    <td>Average COLA Increase (Est.)</td>
                    <td>3.0%</td>
                </tr>
                <tr>
                    <td>Creditable Service</td>
                    <td>{{$fedCase->yosDollar->age}}</td>
                </tr>
                <tr>
                    <td>Sick Leave</td>
                    <td>{{$fedCase->yosDollar->sick_leaves}}</td>
                </tr>
                <tr>
                    <td>Annual Leave</td>
                    <td>{{$fedCase->yosDollar->annual_leaves}}</td>
                </tr>
            </tbody>
        </table>
        <p class="textCenter">You are retiring under the @if($fedCase->retirement_system == 'csrs' || $fedCase->retirement_system == 'csrs_offset') RETIREMENT SYSTEM-CSRS @else RETIREMENT SYSTEM-FERS @endif system
            under the rules of a @if($fedCase->retirement_type == 'fully_eligible_disability') Fully Eligible @else RETIREMENT TYPE @endif retirement 
            as a {{$fedCase->employee_type}} retiree.</p>
    </div>
    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">DAY ONE FINANCIAL OVERVIEW</h4>
        <h6 class="textCenter">These projected numbers compare the monthly income and expenses</h6>
        <h6 class="textCenter">immediately before and after your retirement (minus any TSP income).</h6>
        <p class="headBlue" style="margin-right: 50px;">LAST DAY OF EMPLOYMENT</p><p class="headBlue">FIRST DAY OF RETIREMENT</p>
        <h3 class="textCenter">INCOME</h3>
        <table border="1" style="border-collapse: collapse; margin-right: 50px; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th>Salary</th>
                    <th>$</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    
        <table border="1" style="border-collapse: collapse; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th>$</th>
                    <th>Pension Annuity</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td>Survivor Benefit or Social Security</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Total</td>
                </tr>
            </tbody>
        </table>

        <h3 class="textCenter">EXPENSES</h3>
        <table border="1" style="border-collapse: collapse; margin-right: 50px; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th>FERS Retirement</th>
                    <th>$</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>TSP - Traditional</td>
                    <td></td>
                </tr>
                <tr>
                    <td>TSP - Roth</td>
                    <td></td>
                </tr>
                <tr>
                    <td>TSP - Catch Up (ROTH)</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Social Security</td>
                    <td></td>
                </tr>
                <tr>
                    <td>TAX - Federal Withholding</td>
                    <td></td>
                </tr>
                <tr>
                    <td>TAX - State Withholding</td>
                    <td></td>
                </tr>
                <tr>
                    <td>FEGLI (all)</td>
                    <td></td>
                </tr>
                <tr>
                    <td>FEHB</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Dental</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Vision</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Long-Term Care</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Medicare</td>
                    <td></td>
                </tr>
                <tr>
                    <td>OTHER</td>
                    <td></td>
                </tr>
                <tr>
                    <td>TOTAL</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    
        <table border="1" style="border-collapse: collapse; display: inline-table; width: 300px; padding-top: 100px;">
            <thead>
                <tr>
                    <th>$</th>
                    <th>Early Retirement Age Penalty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td>Unpaid Redeposit</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Unpaid Deposit</td>
                </tr>
                <tr>
                    <td></td>
                    <td>(%) Survivor Benefit</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>TAX - Federal Withholding</td>
                </tr>
                <tr>
                    <td></td>
                    <td>TAX - State Withholding</td>
                </tr>
                <tr>
                    <td></td>
                    <td>FEGLI (all)</td>
                </tr>
                <tr>
                    <td></td>
                    <td>FEHB</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Dental</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Vision</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Long-Term Care</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Medicare</td>
                </tr>
                <tr>
                    <td></td>
                    <td>OTHER</td>
                </tr>
                <tr>
                    <td></td>
                    <td>TOTAL</td>
                </tr>
            </tbody>
        </table>
        <h2 class="headBlue">NET DIFFERENCE IN RETIREMENT DAY ONE</h2>
        <h2>$(Total Income - Total Expenses)</h2>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">RETIREMENT FINANCIAL OVERVIEW</h4>
        <h6 class="textCenter">These numbers use your information to project years into the future</h6>
        <h6 class="textCenter">(minus TSP income).</h6>
        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>AGE IN RETIREMENT</th>
                    <th>YEARLY INCOME</th>
                    <th>YEARLY EXPENSES</th>
                    <th>DIFFERENCE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(Age of first year of Retirement)</td>
                    <td>(ALL INCOME ADDED TOGETHER)</td>
                    <td>ALL EXPENSES ADDED TOGETHER</td>
                    <td>DIFFERENCE - Negatives with a “-” before it and in red</td>
                </tr>
                @foreach($ageCount as $key => $age)
                <tr>
                    <td>{{$age}}</td>
                    <td>{{ number_format($srsArray[$key] + $ssArray[$key] + $pensionArray[$key] )}}</td>
                    <td>{{ number_format($premiums[$key] + $SurvivorBenefitArray[$key] + $fegliAmountArray[$key] )}}</td>
                    @php
                        $income = $srsArray[$key] + $ssArray[$key] + $pensionArray[$key];
                        $expenses = $premiums[$key] + $SurvivorBenefitArray[$key] + $fegliAmountArray[$key];
                        $total = $income - $expenses;  
                    @endphp
                    @if($total < 0)<td style="color: red">{{ number_format($total )}}</td> @else <td>{{ number_format($total )}}</td> @endif 
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">DELAYED RETIREMENT</h4>
        <h6 class="textCenter">What would your income look like if you were to delay your retirement?</h6>
        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>AGE AT RETIREMENT</th>
                    <th>PENSION</th>
                    <th>SPECIAL RETIREMENT SUPPLEMENT</th>
                    <th>SOCIAL SECURITY</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(PENSION AMOUNT AT RETIREMENT)</td>
                    <td>(SRS AMOUNT AT RETIREMENT)</td>
                    <td>(SOCIAL SECURITY AMOUNT AT RETIREMENT)</td>
                    <td>(TOTAL)</td>
                </tr>
                @foreach($ageCount as $key => $age)
                <tr>
                    <td>{{$age}}</td>
                    <td>{{ number_format($pensionArray[$key] )}}</td>
                    <td>{{ number_format($srsArray[$key] )}}</td>
                    <td>{{ number_format($ssArray[$key] )}}</td>
                    <td>{{ number_format($srsArray[$key] + $ssArray[$key] + $pensionArray[$key] )}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">THRIFT SAVINGS PLAN</h4>
        <h6 class="textCenter">What does your TSP look like today compared to the first day of retirement?</h6>
        <h3 style="color: blue;">YOUR TSP TODAY</h3>
        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>TRADITIONAL</th>
                    <th>ROTH</th>
                    <th>TOTAL TSP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$fedCase->TSP->contribute_pp}}</td>
                    <td>(Amount in Roth)</td>
                    <td>(Total TSP)</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>G</th>
                    <th>F</th>
                    <th>C</th>
                    <th>S</th>
                    <th>I</th>
                    <th>L FUND</th>
                    <th>Mutual Funds</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(Total G)</td>
                    <td>(Total F)</td>
                    <td>(Total C)</td>
                    <td>(Total S)</td>
                    <td>(Total I)</td>
                    <td>(Total L FUND)</td>
                    <td>(Total Mutual Funds)</td>
                    <td>(Total TSP)</td>
                </tr>
                <tr>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>100%</td>
                </tr>
            </tbody>
        </table>

        <h3 style="color: blue;">YOUR TSP - RETIREMENT DAY 1</h3>
        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>TRADITIONAL</th>
                    <th>ROTH</th>
                    <th>TOTAL TSP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(Amount in Traditional)</td>
                    <td>(Amount in Roth)</td>
                    <td>(Total TSP)</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>G</th>
                    <th>F</th>
                    <th>C</th>
                    <th>S</th>
                    <th>I</th>
                    <th>L FUND</th>
                    <th>Mutual Funds</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(Total G)</td>
                    <td>(Total F)</td>
                    <td>(Total C)</td>
                    <td>(Total S)</td>
                    <td>(Total I)</td>
                    <td>(Total L FUND)</td>
                    <td>(Total Mutual Funds)</td>
                    <td>(Total TSP)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">THRIFT SAVINGS PLAN</h4>
        <h6 class="textCenter">What does your TSP look like from the first day of retirement if you never took money out to use it?</h6>
        <h6 class="textCenter">We assume the following rate increases per year: </h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>G</th>
                    <th>F</th>
                    <th>C</th>
                    <th>S</th>
                    <th>I</th>
                    <th>L FUND</th>
                    <th>Mutual Funds</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                    <td>(Annual Increase)</td>
                </tr>
                <tr>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                    <td>(% of contribution)</td>
                </tr>
            </tbody>
        </table>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>G</th>
                    <th>F</th>
                    <th>C</th>
                    <th>S</th>
                    <th>I</th>
                    <th>L FUND</th>
                    <th>Mutual Funds</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(TOTAL AT THIS AGE)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(AMONT AT THIS AGE)</td>
                    <td>(TOTAL)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">RETIREMENT INCOME</h4>
        <h6 class="textCenter">Let’s break down your retirement income.</h6>
        <h6 class="textCenter">FERS COLA = 1.7%   SS COLA = 1.9%</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>FERS Annuity</th>
                    <th>FERS SUPPLEMENT</th>
                    <th>SOCIAL SECURITY</th>
                    <th>TOTAL</th>
                    <th>ANNUAL CHANGE</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(PENSION 1ST YEAR OF RETIREMENT)</td>
                    <td>(SRS 1ST YEAR OF RETIREMENT)</td>
                    <td>(SS 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                    <td>(CHANGE 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">RETIREMENT EXPENSES</h4>
        <h6 class="textCenter">Let’s break down your retirement expenses.</h6>
        <h6 class="textCenter">FEHB INCREASE = 1.7%   MEDICARE PART B INCREASE = 4%</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center;">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>FEHB</th>
                    <th>MEDICARE PART B</th>
                    <th>FEGLI</th>
                    <th>SURVIVOR’S BENEFITS</th>
                    <th>LONG-TERM CARE</th>
                    <th>DENTAL/VISION</th>
                    <th>FEDERAL & STATE TAX</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(FEHB 1ST YEAR OF RETIREMENT)</td>
                    <td>(MEDICARE 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL FEGLI 1ST YEAR OF RETIREMENT)</td>
                    <td>(SB 1ST YEAR OF RETIREMENT)</td>
                    <td>(LTC 1ST YEAR OF RETIREMENT)</td>
                    <td>(D/V 1ST YEAR OF RETIREMENT)</td>
                    <td>(FED TAX 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">FEGLI</h4>
        <h6 class="textCenter">Let’s break down your FEGLI Life Insurance expenses.</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center;">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>BASIC OPTION</th>
                    <th>OPTION A</th>
                    <th>OPTION B</th>
                    <th>OPTION C</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(BASIC YEAR OF RETIREMENT)</td>
                    <td>(OPTION A 1ST YEAR OF RETIREMENT)</td>
                    <td>(OPTION B 1ST YEAR OF RETIREMENT)</td>
                    <td>(OPTION C 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">DENTAL & VISION</h4>
        <h6 class="textCenter">Let’s break down your DENTAL & VISION expenses.</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center;">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>DENTAL</th>
                    <th>VISION</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(DENTAL YEAR OF RETIREMENT)</td>
                    <td>(VISION 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">LONG-TERM CARE</h4>
        <h6 class="textCenter">Let’s break down your LONG-TERM CARE expenses.</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center;">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>LTC COST</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(DENTAL 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h4 class="textCenter">FEDERAL & STATE TAX</h4>
        <h6 class="textCenter">Let’s break down your TAX expenses.</h6>

        <table border="1" style="border-collapse: collapse; display: inline-table; padding-top: 100px; text-align:center;">
            <thead>
                <tr>
                    <th>AGE</th>
                    <th>FEDERAL</th>
                    <th>STATE</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>(AGE AT RETIREMENT)</td>
                    <td>(FEDERAL TAX 1ST YEAR OF RETIREMENT)</td>
                    <td>(STATE TAX 1ST YEAR OF RETIREMENT)</td>
                    <td>(TOTAL 1ST YEAR OF RETIREMENT)</td>
                </tr>
                <tr>
                    <td>(+1)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                    <td>(AMOUNT AT THIS AGE)</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>