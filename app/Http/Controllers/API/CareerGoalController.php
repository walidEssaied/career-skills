<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CareerGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CareerGoalController extends Controller
{
    public function index()
    {
        $goals = CareerGoal::where('user_id', Auth::id())->get();
        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'target_date' => 'required|date',
            'status' => 'required|in:not_started,in_progress,completed,on_hold',
            'notes' => 'nullable|string',
        ]);

        $goal = CareerGoal::create([
            'user_id' => Auth::id(),
            'progress' => 0,
            ...$validated
        ]);

        return response()->json($goal, 201);
    }

    public function show(CareerGoal $careerGoal)
    {
        if ($careerGoal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($careerGoal);
    }

    public function update(Request $request, CareerGoal $careerGoal)
    {
        if ($careerGoal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'target_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:not_started,in_progress,completed,on_hold',
            'notes' => 'nullable|string',
        ]);

        $careerGoal->update($validated);

        return response()->json($careerGoal);
    }

    public function destroy(CareerGoal $careerGoal)
    {
        if ($careerGoal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $careerGoal->delete();
        return response()->json(null, 204);
    }

    public function updateProgress(Request $request, CareerGoal $careerGoal)
    {
        if ($careerGoal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $careerGoal->update($validated);

        return response()->json($careerGoal);
    }

    public function statistics()
    {
        $statistics = [
            'by_status' => CareerGoal::where('user_id', Auth::id())
                ->select('status', \DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get(),
            'total_goals' => CareerGoal::where('user_id', Auth::id())->count(),
            'completed_goals' => CareerGoal::where('user_id', Auth::id())
                ->where('status', 'completed')
                ->count(),
            'average_progress' => CareerGoal::where('user_id', Auth::id())
                ->avg('progress')
        ];

        return response()->json($statistics);
    }
}
