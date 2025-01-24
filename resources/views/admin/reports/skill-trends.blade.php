@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @include('admin.reports._navigation')
    
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Skill Trends Report</h1>
        <div class="flex space-x-4">
            <button onclick="downloadChart()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Download Chart
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <canvas id="skillTrendsChart"></canvas>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Top Skills</h2>
        </div>
        <div class="p-6">
            <!-- Skill trends data will be displayed here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('skillTrendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Will be populated with dates
            datasets: [] // Will be populated with skill data
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});

function downloadChart() {
    const canvas = document.getElementById('skillTrendsChart');
    const link = document.createElement('a');
    link.download = 'skill-trends.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
}
</script>
@endpush
