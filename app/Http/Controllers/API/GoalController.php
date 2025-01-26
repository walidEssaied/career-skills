<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::where('user_id', Auth::id())->get();
        return response()->json($goals);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $goal = Goal::create([
            'user_id' => Auth::id(),
            ...$validated
        ]);

        return response()->json($goal, 201);
    }

    public function update(Request $request, Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'target_date' => 'nullable|date',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
        ]);

        $goal->update($validated);

        return response()->json($goal);
    }

    public function destroy(Goal $goal)
    {
        if ($goal->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $goal->delete();

        return response()->json(null, 204);
    }

    public function statistics()
    {
        $user_id = Auth::id();
        
        $total_goals = Goal::where('user_id', $user_id)->count();
        $completed_goals = Goal::where('user_id', $user_id)
            ->where('status', 'completed')
            ->count();
        $in_progress_goals = Goal::where('user_id', $user_id)
            ->where('status', 'in_progress')
            ->count();
        
        return response()->json([
            'total_goals' => $total_goals,
            'completed_goals' => $completed_goals,
            'in_progress_goals' => $in_progress_goals,
            'completion_rate' => $total_goals > 0 ? round(($completed_goals / $total_goals) * 100) : 0
        ]);
    }
}
