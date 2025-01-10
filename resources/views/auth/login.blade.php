
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="initial-scale=1, width=device-width" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" />
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}" />
  <link rel="stylesheet" href="{{ asset('css/global.css')}}" />
  <link rel="stylesheet" href="{{ asset('css/signin.css')}}" />
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
  <title>Sigin in | Fed Benefit Analyzer</title>
</head>
<body>
  <div class="signin row">
    <div class="col-sm-12 col-md-6 col-lg-6 position-relative p-0">
      <img class="signin-child w-100" alt="" src="{{ asset('images/signin/rectangle-1@2x.png')}}" />
      <div class="signin-item w-100"></div>
      <div class="para">
        <b class="fed-benefit-analyzer">FED BENEFIT ANALYZER</b>
        <div class="the-best-analysis my-3">
        The best retirement analysis for federal employees.
        </div>
        <button class="rectangle- parent group-child">
          <b class="learn-more ">Learn More</b>
        </button>
      </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6 position-relative">
      @if(Session::has('success'))
          <div class="alert alert-success">
              {{Session::get('success')}}
          </div>
      @endif
      @if(Session::has('error'))
          <div class="alert alert-danger">
              {{Session::get('error')}}
          </div>
      @endif
      <div class="signin-inner">
        <div class="hello-again-parent">
          <b class="hello-again">HELLO !</b>
          <div class="welcome-back">Sign in to your account</div>
          <form method="POST" action="{{ route('login') }}">
            @csrf
          <div class="rectangle-group">
            <div class="input-group mb-3">
              <span class="iconlylight-outlinemessage" id="basic-addon1"><img class="" alt=""
                  src="{{ asset('images/signin/iconlylightoutlinemessage.svg')}}" /></span>
              <input id="email" type="email" class="form-control group-item  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email Address" aria-label="Email Address"
                aria-describedby="basic-addon1">
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            </div>
          </div>
          <div class="rectangle-container">
            <div class="input-group mb-3">
              <span class="iconlylight-outlinemessage" id="basic-addon1"><img class="" alt=""
                  src="{{ asset('images/signin/iconlylightoutlinepassword.svg')}}" /></span>
              <input id="password" type="password" class="form-control group-item  @error('password') is-invalid @enderror" name="password" placeholder="Password" aria-label="Password"
                aria-describedby="basic-addon1">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
          </div>

          <div class="rectangle-wrapper">
            <button type="submit"  class="login rectangle-button text-decoration-none">Login</button>
        </form>

            <p class="forgot-password text-end mt-3"><a  href="{{ route('password.request') }}" class="text-decoration-none">Forgot password</a></p>
          </div>
          <div class="dont-have-an-container">
            <span class="dont-have-an">Don’t have an account? </span>
            <a href="{{ route('register') }}" class="text-decoration-none"><b style="color:#022181;">Signup</b></a>
        </div>

          <div class="line-div"></div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

