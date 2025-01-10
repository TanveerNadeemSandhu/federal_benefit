@extends('layouts.app')
@section('title', 'Share | Fed Benifit Anaylzer')
@section('content')
<style>
    .alert {
    right: 50%;
    bottom: 50%;
    background: #fff;
    transform: translate(80%, 50%);
    }
</style>
@php
if(auth()->user()->role =='office' || auth()->user()->role =='admin'){
    $titleText = "GET ACCESS FROM AGENCY/PERSONAL ACCOUNT";
    $buttonText = "Request  Access";
    $grantPopupTitle = "Please enter email to request access";
    $shareButtonAccess ="Request now";
    $forward = "from";

}else{
    $titleText = "GIVE ACCESS TO YOUR ACCOUNT";
    $buttonText = "Grant Access";
    $grantPopupTitle = "Share with";
    $shareButtonAccess ="Share now";
    $forward = "to";
}

@endphp
    <form class='row justify-content-between mb-4'action="{{ route('share') }}" method="post">
        @csrf
        <div class="col-12">
            <div class="shareTitle">
                <p class="mb-0">{{$titleText}}</p>
                @if(auth()->user()->role =='agency' || auth()->user()->role =='personal')
                <p class="mb-0">Choose a level of access you wants to grant!</p>
                @endif
            </div>
        </div>
        @if ($message = Session::get('success'))
        <div class="alert alert-default alert-dismissible fade show flex-direction-column card p-5" role="alert">
          {{$message}}<br><br />
          <button type="button" class="grantbutton" data-bs-dismiss="alert" >Ok</button>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($error = Session::get('error'))
        <div class="alert alert-danger">
            <p>{{ $error }}</p>
        </div>
    @endif
        @if(auth()->user()->role =='office' || auth()->user()->role =='agency' || auth()->user()->role =='personal')
            <div class="col-sm-12 col-md-6 col-lg-6 choose-account-type-box ">
            <div class="mainbox">
                <input type="radio" name="role" id="one"  value="office"/>
                <label for="one" class="col-12 mb-2">
                    <!--<input type="radio" name="role" value="office" />-->
                    <div class="box first">
                        <div class="headercard">
                            <div class="circle"></div>
                            <b class="agency-account">BACK OFFICE SUPPORT</b>
                        </div>
                        <div class="cardbody">
                            @if(auth()->user()->role =='agency' || auth()->user()->role =='personal')
                                <p>The user will be able to:</p>
                                <ol>
                                    <li>View all of your cases.</li>
                                    <li>Edit Cases.</li>
                                    <li>Create new cases on your behalf.</li>
                                    <li>View and print reports.</li>
                                </ol>
                                <p>The user will not be able to:</p>
                                <ol>
                                    <li>Edit billing or profile information.</li>
                                    <li>Invite others to your account.</li>
                                    <li>View your messages</li>

                                </ol>
                            @endif
                            @if(auth()->user()->role =='office')
                                <p>With this, you will be able to:</p>
                                <ol>
                                    <li>View all of their cases.</li>
                                    <li>Edit Cases.</li>
                                    <li>Create new cases on their behalf.</li>
                                    <li>View and print reports.</li>
                                </ol>
                                <p>You will not be able to:</p>
                                <ol>
                                    <li>Edit billing or profile information.</li>
                                    <li>Invite others to their account.</li>
                                    <li>View their messages</li>
                                </ol>
                            @endif
                            <!--<input  type="hidden" name="role" value="back office"  />-->
                            <div class="text-center">
                                <button type="button" onclick="checkRadioOne()" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    class="grantbutton">{{$buttonText}}</button>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        @endif
        @if(auth()->user()->role =='admin' || auth()->user()->role =='agency' || auth()->user()->role =='personal')
        <div class="col-sm-12 col-md-6 col-lg-6 choose-account-type-box ">
            <div class="mainbox row">
                <input type="radio" name="role" id="two"  value="admin"/>
                <label for="two" class="col-12 mb-2">
                    <!--<input type="radio" name="role" value="admin" />-->
                    <div class="box second">
                        <div class="headercard">
                            <div class="circle"></div>
                            <b class="agency-account">ADMINISTRATIVE SUPPORT</b>
                        </div>
                        <div class="cardbody">
                        @if(auth()->user()->role =='agency' || auth()->user()->role =='personal')
                            <p>The user will be able to:</p>
                            <ol>
                                <li>View all of your cases.</li>
                                <li>View and print reports.</li>
                            </ol>

                            <p>The user will not be able to:</p>
                            <ol>
                                <li>Edit cases.</li>
                                <li>Create new cases.</li>
                                <li>Edit billing or profile information.</li>
                                <li>Invite others to your account.</li>
                                <li>View your messages.</li>
                            </ol>
                            @endif
                            @if(auth()->user()->role =='admin')
                                <p>With this, you will be able to:</p>
                                <ol>
                                    <li>View all of their cases.</li>
                                    <li>View and print reports.</li>
                                </ol>

                                <p>You will not be able to:</p>
                                <ol>
                                    <li>Edit cases.</li>
                                    <li>Create new cases.</li>
                                    <li>Edit billing or profile information.</li>
                                    <li>Invite others to their account.</li>
                                    <li>View their messages.</li>
                                </ol>
                            @endif
                            <!--<input  type="hidden" name="role" value="support"/>-->

                            <div class="text-center">

                                <button type="button" onclick="checkRadio()" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                    class="grantbutton">{{$buttonText}}</button>
                            </div>
                        </div>
                    </div>
                </label>
            </div>
        </div>
        @endif
        <!-- //Model for Share -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{$grantPopupTitle}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" placeholder="Enter email" class="inputData" name="email" id="emailInput" required>
                    </div>
                    <div class="modal-footer border-0">
                        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                        @if(auth()->user()->role =='admin' || auth()->user()->role =='office')
                            <button type="submit" class="grantbutton" id="submitgrantmail">{{$buttonText}}</button>
                        @else
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal2"
                            class="grantbutton" id="submitgrantmail">{{$buttonText}}</button>
                        @endif
                        {{-- <button type="submit" class="grantbutton" >Share now</button> --}}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="exampleModalLabel">{{$buttonText}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Display your more readable message here -->

                        <p>By confirming, you will grant access of <span id="roleText">BOS/Administrative</span> {{$forward}} <span id="modalEmailPlaceholder">.</span></p>
                    </div>

                    <div class="modal-footer border-0">
                        <!--<button type="button" class="btn btn-secondary rounded-4" data-bs-dismiss="modal">Cancel</button>-->


                        <button type="submit" class="grantbutton">{{$shareButtonAccess}}</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

@endsection
<script>
    function checkRadioOne() {
        var radioButton = document.getElementById('one');
        radioButton.checked = true;
    }
</script>

<script>
    function checkRadio() {
        var radioButton = document.getElementById('two');
        radioButton.checked = true;
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if the alert element exists
        var alert = document.querySelector('.alert.alert-success');

// Set email when the "Grant Access" button is clicked
        var grantButton = document.getElementById('submitgrantmail');
        var emailInput = document.getElementById('emailInput');
        var modalEmailPlaceholder = document.getElementById('modalEmailPlaceholder');
        var roleRadioButtons = document.getElementsByName('role');
var roleText = document.getElementById('roleText');
        // if (grantButton && emailInput && modalEmailPlaceholder) {
            grantButton.addEventListener('click', function() {
                var selectedRole;

                for (var i = 0; i < roleRadioButtons.length; i++) {
                    if (roleRadioButtons[i].checked) {
                        selectedRole = roleRadioButtons[i].value;
                        break;
                    }
                }

                // Use the selected role value as needed
                if(selectedRole =='admin'){
                    selectedRole ='Administrative support'
                }else{
                    selectedRole ='BOS'
                }
                console.log('Selected Role:', emailInput.value);
                modalEmailPlaceholder.textContent = emailInput.value;
                roleText.textContent = selectedRole;
            });
        // }

    });

</script>

