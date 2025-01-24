<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CareerPath;
use App\Models\Skill;

class CareerPathsSeeder extends Seeder
{
    public function run()
    {
        $careerPaths = [
            [
                'title' => 'Full Stack Developer',
                'description' => 'Developer proficient in both frontend and backend technologies',
                'industry' => 'Software Development',
                'required_experience' => 2,
                'salary_range_min' => 70000,
                'salary_range_max' => 120000,
                'growth_potential' => 'high',
                'market_demand' => 'high',
                'required_skills' => ['JavaScript', 'PHP', 'SQL', 'React', 'Laravel']
            ],
            [
                'title' => 'Data Scientist',
                'description' => 'Professional who analyzes and interprets complex data',
                'industry' => 'Data Science',
                'required_experience' => 3,
                'salary_range_min' => 80000,
                'salary_range_max' => 140000,
                'growth_potential' => 'high',
                'market_demand' => 'high',
                'required_skills' => ['Python', 'Machine Learning', 'Statistics', 'SQL', 'Data Analysis']
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Engineer focusing on deployment, automation, and infrastructure',
                'industry' => 'DevOps',
                'required_experience' => 3,
                'salary_range_min' => 85000,
                'salary_range_max' => 130000,
                'growth_potential' => 'high',
                'market_demand' => 'high',
                'required_skills' => ['Docker', 'AWS', 'CI/CD', 'Git', 'Python']
            ],
            [
                'title' => 'Frontend Developer',
                'description' => 'Developer specializing in user interfaces and experience',
                'industry' => 'Web Development',
                'required_experience' => 1,
                'salary_range_min' => 65000,
                'salary_range_max' => 110000,
                'growth_potential' => 'medium',
                'market_demand' => 'medium',
                'required_skills' => ['JavaScript', 'React', 'HTML', 'CSS']
            ],
            [
                'title' => 'Backend Developer',
                'description' => 'Developer focusing on server-side logic and databases',
                'industry' => 'Web Development',
                'required_experience' => 2,
                'salary_range_min' => 70000,
                'salary_range_max' => 115000,
                'growth_potential' => 'medium',
                'market_demand' => 'medium',
                'required_skills' => ['PHP', 'Laravel', 'SQL', 'Java', 'Spring Boot']
            ]
        ];

        foreach ($careerPaths as $path) {
            $requiredSkills = $path['required_skills'];
            unset($path['required_skills']);
            
            $careerPath = CareerPath::create($path);
            
            // Attach skills to career path
            $skills = Skill::whereIn('name', $requiredSkills)->get();
            foreach ($skills as $skill) {
                $careerPath->skills()->attach($skill->id, [
                    'importance_level' => 1
                ]);
            }
        }
    }
}
