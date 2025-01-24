<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get counts
        $totalUsers = User::count();
        $totalSkills = Skill::count();
        $totalCourses = Course::count();
        $totalCareerPaths = CareerPath::count();

        // Get most popular skills
        $popularSkills = DB::table('user_skills')
            ->select('skills.name', DB::raw('count(*) as total'))
            ->join('skills', 'skills.id', '=', 'user_skills.skill_id')
            ->groupBy('skills.id', 'skills.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Get most popular career paths
        $popularCareerPaths = DB::table('career_goals')
            ->select('career_paths.title', DB::raw('count(*) as total'))
            ->join('career_paths', 'career_paths.id', '=', 'career_goals.career_path_id')
            ->groupBy('career_paths.id', 'career_paths.title')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Get recent user registrations
        $recentUsers = User::orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Get course enrollment statistics for the last 7 days
        $courseEnrollments = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $total = DB::table('course_enrollments')
                ->whereDate('created_at', $date)
                ->count();
            
            $courseEnrollments->push([
                'date' => $date,
                'total' => $total
            ]);
        }
        $courseEnrollments = collect($courseEnrollments)->map(function ($item) {
            return (object) $item;
        });

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalSkills',
            'totalCourses',
            'totalCareerPaths',
            'popularSkills',
            'popularCareerPaths',
            'recentUsers',
            'courseEnrollments'
        ));
    }

    public function analytics()
    {
        // User growth over time
        $userGrowth = DB::table('users')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Skill acquisition rate
        $skillAcquisition = DB::table('user_skills')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Average skill proficiency by category
        $skillProficiency = DB::table('user_skills')
            ->select(
                'skills.category',
                DB::raw('avg(user_skills.proficiency_level) as average_level')
            )
            ->join('skills', 'skills.id', '=', 'user_skills.skill_id')
            ->groupBy('skills.category')
            ->get();

        // Course completion rates
        $courseCompletion = DB::table('course_enrollments')
            ->select(
                'courses.title',
                DB::raw('count(*) as total_enrollments'),
                DB::raw('sum(case when completed_at is not null then 1 else 0 end) as completions')
            )
            ->join('courses', 'courses.id', '=', 'course_enrollments.course_id')
            ->groupBy('courses.id', 'courses.title')
            ->orderByDesc('total_enrollments')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'userGrowth',
            'skillAcquisition',
            'skillProficiency',
            'courseCompletion'
        ));
    }
}
