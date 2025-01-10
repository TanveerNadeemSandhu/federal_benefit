
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>Send password | Fed Benefit Analyzer</title>
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap"
    />
  </head>

  <body>
    <div class="signup">
        <img class="signup-child" alt="" src="{{ asset('images/signup/rectangle-1@2x.png') }}" />
        <div class="row m-0">
            <div class="group-div col-md-7 col-lg-6 col-sm-10">
                <div class="hi-there-parent">
                    <b class="hi-there">{{ __('Reset Password') }}</b>
                    <div class="welcome-back">Enter your register email to reset password</div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="rectangle-parent">
                            <div class="input-group mb-2">
                                <span class="" id="basic-addon1">
                                    <img class="iconlylight-outlinemessage" alt="envelope"
                                        src="{{ asset('images/signup/iconlylightoutlinemessage.svg') }}" />
                                </span>
                                <input type="email" class="form-control group-child @error('email') is-invalid @enderror" placeholder="Email Address"
                                    aria-label="Email Address" aria-describedby="basic-addon1" id="email" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="error" id="email-error"></div>

                        
                        <div class='row'>
                            <button type="submit"
                                class="rectangle-button mt-1 d-flex align-items-center justify-content-center"
                                id="register-button" data-spinning-button>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"
                                    style="display: none;"></span>
                                    {{ __('Send Password Reset Link') }}
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>

