@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Career Goals Insights</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Goal Status Statistics -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Goal Status Statistics</h2>
            <div class="space-y-4">
                @foreach($goalStatistics as $stat)
                    <div class="border-b pb-4">
                        <h3 class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $stat->status)) }}</h3>
                        <div class="mt-2 grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Total Goals</p>
                                <p class="font-semibold">{{ $stat->total_goals }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Users</p>
                                <p class="font-semibold">{{ $stat->total_users }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Avg Progress</p>
                                <p class="font-semibold">{{ number_format($stat->average_progress, 1) }}%</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Popular Goal Titles -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Popular Goal Titles</h2>
            <div class="space-y-4">
                @foreach($topGoalTitles as $goal)
                    <div class="flex justify-between items-center border-b pb-2">
                        <span class="text-gray-700">{{ $goal->title }}</span>
                        <span class="text-indigo-600 font-semibold">{{ $goal->total }} users</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
