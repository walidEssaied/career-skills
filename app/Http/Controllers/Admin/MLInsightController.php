<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerPath;

class MLInsightController extends Controller
{
    public function index()
    {
        // Get data for ML insights
        $totalUsers = User::count();
        $totalSkills = Skill::count();
        $totalCourses = Course::count();
        $totalCareerPaths = CareerPath::count();

        return view('admin.ml-insights', compact(
            'totalUsers',
            'totalSkills',
            'totalCourses',
            'totalCareerPaths'
        ));
    }
}
