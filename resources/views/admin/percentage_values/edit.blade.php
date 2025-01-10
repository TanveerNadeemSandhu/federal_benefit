@extends('layouts.app')
@section('title', 'Percentage Values | Fed Benefit Anaylzer')
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
            <form role="form" action="{{ route('admin.percentageValues.update') }}" method="post"  enctype="multipart/form-data">
            
            @method('put')
            @csrf
                    <input type="hidden" name="userId" value="{{$user->id}}">
            <div>
              <p class="mail mt-3">Percentage Values</p>
            </div>


            <div class="input-group mb-3 row">
                <div class="col-sm-12 col-md-5">
                    <label>Annual Salary Increment Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="Annual Salary Increment Percentage" aria-label="Annual Salary Increment Percentage"
                        name="annual_salary_increment" value="{{$percentageValues->annual_salary_increment ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>

                <div class="col-sm-12 col-md-5">
                    <label>CSRS COLA Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="CSRS COLA Percentage" aria-label="CSRS COLA Percentage"
                        name="csrs_cola" value="{{$percentageValues->csrs_cola ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>

                <div class="col-sm-12 col-md-5">
                    <label>FERS COLA Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="FERS COLA Percentage" aria-label="FERS COLA Percentage"
                        name="fers_cola" value="{{$percentageValues->fers_cola ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>

                <div class="col-sm-12 col-md-5">
                    <label>CPI-W Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="CPI-W Percentage" aria-label="CPI-W Percentage"
                        name="cpiw" value="{{$percentageValues->cpiw ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>

                <div class="col-sm-12 col-md-5">
                    <label>TSP Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="TSP Percentage" aria-label="TSP Percentage"
                        name="tsp_increment" value="{{$percentageValues->tsp_increment ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>

                <div class="col-sm-12 col-md-5">
                    <label>FEHB Percentage</label>
                </div>
                <div class="col-sm-11 col-md-6 p-0">
                    <input type="text" class="form-control" placeholder="FEHB Percentage" aria-label="FEHB Percentage"
                        name="fehb_increment" value="{{$percentageValues->fehb_increment ?? '0'}}" >
                </div>
                <div class="col-sm-1 col-md-1">
                    <input type="text" readonly class="form-control"
                         value="%">
                </div>
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

@endsection