@extends('layouts.app')

@section('pageTitle', 'Analytics')

@section('content')

@if (in_array(Auth::user()->role?->role, [2, 3]))
<div class="row">
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

    @if (Auth::user()->role?->role == 3)
    <div class="col-md-3 mb-3">
        <select name="employee" id="employee" class="form-control border-0 shadow-sm">
            <option value="">Company Analytics</option>
            @foreach ($employees as $employee)
            <option value="{{ $employee->id }}">{{ ucwords($employee->name) }}</option>
            @endforeach
        </select>
    </div>

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
    <!-- Skeleton loaders for the analytics cards -->
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
    <!-- Bar chart -->
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
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const dashboardCards = $('#dashboard-cards');
        let barChartInstance = null; // Store chart instance

        // Initial data fetch
        fetchAnalyticsData('{{ $fetchType }}', '{{ $fetchTypeValue }}');

        $('#employee, #branch').on('change', function() {
            const fetchType = $(this).attr('id');
            const fetchTypeValue = $(this).val();
            fetchAnalyticsData(fetchType, fetchTypeValue);
        });

        function fetchAnalyticsData(fetchType, fetchTypeValue) {
            $.ajax({
                url: '/api/fetch-data/analytics',
                type: 'GET',
                data: {
                    fetchType,
                    fetchTypeValue
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                beforeSend: function() {
                    // Reset cards to skeleton loaders
                    dashboardCards.empty();
                    for (let i = 0; i < 4; i++) {
                        dashboardCards.append(`
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
                    // Populate analytics cards
                    dashboardCards.empty();
                    $.each(response.cards, function(key, value) {
                        dashboardCards.append(`
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card shadow-sm analytics-data-card bg-${value.bg} rounded-3 p-3 hover-shadow">
                                    <div class="card-body p-0 d-flex align-items-center">
                                        <div class="col-2 me-3">
                                            <i class="bi ${value.icon} fa-2x"></i>
                                        </div>
                                        <div class="col-10">
                                            <h6 class="mb-1 text-uppercase text-light">${key}</h6>
                                            <h5 class="mb-1 fw-bold text-white">${value.value}</h5>
                                            <div class="mb-2 progress rounded" style="height: 5px;">
                                                <div class="progress-bar bg-light" style="width: ${value.progress}%"></div>
                                            </div>
                                            <a href="#" class="text-white small">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                    });

                    // Update chart
                    updateChart(response.labels, response.data);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching analytics data:", error);
                }
            });
        }

        function updateChart(labels, data) {
            const ctx = $('#bar_chart')[0].getContext('2d');

            if (barChartInstance) {
                // Update existing chart
                barChartInstance.data.labels = labels;
                barChartInstance.data.datasets[0].data = data;
                barChartInstance.update();
            } else {
                // Create new chart
                barChartInstance = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Revenue (Ksh)',
                            data: data,
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
                                    text: 'Dates'
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
            }
        }
    });
</script>
@endpush