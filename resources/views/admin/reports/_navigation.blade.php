<div class="bg-white rounded-lg shadow mb-6">
    <div class="flex flex-wrap border-b">
        <a href="{{ route('admin.reports.user-growth') }}" 
           class="px-6 py-3 text-sm font-medium {{ request()->routeIs('admin.reports.user-growth') ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            User Growth
        </a>
        <a href="{{ route('admin.reports.skill-trends') }}" 
           class="px-6 py-3 text-sm font-medium {{ request()->routeIs('admin.reports.skill-trends') ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Skill Trends
        </a>
        <a href="{{ route('admin.reports.course-analytics') }}" 
           class="px-6 py-3 text-sm font-medium {{ request()->routeIs('admin.reports.course-analytics') ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Course Analytics
        </a>
        <a href="{{ route('admin.reports.career-path-insights') }}" 
           class="px-6 py-3 text-sm font-medium {{ request()->routeIs('admin.reports.career-path-insights') ? 'text-blue-600 border-b-2 border-blue-500' : 'text-gray-500 hover:text-gray-700' }}">
            Career Path Insights
        </a>
    </div>
</div>
