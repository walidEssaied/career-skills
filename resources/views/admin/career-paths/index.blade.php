@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Career Paths Management</h1>
        <div class="flex space-x-4">
            <a href="{{ route('admin.career-paths.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Add New Career Path
            </a>
            <button onclick="document.getElementById('importForm').classList.toggle('hidden')" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                Import Career Paths
            </button>
            <a href="{{ route('admin.career-paths.export') }}" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                Export Career Paths
            </a>
        </div>
    </div>

    <!-- Import Form -->
    <div id="importForm" class="hidden bg-white p-6 rounded-lg shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Import Career Paths</h2>
        <form action="{{ route('admin.career-paths.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="file" class="block text-sm font-medium text-gray-700">Choose CSV File</label>
                <input type="file" name="file" id="file" accept=".csv" required
                    class="mt-1 block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100">
                <p class="mt-1 text-sm text-gray-500">
                    File should be a CSV with columns: Title, Description, Required Experience (Years), Total Users, Required Skills
                </p>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Import
                </button>
            </div>
        </form>
    </div>

    <!-- Career Paths Table -->
    <div class="bg-white shadow-sm rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Industry</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skills</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($careerPaths as $path)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $path->title }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $path->industry }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $path->required_experience }} years</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($path->skills->take(3) as $skill)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                                @if($path->skills->count() > 3)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        +{{ $path->skills->count() - 3 }} more
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $path->users_count ?? 0 }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('admin.career-paths.edit', $path) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                <form action="{{ route('admin.career-paths.destroy', $path) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this career path?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t">
            {{ $careerPaths->links() }}
        </div>
    </div>
</div>
@endsection
