@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @include('admin.reports._navigation')
    
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">User Growth Report</h1>
        <div class="flex space-x-4">
            <button onclick="downloadChart()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Download Chart
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <canvas id="userGrowthChart"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Detailed Data</h2>
        </div>
        <div class="divide-y">
            @foreach($userGrowth as $data)
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">{{ $data->date }}</span>
                    <span class="text-gray-900 font-medium">{{ $data->total }} new users</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctx = document.getElementById('userGrowthChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userGrowth->pluck('date')) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userGrowth->pluck('total')) !!},
                borderColor: '#4F46E5',
                backgroundColor: '#4F46E510',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'User Growth Over Time'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    function downloadChart() {
        const link = document.createElement('a');
        link.download = 'user-growth-chart.png';
        link.href = chart.toBase64Image();
        link.click();
    }
</script>
@endpush
