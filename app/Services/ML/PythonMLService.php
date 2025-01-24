<?php

namespace App\Services\ML;

use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

class PythonMLService
{
    protected $config;

    public function __construct(array $config = null)
    {
        $this->config = $config ?? config('ml');
    }

    public function recommendCourses(array $userSkills)
    {
        try {
            $process = new Process([
                $this->config['python_path'],
                $this->config['script_path'],
                'recommend_courses',
                json_encode($userSkills)
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('ML Service Error: ' . $process->getErrorOutput());
                return [];
            }

            return json_decode($process->getOutput(), true);
        } catch (\Exception $e) {
            Log::error('ML Service Exception: ' . $e->getMessage());
            return [];
        }
    }

    public function predictCareerPath(array $userProfile)
    {
        try {
            $process = new Process([
                $this->config['python_path'],
                $this->config['script_path'],
                'predict_career',
                json_encode($userProfile)
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('ML Service Error: ' . $process->getErrorOutput());
                return [];
            }

            return json_decode($process->getOutput(), true);
        } catch (\Exception $e) {
            Log::error('ML Service Exception: ' . $e->getMessage());
            return [];
        }
    }

    public function analyzeSkillGaps(array $userSkills, array $targetRoleSkills)
    {
        try {
            $process = new Process([
                $this->config['python_path'],
                $this->config['script_path'],
                'analyze_gaps',
                json_encode([
                    'user_skills' => $userSkills,
                    'target_skills' => $targetRoleSkills
                ])
            ]);

            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('ML Service Error: ' . $process->getErrorOutput());
                return [];
            }

            return json_decode($process->getOutput(), true);
        } catch (\Exception $e) {
            Log::error('ML Service Exception: ' . $e->getMessage());
            return [];
        }
    }
}
