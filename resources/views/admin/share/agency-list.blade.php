@extends('layouts.app')
@section('title', 'Agencies List | Fed Benefit Anaylzer')
@section('content')

      <div class='row justify-content-between mb-4'>
        <div class="col-12">
          <h4 style="margin-bottom:0;color: #1F263E;font-size: 20px;font-family: Oxygen;font-weight: 700;word-wrap: break-word;
          ">Agencies</h4>
        </div>
        
      </div>
      <div class="row">
        <div class="col-12">
          <table id="agency-list" class="display border-0">
            <thead>
              <tr>
                <th></th>
                <th>Advisor Name</th>
                <th>Advisor Company</th>
                <!-- <th>Description</th> -->
                <!-- <th>Action</th> -->
              </tr>
            </thead>
            <tbody>
                @foreach ($shareAgency as $share)

              <tr>
                <td><img src="{{ asset('images/message/ellipse-10@2x.png')}}" alt="" class="logoagency"></td>
                <td>{{$share->users->first_name}} {{$share->users->last_name}}</td>
                <td class="ades">{{$share->users->company_name}}</td>
                <td class="text-end abutton">
                  <a href="{{route('share.caseList', ['userId' => $share->user_id, 'shareId' => $share->share_id])}}"><i class='fa fa-view'></i> view case</a> 
                  </td>
              </tr>
              @endforeach

            </tbody>
            {{-- share --}}
            {{-- <tbody>
                @foreach ($shares as $permission)
              <tr>

                <td><img src="/images/message/ellipse-10@2x.png" alt="" class="logoagency"></td>
                <td>{{$permission->shareUsers->first_name}} {{$permission->shareUsers->last_name}}</td>
                <td class="ades">{{$permission->shareUsers->company_name}}</td>
                <td class="text-end abutton">
                  <a href="{{route('share.caseList')}}"><i class='fa fa-view'></i> view case 1</a>

                  </td>
              </tr>
              @endforeach

            </tbody> --}}
          </table>
        </div>
      </div>


@endsection
