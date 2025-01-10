<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/accounttype.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/checkout.css')}}" />
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <title>Account Type | Fed Benefit Analyzer</title>

</head>

<body>

    <div class="container">
        @if ($error = Session::get('error'))
            <div class="alert alert-danger">
                <p>{{ $error }}</p>
            </div>
        @endif

        <form role="form" action="{{ route('admin.accountType') }}" method="post" class="require-validation"
            data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
            @csrf
            <div class="align-items-center row m-0 p-0">
                <img class="signup-child" alt="" src="{{ asset('images/accounttype/rectangle-1@2x.png')}}" />
                <div class="col-sm-12 col-md-9 col-lg-8 choose-account-type-box ">
                    <div class="mainbox row">
                        <input type="radio" name="rd" id="one" />
                        <input type="radio" name="rd" id="two" />
                        <input type="radio" name="rd" id="three" />
                        <input type="radio" name="rd" id="four" />
                        <label for="one" class="col-sm-12 col-md-6 col-lg-6 mb-2 checkValue">
                            <input type="radio" name="rd" value="agency" />
                            <div class="box first">
                                <div class="headercard">
                                    <div class="circle"></div>
                                    <div class="cardTag" name="account" value="84">$84/mo (billed annually)</div>
                                </div>
                                <div class="cardbody">
                                    <b class="agency-account" name="acount_type" value="agency">Agency Account:</b>
                                    <div class="group-container">
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode.svg')}}">
                                            <div class="create-unlimited-number" name="case" value="unlimited">
                                                Create unlimited number of cases.
                                            </div>
                                        </div>
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon1" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode1.svg')}}">
                                            <div class="unlimited-revisions" name="revision" value="unlimited">Unlimited
                                                Revisions.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </label>
                        <label for="two" class="col-sm-12 col-md-6 col-lg-6 mb-2 checkValue">
                            <input type="radio" name="rd" value="personal" />
                            <div class="box second">
                                <div class="headercard">
                                    <div class="circle"></div>
                                    <div class="cardTag">$100 (One time charge)</div>
                                </div>
                                <div class="cardbody">
                                    <b class="agency-account">Personal Account:</b>
                                    <div class="group-container">
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode.svg')}}">
                                            <div class="create-unlimited-number">
                                                Create one case
                                            </div>
                                        </div>
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon1" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode1.svg')}}">
                                            <div class="unlimited-revisions">Unlimited Revisions for 30 days.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </label>
                        <label for="three" class="col-sm-12 col-md-6 col-lg-6 mb-2 checkValue">
                            <input type="radio" name="rd" value="office" />
                            <div class="box third">
                                <div class="headercard">
                                    <div class="circle"></div>
                                    <div class="cardTag">Free</div>
                                </div>
                                <div class="cardbody">
                                    <b class="agency-account">Back Office Support:</b>
                                    <div class="group-container mb-1">
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode.svg')}}">
                                            <div class="create-unlimited-number">
                                                Be invited to agency and personal accounts.
                                            </div>
                                        </div>
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon1" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode1.svg')}}">
                                            <div class="unlimited-revisions"></div>Cannot create your own cases.
                                        </div>
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon1" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode1.svg')}}">
                                            <div class="unlimited-revisions"></div>Can create and edit cases after
                                            joining agency.
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="price" value="0" />
                            <input type="hidden" name="account_type" value="back_office" />
                            <input type="hidden" name="case" value="Cannot create" />
                            <input type="hidden" name="revision" value=" create and edit" />
                        </label>
                        <label for="four" class="col-sm-12 col-md-6 col-lg-6 mb-2 checkValue">
                            <input type="radio" name="rd" value="admin" />
                            <div class="box fourth">
                                <div class="headercard">
                                    <div class="circle"></div>
                                    <div class="cardTag">Free</div>
                                </div>
                                <div class="cardbody">
                                    <b class="agency-account">Admin Support:</b>
                                    <div class="group-container fourth">
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode.svg')}}">
                                            <div class="create-unlimited-number">
                                                Be invited to agency and personal accounts.
                                            </div>
                                        </div>
                                        <div class="create-unlimited-number">
                                            <img class="isolation-mode-icon1" alt=""
                                                src="{{ asset('images/accounttype/isolation-mode1.svg')}}">
                                            <div class="unlimited-revisions">Can view and print reports.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </label>
                    </div>
                </div>
                <div class="col-sm-12 col-md-3 col-lg-4 choose-account-type text-center">
                    <div class="formCheckout">
                        <h1 style="color: #1e1e1e;font-size: 20px;text-align: start;" class="title">CHECKOUT</h1>
                        <div class="forminput" style="display: none;">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control w-100 card-name"
                                    placeholder="Name on card:" aria-label="Name on card" name="card_name" required>

                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control w-100 card-number" placeholder="New CC#:"
                                    aria-label="New CC#:" name="card_number" id="card-number" required>

                                {{-- <input type="text" class="form-control w-100 card-number" placeholder="New CC#:" aria-label="New CC#:" name="card_number"required> --}}
                            </div>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control w-100 card-expiry-month"
                                    placeholder="Expiry Month:" aria-label="Expiry Month:" name="month" required
                                    oninput="this.value = this.value.slice(0, 2)">
                            </div>
                            <div class="input-group mb-3">
                                <div class="col-6 p-0 pe-4">
                                    <input type="text" class="form-control w-100  card-expiry-year"
                                        placeholder="Expiry Year:" aria-label="Expiry Year:" name="year" required
                                        oninput="this.value = this.value.slice(0, 4)">
                                </div>
                                <div class="col-6 p-0 pe-4">
                                    <input type="text" class="form-control card-cvc" placeholder="CSV:"
                                        aria-label="CSV" name="csv" required
                                        oninput="this.value = this.value.slice(0, 3)">
                                </div>

                            </div>
                        </div>

                        <div class="col-12 mt-1 d-flex flex-wrap">
                            <button type="submit" class="purchase col-8 col-sm-12 mt-3 align-items-center"
                                id="register-button">
                                Processed
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var alertError = document.querySelector('.alert.alert-danger');

                if (alertError) {
                    setTimeout(function() {
                        alertError.style.display = 'block'; // Change 'none' to 'block'
                    }, 2000); // 2000 milliseconds = 2 seconds
                }
            });


            $(function() {
                // Set default values for the card fields
                $('.card-number').val('4242 4242 4242 4242');
                $('.card-cvc').val('123');
                $('.card-expiry-month').val('12');
                $('.card-expiry-year').val('2028');
                $('.card-name').val('Test');


                $('.purchase').on('click', function(e) {
                    disableButton();

                    var $form = $(".require-validation");


                    $form.find('.error').addClass('hide');
                    if (!$form.data('cc-on-file')) {
                        e.preventDefault();
                        var publishableKey = $form.data('stripe-publishable-key');
                        Stripe.setPublishableKey(publishableKey);
                        Stripe.createToken({
                            number: $('.card-number').val(),
                            cvc: $('.card-cvc').val(),
                            exp_month: $('.card-expiry-month').val(),
                            exp_year: $('.card-expiry-year').val()
                        }, function(status, response) {
                            stripeResponseHandler(status, response, $form);
                        });
                    }
                });

                function disableButton() {
                    const registerButton = $("#register-button");
                    const spinner = $("#submit-loader"); // Add this line to select the loading spinner
                    registerButton.prop("disabled", true);
                    spinner.show(); // Show the loading spinner
                }

                function enableButton() {
                    const registerButton = $("#register-button");
                    const spinner = $("#submit-loader"); // Add this line to select the loading spinner
                    registerButton.prop("disabled", false);
                    spinner.hide(); // Hide the loading spinner
                }





                function stripeResponseHandler(status, response, $form) {
                    if (response.error) {
                        $('.error')
                            .removeClass('hide')
                            .find('.alert')
                            .text(response.error.message);
                    } else {
                        var token = response['id'];
                        $form.find('input[type=text]').empty();
                        $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                        $form.get(0).submit();
                    }
                }
            });
        </script>

        <script type="text/javascript">
            var title = $('.title');
            title.text('Please Select a Plain');
            title.css({
                'text-align': 'center'
            })
            $('.purchase').hide();

            $('.checkValue').click(function() {
                $('.purchase').css({
                    'display': 'flex'
                });
                title.css({
                    'text-align': 'start'
                })
                checker = $(this)[0].firstElementChild.value;
                if (checker === 'agency') {
                    $('.title').text('Checkout Agency account');
                    $('.forminput').show();
                    $('.purchase').html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" style="display: none;" id="submit-loader"></span> Purchase'
                        );
                    // Populate the hidden input fields for the agency
                    $('input[name="price"]').val("8400");
                    $('input[name="account_type"]').val("agency");
                    $('input[name="case"]').val("unlimited");
                    $('input[name="revision"]').val("unlimited");
                    $('input[name="stripeToken"]').val(
                        "pk_test_51O72rPKtqabKmNfdJ1jUdWl0rEuOtEIHJEMs3OZ1eYQU3USwc8YigTeYBk9SkLGByDe7U7r8eU4jazzv4lMnWaux00eAKC8nqI"
                        );


                }
                if (checker === 'personal') {
                    $('.title').text('Checkout Personal account');
                    $('.forminput').show();
                    $('.purchase').html(
                        ' <span class="spinner-border spinner-border-sm me-1" role="status" style="display: none;" id="submit-loader"></span> Purchase'
                        );

                    $('input[name="price"]').val("10000");
                    $('input[name="account_type"]').val("personal");
                    $('input[name="case"]').val("one case");
                    $('input[name="revision"]').val("30 days");
                }
                if (checker === 'office') {
                    $('.forminput').hide();
                    title.text('You selected Back Office Support free version');
                    $('.purchase').html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" style="display: none;" id="submit-loader"></span> Create this account type'
                        );
                    $('input[name="price"]').val("0");
                    $('input[name="account_type"]').val("office");
                    $('input[name="case"]').val("invited");
                    $('input[name="revision"]').val("create and edit");

                }
                if (checker === 'admin') {
                    $('.forminput').hide();
                    title.text('You selected Admin Support free version');
                    $('.purchase').html(
                        '<span class="spinner-border spinner-border-sm me-1" role="status" style="display: none;" id="submit-loader"></span> Create this account type'
                        );
                    $('input[name="price"]').val("0");
                    $('input[name="account_type"]').val("admin");
                    $('input[name="case"]').val("invited");
                    $('input[name="revision"]').val("view");

                }
            })
        </script>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Get the card number input element
                var cardNumberInput = $('#card-number');

                // Listen for input events (e.g., when the user types or pastes)
                cardNumberInput.on('input', function() {
                    // Remove any non-digit characters
                    var cardNumber = $(this).val().replace(/\D/g, '');

                    // Add a space after every 4 digits (if it's not the last 4 digits)
                    if (cardNumber.length > 4) {
                        cardNumber = cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');
                    }

                    // Limit the card number to a maximum of 19 characters
                    cardNumber = cardNumber.substring(0, 19);

                    // Update the input field's value
                    $(this).val(cardNumber);
                });
            });
        </script>
    </div>


</html>
