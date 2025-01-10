
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <title>Email Verification | Fed Benefit Analyzer</title>
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/global.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/signup.css')}}" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap"
    />

  </head>

  <body>
    <div class="signup">
      <img class="signup-child" alt="" src="{{ asset('images/signup/rectangle-1@2x.png')}}" />
      <div class="row m-0">
        <div class="group-div col-md-7 col-lg-6 col-sm-10">
          <div class="hi-there-parent">
            <b class="hi-there">You are one step away!</b>


            </div>
            <br />
            <div class="rectangle-parent">
                <div class="input-group mb-3">
                  <span class="" id="basic-addon1"><img class="iconlylight-outlinemessage" alt=""
                      src="{{ asset('images/signin/iconlylightoutlinemessage.svg')}}" /></span>
                  <input id="password" type="password" class="form-control group-child  @error('email') is-invalid @enderror" placeholder="Please check your email and verify your account." aria-label="Password"
                    aria-describedby="basic-addon1"  readonly>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                  </div>
                  <div class="row">
                    <div class="col-6">
                      <form id="logout-form" action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" href="{{route('logout')}}" class="rectangle-button text-decoration-none" style='width: 35%;height: 38px;background: linear-gradient(219.34deg, #0570e0, #022181);text-align:center;'>Login</button>
                    </form>
                    </div>
                    <div class="col-6">
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                                <button class="rectangle-button text-decoration-none" type="submit" style='width: 35%;height: 38px;background: linear-gradient(219.34deg, #0570e0, #022181);text-align:center;'>Resend Email</button>
                        </form>
                    </div>
                  </div>
                    
              </div>

        </div>
      </div>

      </div>
    </div>
  </body>
</html>

