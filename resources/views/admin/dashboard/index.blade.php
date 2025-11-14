@extends('admin.layouts.app')

@section('title', __('admin.dashboard'))

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.statistics') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Active Users Card -->
                        <div class="col-md-3 mb-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stats-text">{{ __('admin.active_users') }}</h6>
                                            <h2 class="stats-number">{{ $activeUsersCount }}</h2>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Products Card -->
                        <div class="col-md-3 mb-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stats-text">{{ __('admin.total_products') }}</h6>
                                            <h2 class="stats-number">{{ $productsCount }}</h2>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-box"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- New Users This Month Card -->
                        <div class="col-md-3 mb-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stats-text">{{ __('admin.new_users_month') }}</h6>
                                            <h2 class="stats-number">{{ $newUsersCount }}</h2>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Activity Logs Card -->
                        <div class="col-md-3 mb-4">
                            <div class="card stats-card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="stats-text">{{ __('admin.activity_logs') }}</h6>
                                            <h2 class="stats-number">{{ $activityLogsCount }}</h2>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-history"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('admin.products_chart') }}</h5>
                </div>
                <div class="card-body">
                    <canvas id="productsChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Products Chart
        var ctx = document.getElementById('productsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: '{{ __("admin.products") }}',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
