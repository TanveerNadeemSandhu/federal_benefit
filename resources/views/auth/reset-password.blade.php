
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>Reset Password | Fed Benefit Analyzer</title>
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
                    
                    <form method="POST" action="{{ route('password.store') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                        <input id="email" hidden type="email" name="email" value="{{ $request->email }}">
                        <div class='row'>
                            <div class='col-sm-12 col-md-d col-lg-12'>
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
                        </div>

                        <div class='row'>
                            <div class='col-sm-12 col-md-d col-lg-12'>
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
                        </div>



                        
                        <div class='row'>
                            <button type="submit"
                                class="rectangle-button mt-1 d-flex align-items-center justify-content-center"
                                id="register-button" data-spinning-button>
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"
                                    style="display: none;"></span>
                                    {{ __('Reset Password') }}
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</body>


