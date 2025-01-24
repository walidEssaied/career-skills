<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\CareerPath;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_skills' => Skill::count(),
            'total_career_paths' => CareerPath::count(),
            'total_courses' => Course::count(),
        ];

        $recent_users = User::latest()->take(5)->get();
        $popular_skills = Skill::withCount('users')->orderBy('users_count', 'desc')->take(5)->get();
        $popular_careers = CareerPath::withCount('users')->orderBy('users_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'popular_skills', 'popular_careers'));
    }

    public function skills()
    {
        $skills = Skill::withCount('users')->orderBy('name')->paginate(10);
        return view('admin.skills.index', compact('skills'));
    }

    public function careerPaths()
    {
        $careerPaths = CareerPath::withCount('users')->orderBy('title')->paginate(10);
        return view('admin.career-paths.index', compact('careerPaths'));
    }

    public function courses()
    {
        $courses = Course::withCount('users')->orderBy('title')->paginate(10);
        return view('admin.courses.index', compact('courses'));
    }

    public function users()
    {
        $users = User::with(['skills', 'careerPath'])->orderBy('name')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function mlInsights()
    {
        // Get ML model performance metrics and insights
        $insights = [
            'recommendation_accuracy' => 85.5, // Example value
            'career_prediction_accuracy' => 78.2,
            'most_recommended_courses' => Course::withCount('recommendations')
                ->orderBy('recommendations_count', 'desc')
                ->take(5)
                ->get(),
            'most_predicted_careers' => CareerPath::withCount('predictions')
                ->orderBy('predictions_count', 'desc')
                ->take(5)
                ->get(),
            'average_skill_gap' => 4.2, // Example value
        ];

        return view('admin.ml-insights', compact('insights'));
    }
}
