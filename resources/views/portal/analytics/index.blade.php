@extends('layouts.app')

@section('pageTitle', 'analytics')

@section('content')

<div class="row g-4">
    @foreach ($cards as $key => $value)
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card shadow-sm analytics-data-card bg-{{ $value['bg'] }} rounded-3 p-3 hover-shadow p-0">
            <div class="card-body p-0 d-flex align-items-center">
                <div class="col-2 me-3">
                    <i class="bi {{ $value['icon'] }} fa-2x"></i>
                </div>
                <div class="col-10">
                    <h6 class="mb-1 text-uppercase text-light">{{ $key }}</h6>
                    <h5 class="mb-1 fw-bold text-white">{{ $value['value'] }}</h5>
                    <div class="mb-2 progress rounded" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="height: 5px;">
                        <div class="progress-bar bg-light" data-width="{{ $value['progress'] }}"></div>
                    </div>
                    <a href="" class="d-flex justify-content-between align-items-center text-white small">
                        View Details <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row g-4">
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
        $('.progress-bar').each(function() {
            const width = $(this).data('width'); // Get the data-width attribute
            if (width) {
                $(this).css('width', `${width}%`); // Set the width dynamically
            }
        });

        // Fetch chart data using jQuery AJAX
        $.ajax({
            url: '{{ route("analytics.graph") }}',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                // Initialize the chart
                const ctx = $('#bar_chart')[0].getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels, // Dates of the week
                        datasets: [{
                            label: 'Total Revenue (Ksh)',
                            data: data.data, // Revenue values
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
                console.error("Error fetching sales chart data:", error);
            }
        });
    });
</script>
@endpush