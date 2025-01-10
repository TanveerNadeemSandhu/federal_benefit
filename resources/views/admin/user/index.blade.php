@extends('layouts.app')
@section('title', 'Users | Fed Benifit Anaylzer')
@section('content')
    <div class='row justify-content-between mb-4'>
        <div class="col-12">
            <h4
                style="margin-bottom:0;color: #1F263E;font-size: 20px;font-family: Oxygen;font-weight: 700;word-wrap: break-word;
  ">
                Users</h4>

        </div>

    </div>
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="row">
        <div class="col-12">
            <table id="table-demo" class="display border-0">
                <thead>
                    <tr>
                        <!-- This is for the checkbox column -->
                        <th>User Name</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>No of Cases</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
            @foreach ($users as $user)
            @php
                $role = isset($user->roles[0])?$user->roles[0]->name:''
            @endphp
                        <tr>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'super admin')
                                    Super Admin
                                @elseif($user->role == 'agency')
                                    Agency Account
                                @elseif($user->role == 'personal')
                                    Personal Account
                                @elseif($user->role == 'admin')
                                    Admin Support
                                @elseif($user->role == 'office')
                                    Back Office Support
                                @else
                                    Has no role
                                @endif
                            </td>
                            <td>{{$user->FedCase()->count()}}</td>
                            <td>
                                <button type="submit" class="btn btn-primary" {{ $user->role == 'super admin' ? 'disabled' : '' }}>
                                    <a href="{{ route('admin.user-statusChange', $user->id) }}" class="text-black" style="color: black;" onmouseover="this.style.color='white';" onmouseout="this.style.color='black';">
                                    @if($user->status == '1')Active @else Inactive @endif
                                </a>
                               </button>
                            </td>

                            <td class="text-end">
                                <button><img src="{{ asset('images/users/black7.svg')}}" /> <a
                                        href="{{route('admin.profileEdit',$user->id)}}"> Edit</a></button>
                                       <!--<button data-bs-toggle="modal" data-bs-target="#deleteModal"><i class='fa fa-trash'></i> Delete</button>-->
                                       {{-- <form method="post" action="{{ route('users_destroy', $user->id) }}">
                                            @method('delete')
                                           @csrf
                                          @if($role!=='super admin') <button type="submit"><i class='fa fa-trash'></i> Delete</button>@endif
                                       </form> --}}
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal_{{ $user->id }}" data-user-id="{{ $user->id }}" {{ $user->role == 'super admin' ? 'disabled' : '' }}><i class='fa fa-trash'></i> Delete</button>

                            </td>
                        </tr>

          <div class="modal fade" id="exampleModal_{{ $user->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content bg-white">
                      <div class="modal-header border-0">
                          <h5 class="modal-title" id="exampleModalLabel">Delete item</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                          <p>Are you sure delete selected item?</p>
                      </div>

                      <div class="modal-footer border-0">
                          <button type="button" class="btn btn-dark case" data-bs-dismiss="modal">Cancel</button>
                          <a href="{{ route('admin.user-destroy', $user->id) }}" class="text-white">
                          <button type="submit" class="btn btn-primary case">
                              Confirm
                          </button>
                      </a>

                      </div>

                  </div>
              </div>
          </div>

<!--deleteModal Modal -->
<!--<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">-->
<!--  <div class="modal-dialog modal-dialog-centered">-->
<!--    <div class="modal-content">-->
<!--      <div class="modal-header">-->
<!--        <h5 class="modal-title" id="deleteModal">Delete user</h5>-->
        <!--<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>-->
<!--      </div>-->
<!--      <div class="modal-body">-->
<!--        Are you sure delete selected user?-->
<!--      </div>-->
<!--      <div class="modal-footer">-->
<!--        <button type="button" class="btn btn-dark case text-dark" data-bs-dismiss="modal" style='background:#fff!important;'>Cancel</button>-->
<!--        <form method="post" action="{{ route('admin.user-destroy', $user->id) }}">-->
<!--                                            @method('delete')-->
<!--                                            @csrf-->
<!--                                            @if($role!=='super admin') <button type="submit" class='case'><i class='fa fa-trash'></i> Delete</button>@endif-->
<!--                                        </form>-->
<!--      </div>-->
<!--    </div>-->
<!--  </div>-->
<!--</div>-->

                    @endforeach
                    {{-- <a href="{{ route('posts.edit', $post->id) }}"
        class="btn btn-primary btn-sm">Edit</a> --}}
                </tbody>
            </table>
        </div>
    </div>
    <!--//Delete modal-->
    <!-- Button trigger modal -->


    @endsection

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if the alert element exists
            var alert = document.querySelector('.alert.alert-success');

            // If the alert element exists, hide it after 2 seconds
            if (alert) {
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 2000); // 2000 milliseconds = 2 seconds
            }
        });
    </script>
