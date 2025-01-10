<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>Signup | Fed Benefit Analyzer</title>
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/signup.css') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" />
</head>

<body>
    <div class="signup">
        <img class="signup-child" alt="" src="{{ asset('images/signup/rectangle-1@2x.png') }}" />
        <div class="row m-0">
            <div class="group-div col-md-7 col-lg-6 col-sm-10">
                <div class="hi-there-parent">
                    <b class="hi-there">Welcome!</b>
                    <div class="welcome-back">Create your new account</div>
                    <form id="registerForm" method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class='row'>
                            <div class='col-sm-12 col-md-d col-lg-6'>
                                <div class="rectangle-parent">
                                    <div class="input-group mb-2">
                                        <span class="" id="basic-addon2">
                                            <img style='object-fit:contain;' class="iconlylight-outlinemessage"
                                                alt="text1"
                                                src="{{ asset('images/signup/combinedshape.svg') }}" />
                                        </span>
                                        <input type="text" class="form-control group-child" placeholder="First name"
                                            aria-label="First Name" aria-describedby="basic-addon2" name="first_name"
                                            value="{{ old('first_name') }}">

                                    </div>
                                    @error('first_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="error" id="first_name-error"></div>
                            </div>

                            <div class='col-sm-12 col-md-d col-lg-6'>
                                <div class="rectangle-parent">
                                    <div class="input-group mb-2">
                                        <span class="" id="basic-addon2">
                                            <img style='object-fit:contain;' class="iconlylight-outlinemessage"
                                                alt="text2"
                                                src="{{ asset('images/signup/combinedshape.svg') }}" />
                                        </span>
                                        <input type="text2" class="form-control group-child" placeholder="Last name"
                                            aria-label="Last Name" aria-describedby="basic-addon6" name="last_name"
                                            value="{{ old('last_name') }}">

                                    </div>
                                    @error('last_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="error" id="last_name-error"></div>
                            </div>

                        </div>
                        <div class="rectangle-parent">
                            <div class="input-group mb-2">
                                <span class="" id="basic-addon1">
                                    <img class="iconlylight-outlinemessage" alt="envelope"
                                        src="{{ asset('images/signup/iconlylightoutlinemessage.svg') }}" />
                                </span>
                                <input type="email" class="form-control group-child" placeholder="Email Address"
                                    aria-label="Email Address" aria-describedby="basic-addon1" name="email"
                                    value="{{ old('email') }}">
                            </div>
                            @error('email')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="error" id="email-error"></div>
                        <div class='row'>
                            <div class='col-sm-12 col-md-d col-lg-6'>
                                <div class="rectangle-parent">
                                    <div class="input-group mb-2">
                                        <span class="" id="basic-addon1"><img class="iconlylight-outlinemessage"
                                                alt="pass1"
                                                src="{{ asset('images/signup/iconlylightoutlinepassword.svg') }}" /></span>
                                        <input id="password" type="password" class="form-control group-child"
                                            placeholder="Password" aria-label="Password" aria-describedby="basic-addon1"
                                            name="password">

                                    </div>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="error" id="password-error"></div>
                            </div>
                            <div class='col-sm-12 col-md-d col-lg-6'>
                                <div class="rectangle-parent">
                                    <div class="input-group mb-2">
                                        <span class="" id="basic-addon1"><img
                                                class="iconlylight-outlinemessage" alt="pass2"
                                                src="{{ asset('images/signup/iconlylightoutlinepassword.svg') }}" /></span>
                                        <input id="password-confirm" type="password" class="form-control group-child"
                                            placeholder="Verify Password" aria-label="Verify Password"
                                            aria-describedby="basic-addon1" name="password_confirmation">

                                    </div>
                                    @error('password_confirmation')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="error" id="password-confirm-error"></div>
                            </div>

                            <button type="submit"
                                class="rectangle-button mt-1 d-flex align-items-center justify-content-center"
                                id="register-button" data-spinning-button>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"
                                    style="display: none;"></span>
                                Register
                            </button>
                    </form>

                    <div class="already-have-an-container">
                        <span class="already-have-an">Already have an account? </span>
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <b class="login">
                                Login
                            </b>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>

</html>
