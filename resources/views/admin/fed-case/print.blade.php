<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}" />
  <link rel="stylesheet" href="{{ asset('css/present.css')}}" />
  <link rel="stylesheet" href="{{ asset('css/layout.css')}}" />
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

    <nav class="dashboard-nav-list navpre">
      <div class="group">
        <label class="label">Retirement date:</label>
        <input type="date" class="inputData" value="{{$fedCase->retirement_type_date}}">
      </div>
      <div class="group">
        <label class="label">Retirement age:</label>
        <input type="text" class="inputData" value="{{$fedCase->retirement_type_age}}">
      </div>
      <div class="group">
        <label class="label">High3:</label>
        <input type="text" class="inputData" value="${{number_format($highThree->value, 0)}}">
      </div>
      <div class="group">
        <label class="label">SS-Take at FEGLI:</label>
        <input type="text" class="inputData" value="62">
      </div>

      <div class="form-group">
        <label class="label">Basic @$102,000 :</label><br>
        <span class="text-white">%</span>
        <input type="number" class="inputData" value="100" style="width: 4rem;">
        <input type="text" class="inputData" value="$123">
      </div>
      <div class="group">
        <label class="label">Option A: </label>
        <input type="text" class="inputData" value="$123">
      </div>

      <div class="form-group">
        <label class="label">Option B :</label><br>
        <span class="text-white">%</span>
        <input type="number" class="inputData" value="100" style="width: 4rem;">
        <input type="text" class="inputData" value="$123">
      </div>
      <div class="form-group">
        <label class="label">Option C:</label><br>
        <span class="text-white">%</span>
        <input type="number" class="inputData" value="100" style="width: 4rem;">
        <input type="text" class="inputData" value="$123">
      </div>
      <label class="label">Survivor benefits:</label>
      <div class="form-group">
        <span class="text-white">%</span>
        <input type="number" class="inputData" value="100" style="width: 4rem;">
        <input type="text" class="inputData" value="$123">
      </div>
      <div class="group">
        <label class="label">FEHB: </label>
        <input type="text" class="inputData" value="$123">
      </div>
      <label class="label"><input type="checkbox" name="" id=""> TSP</label>
      <div class="form-group">
        <label class="label">Cash: </label>
        <input type="text" class="inputData" value="$123"><br>
        <label class="label">Installment: </label>
        <input type="text" class="inputData" value="$123"><br>
        <label class="label">Annuity: </label>
        <input type="text" class="inputData" value="$123">
      </div>
      <div class="form-group">
        <label class="label">Other: </label>
        <input type="text" class="inputData" value="$123">
      </div>
      <div class="form-group">
        <input type="text" class="inputData" placeholder="Snapshot title">
      </div>
      <textarea name="" id="" cols="10" rows="1"></textarea>
      <button>Save</button>
    </nav>
  </div>
  <div class='dashboard-app'>
    <div class='dashboard-content'>
      <div class='row justify-content-between mb-4 printHeader'>

        <div class="col-sm-3 col">
          <div class="position-relative d-flex align-items-center ">
            <a href="#!" class="menu-toggle ps-3 pt-2"><i class="fas fa-bars"></i></a>
            <a href="javascript:void(0);" onclick="history.back();">
              <img class="group-icon me-2" alt="" src="{{ asset('images/accountagency/group-1442.svg')}}">
            </a>
          </div>
        </div>
        <div class="col-sm-4 col actionButton text-end">
          <button onclick="printPresent()"><img src="{{ asset('images/dashboard/black3.svg')}}" /> Print</button>
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
      <div class="row present justify-content-around">
        <div class="col-sm-6 col-md-4 graph incomeShow" id="graphSingleLine">
          <h4 class="text-center fw-bold">Income <i id="hideShow" class="fa fa-eye incomeShow"></i> </h4>
          <canvas id="singleline"></canvas>
          <h4 class="text-center fw-bold">$4000</h4>

        </div>
        <div class="col-sm-6 col-md-4 graph expensesShow" id="expensesLine">
          <h4 class="text-center fw-bold">Expenses <i id="expensesShow" class="fa fa-eye expensesShow"></i></h4>
          <canvas id="singlelinee"></canvas>
          <h4 class="text-center fw-bold">$5000</h4>
        </div>
        <div class="col-sm-6 col-md-4 graph cummulativeShow" id="cummulativeLine">
          <h4 class="text-center fw-bold">Cummulative <i id="cummulativeShow" class="fa fa-eye cummulativeShow"></i>
          </h4>
          <canvas id="multiline"></canvas>
          @if($totalDiffIncomeExpense >= 0)
            <h4 class="text-center fw-bold text-primary">+${{number_format($totalDiffIncomeExpense)}}</h4>
          @else
            <h4 class="text-center fw-bold text-danger">-${{number_format($totalDiffIncomeExpense)}}</h4>
          @endif
        </div>
        <br><br>
        <div class="col-sm-6 col-md-3">
          <h6 class="text-center fw-bold">Income each year</h6>
          <p class="d-flex justify-content-between fw-bold"><span>FERS Pension</span><span>${{number_format($pensionAmount ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Spec. Ret. Sup.</span><span>${{number_format($srsAmount ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Social Security</span><span>${{number_format($ssAmount ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>TSP</span><span>${{number_format($tspCalculation->totalTSPCalculate ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Other</span><span>$0</span></p>
          <hr class="fw-bold">
          <p class="d-flex justify-content-between fw-bold"><span>Total</span><span>$ {{number_format($pensionAmount + $srsAmount + $ssAmount + $tspCalculation->totalTSPCalculate)}}</span></p>
        </div>
        <div class="col-sm-6 col-md-3">
          <h6 class="text-center fw-bold">Expenses each year</h6>
          <p class="d-flex justify-content-between fw-bold"><span>FEHB</span><span>${{number_format($premiumAmount ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Dental/Vision</span><span>${{number_format($dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Servivor's Ben</span><span>${{number_format($survivorBenefitAmount ?? 0, 0)}}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>FEGLI</span><span>${{ number_format($fegliAmountTotal ?? 0, 0) }}</span></p>
          <p class="d-flex justify-content-between fw-bold"><span>Long-Term Care</span><span>${{number_format($totalFLTCIPSum)}}</span></p>
          <hr class="fw-bold">
          <p class="d-flex justify-content-between fw-bold"><span>Total</span><span>$ {{number_format($premiumAmount + $dentalAndVision->dentalPremiumAmount + $dentalAndVision->visionPremiumAmount + $fegliAmountTotal)}}</span></p>
        </div>
        <div class="col-sm-6 col-md-4 graph tspShow" id="tspLine">
          <h4 class="text-center fw-bold">TSP <i id="tspShow" class="fa fa-eye tspShow"></i></h4>
          <canvas id="singlelinet"></canvas>
          <h4 class="text-center fw-bold">Total in TSP: $5000</h4>

        </div>
      </div>

    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<!-- //Charts  -->
<script>
const ctx = document.getElementById('singleline');
var datasrs = {!! json_encode($srsArray) !!};
var datass = {!! json_encode($ssArray) !!};
var datapension = {!! json_encode($pensionArray) !!};
var label = {!! json_encode($ageCount) !!};

var dataSrSData = {
  label: "SRS",
  data: datasrs,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'black'
};
var dataSSData = {
  label: "SS",
  data: datass,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'yellow'
};
var dataPensionData = {
  label: "PENSION",
  data: datapension,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'blue'
};

var speedData = {
  labels: label,
  datasets: [dataSrSData, dataSSData, dataPensionData]
};

var chartOptions = {
  legend: {
    display: true,
    position: 'top',
    labels: {
      boxWidth: 80,
      fontColor: 'black'
    }
  }
};

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
  const ctx1 = document.getElementById('singlelinee');
var dataFehb = {!! json_encode($premiums) !!}; 
var dataSBP = {!! json_encode($SurvivorBenefitArray) !!};
var FEGLI = {!! json_encode($fegliAmountArray) !!};
var label = {!! json_encode($ageCount) !!};
// var FEGLI = ['400000', '500000', '600000', '700000', '800000', '900000', '1000000', '1100000', '1200000','1300000'];
// var label = [55, 60, 65, 70, 75, 80, 85, 90, 95, 100];

var dataFehbData = {
  label: "FEHB",
  data: dataFehb,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'black'
};
var dataSBPData = {
  label: "SBP",
  data: dataSBP,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'yellow'
};
var FEGLIData = {
  label: "FEGLI",
  data: FEGLI,
  lineTension: 0,
  fill: 'start',
  backgroundColor: 'blue'
};

var speedData = {
  labels: label,
  datasets: [dataFehbData, dataSBPData, FEGLIData]
};

var chartOptions = {
  legend: {
    display: true,
    position: 'top',
    labels: {
      boxWidth: 80,
      fontColor: 'black'
    }
  }
};

var lineChart = new Chart(ctx1, {
  type: 'line',
  data: speedData,
  options: chartOptions
});

  const ctxt = document.getElementById('singlelinet');
  var data = {!! json_encode($tspCalculationTotalArray) !!};
  var label = {!! json_encode($ageCount) !!};
  new Chart(ctxt, {
    type: 'line',
    data: {
      labels: label,
      datasets: [{
        label: 'Income',
        data: data,
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
  // Double line
  var speedCanvas = document.getElementById("multiline");


  var dataFirst = {
    label: "Expenses",
    data: {!! json_encode($totalExpensesArray) !!},
    lineTension: 0,
    fill: false,
    borderColor: 'red'
  };

  var dataSecond = {
    label: "Income",
    data: {!! json_encode($totalIncomeArray) !!},
    lineTension: 0,
    fill: false,
    borderColor: 'blue'
  };

  var speedData = {
    labels: {!! json_encode($ageCount) !!},
    datasets: [dataFirst, dataSecond]
  };

  var chartOptions = {
    legend: {
      display: true,
      position: 'top',
      labels: {
        boxWidth: 80,
        fontColor: 'black'
      }
    }
  };

  var lineChart = new Chart(speedCanvas, {
    type: 'line',
    data: speedData,
    options: chartOptions
  });
</script>
<!-- //End  -->
<script>
  const mobileScreen = window.matchMedia("(max-width: 990px )");
  $(document).ready(function () {
    $(".dashboard-nav-dropdown-toggle").click(function () {
      $(this).closest(".dashboard-nav-dropdown")
        .toggleClass("show")
        .find(".dashboard-nav-dropdown")
        .removeClass("show");
      $(this).parent()
        .siblings()
        .removeClass("show");
    });
    $(".menu-toggle").click(function () {
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
  window.print();
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
  $(document).ready(function () {
    $("#hideShow").click(function () {
      showHideMap('incomeShow', 'incomeHide', $('#graphSingleLine'), $(this))
    });
    $("#expensesShow").click(function () {
      showHideMap('expensesShow', 'expensesHide', $('#expensesLine'), $(this))
    });
    $("#cummulativeShow").click(function () {
      showHideMap('cummulativeShow', 'cummulativeHide', $('#cummulativeLine'), $(this))
    });
    $("#tspShow").click(function () {
      showHideMap('tspShow', 'tspHide', $('#tspLine'), $(this))
    });


  });
</script>


</html>