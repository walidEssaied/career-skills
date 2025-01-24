<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;

class SkillsSeeder extends Seeder
{
    public function run()
    {
        $skills = [
            // Technical Skills
            ['name' => 'Python', 'category' => 'technical', 'description' => 'Python programming language'],
            ['name' => 'JavaScript', 'category' => 'technical', 'description' => 'JavaScript programming language'],
            ['name' => 'PHP', 'category' => 'technical', 'description' => 'PHP programming language'],
            ['name' => 'Java', 'category' => 'technical', 'description' => 'Java programming language'],
            ['name' => 'Laravel', 'category' => 'technical', 'description' => 'PHP web application framework'],
            ['name' => 'React', 'category' => 'technical', 'description' => 'JavaScript library for building user interfaces'],
            ['name' => 'Django', 'category' => 'technical', 'description' => 'Python web framework'],
            ['name' => 'Spring Boot', 'category' => 'technical', 'description' => 'Java-based framework'],
            ['name' => 'Machine Learning', 'category' => 'technical', 'description' => 'Building systems that can learn from data'],
            ['name' => 'Data Analysis', 'category' => 'technical', 'description' => 'Analyzing data to find insights'],
            ['name' => 'SQL', 'category' => 'technical', 'description' => 'Database query language'],
            ['name' => 'Docker', 'category' => 'technical', 'description' => 'Containerization platform'],
            ['name' => 'Git', 'category' => 'technical', 'description' => 'Version control system'],
            ['name' => 'AWS', 'category' => 'technical', 'description' => 'Amazon Web Services cloud platform'],
            ['name' => 'CI/CD', 'category' => 'technical', 'description' => 'Continuous Integration and Deployment'],
            
            // Soft Skills
            ['name' => 'Communication', 'category' => 'soft_skill', 'description' => 'Effective communication skills'],
            ['name' => 'Problem Solving', 'category' => 'soft_skill', 'description' => 'Analytical and problem-solving abilities'],
            ['name' => 'Team Leadership', 'category' => 'soft_skill', 'description' => 'Leading and managing teams'],
            ['name' => 'Project Management', 'category' => 'soft_skill', 'description' => 'Managing projects and resources'],
            
            // Languages
            ['name' => 'English', 'category' => 'language', 'description' => 'Professional English language proficiency'],
            ['name' => 'Spanish', 'category' => 'language', 'description' => 'Professional Spanish language proficiency'],
            ['name' => 'Mandarin', 'category' => 'language', 'description' => 'Professional Mandarin language proficiency']
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }
    }
}
