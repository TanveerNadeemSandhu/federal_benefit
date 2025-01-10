@extends('layouts.app')
@section('title', 'Profile | Fed Benefit Anaylzer')
@section('content')
<div class="row">
    <div class="col-12 profile">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif

        <div class="alert alert-success" style='display:none;'></div>
        <div class="position-relative">
                <form role="form" action="{{ route('profile.update') }}" method="post"  enctype="multipart/form-data">
            @method('put')
            @csrf
                    <input type="hidden" name="userId" value="{{$user->id}}">
            <div class="col-12 position-relative">
                <img class="w-100 profilebanner" id="bannerImage" src="{{ $profile->bg_image ? asset('upload/background/' . $profile->bg_image) : asset('images/profile/rectangle-547@2x.png') }}" />
                <label class="fa fa-edit position-absolute" for='bannerImageUpload' style="font-size:24px;margin-left: -2rem;bottom: 2rem;color: #fff;right: 2rem;"></label>
                <input type="file" id="bannerImageUpload" style="display: none;" accept="image/*" name="bg_image" />
            </div>

            <div>

                <div class='position-relative'>
                    <img class="profileimage" id="profileImage" src="{{ $profile->image ? asset('upload/profile/' . $profile->image) : 'https://t4.ftcdn.net/jpg/03/59/58/91/360_F_359589186_JDLl8dIWoBNf1iqEkHxhUeeOulx0wOC5.jpg' }}" />
                    <label class="fa fa-edit position-absolute" for='profileImageUpload' style="font-size:24px;margin-left: -2rem;"></label>
                    <input type="file" id="profileImageUpload" style="display: none;" accept="image/*" name="image" />
                </div>

              <p class="mail mt-3">{{ $user->email }}</p>
            </div>


            <div class="input-group mb-3 row">
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" placeholder="First Name" aria-label="First Name"
                        name="first_name" value="{{ $user->first_name }}">
                </div>
                <div class="col-sm-12 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="Phone No 1" aria-label="Phone No 1"
                        name="phone_1" value="{{ $profile->phone_1 }}">
                </div>
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" placeholder="Last Name" aria-label="Last Name"
                        name="last_name" value="{{ $user->last_name }}">
                </div>
                <div class="col-sm-12 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="Phone No 1 (Type)"
                        aria-label="Phone No 1 (Type)" name="phone_1_type" value="{{ $profile->phone_1_type }}">
                </div>
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" placeholder="Title" aria-label="Title" name="title"
                        value="{{ $profile->title }}">
                </div>
                <div class="col-sm-12 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="Phone No 2" aria-label="Phone No 2"
                        name="phone_2" value="{{ $profile->phone_2 }}">
                </div>
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" placeholder="Company Name" aria-label="Company Name"
                        name="company_name" value="{{ $profile->company_name }}">
                </div>
                <div class="col-sm-12 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="Phone No 2 (Type)"
                        aria-label="Phone No 2 (Type)" name="phone_2_type" value="{{ $profile->phone_2_type }}">
                </div>
                <div class="col-sm-12 col-md-6">
                    <input type="text" class="form-control" placeholder="Street Address" aria-label="Company Name"
                        name="address" value="{{ $profile->address }}">
                </div>

                <div class="col-sm-12 col-md-6 ps-0">
                    <input type="text" class="form-control" placeholder="City" aria-label="City" name="city"
                        value="{{ $profile->city }}">
                </div>
                @if(auth()->user()->role == 'agency')
                <div class="col-12">
                <p class="des mt-1 mb-0">Your disclaimer statement to be shown to clients: </p>
                    <textarea rows="4" cols="50" class="form-cont  rol" placeholder="Your disclaimer statement to be shown to clients."
                        name="statement">  {{ $profile->statement }}</textarea>
                </div>
                @endif
                <!-- <p class="des mt-1 mb-4">( “Your disclaimer statement to be shown to clients.”)</p> -->
                <div class="col-12">
                    <button class="saveProfile">
                        Save
                    </button>
                </div>
            </div>
        </form>
      </div>

    </div>
</div>
{{-- script for bg image preview --}}
<script>
    document.getElementById('bannerImageUpload').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var bannerImage = document.getElementById('bannerImage');
                bannerImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

{{-- script for bg image preview --}}
<script>
    document.getElementById('profileImageUpload').addEventListener('change', function(event) {
        var file = event.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var bannerImage = document.getElementById('profileImage');
                bannerImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
</script>

@endsection