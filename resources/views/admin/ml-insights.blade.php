@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-900">Machine Learning Insights</h1>
        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600" onclick="refreshInsights()">
            Refresh Data
        </button>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recommendation Accuracy -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Course Recommendation Accuracy</h3>
                <span class="text-2xl font-semibold text-blue-500">{{ number_format($insights['recommendation_accuracy'], 1) }}%</span>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-500 rounded-full h-2" style="width: {{ $insights['recommendation_accuracy'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Career Prediction Accuracy -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Career Prediction Accuracy</h3>
                <span class="text-2xl font-semibold text-green-500">{{ number_format($insights['career_prediction_accuracy'], 1) }}%</span>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-500 rounded-full h-2" style="width: {{ $insights['career_prediction_accuracy'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Most Recommended Courses -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Most Recommended Courses</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($insights['most_recommended_courses'] as $course)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $course->title }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($course->description, 100) }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $course->recommendations_count }} recommendations</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Most Predicted Careers -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-800">Most Predicted Career Paths</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($insights['most_predicted_careers'] as $career)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-900">{{ $career->title }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($career->description, 100) }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500">{{ $career->predictions_count }} predictions</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Average Skill Gap -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Average Skill Gap</h3>
            <span class="text-2xl font-semibold text-orange-500">{{ number_format($insights['average_skill_gap'], 1) }} skills</span>
        </div>
        <p class="mt-2 text-sm text-gray-500">Average number of skills users need to acquire for their target roles</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function refreshInsights() {
    // Add AJAX call to refresh ML insights
    window.location.reload();
}

// Initialize charts if needed
document.addEventListener('DOMContentLoaded', function() {
    // Add any chart initialization here
});
</script>
@endpush
