@extends('layouts.app')

@section('pageTitle', ucwords($user->name))

@section('content')

<div class="row">
    <div class="col-md-12">

        <!-- Profile Card -->
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="text-center">
                    <!-- Passport Photo -->
                    @php
                    // Retrieve the passport photo URL from the user's KYC data
                    $passportPhoto = $user->kyc->where('doc_type', 'passport_photo')->first();
                    $passportPath = $passportPhoto ? asset($passportPhoto->document) : asset('passport.png');
                    @endphp

                    <img src="{{ $passportPath }}"
                        alt="Profile Picture"
                        class="rounded-circle img-fluid profile-picture mb-3 p-1 shadow-sm bg-white"
                        style="margin-top: -50px;">

                    <!-- Full Name -->
                    <h3 class="fw-bold">{{ $user->name }}</h3>
                    <!-- Edit icon -->
                    <a href="{{ route('employee.edit', $user->id) }}" class="btn btn-outline-primary mb-3">
                        <i class="fas fa-pencil"></i> Edit Profile
                    </a>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <a href="" class="btn btn-link"><i class="fas fa-print"></i> Print Data</a>
                    <div>
                        <input type="text" id="daterange" class="form-control" placeholder="Select Date Range" />
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4" id="user-analytics-cards">
            @for ($i = 0; $i < 5; $i++)
                <div class="col">
                <div class="card shadow-sm rounded-3 p-3 bg-white placeholder-glow">
                    <div class="card-body p-0 d-flex align-items-center">
                        <div class="col-2 me-3">
                            <div class="placeholder rounded-circle bg-secondary" style="width: 40px; height: 40px;"></div>
                        </div>
                        <div class="col-10">
                            <h6 class="placeholder bg-secondary rounded mb-2" style="width: 60%; height: 15px;"></h6>
                            <h5 class="placeholder bg-secondary rounded mb-2" style="width: 80%; height: 20px;"></h5>
                            <div class="mb-2 progress rounded" style="height: 5px;">
                                <div class="progress-bar placeholder bg-secondary" style="width: 50%;"></div>
                            </div>
                            <div class="placeholder bg-secondary rounded mt-2" style="width: 50%; height: 10px;"></div>
                        </div>
                    </div>
                </div>
        </div>
        @endfor
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-capitalize active" id="kyc-tab" data-bs-toggle="tab" data-bs-target="#kyc-tab-pane" type="button" role="tab" aria-controls="kyc-tab-pane" aria-selected="true">
                        <i class="fas fa-user-circle"></i> KYC Information
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-capitalize" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions-tab-pane" type="button" role="tab" aria-controls="permissions-tab-pane" aria-selected="false">
                        <i class="fas fa-user-cog"></i> permissions
                    </button>
                </li>
                <!-- <li class="nav-item" role="presentation">
                    <button class="nav-link text-capitalize" id="commission-tab" data-bs-toggle="tab" data-bs-target="#commission-tab-pane" type="button" role="tab" aria-controls="commission-tab-pane" aria-selected="false">
                        <i class="fas fa-wallet"></i> commission
                    </button>
                </li> -->
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade py-3 show active" id="kyc-tab-pane" role="tabpanel" aria-labelledby="kyc-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted">Email:</span>
                                        {{ $user->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted">Phone Number:</span>
                                        {{ $user->phone }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted">Gender:</span>
                                        {{ $user->gender }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted">Branch:</span>
                                        {{ $user->branch->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span class="text-muted">Account Status:</span>
                                        {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                                    </td>
                                </tr>

                                @foreach($user->kyc->chunk(3) as $kycChunk)
                                <tr>
                                    @foreach($kycChunk as $value)
                                    <td>
                                        <strong class="text-muted">{{ ucwords(str_replace('_', ' ', $value->doc_type)) }}</strong><br>
                                        @php
                                        $filePath = public_path($value->document);
                                        $isImage = @getimagesize($filePath);
                                        @endphp

                                        @if($isImage)
                                        <a href="{{ asset($value->document) }}" target="_blank">
                                            <img src="{{ asset($value->document) }}" alt="" style="max-width: 100px">
                                        </a>
                                        @else
                                        <a href="{{ asset($value->document) }}" target="_blank" class="btn btn-primary">
                                            View Document
                                        </a>
                                        @endif
                                    </td>
                                    @endforeach

                                    {{-- Fill empty cells if the chunk has less than 3 items --}}
                                    @for($i = count($kycChunk); $i < 3; $i++)
                                        <td>
                                        </td>
                                        @endfor
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade py-3" id="permissions-tab-pane" role="tabpanel" aria-labelledby="permissions-tab" tabindex="0">
                    <h5 class="bg-light p-2">User Permissions</h5>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Permission</th>
                                    <th>More Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($user->permissions->isEmpty())
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No permissions assigned.</td>
                                </tr>
                                @else
                                @foreach ($user->permissions as $key => $permission)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <!-- Display additional information based on permission slug -->
                                        @switch($permission->slug)
                                        @case('manager_branch')
                                        <ul>
                                            @foreach($user->selectedBranches() as $branch)
                                            <li>{{ $branch->name }}</li>
                                            @endforeach
                                        </ul>
                                        @break

                                        @case('manager_product')
                                        <ul>
                                            @foreach($user->selectedProducts() as $product)
                                            <li>{{ $product->name }}</li>
                                            @endforeach
                                        </ul>
                                        @break

                                        @case('manager_catalogue')
                                        <ul>
                                            @foreach($user->selectedCatalogues() as $catalogue)
                                            <li>{{ $catalogue->name }}</li>
                                            @endforeach
                                        </ul>
                                        @break

                                        @default
                                        <!-- You can add a default case here if needed -->
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- <div class="tab-pane fade py-3" id="commission-tab-pane" role="tabpanel" aria-labelledby="commission-tab" tabindex="0">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="table-primary">
                                <tr>
                                    <th>Period</th>
                                    <th>Amount Earned</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div> -->
            </div>

        </div>
    </div>

</div>

@endsection

@push('script')
<script>
    $(document).ready(function() {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const userAnalyticsCards = $('#user-analytics-cards');
        const startOfMonth = moment().startOf('month');
        const endOfMonth = moment().endOf('month');

        fetchAnalyticsData('');

        function fetchAnalyticsData(dates) {
            $.ajax({
                url: "{{ route('employee.show', $user->id) }}", // Adjust the URL based on your actual route
                type: 'GET',
                data: {
                    dates: dates,
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                beforeSend: function() {
                    // Reset cards to skeleton loaders
                    userAnalyticsCards.empty();
                    for (let i = 0; i < 5; i++) {
                        userAnalyticsCards.append(`
                            <div class="col">
                                <div class="card shadow-sm rounded-3 p-3 bg-light placeholder-glow">
                                    <div class="card-body p-0 d-flex align-items-center">
                                        <div class="col-2 me-3">
                                            <div class="placeholder rounded-circle bg-secondary" style="width: 40px; height: 40px;"></div>
                                        </div>
                                        <div class="col-10">
                                            <h6 class="placeholder bg-secondary rounded mb-2" style="width: 60%; height: 15px;"></h6>
                                            <h5 class="placeholder bg-secondary rounded mb-2" style="width: 80%; height: 20px;"></h5>
                                            <div class="mb-2 progress rounded" style="height: 5px;">
                                                <div class="progress-bar placeholder bg-secondary" style="width: 50%;"></div>
                                            </div>
                                            <div class="placeholder bg-secondary rounded mt-2" style="width: 50%; height: 10px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
                },
                success: function(response) {
                    console.log(response); // Log the entire response to inspect its structure

                    // Clear previous data
                    userAnalyticsCards.empty();

                    // Check if response.cards is an object and iterate over it
                    if (response.cards && typeof response.cards === 'object') {
                        Object.keys(response.cards).forEach(key => {
                            const card = response.cards[key];
                            userAnalyticsCards.append(`
                                <div class="col">
                                    <div class="card shadow-sm analytics-data-card bg-${card.bg} rounded-3 p-3 hover-shadow">
                                        <div class="card-body p-0 d-flex align-items-center">
                                            <div class="col-2 me-3">
                                                <div class="bi ${card.icon} fs-3"></div>
                                            </div>
                                            <div class="col-10">
                                                <h6 class="mb-1 text-uppercase text-light">${key}</h6>
                                                <h5 class="mb-1 fw-bold text-white">${card.value}</h5>
                                                <div class="mb-2 progress rounded" style="height: 5px;">
                                                    <div class="progress-bar bg-light" style="width: ${card.progress}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `);
                        });
                    } else {
                        console.error("Expected 'cards' to be an object, but got:", response.cards);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching analytics data:", error);
                }
            });
        }

        $(document).ready(function() {
            // Get the start and end dates of the current month
            var startOfMonth = moment().startOf('month');
            var endOfMonth = moment().endOf('month');

            // Initialize the date range picker with default values
            $('#daterange').daterangepicker({
                opens: 'left', // Customize where the picker opens
                locale: {
                    format: 'YYYY-MM-DD' // Set the date format
                },
                startDate: startOfMonth, // Set default start date
                endDate: endOfMonth // Set default end date
            });

            // Update input field and call the analytics function on apply
            $('#daterange').on('apply.daterangepicker', function(ev, picker) {
                // Format selected date range
                var selectedDates = picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD');

                // Update the input field value
                $(this).val(selectedDates);

                // Call your analytics function
                fetchAnalyticsData(picker.startDate.format('YYYY-MM-DD'), picker.endDate.format('YYYY-MM-DD'));
            });
        });
    });
</script>
@endpush