@extends('layouts.app') {{-- sesuaikan jika nama layout berbeda --}}

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="row">
        <!-- Chart Kasbon -->

        @if(session('user')->role_name === 'hr')
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Kasbon</h6>
                </div>
                <div class="card-body">
                    <canvas id="kasbonChart"></canvas>
                </div>
            </div>
        </div>
        @endif
        <!-- Tempat untuk fitur lain -->
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-body">
                    {{-- Placeholder untuk fitur lain --}}
                    <p class="text-muted">Konten lain akan ditambahkan di sini.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
    const ctx = document.getElementById('kasbonChart').getContext('2d');
    const kasbonChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Menunggu', 'Disetujui', 'Ditolak'],
            datasets: [{
                label: 'Total Nominal Kasbon (Rp)',
                data: [
                    {{ $kasbonChartData['menunggu'] }},
                    {{ $kasbonChartData['disetujui'] }},
                    {{ $kasbonChartData['ditolak'] }}
                ],
                backgroundColor: [
                    '#f6c23e', // menunggu
                    '#1cc88a', // disetujui
                    '#e74a3b'  // ditolak
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    color: '#fff',
                    formatter: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    },
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                },
                legend: {
                    position: 'bottom'
                }
            },
            responsive: true,
            maintainAspectRatio: false
        },
        plugins: [ChartDataLabels]
    });
</script>
@endpush
