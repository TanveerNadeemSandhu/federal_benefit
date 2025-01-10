@extends('layouts.app')
@section('title', 'Case List | Fed Benefit Anaylzer')
@section('content')

    <div class='row justify-content-between mb-4'>
        <div class="col-4">
            <div class="position-relative d-flex align-items-center ">
                <select class="form-select showStatus text-dark fs-6" aria-label="Default select example" id="searchSelect">
                    <option selected value="">All</option>
                    <option value="New">New</option>
                    <option value="For bos review">For bos review</option>
                    <option value="Need information">Need information</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
        </div>

        @if (!empty($userId))

            @php
                $userShare = \App\Models\User::find($userId);
                $casesShares = \App\Models\FedCase::where('user_id', $userShare->id)->get();
                $countCases = $count;

            @endphp
            @if ($shareUser->role == 'personal' && $countCases > 0)
                <div class="col-4 text-end">
                    <button class="case" disabled>
                        <img class="search-icon">
                        Cannot Add More Than One Case
                    </button>

                </div>
            @elseif($userShare->role == 'admin')
                <div class="col-4 text-end">
                    <button class="case" disabled>
                        <img class="search-icon">
                        You have not access to add case
                    </button>
                </div>
            @else
                <div class="col-4 text-end">
                    <form action="{{ route('share.caseAdd') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $userId }}" />
                        <input type="hidden" name="shareUserId" value="{{ $shareUser->id }}" />
                        <button type="submit" class="case"><img class="search-icon" alt=""
                                src="{{ asset('images/dashboard/black7.svg')}}">Create new case</button></a>
                    </form>
                </div>
            @endif
        @else
            @if ($user->role == 'personal' && $count > 0)
                <div class="col-4 text-end">
                    <button class="case" disabled>
                        <img class="search-icon">
                        Cannot Add More Than One Case
                    </button>

                </div>
            @else
                <div class="col-4 text-end">
                    <a href="{{ route('fed-case.create') }}"><button class="case"> <img class="search-icon" alt=""
                                src="{{ asset('images/dashboard/black7.svg')}}">Create new case</button></a>
                </div>
            @endif

        @endif
    </div>
    <div class="row">
        <div class="col-12">
            <table id="table-case" class="display">
                <thead>
                    <tr>
                        <th class="checkbox"><input type="checkbox"></th> <!-- This is for the checkbox column -->
                        <th>Name</th>
                        <th>Status</th>
                        <th>Last updated</th>
                        <th>Creation date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cases as $case)
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>{{ $case->name }}</td>
                            <td>{{ $case->status }}</td>
                            <td>{{ $case->updated_at }}</td>
                            <td>{{ $case->created_at }}</td>
                            <td class="text-end d-flex align-items-center justify-content-around">
                                <button><img src="{{ asset('images/dashboard/black3.svg')}}" /> Print</button>
                                <a href="{{ route('fed-case.show', $case->id) }}"><button><img
                                            src="{{ asset('images/dashboard/vector.svg')}}" />Present</button></a>
                                <a href="{{route('calculation.show',$case->id)}}">Calculate</a>

                                @if (!empty($userId))
                                    <div class="col-4 text-end"
                                        @if ($userShare->role == 'personal' && $countCases > 0) style="display: none;" @elseif($userShare->role == 'admin') style="display: none;" @endif>

                                        <a href="{{ route('share.caseEdit', $case->id) }}"><button><i
                                                    class='fa fa-edit'></i>
                                                Edit</button></a>
                                    </div>
                                @else
                                    <div class="col-4 text-end">
                                        <a href="{{ route('share.caseEdit', $case->id) }}"><button><i
                                                    class='fa fa-edit'></i>
                                                Edit</button></a>
                                    </div>
                                @endif


                            </td>
                        </tr>

                        <div class="modal fade" id="exampleModal_{{ $case->id }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content bg-white">
                                    <div class="modal-header border-0">
                                        <h5 class="modal-title" id="exampleModalLabel">Delete item</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure delete selected item?</p>
                                    </div>

                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-dark case"
                                            data-bs-dismiss="modal">Cancel</button>
                                        <a href="#" onclick="DeleteData({{ $case->id }})"
                                            class="btn btn-primary case">
                                            Confirm
                                        </a>

                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- //Change slection status change record table  -->
    <script>
        $(document).ready(function() {
            var table = $('#table-case').DataTable({
                dom: 'Bfrtip',
                buttons: [],
                searching: true,
                lengthChange: false,
                paging: false,
                language: {
                    emptyTable: "You/Agency haven't created any case yet..."
                },
            });

            $('#searchSelect').on('change', function() {
                var filter = $(this).val();
                // Clear previous search
                table.search('').draw();
                // Apply new search
                if (filter !== "0") {
                    table.column(2).search(filter)
                        .draw(); // Assuming status column is the 3rd column (index 2)
                }
            });
        });


        function DeleteData(id) {
            var url = '{{ route('fed-case.destroy', 'ID') }}';
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
                        window.location.href = "{{ route('fed-case.index') }}";
                    } else {
                        window.location.href = "{{ route('fed-case.index') }}";
                    }
                }
            });
        }
    </script>

@endsection
