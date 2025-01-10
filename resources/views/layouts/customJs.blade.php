<script>
    $(document).ready(function() {
        var table = $('#table-demo').DataTable({
            searching: false,
            paging: false,
        });
    });

    $(document).ready(function() {
        var table = $('#users-share-list').DataTable({
            searching: false,
            paging: false,
            language: {
                emptyTable: "You haven't given access to anyone yet..."
            },
        });
    });

    $(document).ready(function() {
        var table = $('#agency-list').DataTable({
            searching: false,
            paging: false,
            language: {
                emptyTable: "You don't have access to any agency yet..."
            },
        });
    });
</script> 
 
 
 {{--  Function to calculate the sum of all number fields --}}
    <script>
        function calculateTotalNumber() {
            let total = 0;
            const numberInputs = document.querySelectorAll('.number-input');
            numberInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            // Display the total in the total number field
            const totalField = document.getElementById('total_amount');
            totalField.value = total;
        }

        // Add event listeners to all number input fields
        const numberInput = document.querySelectorAll('.number-input');
        numberInput.forEach(input => {
            input.addEventListener('input', calculateTotalNumber);
        });
    </script>
    {{-- End Function to calculate the sum of all number fields --}}

    {{-- Function to calculate the sum of all percentage fields --}}
    <script>
        function calculateTotalPercentage() {
            let total = 0;
            const percentageInputs = document.querySelectorAll('.percentage-input');
            percentageInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            // Display the total in the result field
            const resultField = document.getElementById('total_amount_percentage');
            resultField.value = total ;

            // Validate if the total is either 0% or 100%
            const errorMessage = document.getElementById('error-message');
            if (total !== 0 && total !== 100) {
                errorMessage.textContent = "Please check your value, it should be zero or hundred in total.";
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
            }
        }

        // Add event listeners to all percentage input fields
        const percentageInputs = document.querySelectorAll('.percentage-input');
        percentageInputs.forEach(input => {
            input.addEventListener('input', calculateTotalPercentage);
        });
    </script>
    {{-- End Function to calculate the sum of all percentage fields --}}


    {{-- script for tsp value --}}

    <script>
        function parseSalary(salary) {
            // Remove commas from the salary string and convert to a float
            return parseFloat(salary.replace(/,/g, ''));
        }
        
        document.getElementById('contribute_pp').addEventListener('input', function() {
            let salary = parseSalary(document.getElementById('salary_1').value); // Convert salary to number
            let contribution = parseFloat(this.value);
            
            if (!isNaN(salary) && salary > 0 && !isNaN(contribution)) {
                let percentage = (contribution / salary) * 100;
                document.getElementById('contribute_pp_percentage').value = percentage.toFixed(2);
            } else {
                document.getElementById('contribute_pp_percentage').value = ''; // Clear if invalid
            }
        });
        
        document.getElementById('contribute_pp_percentage').addEventListener('input', function() {
            let salary = parseSalary(document.getElementById('salary_1').value); // Convert salary to number
            let percentage = parseFloat(this.value);
            
            if (!isNaN(salary) && salary > 0 && !isNaN(percentage)) {
                let contribution = (percentage / 100) * salary;
                document.getElementById('contribute_pp').value = contribution.toFixed(2);
            } else {
                document.getElementById('contribute_pp').value = ''; // Clear if invalid
            }
        });
        
        document.getElementById('contribute_tsp_pp').addEventListener('input', function() {
            let salary = parseSalary(document.getElementById('salary_1').value); // Convert salary to number
            let contribution = parseFloat(this.value);
            
            if (!isNaN(salary) && salary > 0 && !isNaN(contribution)) {
                let percentage = (contribution / salary) * 100;
                document.getElementById('contribute_tsp_pp_percentage').value = percentage.toFixed(2);
            } else {
                document.getElementById('contribute_tsp_pp_percentage').value = ''; // Clear if invalid
            }
        });
        
        document.getElementById('contribute_tsp_pp_percentage').addEventListener('input', function() {
            let salary = parseSalary(document.getElementById('salary_1').value); // Convert salary to number
            let percentage = parseFloat(this.value);
            
            if (!isNaN(salary) && salary > 0 && !isNaN(percentage)) {
                let contribution = (percentage / 100) * salary;
                document.getElementById('contribute_tsp_pp').value = contribution.toFixed(2);
            } else {
                document.getElementById('contribute_tsp_pp').value = ''; // Clear if invalid
            }
        });
        
    </script>
    {{-- end script for tsp value --}}

    {{-- start script for saviour benefit input field --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const fersRadios = ['fers', 'ft', 'fr', 'frh'];
        const optionField = document.getElementById('survior_benefit_fers');
        const inputField = document.getElementById('survior_benefit_csrs');
        
        // Event listener for radio buttons
        document.querySelectorAll('input[name="retirement_system"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (fersRadios.includes(radio.id)) {
                    // Enable option field and disable input field
                    optionField.disabled = false;
                    inputField.disabled = true;
                } else {
                    // Disable option field and enable input field
                    optionField.disabled = true;
                    inputField.disabled = false;
                }
            });
        });
    
        // Validate input field for numbers between 1 and 55
        inputField.addEventListener('input', function () {
            if (this.value < 1) this.value = 1;
            if (this.value > 55) this.value = 55;
        });
    });
</script>
    {{-- end script for saviour benefit input field --}}
