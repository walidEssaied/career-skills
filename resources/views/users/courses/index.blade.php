@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Course Management</h1>
            <p class="text-sm text-gray-600">User: {{ $user->name }}</p>
        </div>

        {{-- Enrolled Courses Section --}}
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-lg font-medium text-gray-900">Enrolled Courses</h2>
            </div>
            <div class="border-t border-gray-200">
                <ul class="divide-y divide-gray-200">
                    @forelse($userCourses as $course)
                    <li class="px-4 py-4 sm:px-6 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-medium text-blue-600">{{ $course->title }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ $course->provider }}</p>
                                    </div>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($course->pivot->status === 'completed') bg-green-100 text-green-800
                                            @elseif($course->pivot->status === 'in_progress') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $course->pivot->status)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $course->duration }}
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-600">
                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ ucfirst($course->difficulty_level) }}
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="flex items-center">
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 rounded h-2">
                                                <div class="bg-blue-600 rounded h-2" style="width: {{ $course->pivot->progress }}%"></div>
                                            </div>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-600">{{ $course->pivot->progress }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4">
                                <form action="{{ route('users.courses.destroy', [$user, $course]) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-5 sm:px-6 text-sm text-gray-500">No courses enrolled yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Add New Course Section --}}
        <div class="mt-8 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900">Enroll in New Course</h3>
                <form action="{{ route('users.courses.store', $user) }}" method="POST" class="mt-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="course_id" class="block text-sm font-medium text-gray-700">Course</label>
                            <select id="course_id" name="course_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Select a course</option>
                                @foreach($availableCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }} ({{ $course->provider }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="not_started">Not Started</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="sm:col-span-1">
                            <label for="progress" class="block text-sm font-medium text-gray-700">Progress (%)</label>
                            <input type="number" name="progress" id="progress" min="0" max="100" value="0"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Enroll in Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
