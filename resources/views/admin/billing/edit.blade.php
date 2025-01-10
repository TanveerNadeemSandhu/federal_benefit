@extends('layouts.app')
@section('title', 'Billing | Fed Benefit Anaylzer')


@section('content')


    <div class="row">
        <div class="col-12 position-relative">
            <img class="w-100 profilebanner position-relative" src="{{ asset('images/billing/frame-527@2x.png')}}" />
            <h1 class="bannerTitle">BILLING</h1>
        </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-success mt-2">
            <p>{{ $message }}</p>
        </div>
    @endif

    @if ($error = Session::get('error'))
        <div class="alert alert-danger mt-2">
            <p>{{ $error }}</p>
        </div>
    @endif

        <div class="col-sm-12 col-md-6 d-flex carddetails justify-content-between align-items-center">
            <div class="title">
                Current Credit Card:
            </div>
            @if ($subscription)
                <div class="cardNumber">
                    {{ $subscription->card_number }}
                </div>
            @else
                <div class="cardNumber">
                    *-*-*-*
                </div>
            @endif

        </div>
        <div class="col-sm-12 col-md-6 carddetails border-0  bg-transparent">
            <p class="title" style="color: #0451BB;">
                Account: {{ ucfirst($subscription->account_type) }}
            </p>
            <p class="title" style="color: #1E1E1E;">
             {{ \Carbon\Carbon::parse($subscription->date)->addMonth()->format('F d, Y') }}
            </p>
        </div>
    </div>
    <div class="row">
        <div
            class="col-12 mt-4 carddetails border-0 d-flex align-items-center justify-content-between bg-transparent">
            <div class="title" style="color: #1E1E1E;">
                Enter a new card details:
            </div>
            <button class="viewall" style="color: #1E1E1E;">
                View All
        </div>
        <form role="form" action="{{ route('billing.update') }}" method="post"
            class="col-sm-12 col-md-6 billingForm require-validation" data-cc-on-file="false"ne
            data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
            @method('put')
            @csrf
            <input type="text" class="form-control input" placeholder="Name on card:"
                aria-label="Name on card:" name="card_name" required>
                <input type="text" class="form-control input card-number" placeholder="Credit Card#:" aria-label="InvoicesNew CC#:" name="card_number" id="card-number" required>

                <input type="number" class="form-control input card-expiry-month" id="card-expiry-month" placeholder="Expiry Month:"
                aria-label="Expiry Month:" name="month" required oninput="this.value = this.value.slice(0, 2)">



            <input type="number" class="form-control input card-expiry-year" placeholder="Expiry Year:"
                aria-label="Year:" name="year" id="card-expiry-year" required oninput="this.value = this.value.slice(0, 4)">
            <input type="number" class="form-control input card-cvc" id="card-cvc" placeholder="CSV:" aria-label="CSV"
                name="csv" required oninput="this.value = this.value.slice(0, 3)">
                <div id="card-errors" style="color: red;"></div>

            <button class="save" type="submit">
                Save
            </button>
        </form>

        <div class="col-sm-12 col-md-6 pas_invoice">
            <p class="title">Past Invoices</p>
            @if($subscription)
            @foreach ($paymentHistory as $index => $history)
                <p class="details d-flex justify-content-between align-items-center">
                    <span>{{ \Carbon\Carbon::parse($history->payment_date)->format('F d, Y') }}</span>
                    <a href="#" data-toggle="modal" data-target="#myModal{{ $index }}">
                        <span><img src="{{ asset('images/billing/group-1430.svg')}}" alt=""></span>
                    </a>
                </p>
            @endforeach
            @endif
        </div>
        @if($paymentHistory)

        @foreach ($paymentHistory as $index => $history)
            <div class="modal fade" id="myModal{{ $index }}">
                <div class="modal-dialog modal-lg  bg-white">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Invoice Details</h4>
                            <button type="button" class="close border-0 bg-transparent fs-3" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="invoice-info">
                                        <p><strong>Price:</strong> ${{ $history['price']/100 }}</p>
                                        <p><strong>Card Name:</strong> {{ $history['card_name'] }}</p>
                                        <p><strong>Card Number:</strong> {{ $history['card_number'] }}</p>
                                        <p><strong>CSV:</strong> {{ $history['csv'] }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="invoice-info">
                                        <p><strong>Exp. Date:</strong>
                                            {{ $history['month'] }}/{{ $history['year'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="grantbutton"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @endif

        {{--  --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if the alert element exists
        var alert = document.querySelector('.alert.alert-success');

        // If the alert element exists, hide it after 2 seconds
        if (alert) {
            setTimeout(function() {
                alert.style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
        }

        var alertError = document.querySelector('.alert.alert-danger');

        // If the alert element exists, hide it after 2 seconds
        if (alertError) {
            setTimeout(function() {
                alertError.style.display = 'none';
            }, 2000); // 2000 milliseconds = 2 seconds
        }
    });
        $(document).ready(function () {
            // Get the card number input element
            var cardNumberInput = $('#card-number');

            // Listen for input events (e.g., when the user types or pastes)
            cardNumberInput.on('input', function () {
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
    @endsection

