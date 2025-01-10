<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            margin-top: 0 !important; /* Important to override Bootstrap's default styles */
        }
        h1 {
            margin-bottom: 30px;
            text-align: center;
            color: #343a40;
        }
        table {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: auto;
        }
        thead {
            background-color: #343a40;
            color: #ffffff;
        }
        tbody tr:hover {
            background-color: #f1f1f1;
        }
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #e9ecef;
        }
        .table-striped > tbody > tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        td, th {
            padding: 0; /* Padding ko 0 kar diya */
            margin: 0; /* Margin ko bhi 0 kar diya */
            vertical-align: top; /* Content ko top aligned kiya */
        }
    </style>
</head>
<body>
        <div class="container"style="margin:auto;with:50%;padding:10px;">
                <div class="row">
                    <h1>Here is all Calculations</h1>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Formula</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Federal Employee Years</td>
                                <td>{{$yosDollar->age}}</td>
                            </tr>
                            <tr>
                                <td>YOS($)</td>
                                <td>{{$yosDollar->value}}</td>
                            </tr>
        
                            <tr>
                                <td>YOS(E)</td>
                                <td>{{$yosE->age}}</td>
                            </tr>
                            <tr>
                                <td>YOS(E)</td>
                                <td>{{$yosE->value}}</td>
                            </tr>
                            
                            <tr>
                                <td>High-3</td>
                                <td>{{$highThree->value}}</td>
                            </tr>
        
                            <tr>
                                <td>First Year Pension</td>
                                <td>{{$pension->amount ?? ''}}</td>
                            </tr>
        
        
                            <tr>
                                <td>Insurance Cost Basic</td>
                                <td>{{$fegliInsuranceCost->basic ?? 'NA'}}</td>
                            </tr>
                            <tr>
                                <td>Insurance Cost A</td>
                                <td>{{$fegliInsuranceCost->optionA ?? 'NA'}}</td>
                            </tr>
                            <tr>
                                <td>Insurance Cost B</td>
                                <td>{{$fegliInsuranceCost->optionB ?? 'NA'}}</td>
                            </tr>
                            <tr>
                                <td>Insurance Cost C</td>
                                <td>{{$fegliInsuranceCost->optionC ?? 'NA'}}</td>
                            </tr>
        
                            <tr>
                                <td>SRS Value</td>
                                <td>{{$srsValue->amount ?? 'NA'}}</td>
                            </tr>
        
                            <tr>
                                <td>Dental Value</td>
                                <td>{{$dentalAndVisionValue->dentalPremiumAmount ?? 'NA'}}</td>
                            </tr>
        
                            <tr>
                                <td>Vision Value</td>
                                <td>{{$dentalAndVisionValue->visionPremiumAmount ?? 'NA'}}</td>
                            </tr>
        
                            <tr>
                                <td>FEHB VP value</td>
                                <td>{{$fehbVPValue->fehbPremiumAmount ?? 'NA'}}</td>
                            </tr>
        
                            <tr>
                                <td>FLTCIP yearly amount</td>
                                <td>{{$fltcipValue->yearlyPremiumAmount ?? 'NA'}}</td>
                            </tr>
                            
                            <tr>
                                <td>FLTCIP premium amount</td>
                                <td>{{$fltcipValue->insurancePurchasePremiumAmount ?? 'NA'}}</td>
                            </tr>

                            <tr>
                                <td>Survivor Benefit</td>
                                <td>{{$survivorBenefit->cost ?? 'NA'}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
        <div class="container" style="margin:auto;with:50%;padding:10px;">
                <div class="row">
                    <h2 class="text-center">Increment Table</h2>
                    <table class="table table-bordered table-striped mt-4">
                        <thead class="thead-dark">
                        <tr>
                            <th style="padding: 10px;">Age</th>
                            <th style="padding: 10px;">Year</th>
                            <th style="padding: 10px;">Dental Value</th>
                            <th style="padding: 10px;">Vision Value</th>
                            <th style="padding: 10px;">FEHBVP Value</th>
                            <th style="padding: 10px;">FLTCIP Value</th>
                        </tr>
                        </thead>
                        <tbody id="pensionTable">
                        </tbody>
                    </table>
                </div>

        </div>

   

    <!-- Bootstrap 5 JS and Popper.js CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>

    <script>
        const age = {{ $age }}; 
        let todayDateYear = {{ $todayDateYear }}; 
        let dentalPremiumAmount = {{ $dentalAndVisionValue->dentalPremiumAmount ?? 0}}; 
        let visionPremiumAmount = {{ $dentalAndVisionValue->visionPremiumAmount ?? 0}}; 
        let fehbPremiumAmount = {{ $fehbVPValue->fehbPremiumAmount ?? 0}}; 
        let pensionValue = {{ $fltcipValue->yearlyPremiumAmount ?? 0}};
        const incrementRate = 0.01; // 5% increment
        const fltcipIncrementRate = 0.05; // 5% increment
    
        function calculatePension() {
            const tableBody = document.getElementById('pensionTable');
            for (let year = age; year <= 90; year++) {
                // Create a new row
                const row = document.createElement('tr');
    
                // Create cells for year and pension value
                const yearCell = document.createElement('td');
                yearCell.textContent = year;
                row.appendChild(yearCell);
    
                const yearIncrementCell = document.createElement('td');
                const dentalPremiumAmountCell = document.createElement('td');
                const visionPremiumAmountCell = document.createElement('td');
                const fehbPremiumAmountCell = document.createElement('td');
                const pensionCell = document.createElement('td');
                yearIncrementCell.textContent = todayDateYear;
                dentalPremiumAmountCell.textContent = dentalPremiumAmount.toFixed(2);
                visionPremiumAmountCell.textContent = visionPremiumAmount.toFixed(2);
                fehbPremiumAmountCell.textContent = fehbPremiumAmount.toFixed(2);
                pensionCell.textContent = pensionValue.toFixed(2);
                row.appendChild(yearIncrementCell);
                row.appendChild(dentalPremiumAmountCell);
                row.appendChild(visionPremiumAmountCell);
                row.appendChild(fehbPremiumAmountCell);
                row.appendChild(pensionCell);
    
                // Append the row to the table
                tableBody.appendChild(row);
    
                // Increment pension value by 5%
                todayDateYear++;
                dentalPremiumAmount += dentalPremiumAmount * incrementRate;
                visionPremiumAmount += visionPremiumAmount * incrementRate;
                fehbPremiumAmount += fehbPremiumAmount * incrementRate;
                pensionValue += pensionValue * fltcipIncrementRate;
            }
        }
    
        // Calculate and display pension when the page loads
        document.addEventListener('DOMContentLoaded', calculatePension);
    </script>
</body>
</html>
