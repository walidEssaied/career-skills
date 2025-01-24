<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerPath;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function userGrowth()
    {
        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        return view('admin.reports.user-growth', compact('userGrowth'));
    }

    public function skillTrends()
    {
        $popularSkills = DB::table('user_skills')
            ->select('skills.name', DB::raw('count(*) as total'))
            ->join('skills', 'skills.id', '=', 'user_skills.skill_id')
            ->groupBy('skills.id', 'skills.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.reports.skill-trends', compact('popularSkills'));
    }

    public function courseAnalytics()
    {
        $courseEnrollments = Course::withCount('users')->orderByDesc('users_count')->limit(10)->get();
        
        $completionRates = CourseEnrollment::select('course_id')
            ->whereNotNull('completed_at')
            ->groupBy('course_id')
            ->selectRaw('course_id, COUNT(*) as completed_count, 
                (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM course_enrollments WHERE course_id = course_enrollments.course_id)) as completion_rate')
            ->orderByDesc('completion_rate')
            ->limit(10)
            ->get();

        return view('admin.reports.course-analytics', compact('courseEnrollments', 'completionRates'));
    }

    public function careerPathInsights()
    {
        $popularPaths = CareerPath::withCount('users')
            ->orderByDesc('users_count')
            ->limit(10)
            ->get();

        $progressData = DB::table('career_goals')
            ->select('career_paths.title', DB::raw('AVG(career_goals.progress) as avg_progress'))
            ->join('career_paths', 'career_paths.id', '=', 'career_goals.career_path_id')
            ->groupBy('career_paths.id', 'career_paths.title')
            ->orderByDesc('avg_progress')
            ->limit(10)
            ->get();

        return view('admin.reports.career-path-insights', compact('popularPaths', 'progressData'));
    }
}
