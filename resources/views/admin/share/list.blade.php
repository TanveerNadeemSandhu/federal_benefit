@extends('layouts.app')
@section('title', 'Share List | Fed Benifit Anaylzer')
@section('content')
    <div class='row justify-content-between mb-4'>
        <div class="col-12">
            <h4
                style="margin-bottom:0;color: #1F263E;font-size: 20px;font-family: Oxygen;font-weight: 700;word-wrap: break-word;
  ">
                Share List</h4>

        </div>

    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <table id="users-share-list" class="display border-0">
                <thead>
                    <tr>
                        <th class="checkbox"><input type="checkbox"></th>
                        <!-- This is for the checkbox column -->
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Access Level</th>
                        <th>Status</th>


                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>


                    @foreach ($shares as $share)
                        @php
                            if ($share->share_role == 'support') {
                                $share->share_role = 'Administrative support';
                            }
                            if ($share->share_role == 'back office') {
                                $share->share_role = 'BOS';
                            }
                        @endphp
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>{{ $share->shareUsers->first_name }} {{ $share->shareUsers->last_name }}</td>
                            <td>{{ $share->shareUsers->email }}</td>
                            <td>
                                @if ($share->share_role == 'admin')
                                    Administrative Support
                                @else
                                    Back Office Support
                                @endif

                            </td>
                            <td>
                                {{ $share->status == 'inactive' ? 'Not approved' : 'Approved' }}
                            </td>
                            <td class="text-end">
                                @if ($share->status == 'inactive')
                                    <a href="{{ route('share.statusChange', $share->id) }}"
                                        class={{ $share->status == 'inactive' ? 'text-sucess' : 'text-danger' }}>
                                        <button type="submit">
                                            Approve
                                        </button>
                                    </a>
                                @else
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal_{{ $share->shareUsers->id }}"
                                        data-user-id="{{ $share->shareUsers->id }}"> Remove access</button>
                                @endif
                                @if ($share->status == 'inactive')
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal_{{ $share->shareUsers->id }}"
                                        data-user-id="{{ $share->shareUsers->id }}"> Cancel</button>
                                @endif
                            </td>

                        </tr>

                        <div class="modal fade" id="exampleModal_{{ $share->shareUsers->id }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-white">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="exampleModalLabel">Remove access</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>By confirming, are you sure you want to revoke permission for the user with email
                                            <span class="fw-bold">{{ $share->shareUsers->email }}</span>?
                                        </p>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <a href="#" onclick="DeleteData({{ $share->id }})"
                                            class="btn btn-primary">Confirm</a>

                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
                {{-- share --}}

            </table>
        </div>
    </div>

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

    function DeleteData(id) {
        var url = '{{ route('share.destroy', 'ID') }}';
        var newurl = url.replace("ID", id);
        jQuery.ajax({
            url: newurl,
            type: 'delete',
            data: {},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response["status"] == true) {
                    window.location.href = "{{ route('share.list') }}";
                } else {
                    window.location.href = "{{ route('share.list') }}";
                }
            }
        });
    }
</script>
