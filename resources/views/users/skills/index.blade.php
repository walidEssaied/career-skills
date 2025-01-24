@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900">Skills Management</h1>
            <p class="text-sm text-gray-600">User: {{ $user->name }}</p>
        </div>

        {{-- Current Skills Section --}}
        <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h2 class="text-lg font-medium text-gray-900">Current Skills</h2>
            </div>
            <div class="border-t border-gray-200">
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6 font-medium">
                    <div>Skill Name</div>
                    <div>Category</div>
                    <div>Proficiency</div>
                    <div>Target Level</div>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($userSkills as $skill)
                    <li class="px-4 py-4 sm:grid sm:grid-cols-4 sm:gap-4 sm:px-6 hover:bg-gray-50">
                        <div class="text-sm font-medium text-gray-900">{{ $skill->name }}</div>
                        <div class="text-sm text-gray-600">{{ ucfirst($skill->category) }}</div>
                        <div class="text-sm text-gray-600">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded h-2 mr-2">
                                    <div class="bg-blue-600 rounded h-2" style="width: {{ ($skill->pivot->proficiency_level / 5) * 100 }}%"></div>
                                </div>
                                <span>{{ $skill->pivot->proficiency_level }}/5</span>
                            </div>
                        </div>
                        <div class="text-sm text-gray-600 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded h-2 mr-2">
                                    <div class="bg-green-600 rounded h-2" style="width: {{ ($skill->pivot->target_level / 5) * 100 }}%"></div>
                                </div>
                                <span>{{ $skill->pivot->target_level }}/5</span>
                            </div>
                            <form action="{{ route('users.skills.destroy', [$user, $skill]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-5 sm:px-6 text-sm text-gray-500">No skills added yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        {{-- Add New Skill Section --}}
        <div class="mt-8 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900">Add New Skill</h3>
                <form action="{{ route('users.skills.store', $user) }}" method="POST" class="mt-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-2">
                            <label for="skill_id" class="block text-sm font-medium text-gray-700">Skill</label>
                            <select id="skill_id" name="skill_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                <option value="">Select a skill</option>
                                @foreach($availableSkills as $skill)
                                <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="proficiency_level" class="block text-sm font-medium text-gray-700">Current Proficiency</label>
                            <select id="proficiency_level" name="proficiency_level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ ['Beginner', 'Basic', 'Intermediate', 'Advanced', 'Expert'][$i-1] }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="target_level" class="block text-sm font-medium text-gray-700">Target Level</label>
                            <select id="target_level" name="target_level" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ ['Beginner', 'Basic', 'Intermediate', 'Advanced', 'Expert'][$i-1] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Skill
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
