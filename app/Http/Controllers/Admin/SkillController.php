<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.skills.index', compact('skills'));
    }

    public function create()
    {
        return view('admin.skills.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,soft_skill,language',
            'metadata' => 'nullable|json',
        ]);

        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        Skill::create($validated);

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill created successfully.');
    }

    public function edit(Skill $skill)
    {
        return view('admin.skills.edit', compact('skill'));
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:technical,soft_skill,language',
            'metadata' => 'nullable|json',
        ]);

        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        $skill->update($validated);

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();

        return redirect()
            ->route('admin.skills.index')
            ->with('success', 'Skill deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        DB::beginTransaction();
        try {
            $handle = fopen($path, 'r');
            $header = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                if (isset($data['metadata'])) {
                    $data['metadata'] = json_decode($data['metadata'], true);
                }
                Skill::create($data);
            }
            
            fclose($handle);
            DB::commit();
            
            return redirect()
                ->route('admin.skills.index')
                ->with('success', 'Skills imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.skills.index')
                ->with('error', 'Error importing skills: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $skills = Skill::all();
        $filename = 'skills-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($skills) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['name', 'description', 'category', 'metadata']);
            
            foreach ($skills as $skill) {
                fputcsv($file, [
                    $skill->name,
                    $skill->description,
                    $skill->category,
                    json_encode($skill->metadata)
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
