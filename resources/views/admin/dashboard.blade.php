@extends('admin.layout.dashboardApp')
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
                $casesShares = \App\Models\Cases::where('user_id', $userShare->id)->get();
                $countCases = $casesShares->count();

            @endphp
            @if ($userShare->roles->first()->name == 'personal' && $countCases > 0)
                <div class="col-4 text-end">
                    <button class="case" disabled>
                        <img class="search-icon">
                        Cannot Add More Than One Case
                    </button>

                </div>
            @else
                <div class="col-4 text-end">
                    <form action="{{ route('agency_add_case') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $userId }}" />
                        <button type="submit" class="case"><img class="search-icon" alt=""
                                src="{{ asset('images/dashboard/black7.svg')}}">Create new case id</button></a>
                    </form>
                </div>
            @endif
        @else
            @if ($user->roles->first()->name == 'personal' && $count > 0)
                <div class="col-4 text-end">
                    <button class="case" disabled>
                        <img class="search-icon">
                        Cannot Add More Than One Case
                    </button>

                </div>
            @else
                <div class="col-4 text-end">
                    <a href="{{ route('add_case') }}"><button class="case"> <img class="search-icon" alt=""
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
                                <a href="{{ route('present') }}"><button><img
                                            src="{{ asset('images/dashboard/vector.svg')}}" />Present</button></a>
                                            <a href="{{ route('calculation.show', $case->id) }}">Calculate</a>

                                @if (!empty($userId))
                                    <div class="col-4 text-end"
                                        @if ($userShare->roles->first()->name == 'personal' && $countCases > 0) style="display: none;" @endif>

                                        <form action="{{ route('agency_edit_case') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $userId }}" />
                                            <input type="hidden" name="case_id" value="{{ $case->id }}" />
                                            <button type="submit"><i class='fa fa-edit'></i> Edit</button></a>
                                        </form>
                                    </div>
                                @else
                                    <div class="col-4 text-end">
                                        <a href="{{ route('cases_edit', $case->id) }}"><button><i class='fa fa-edit'></i>
                                                Edit</button></a>
                                    </div>
                                @endif

                                @if (!empty($userId))
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal_{{ $case->id }}"
                                        data-user-id="{{ $case->id }}"
                                        @if ($userShare->roles->first()->name == 'personal' && $countCases > 0) style="display: none;" @endif><i
                                            class='fa fa-trash'></i> Delete</button>
                                @else
                                    <button type="button" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal_{{ $case->id }}"
                                        data-user-id="{{ $case->id }}"><i class='fa fa-trash'></i> Delete</button>
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
                                        <a href="{{ route('cases_destroy', $case->id) }}" class="text-white">
                                            <button type="submit" class="btn btn-primary case">
                                                Confirm
                                            </button>
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
    </script>

@endsection
