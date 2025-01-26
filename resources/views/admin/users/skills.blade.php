@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 sm:px-0 mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">User Skills</h1>
            <p class="mt-1 text-sm text-gray-600">Skills for {{ $user->name }}</p>
        </div>
        <div class="flex space-x-3">
            <button type="button" onclick="document.getElementById('addSkillModal').classList.remove('hidden')"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Skill
            </button>
            <a href="{{ route('admin.users.show', $user) }}" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Profile
            </a>
        </div>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        @if($skills->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skill</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proficiency</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($skills as $skill)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $skill->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $skill->category }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($skill->pivot->proficiency_level === 'beginner') bg-gray-100 text-gray-800
                                @elseif($skill->pivot->proficiency_level === 'intermediate') bg-blue-100 text-blue-800
                                @elseif($skill->pivot->proficiency_level === 'advanced') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ ucfirst($skill->pivot->proficiency_level) }}
                            </span>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            </td>
                            <button onclick="document.getElementById('editSkillModal{{ $skill->id }}').classList.remove('hidden')"
                                class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                            <form action="{{ route('admin.users.skills.destroy', [$user->id, $skill->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No skills found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding a new skill.</p>
                <div class="mt-6">
                    <button type="button" onclick="document.getElementById('addSkillModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Skill
                    </button>
                </div>
            </div>
        @endif

        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
            {{ $skills->links() }}
        </div>
    </div>
</div>

<!-- Add Skill Modal -->
<div id="addSkillModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form action="{{ route('admin.users.skills.store', $user) }}" method="POST">
                @csrf
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add New Skill</h3>
                        <div class="mt-4">
                            <label for="skill" class="block text-sm font-medium text-gray-700">Skill</label>
                            <select name="skill_id" id="skill" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select a skill</option>
                                @foreach($availableSkills as $skill)
                                    <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="proficiency_level" class="block text-sm font-medium text-gray-700">Current Proficiency Level</label>
                            <select name="proficiency_level" id="proficiency_level" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="target_level" class="block text-sm font-medium text-gray-700">Target Level</label>
                            <select name="target_level" id="target_level" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">Select target level</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Skill
                    </button>
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                        onclick="document.getElementById('addSkillModal').classList.add('hidden')">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
