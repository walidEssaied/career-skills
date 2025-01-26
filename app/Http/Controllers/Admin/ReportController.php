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
        $goalStatistics = DB::table('career_goals')
            ->select('status', DB::raw('COUNT(*) as total_goals'))
            ->addSelect(DB::raw('COUNT(DISTINCT user_id) as total_users'))
            ->addSelect(DB::raw('AVG(progress) as average_progress'))
            ->groupBy('status')
            ->orderByDesc('total_goals')
            ->get();

        $topGoalTitles = DB::table('career_goals')
            ->select('title', DB::raw('COUNT(*) as total'))
            ->groupBy('title')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.reports.career-path-insights', compact('goalStatistics', 'topGoalTitles'));
    }
}
