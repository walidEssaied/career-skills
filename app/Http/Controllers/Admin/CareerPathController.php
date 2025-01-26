<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareerPath;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CareerPathController extends Controller
{
    public function index()
    {
        $careerPaths = CareerPath::with(['skills', 'users'])
            ->withCount('users')
            ->orderBy('title')
            ->paginate(1000);
        return view('admin.career-paths.index', compact('careerPaths'));
    }

    public function create()
    {
        $skills = Skill::orderBy('name')->get();
        return view('admin.career-paths.create', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'industry' => 'required|string|max:255',
            'required_experience' => 'required|integer|min:0',
            'estimated_salary' => 'required|numeric|min:0',
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,id',
            'importance_level' => 'required|array',
            'importance_level.*' => 'required|integer|min:1|max:3',
            'metadata' => 'nullable|json',
        ]);

        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        DB::beginTransaction();
        try {
            $careerPath = CareerPath::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'industry' => $validated['industry'],
                'required_experience' => $validated['required_experience'],
                'estimated_salary' => $validated['estimated_salary'],
                'metadata' => $validated['metadata'] ?? null,
            ]);

            foreach ($validated['skills'] as $skillId) {
                $careerPath->skills()->attach($skillId, [
                    'importance_level' => $validated['importance_level'][$skillId],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.career-paths.index')
                ->with('success', 'Career path created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error creating career path: ' . $e->getMessage());
        }
    }

    public function edit(CareerPath $careerPath)
    {
        $skills = Skill::orderBy('name')->get();
        return view('admin.career-paths.edit', compact('careerPath', 'skills'));
    }

    public function update(Request $request, CareerPath $careerPath)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'industry' => 'required|string|max:255',
            'required_experience' => 'required|integer|min:0',
            'estimated_salary' => 'required|numeric|min:0',
            'skills' => 'required|array|min:1',
            'skills.*' => 'exists:skills,id',
            'importance_level' => 'required|array',
            'importance_level.*' => 'required|integer|min:1|max:3',
            'metadata' => 'nullable|json',
        ]);

        if (isset($validated['metadata'])) {
            $validated['metadata'] = json_decode($validated['metadata'], true);
        }

        DB::beginTransaction();
        try {
            $careerPath->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'industry' => $validated['industry'],
                'required_experience' => $validated['required_experience'],
                'estimated_salary' => $validated['estimated_salary'],
                'metadata' => $validated['metadata'] ?? null,
            ]);

            $syncData = [];
            foreach ($validated['skills'] as $skillId) {
                $syncData[$skillId] = [
                    'importance_level' => $validated['importance_level'][$skillId],
                ];
            }
            $careerPath->skills()->sync($syncData);

            DB::commit();

            return redirect()
                ->route('admin.career-paths.index')
                ->with('success', 'Career path updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error updating career path: ' . $e->getMessage());
        }
    }

    public function destroy(CareerPath $careerPath)
    {
        $careerPath->delete();
        return redirect()->route('admin.career-paths.index')
            ->with('success', 'Career path deleted successfully.');
    }

    public function export()
    {
        $careerPaths = CareerPath::with(['skills', 'users'])->get();
        
        $csvFileName = 'career_paths_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $csvFileName . '"',
        ];

        $handle = fopen('php://temp', 'w+');
        
        // Add headers
        fputcsv($handle, ['Title', 'Description', 'Required Experience (Years)', 'Total Users', 'Required Skills']);

        // Add data
        foreach ($careerPaths as $path) {
            $skills = $path->skills->pluck('name')->implode(', ');
            fputcsv($handle, [
                $path->title,
                $path->description,
                $path->required_experience_years,
                $path->users->count(),
                $skills
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle); // Skip header row
        
        $importCount = 0;
        $errors = [];
        
        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 3) { // Ensure we have at least title, description, and experience
                    $careerPath = CareerPath::create([
                        'title' => $row[0],
                        'description' => $row[1],
                        'required_experience_years' => intval($row[2])
                    ]);
                    
                    // If skills are provided in the CSV (column 4)
                    if (isset($row[4]) && !empty($row[4])) {
                        $skillNames = array_map('trim', explode(',', $row[4]));
                        foreach ($skillNames as $skillName) {
                            $skill = Skill::firstOrCreate(['name' => $skillName]);
                            $careerPath->skills()->attach($skill->id, ['importance_level' => 3]); // Default importance
                        }
                    }
                    
                    $importCount++;
                } else {
                    $errors[] = "Row " . ($importCount + 2) . " has invalid format";
                }
            }
            
            DB::commit();
            fclose($handle);
            
            return redirect()->route('admin.career-paths.index')
                ->with('success', "Successfully imported {$importCount} career paths" . 
                    (count($errors) > 0 ? " with " . count($errors) . " errors" : ""));
                    
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            
            return redirect()->route('admin.career-paths.index')
                ->with('error', 'Error importing career paths: ' . $e->getMessage());
        }
    }
}
