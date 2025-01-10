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
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    #table-case_filter {
        display: none;
    }

    button.logout-btn {
        background: transparent;
        border: none;
        color: #fff;
    }
    .messenger{
        height: 85vh !important;
    }
</style>