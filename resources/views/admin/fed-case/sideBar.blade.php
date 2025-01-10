<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/present.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oxygen:wght@300;400;700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=PingFang SC:wght@200;400&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&amp;display=swap">
    <title> Present | Fed Benifit Anaylzer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css">
    <!-- //Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            .incomeHide {
                display: none !important;
            }

            .expensesHide {
                display: none !important;
            }

            .cummulativeHide {
                display: none !important;
            }

            .tspHide {
                display: none !important;
            }
        }
    </style>
</head>
<div class='dashboard'>
    <div class="dashboard-nav">
        <a href="#!" class="menu-toggle ps-3 pt-3"><i class="fas fa-bars"></i></a>
        <header>
            <a href="#" class="brand-logo"><img class="logo-full-colour" alt=""
                    src="/images/dashboard/logo--full-colour.svg"><span class="logoname">FED BENEFIT</span>
            </a>
        </header>
        <form method="POST" action="{{ route('fed-case.sideBar', ['fedCase' => $fedCase]) }}">
            @csrf
            <nav class="dashboard-nav-list navpre">
                <div class="group">
                    <label class="label">Retirement date:</label>
                    <input type="date" class="inputData" id="retirement_type_date" name="retirement_type_date" onchange="calculateRetirementAge(); calculateYoSDollar()" value="{{ $retirement_type_date }}">
                </div>
                    <input type="date" class="inputData" value="{{ $fedCase->rscd }}" id="rscd" hidden>
                    <input type="date" class="inputData" value="{{ $fedCase->dob }}" id="dob" hidden>
                <div class="group">
                    <label class="label">Retirement age:</label>
                    <input type="text" class="inputData" name="retirement_type_age" value="{{ $retirement_type_age }}"  readonly id="retirement_type_age">
                </div>
                <div class="group">
                    <label class="label">High3:</label>
                    <input type="text" class="inputData" name="highThree"
                        value="${{ number_format($highThree, 0) }}">
                </div>
                <div class="group">
                    <label class="label">YOS($):</label>
                    <input type="text" class="inputData" name="yosDollar" value="{{ $yosDollar}}" readonly id="yosDollar">
                </div>
                <div class="group">
                    <label class="label">SS-Take at FEGLI:</label>
                    <input type="text" class="inputData" name="fegli" value="${{ number_format($fegli, 0) }}">
                </div>

                <div class="form-group">
                    <label class="label">Basic @$102,000 :</label><br>
                    <span class="text-white">%</span>
                    <input type="number" class="inputData" value="100" style="width: 4rem;">
                    @if ($fedCase->FEGLI)
                        <input type="text" class="inputData" name="fegliBasic"
                            value="${{ number_format($fedCase->FEGLI->basic, 0) }}">
                    @else
                        <input type="text" class="inputData" value="$0">
                    @endif
                </div>
                <div class="group">
                    <label class="label">Option A: </label>
                    @if ($fedCase->FEGLI)
                        <input type="text" class="inputData" name="fegliOptA"
                            value="${{ number_format($fedCase->FEGLI->optionA, 0) }}">
                    @else
                        <input type="text" class="inputData" value="$0">
                    @endif
                </div>

                <div class="form-group">
                    <label class="label">Option B :</label><br>
                    <span class="text-white">%</span>
                    <input type="number" class="inputData" value="100" style="width: 4rem;">
                    @if ($fedCase->FEGLI)
                        <input type="text" class="inputData" name="fegliOptB"
                            value="${{ number_format($fedCase->FEGLI->optionB, 0) }}">
                    @else
                        <input type="text" class="inputData" value="$0">
                    @endif
                </div>
                <div class="form-group">
                    <label class="label">Option C:</label><br>
                    <span class="text-white">%</span>
                    <input type="number" class="inputData" value="100" style="width: 4rem;">
                    @if ($fedCase->FEGLI)
                        <input type="text" class="inputData" name="fegliOptC"
                            value="${{ number_format($fedCase->FEGLI->optionC, 0) }}">
                    @else
                        <input type="text" class="inputData" value="$0">
                    @endif
                </div>
                <label class="label">Survivor benefits:</label>
                <div class="form-group">
                    <span class="text-white">%</span>
                    <input type="number" class="inputData" value="100" style="width: 4rem;">
                    <input type="text" class="inputData" name="sb" value="${{ number_format($SBP, 0) }}">
                </div>
                <div class="group">
                    <label class="label">FEHB: </label>
                    <input type="text" class="inputData" name="fehb"
                        value="${{ number_format($fehbVP, 0) }}">
                </div>
                <label class="label"><input type="checkbox" name="" id=""> TSP</label>
                <div class="form-group">
                    <label class="label">Cash: </label>
                    <input type="text" class="inputData" value="$0"><br>
                    <label class="label">Installment: </label>
                    <input type="text" class="inputData" value="$0"><br>
                    <label class="label">Annuity: </label>
                    <input type="text" class="inputData" value="$0">
                </div>
                <div class="form-group">
                    <label class="label">Other: </label>
                    <input type="text" class="inputData" value="$0">
                </div>
                <button>Save</button>
            </nav>
        </form>
    </div>
    <div class='dashboard-app'>
        <div class='dashboard-content'>
            <div class='row justify-content-between mb-4 printHeader'>

                <div class="col-sm-3 col">
                    <div class="position-relative d-flex align-items-center ">
                        <a href="#!" class="menu-toggle ps-3 pt-2"><i class="fas fa-bars"></i></a>
                        <a href="javascript:void(0);" onclick="history.back();">
                            <img class="group-icon me-2" alt=""
                                src="{{ asset('images/accountagency/group-1442.svg') }}">
                        </a>
                    </div>
                </div>
                <div class="col-sm-4 col actionButton text-end">
                    <button onclick="printPresent()"><img src="{{ asset('images/dashboard/black3.svg') }}" />
                        Print</button>
                    <!-- <button><img src="./images/dashboard/vector.svg" />Present</button> -->
                </div>
                <!-- <div class="col-sm-3 col text-end">
          <span class="position-relative">
            <img class="noti-icon" alt="" src="./images/accountagency/notification-1@2x.png">
            <span class="noti-number">1</span>
          </span>
          <button class="case"> Save</button>
        </div> -->
            </div>
            <!-- section one  -->
            {{-- <div id="dataDisplay" style="margin-top: 20px;">
        <h3>Selected Data:</h3>
        <p id="dataPoint"></p>
    </div> --}}
            <div class="row present justify-content-around">
                <div class="col-sm-6 col-md-4 graph incomeShow" id="graphSingleLine">
                    <h4 class="text-center fw-bold">Income <i id="hideShow" class="fa fa-eye incomeShow"></i> </h4>
                    <canvas id="singleline"></canvas>
                    <h4 class="text-center fw-bold">$<span id="totalIncomeId1">0</span></h4>

                </div>
                <div class="col-sm-6 col-md-4 graph expensesShow" id="expensesLine">
                    <h4 class="text-center fw-bold">Expenses <i id="expensesShow" class="fa fa-eye expensesShow"></i>
                    </h4>
                    <canvas id="singlelinee"></canvas>
                    <h4 class="text-center fw-bold">$<span id='totalExpensesId1'>0</span></h4>
                </div>
                <div class="col-sm-6 col-md-4 graph cummulativeShow" id="cummulativeLine">
                    <h4 class="text-center fw-bold">Cummulative <i id="cummulativeShow"
                            class="fa fa-eye cummulativeShow"></i>
                    </h4>
                    <canvas id="multiline"></canvas>
                    {{-- @if ($totalDiffIncomeExpense >= 0) --}}
                    <h4 class="text-center fw-bold"><span id="totalId">$0</span></h4>
                    {{-- @else --}}
                    {{-- <h4 class="text-center fw-bold">-${{ number_format(abs($totalDiffIncomeExpense)) }}</h4> --}}
                    {{-- @endif --}}
                </div>
                <br><br>
                <div class="col-sm-6 col-md-3">
                    <h6 class="text-center fw-bold">Income each year</h6>
                    <p class="d-flex justify-content-between fw-bold"><span>FERS Pension</span><span>$<span
                                id="pensionAmountID">0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>Spec. Ret. Sup.</span><span>$<span
                                id="srsAmountID">0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>Social Security</span><span>$<span
                                id="ssAmountID">0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>TSP</span><span>$<span
                                id="tspAmountID">0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>Other</span><span>$0</span></p>
                    <hr class="fw-bold">
                    <p class="d-flex justify-content-between fw-bold"><span>Total</span><span>$<span
                                id="totalIncomeId">0</span></span></p>
                </div>
                <div class="col-sm-6 col-md-3">
                    <h6 class="text-center fw-bold">Expenses each year</h6>
                    <p class="d-flex justify-content-between fw-bold"><span>FEHB</span><span>$<span
                                id='fehbAmountID'>0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold">
                        <span>Dental/Vision</span>${{ number_format($dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount, 0) }}</span>
                    </p>
                    <p class="d-flex justify-content-between fw-bold"><span>Servivor's Ben</span><span>$<span
                                id='sbpAmountID'>0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>FEGLI</span><span>$<span
                                id='fegliAmountID'>0</span></span></p>
                    <p class="d-flex justify-content-between fw-bold"><span>Long-Term
                            Care</span><span>${{ number_format($totalFLTCIPSum) }}</span></p>
                    <hr class="fw-bold">
                    <p class="d-flex justify-content-between fw-bold"><span>Total</span><span>$<span
                                id='totalExpensesId'>0</span></span></p>
                </div>
                <div class="col-sm-6 col-md-4 graph tspShow" id="tspLine">
                    <h4 class="text-center fw-bold">TSP <i id="tspShow" class="fa fa-eye tspShow"></i></h4>
                    <canvas id="singlelinet"></canvas>
                    <h4 class="text-center fw-bold">Total in TSP: $<span id="tspGraphAmountID">0</span></h4>

                </div>
            </div>

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- //Charts  -->

<script>

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
    
    
    
    
    </script>


<script>
    const ctx = document.getElementById('singleline').getContext('2d');

    // Data arrays from backend
    var datasrs = {!! json_encode($srsArray) !!};
    var datass = {!! json_encode($ssArray) !!};
    var datapension = {!! json_encode($pensionArray) !!};
    var dataTSP = {!! json_encode($tspCalculationTotalArray) !!};
    // Function to round values to 2 decimal places
    function roundValues(arr) {
        return arr.map(function(value) {
            return Math.round(value); // Round to 2 decimal places
        });
    }
    var label = {!! json_encode($ageCount) !!};

    // Round the values for each array
    var roundedSRSData = roundValues(datasrs);
    var roundedSSData = roundValues(datass);
    var roundedPensionData = roundValues(datapension);
    var roundedTSPData = roundValues(dataTSP);

    // Define each dataset for the graph
    var dataSrSData = {
        label: "SRS",
        data: roundedSRSData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(0, 0, 0, 0.3)', // Adjust the transparency of black
        borderColor: 'black',
        borderWidth: 2
    };

    var dataSSData = {
        label: "SS",
        data: roundedSSData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(255, 255, 0, 0.3)', // Adjust transparency of yellow
        borderColor: 'yellow',
        borderWidth: 2
    };

    var dataPensionData = {
        label: "PENSION",
        data: roundedPensionData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(0, 0, 255, 0.3)', // Adjust transparency of blue
        borderColor: 'blue',
        borderWidth: 2
    };

    // Combine datasets and labels for the chart
    var speedData = {
        labels: label,
        datasets: [dataSrSData, dataSSData, dataPensionData]
    };

    // Chart options with Y-axis callback for adding dollar sign
    var chartOptions = {
        responsive: true,
        legend: {
            display: true,
            position: 'top',
            labels: {
                boxWidth: 80,
                fontColor: 'black'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value; // Add dollar sign to Y-axis values
                    }
                }
            }
        },
        onClick: function(evt) {
            var activePoints = lineChart.getElementsAtEventForMode(evt, 'nearest', {
                intersect: true
            }, false);
            if (activePoints.length > 0) {
                var firstPoint = activePoints[0];
                var index = firstPoint.index; // Get the index of the clicked point
                var age = label[index]; // Get the corresponding age from the label
                var srsValue = roundedSRSData[index]; // Get SRS value
                var ssValue = roundedSSData[index]; // Get SS value
                var pensionValue = roundedPensionData[index]; // Get Pension value
                var tspValue = roundedTSPData[index]; // Get TSP value
                var totalIncome = srsValue + ssValue + pensionValue + tspValue;
                // Show the selected data in the HTML div
                document.getElementById('pensionAmountID').innerText = pensionValue.toLocaleString();
                document.getElementById('srsAmountID').innerText = srsValue.toLocaleString();
                document.getElementById('ssAmountID').innerText = ssValue.toLocaleString();
                document.getElementById('tspAmountID').innerText = tspValue.toLocaleString();
                document.getElementById('totalIncomeId').innerText = totalIncome.toLocaleString();
                document.getElementById('totalIncomeId1').innerText = totalIncome.toLocaleString();
            }
        }
    };

    // Create the line chart
    var lineChart = new Chart(ctx, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });



    // new Chart(ctx, {
    //   type: 'line',
    //   data: {
    //     labels: label,
    //     datasets: [{
    //       label: 'Income',
    //       data: data,
    //       borderWidth: 1
    //     }]
    //   },
    //   options: {
    //     scales: {
    //       y: {
    //         beginAtZero: true
    //       }
    //     }
    //   }
    // });
    const ctx1 = document.getElementById('singlelinee').getContext('2d');

    // Data arrays from backend
    var dataFehb = {!! json_encode($premiums) !!};
    var dataSBP = {!! json_encode($SurvivorBenefitArray) !!};
    var FEGLI = {!! json_encode($fegliAmountArray) !!};
    // Function to round values to 2 decimal places
    function roundValues(arr) {
        return arr.map(function(value) {
            return Math.round(value); // Round to 2 decimal places
        });
    }
    var label = {!! json_encode($ageCount) !!};

    // Round the values for each array
    var roundedFEHBData = roundValues(dataFehb);
    var roundedSBPData = roundValues(dataSBP);
    var roundedFEGLIData = roundValues(FEGLI);

    // Define each dataset for the graph
    var dataFehbData = {
        label: "FEHB",
        data: roundedFEHBData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(0, 0, 0, 0.3)', // Adjust transparency of black
        borderColor: 'black',
        borderWidth: 2
    };

    var dataSBPData = {
        label: "SBP",
        data: roundedSBPData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(255, 255, 0, 0.3)', // Adjust transparency of yellow
        borderColor: 'yellow',
        borderWidth: 2
    };

    var FEGLIData = {
        label: "FEGLI",
        data: roundedFEGLIData,
        lineTension: 0,
        fill: 'start',
        backgroundColor: 'rgba(0, 0, 255, 0.3)', // Adjust transparency of blue
        borderColor: 'blue',
        borderWidth: 2
    };

    // Combine datasets and labels for the chart
    var speedData = {
        labels: label,
        datasets: [dataFehbData, dataSBPData, FEGLIData]
    };

    // Chart options with Y-axis callback for adding dollar sign
    var chartOptions = {
        responsive: true,
        legend: {
            display: true,
            position: 'top',
            labels: {
                boxWidth: 80,
                fontColor: 'black'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value; // Add dollar sign to Y-axis values
                    }
                }
            }
        },
        onClick: function(evt) {
            var activePoints = lineChart.getElementsAtEventForMode(evt, 'nearest', {
                intersect: true
            }, false);
            if (activePoints.length > 0) {
                var firstPoint = activePoints[0];
                var index = firstPoint.index; // Get the index of the clicked point
                var age = label[index]; // Get the corresponding age from the label
                var fehbValue = roundedFEHBData[index]; // Get FEHB value
                var sbpValue = roundedSBPData[index]; // Get SBP value
                var fegliValue = roundedFEGLIData[index]; // Get FEGLI value
                var totalExpenses = fehbValue + sbpValue + fegliValue;
                // Show the selected data in the HTML div
                document.getElementById('fehbAmountID').innerText = fehbValue.toLocaleString();
                document.getElementById('sbpAmountID').innerText = sbpValue.toLocaleString();
                document.getElementById('fegliAmountID').innerText = fegliValue.toLocaleString();
                document.getElementById('totalExpensesId').innerText = totalExpenses.toLocaleString();
                document.getElementById('totalExpensesId1').innerText = totalExpenses.toLocaleString();
            }
        }
    };

    // Create the line chart
    var lineChart = new Chart(ctx1, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });

    // First Line Chart (Single Line)
    const ctxt = document.getElementById('singlelinet').getContext('2d');
    var data = {!! json_encode($tspCalculationTotalArray) !!};
    // Function to round values to 2 decimal places
    function roundValues(arr) {
        return arr.map(function(value) {
            return Math.round(value); // Round to 2 decimal places
        });
    }
    var label = {!! json_encode($ageCount) !!};

    // Round the values for each array
    var roundedTSPData = roundValues(data);

    new Chart(ctxt, {
        type: 'line',
        data: {
            labels: label,
            datasets: [{
                label: 'Income',
                data: roundedTSPData,
                borderWidth: 2,
                borderColor: 'green', // Customize color
                backgroundColor: 'rgba(0, 255, 0, 0.2)', // Add transparency
                fill: 'start'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value; // Add dollar sign to Y-axis values
                        }
                    }
                }
            },
            onClick: function(evt) {
                var activePoints = lineChart.getElementsAtEventForMode(evt, 'nearest', {
                    intersect: true
                }, false);
                if (activePoints.length > 0) {
                    var firstPoint = activePoints[0];
                    var index = firstPoint.index; // Get the index of the clicked point
                    var age = label[index]; // Get the corresponding age from the label
                    var tspValue = roundedTSPData[index]; // Get TSP value
                    // Show the selected data in the HTML div
                    document.getElementById('tspGraphAmountID').innerText = tspValue.toLocaleString();
                }
            }
        }
    });

    // Second Line Chart (Double Line)
    var speedCanvas = document.getElementById("multiline").getContext('2d');
    var dataExpenses = {!! json_encode($totalExpensesArray) !!};
    var dataIncome = {!! json_encode($totalIncomeArray) !!};
    // Function to round values to 2 decimal places
    function roundValues(arr) {
        return arr.map(function(value) {
            return Math.round(value); // Round to 2 decimal places
        });
    }

    // Round the values for each array
    var roundedExpensesData = roundValues(dataExpenses);
    var roundedIncomeData = roundValues(dataIncome);

    // First dataset (Expenses)
    var dataFirst = {
        label: "Expenses",
        data: roundedExpensesData,
        lineTension: 0,
        fill: false,
        borderColor: 'red',
        borderWidth: 2
    };

    // Second dataset (Income)
    var dataSecond = {
        label: "Income",
        data: roundedIncomeData,
        lineTension: 0,
        fill: false,
        borderColor: 'blue',
        borderWidth: 2
    };

    // Combine datasets and labels for the second chart
    var speedData = {
        labels: {!! json_encode($ageCount) !!},
        datasets: [dataFirst, dataSecond]
    };

    // Chart options with Y-axis callback for adding dollar sign
    var chartOptions = {
        responsive: true,
        legend: {
            display: true,
            position: 'top',
            labels: {
                boxWidth: 80,
                fontColor: 'black'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value; // Add dollar sign to Y-axis values
                    }
                }
            }
        },
        onClick: function(evt) {
            var activePoints = lineChart.getElementsAtEventForMode(evt, 'nearest', {
                intersect: true
            }, false);
            if (activePoints.length > 0) {
                var firstPoint = activePoints[0];
                var index = firstPoint.index; // Get the index of the clicked point
                var age = label[index]; // Get the corresponding age from the label
                var expensesValue = roundedExpensesData[index]; // Get SRS value
                var incomeValue = roundedIncomeData[index]; // Get SS value
                var total = incomeValue - expensesValue;
                // Show the selected data in the HTML div
                // document.getElementById('expensesAmountID').innerText = expensesValue;
                // document.getElementById('incomeAmountID').innerText = incomeValue;
                document.getElementById('totalId').innerText = total.toLocaleString();
                // Check if total is greater than zero
                if (total > 0) {
                    totalId.innerText = '$' + Math.abs(total).toLocaleString(); // Show total
                    totalId.style.color = 'blue'; // Set color to blue
                }
                // Check if total is less than zero
                else if (total < 0) {
                    totalId.innerText = '-$' + Math.abs(total).toLocaleString(); // Show total with minus sign
                    totalId.style.color = 'red'; // Set color to red
                }
                // If total is exactly zero
                else {
                    totalId.innerText = total; // Show total as zero
                    totalId.style.color = 'black'; // Set default color (optional)
                }
            }
        }
    };

    // Create the second line chart (Multi-Line)
    var lineChart = new Chart(speedCanvas, {
        type: 'line',
        data: speedData,
        options: chartOptions
    });
</script>
<!-- //End  -->
<script>
    const mobileScreen = window.matchMedia("(max-width: 990px )");
    $(document).ready(function() {
        $(".dashboard-nav-dropdown-toggle").click(function() {
            $(this).closest(".dashboard-nav-dropdown")
                .toggleClass("show")
                .find(".dashboard-nav-dropdown")
                .removeClass("show");
            $(this).parent()
                .siblings()
                .removeClass("show");
        });
        $(".menu-toggle").click(function() {
            if (mobileScreen.matches) {
                $(".dashboard-nav").toggleClass("mobile-show");
            } else {
                $(".dashboard").toggleClass("dashboard-compact");
            }
        });
    });


    // Print 
    function printPresent() {
        //   alert('hello')
        // $('.dashboard-nav').css({
        //   'display':'block',
        //   'min-width': '292px',
        //   'background-color':'#1f263'  
        // })
        window.print();

    }
    //Show hide Icon function and grap from print screen 
    function showHideMap(incomeShow, incomeHide, graphId, targetClass) {
        var classCaptured = targetClass.attr('class');
        if (classCaptured == 'fa fa-eye ' + incomeShow) {
            targetClass.removeClass("fa fa-eye " + incomeShow);
            targetClass.addClass("fa fa-eye-slash " + incomeHide);
            graphId.addClass(incomeHide);
            graphId.removeClass(incomeShow);
        }
        if (classCaptured == 'fa fa-eye-slash ' + incomeHide) {
            targetClass.removeClass("fa fa-eye-slash " + incomeHide);
            targetClass.addClass("fa fa-eye " + incomeShow);
            graphId.addClass(incomeShow);
            graphId.removeClass(incomeHide);
        }
    }
    $(document).ready(function() {
        $("#hideShow").click(function() {
            showHideMap('incomeShow', 'incomeHide', $('#graphSingleLine'), $(this))
        });
        $("#expensesShow").click(function() {
            showHideMap('expensesShow', 'expensesHide', $('#expensesLine'), $(this))
        });
        $("#cummulativeShow").click(function() {
            showHideMap('cummulativeShow', 'cummulativeHide', $('#cummulativeLine'), $(this))
        });
        $("#tspShow").click(function() {
            showHideMap('tspShow', 'tspHide', $('#tspLine'), $(this))
        });


    });

</script>


</html>
