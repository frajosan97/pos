@extends('layouts.app')

@section('pageTitle', 'Analytics')

@section('content')

<div class="container-fluid">
    <!-- Control buttons -->
<ul class="nav nav-pills rounded bg-white mb-3">
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="fas fa-chart-pie"></i> Analytics
        </a>
    </li>
    @include('layouts.partials.filters')
</ul>
<!-- /end control buttons -->

<!-- Dashboard Analytics Cards -->
<div id="dashboard-cards" class="row g-2">
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

<!-- Bar Chart -->
<div class="row">
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
</div>

@endsection

@push('script')
<script>
    $(document).ready(function () {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const dashboardCards = $('#dashboard-cards');
        let barChartInstance = null;

        fetchAnalyticsData();

        $(document).on('click', '.filter-employee, .filter-branch, .filter-catalogue, .filter-product', function (e) {
            e.preventDefault();
            const filterClass = '.' + $(this).attr('class').split(' ')[0];
            $(filterClass).removeClass('active');
            $(this).addClass('active');
            fetchAnalyticsData();
        });

        function renderSkeletonLoaders(count = 4) {
            dashboardCards.empty();
            for (let i = 0; i < count; i++) {
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
        }

        function fetchAnalyticsData() {
            $.ajax({
                url: "{{ route('analytics.index') }}",
                type: 'GET',
                data: {
                    branch: $('.filter-branch.active').data('value'),
                    catalogue: $('.filter-catalogue.active').data('value'),
                    employee: $('.filter-employee.active').data('value'),
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                dataType: 'json',
                beforeSend: function () {
                    renderSkeletonLoaders();
                },
                success: function (response) {
                    dashboardCards.empty();
                    $.each(response.cards, function (key, value) {
                        dashboardCards.append(`
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="card shadow-sm analytics-data-card bg-${value.bg} rounded-3 p-3 hover-shadow">
                                    <div class="card-body p-0 d-flex align-items-center">
                                        <div class="col-2 me-3">
                                            <i class="bi ${value.icon} fa-2x text-white"></i>
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

                    updateChart(response.labels, response.data);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching analytics data:", error);
                }
            });
        }

        function updateChart(labels, data) {
            const ctx = document.getElementById('bar_chart').getContext('2d');

            if (barChartInstance) {
                barChartInstance.data.labels = labels;
                barChartInstance.data.datasets[0].data = data;
                barChartInstance.update();
            } else {
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
                                position: 'top'
                            }
                        }
                    }
                });
            }
        }
    });
</script>
@endpush
