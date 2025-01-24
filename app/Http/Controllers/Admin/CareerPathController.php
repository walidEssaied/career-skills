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
        $careerPaths = CareerPath::withCount('users')
            ->with('skills')
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
        return redirect()->route('admin.career-paths.index')->with('success', 'Career path deleted successfully.');
    }

    public function export()
    {
        $careerPaths = CareerPath::with(['skills'])->get();
        
        $csvData = [];
        $csvData[] = [
            'ID', 'Title', 'Description', 'Industry', 'Required Experience', 
            'Estimated Salary', 'Skills', 'Created At', 'Updated At'
        ];

        foreach ($careerPaths as $careerPath) {
            $csvData[] = [
                $careerPath->id,
                $careerPath->title,
                $careerPath->description,
                $careerPath->industry,
                $careerPath->required_experience,
                $careerPath->estimated_salary,
                $careerPath->skills->pluck('name')->implode(', '),
                $careerPath->created_at,
                $careerPath->updated_at,
            ];
        }

        $filename = 'career_paths_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            if (($handle = fopen($path, 'r')) !== false) {
                // Skip header row
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle)) !== false) {
                    $careerPath = CareerPath::create([
                        'title' => $data[1],
                        'description' => $data[2],
                        'industry' => $data[3],
                        'required_experience' => $data[4],
                        'estimated_salary' => $data[5],
                    ]);

                    // Handle skills
                    if (!empty($data[6])) {
                        $skillNames = array_map('trim', explode(',', $data[6]));
                        $skillIds = Skill::whereIn('name', $skillNames)->pluck('id');
                        $careerPath->skills()->attach($skillIds);
                    }
                }
                fclose($handle);
            }
            
            DB::commit();
            return redirect()->route('admin.career-paths.index')
                ->with('success', 'Career paths imported successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.career-paths.index')
                ->with('error', 'Error importing career paths: ' . $e->getMessage());
        }
    }
}
