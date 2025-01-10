<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="initial-scale=1, width=device-width" />
    <link rel="icon" href="{{ asset('images/dashboard/logo--full-colour.svg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/profile.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/billing.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/share.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/users.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/accountagency.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/caselist.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/message.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/layout.css')}}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Oxygen:wght@300;400;700&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=PingFang SC:wght@200;400&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&amp;display=swap">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    #table-case_filter {
        display: none;
    }
</style>
</head>

<body>
    <div class='dashboard'>
        <div class="dashboard-nav">
            <header>
               
                <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
                <a href="#" class="brand-logo"><img class="logo-full-colour" alt=""
                        src="{{ asset('images/dashboard/logo--full-colour.svg')}}"><span class="logoname">FED
                        BENEFIT</span></a>
            </header>
            <nav class="dashboard-nav-list">
                @if(auth()->user()->roles[0]->name==='super admin' || auth()->user()->roles[0]->name==='personal' || auth()->user()->roles[0]->name==='agency')
                <a href="{{route('cases')}}" class="dashboard-nav-item {{ Route::currentRouteName() == 'cases' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black10.svg')}}">
                    Case List
                </a>
                @endif
                 @if(auth()->user()->roles[0]->name==='back office' || auth()->user()->roles[0]->name==='support')
                <a href="{{route('agencies')}}" class="dashboard-nav-item {{ Route::currentRouteName() == 'agencies' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black10.svg')}}">
                    Agencies
                </a>
                @endif
                <a href="{{ route('profile') }}" class="dashboard-nav-item {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black1.svg')}}">
                    Profile
                </a>
                @if(auth()->user()->roles[0]->name==='agency' || auth()->user()->roles[0]->name==='personal')

                <a href="{{ route('billing') }}" class="dashboard-nav-item {{ Route::currentRouteName() == 'billing' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/group-19.svg')}}">
                    Billing
                </a>
                @endif
                @if(auth()->user()->roles[0]->name==='back office' || auth()->user()->roles[0]->name==='support' ||auth()->user()->roles[0]->name==='agency' || auth()->user()->roles[0]->name==='personal')

                <a href="{{route('share')}}" class="dashboard-nav-item {{ Route::currentRouteName() == 'share' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/group-24.svg')}}">
                    @if(auth()->user()->roles[0]->name==='support'|| auth()->user()->roles[0]->name==='back office')
                    Get Access
                    @else
                        Share Access
                    @endif
                </a>
                @endif

                <a href="{{ route('chat') }}" class="dashboard-nav-item {{ Route::currentRouteName() == 'chat' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black2.svg')}}">
                    Message
                </a>
                @if(auth()->user()->roles[0]->name==='agency' || auth()->user()->roles[0]->name==='personal')

                <a href={{ route('share_agency') }} class="dashboard-nav-item {{ Route::currentRouteName() == 'share_agency' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black9.svg')}}">
                   Shared Users
                </a>

                @endif

                @if(auth()->user()->roles[0]->name==='super admin')

                <a href={{ route('users') }} class="dashboard-nav-item {{ Route::currentRouteName() == 'users' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black9.svg')}}">
                    All Users
                </a>

                @endif


                <a href="{{ route('logout') }}" class="dashboard-nav-item position-absolute bottom-0 w-100">

                  <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black8.svg')}}" />


                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>

            </nav>
        </div>
        <div class='dashboard-app'>
            <header class='dashboard-toolbar row justify-content-end'>
                <div class="col-4 text-end">
                    @if(auth()->user()->profile)
                        <img class="dashboard-agency-account-child" style="width: 40px; height: 40px; object-fit: cover;border-radius:50%" alt="" src="{{ asset('upload/profile/' . auth()->user()->profile) }}" />
                    @else
                        <img class="dashboard-agency-account-child" style="width: 40px; height: 40px; object-fit: cover" alt="" src="https://t4.ftcdn.net/jpg/03/59/58/91/360_F_359589186_JDLl8dIWoBNf1iqEkHxhUeeOulx0wOC5.jpg" />
                    @endif
                </div>
            </header>
            <div class='dashboard-content mt-4'>
                @yield('content')
            </div>
        </div>
    </div>
    {{-- //Profile script --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
     <!-- DataTable -->

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
     <script>
         $(document).ready(function() {
             var table = $('#table-demo').DataTable({
                 searching: false,
                 paging: false,
             });
         });
     </script>
     {{-- Message  --}}
     <script>
        jQuery(document).ready(function() {

            $(".chat-list a").click(function() {
                $(".chatbox").addClass('showbox');
                return false;
            });

            $(".chat-icon").click(function() {
                $(".chatbox").removeClass('showbox');
            });


        });
    </script>
    <!--//Js priview image-->
    <script>

    function changeImage (uploadImage, previewImage){
        document.getElementById(uploadImage).addEventListener('change', function (event) {
            var input = event.target;
            var preview = document.getElementById(previewImage);

            var reader = new FileReader();

            reader.onload = function () {
                preview.src = reader.result;
            };

            if (input.files && input.files[0]) {
                reader.readAsDataURL(input.files[0]);
                const alert1 = document.querySelector('.alert.alert-success');
                alert1.style.display = 'flex';
                alert1.innerHTML = '<p>Please click save for image update</p>'; // Use innerHTML to set HTML content

                setTimeout(function() {
                    alert1.style.display = 'none'; // Use alert1 instead of alert
                }, 2000);
            }


        });
    }

        changeImage ('profileImageUpload', 'profileImage')
        changeImage ('bannerImageUpload', 'bannerImage')
    </script>


</body>

</html>
