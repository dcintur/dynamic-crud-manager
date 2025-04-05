@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-file"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pagine Dinamiche</span>
                    <span class="info-box-number">{{ $stats['total_pages'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-database"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Records Totali</span>
                    <span class="info-box-number">{{ $stats['total_records'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Utenti</span>
                    <span class="info-box-number">{{ $stats['total_users'] }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pagine Attive</span>
                    <span class="info-box-number">{{ $stats['active_pages'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Distribuzione dei Dati</h3>
                </div>
                <div class="card-body">
                    <canvas id="dataDistributionChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Popular pages -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pagine Più Popolate</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @foreach($popularPages as $page)
                        <li class="item">
                            <div class="product-img">
                                <i class="fas fa-file-alt fa-2x text-info"></i>
                            </div>
                            <div class="product-info">
                                <a href="{{ route('dynamic-data.page', $page) }}" class="product-title">
                                    {{ $page->name }}
                                    <span class="badge badge-primary float-right">{{ $page->data_count }}</span>
                                </a>
                                <span class="product-description">
                                    {{ $page->fields->count() }} campi
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent activity -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Attività Recenti</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pagina</th>
                                    <th>Dati</th>
                                    <th>Creato</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivity as $activity)
                                <tr>
                                    <td>{{ $activity->id }}</td>
                                    <td>{{ $activity->page->name }}</td>
                                    <td>
                                        @php
                                            $firstField = $activity->page->fields->first();
                                            echo $firstField ? ($activity->data[$firstField->name] ?? 'N/A') : 'N/A';
                                        @endphp
                                    </td>
                                    <td>{{ $activity->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    // Grafico distribuzione dati
    var ctx = document.getElementById('dataDistributionChart').getContext('2d');
    var data = @json($dataDistribution);
    
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.label),
            datasets: [{
                data: data.map(item => item.value),
                backgroundColor: [
                    '#3c8dbc', '#00a65a', '#f39c12', '#00c0ef', '#f56954',
                    '#d2d6de', '#6f42c1', '#e83e8c', '#fd7e14', '#20c997'
                ],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@stop