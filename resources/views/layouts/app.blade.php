<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.head')
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
                @if (auth()->user()->role == 'super admin' || auth()->user()->role == 'personal' || auth()->user()->role == 'agency')
                    <a href="{{ route('fed-case.index') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'fed-case.index' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black10.svg')}}">
                        Case List
                    </a>
                @endif
                @if (auth()->user()->role == 'office' || auth()->user()->role == 'admin')
                    <a href="{{ route('share.agenciesList') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'share.agenciesList' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black10.svg')}}">
                        Agencies
                    </a>
                @endif
                <a href="{{ route('profile.edit') }}"
                    class="dashboard-nav-item {{ Route::currentRouteName() == 'profile.edit' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black1.svg')}}">
                    Profile
                </a>
                <!--<a href="help.html" class="dashboard-nav-item ">-->
                <!--    <img class="black-icon10" alt="" src="/images/dashboard/black.svg">-->
                <!--    Help-->
                <!--</a>-->
                @if (auth()->user()->role == 'agency' || auth()->user()->role == 'personal')
                    <a href="{{ route('billing.edit') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'billing' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/group-19.svg')}}">
                        Billing
                    </a>
                @endif
                @if (auth()->user()->role == 'office' ||
                        auth()->user()->role == 'admin' ||
                        auth()->user()->role == 'agency' ||
                        auth()->user()->role == 'personal')

                    <a href="{{ route('process.share') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'process.share' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/group-24.svg')}}">
                        @if (auth()->user()->role == 'admin' || auth()->user()->role == 'office')
                            Get Access
                        @else
                            Share Access
                        @endif
                    </a>
                @endif
                {{-- code for message notification --}}
                @php
                    use Illuminate\Support\Facades\Auth;
                    use App\Models\ChMessage;

                    $userId = Auth::id();
                    $unreadCount = ChMessage::where('to_id', $userId)
                          ->where('seen', 0)
                          ->count();
                @endphp
                <a href="{{route('chatify')}}" class="dashboard-nav-item {{ Route::currentRouteName() == 'chatify' ? 'active' : '' }}">
                    <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black2.svg')}}">
                    Message ({{$unreadCount}})
                </a>
                @if (auth()->user()->role == 'agency' || auth()->user()->role == 'personal')
                    <a href="{{ route('share.list') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'share.list' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black9.svg')}}">
                        Shared Users
                    </a>
                @endif

                @if (auth()->user()->role == 'super admin')
                    <a href="{{ route('admin.user-index') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'admin.user-index' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black9.svg')}}">
                        All Users
                    </a>


                    <a href="{{ route('admin.percentageValues.edit') }}"
                        class="dashboard-nav-item {{ Route::currentRouteName() == 'admin.percentageValues.edit' ? 'active' : '' }}">
                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/settings-gear.svg')}}">
                        Percentage Values
                    </a>


                @endif


                <form id="logout-form" class="" action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn dashboard-nav-item position-absolute bottom-0 w-100">

                        <img class="black-icon10" alt="" src="{{ asset('images/dashboard/black8.svg')}}" />
                        Logout
                    </button>

                </form>

            </nav>
        </div>
        <div class='dashboard-app'>
            <header class='dashboard-toolbar row justify-content-end'>
                <!--<a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>-->
                <!--<div class="col-4">-->
                <!--    <div class="position-relative d-flex align-items-center ">-->
                <!--        <img class="search-icon position-absolute" alt="" src="/images/dashboard/search.svg">-->
                <!--        <input type="text" class="form-control start-search " placeholder="Search anything"-->
                <!--            aria-label="Email Address" aria-describedby="basic-addon1">-->
                <!--    </div>-->
                <!--</div>-->
                <div class="col-4 text-end">
                    @if (auth()->user()->profile->image)
                        <img class="dashboard-agency-account-child"
                            style="width: 40px; height: 40px; object-fit: cover;border-radius:50%" alt=""
                            src="{{ asset('upload/profile/' . auth()->user()->profile->image) }}" />
                    @else
                        <img class="dashboard-agency-account-child"
                            style="width: 40px; height: 40px; object-fit: cover" alt=""
                            src="{{ asset('images/profile/default-profile-image.jpg')}}" />
                    @endif
                </div>
            </header>
            <div class='dashboard-content mt-4'>
                @yield('content')
            </div>
        </div>
    </div>
    {{-- //Profile script --}}



    @include('layouts.js')
    @include('layouts.customJs')
</body>

</html>
