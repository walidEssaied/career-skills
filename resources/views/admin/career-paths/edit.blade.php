@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Career Path</h1>
        <a href="{{ route('admin.career-paths.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
            Back to Career Paths
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.career-paths.update', $careerPath) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $careerPath->title) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $careerPath->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="industry" class="block text-sm font-medium text-gray-700">Industry</label>
                <input type="text" name="industry" id="industry" value="{{ old('industry', $careerPath->industry) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('industry')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="required_experience" class="block text-sm font-medium text-gray-700">Required Experience (years)</label>
                <input type="number" name="required_experience" id="required_experience" value="{{ old('required_experience', $careerPath->required_experience) }}" min="0" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('required_experience')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="estimated_salary" class="block text-sm font-medium text-gray-700">Estimated Salary</label>
                <input type="number" name="estimated_salary" id="estimated_salary" value="{{ old('estimated_salary', $careerPath->estimated_salary) }}" min="0" step="1000" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('estimated_salary')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Required Skills</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($skills as $skill)
                        <div class="flex items-center space-x-3">
                            <input type="checkbox" name="skills[]" value="{{ $skill->id }}" id="skill_{{ $skill->id }}"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ in_array($skill->id, old('skills', $careerPath->skills->pluck('id')->toArray())) ? 'checked' : '' }}>
                            <div>
                                <label for="skill_{{ $skill->id }}" class="text-sm font-medium text-gray-700">{{ $skill->name }}</label>
                                <select name="importance_level[{{ $skill->id }}]" class="mt-1 block w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @php
                                        $importance = old(
                                            "importance_level.{$skill->id}",
                                            $careerPath->skills->firstWhere('id', $skill->id)?->pivot?->importance_level ?? 1
                                        );
                                    @endphp
                                    <option value="1" {{ $importance == 1 ? 'selected' : '' }}>Basic</option>
                                    <option value="2" {{ $importance == 2 ? 'selected' : '' }}>Intermediate</option>
                                    <option value="3" {{ $importance == 3 ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('skills')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="metadata" class="block text-sm font-medium text-gray-700">Metadata (JSON)</label>
                <textarea name="metadata" id="metadata" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('metadata', json_encode($careerPath->metadata, JSON_PRETTY_PRINT)) }}</textarea>
                <p class="mt-1 text-sm text-gray-500">Optional. Enter valid JSON data.</p>
                @error('metadata')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Update Career Path
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
