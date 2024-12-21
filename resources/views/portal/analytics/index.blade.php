@extends('layouts.app')

@section('pageTitle', ucwords($title))

@section('content')

@if (in_array(Auth::user()->role?->role, [2, 3]))
<div class="row">
    <!-- Dropdown for employees in a branch -->
    @if (Auth::user()->role?->role == 2)
    <div class="col-md-3 mb-3">
        <select name="employee" id="employee" class="form-control border-0 shadow-sm">
            <option value="">Branch Analytics</option>
            @foreach ($employees as $employee)
            @if ($employee->branch_id == Auth::user()->branch_id)
            <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
            @endif
            @endforeach
        </select>
    </div>
    @endif

    <!-- Dropdown for entire company employees -->
    @if (Auth::user()->role?->role == 3)
    <div class="col-md-3 mb-3">
        <select name="employee" id="employee" class="form-control border-0 shadow-sm">
            <option value="">Company Analytics</option>
            @foreach ($employees as $employee)
            <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
            @endforeach
        </select>
    </div>

    <!-- Dropdown for branches -->
    <div class="col-md-3 mb-3">
        <select name="branch" id="branch" class="form-control border-0 shadow-sm">
            <option value="">Company Analytics</option>
            @foreach ($branches as $branch)
            <option value="{{ $branch->id }}">{{ ucwords($branch->name) }}</option>
            @endforeach
        </select>
    </div>
    @endif
</div>
@endif

<div id="dashboard-cards" class="row g-4">
    <!-- Skeleton Loader for Analytics Cards -->
    @for ($i = 0; $i < 4; $i++)
        <div class="col-lg-3 col-md-4 col-sm-6">
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
@endfor
</div>

<div class="row g-4">
    <!-- Skeleton Loader for Bar Chart -->
    <div class="col-md-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">This Week's/Month's Sales</h5>
            </div>
            <div class="card-body">
                <canvas id="bar_chart" style="height:260px;"></canvas>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Important parameters
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const dashboardCards = $('#dashboard-cards');
        // Default initiation of data fetch
        fetchAnalyticsData('{{ $fetchType }}', '{{ $fetchTypeValue }}');

        $('#employee, #branch').on('change', function() {
            var fetchType = $(this).attr('id');
            var fetchTypeValue = $(this).val();
            // Initiate Analytics Fetch
            fetchAnalyticsData(fetchType, fetchTypeValue);
        });

        function fetchAnalyticsData(fetchType, fetchTypeValue) {
            // Fetch cards and chart data using AJAX
            $.ajax({
                url: '/api/fetch-data/analytics',
                type: 'GET',
                data: {
                    fetchType: fetchType,
                    fetchTypeValue: fetchTypeValue
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                beforeSend: function() {
                    // Empty cards
                    dashboardCards.empty();
                    // Append cards
                    for (let i = 0; i < 4; i++) {
                        $('#dashboard-cards').append(`
                            <div class="col-lg-3 col-md-4 col-sm-6">
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
                    // Clear skeleton loaders
                    dashboardCards.empty();

                    // Populate cards dynamically
                    $.each(response.cards, function(key, value) {
                        const card = `
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card shadow-sm analytics-data-card bg-${value.bg} rounded-3 p-3 hover-shadow p-0">
                                    <div class="card-body p-0 d-flex align-items-center">
                                        <div class="col-2 me-3">
                                            <i class="bi ${value.icon} fa-2x"></i>
                                        </div>
                                        <div class="col-10">
                                            <h6 class="mb-1 text-uppercase text-light">${key}</h6>
                                            <h5 class="mb-1 fw-bold text-white">${value.value}</h5>
                                            <div class="mb-2 progress rounded" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height: 5px;">
                                                <div class="progress-bar bg-light" style="width: ${value.progress}%"></div>
                                            </div>
                                            <a href="" class="d-flex justify-content-between align-items-center text-white small">
                                                View Details <i class="fa fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        dashboardCards.append(card);
                    });

                    // Initialize the chart
                    const ctx = $('#bar_chart')[0].getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels, // Dates of the week
                            datasets: [{
                                label: 'Total Revenue (Ksh)',
                                data: response.data, // Revenue values
                                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Revenue (Ksh)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Dates of sales'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching dashboard data:", error);
                }
            });
        }
    });
</script>
@endpush