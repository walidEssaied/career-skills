<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skill;
use App\Models\Course;
use App\Models\CareerGoal;
use App\Models\CareerPath;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Create Skills
        $skills = [
            [
                'name' => 'PHP',
                'category' => 'technical',
                'description' => 'Server-side scripting language designed for web development'
            ],
            [
                'name' => 'Laravel',
                'category' => 'technical',
                'description' => 'PHP web application framework with expressive, elegant syntax'
            ],
            [
                'name' => 'Vue.js',
                'category' => 'technical',
                'description' => 'Progressive JavaScript framework for building user interfaces'
            ],
            [
                'name' => 'React',
                'category' => 'technical',
                'description' => 'JavaScript library for building user interfaces'
            ],
            [
                'name' => 'JavaScript',
                'category' => 'technical',
                'description' => 'Programming language that enables interactive web pages'
            ],
            [
                'name' => 'Python',
                'category' => 'technical',
                'description' => 'High-level programming language for general-purpose programming'
            ],
            [
                'name' => 'Docker',
                'category' => 'technical',
                'description' => 'Platform for developing, shipping, and running applications in containers'
            ],
            [
                'name' => 'AWS',
                'category' => 'technical',
                'description' => 'Comprehensive cloud computing platform'
            ],
            [
                'name' => 'SQL',
                'category' => 'technical',
                'description' => 'Standard language for storing, manipulating and retrieving data in databases'
            ],
            [
                'name' => 'MongoDB',
                'category' => 'technical',
                'description' => 'NoSQL database program that uses JSON-like documents'
            ],
            [
                'name' => 'Git',
                'category' => 'technical',
                'description' => 'Distributed version control system for tracking changes in source code'
            ],
            [
                'name' => 'Agile',
                'category' => 'soft_skill',
                'description' => 'Project management approach that emphasizes flexibility and continuous improvement'
            ],
            [
                'name' => 'Scrum',
                'category' => 'soft_skill',
                'description' => 'Agile framework for managing complex projects'
            ],
            [
                'name' => 'UI/UX Design',
                'category' => 'technical',
                'description' => 'Process of designing user interfaces and experiences'
            ],
            [
                'name' => 'TypeScript',
                'category' => 'technical',
                'description' => 'Typed superset of JavaScript that compiles to plain JavaScript'
            ],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // Create Courses
        $courses = [
            [
                'title' => 'Advanced Laravel Development',
                'description' => 'Master Laravel framework with advanced concepts and best practices',
                'provider' => 'Laracasts',
                'url' => 'https://laracasts.com/series/advanced-laravel',
                'duration' => '12 weeks',
                'difficulty_level' => 'advanced',
                'price' => 29.99,
                'language' => 'English',
                'certificate_offered' => true,
            ],
            [
                'title' => 'Vue.js for Beginners',
                'description' => 'Learn the basics of Vue.js framework',
                'provider' => 'Vue School',
                'url' => 'https://vueschool.io/courses/vuejs-fundamentals',
                'duration' => '8 weeks',
                'difficulty_level' => 'beginner',
                'price' => 19.99,
                'language' => 'English',
                'certificate_offered' => true,
            ],
            [
                'title' => 'Full Stack JavaScript',
                'description' => 'Comprehensive JavaScript course covering both frontend and backend',
                'provider' => 'Udemy',
                'url' => 'https://udemy.com/course/fullstack-javascript',
                'duration' => '16 weeks',
                'difficulty_level' => 'intermediate',
                'price' => 49.99,
                'language' => 'English',
                'certificate_offered' => true,
            ],
            [
                'title' => 'AWS Cloud Practitioner',
                'description' => 'Prepare for AWS Cloud Practitioner certification',
                'provider' => 'AWS Training',
                'url' => 'https://aws.training/certification',
                'duration' => '6 weeks',
                'difficulty_level' => 'beginner',
                'price' => 99.99,
                'language' => 'English',
                'certificate_offered' => true,
            ],
            [
                'title' => 'DevOps Essentials',
                'description' => 'Learn essential DevOps tools and practices',
                'provider' => 'Linux Academy',
                'url' => 'https://linuxacademy.com/course/devops-essentials',
                'duration' => '10 weeks',
                'difficulty_level' => 'intermediate',
                'price' => 79.99,
                'language' => 'English',
                'certificate_offered' => true,
            ],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        // Create Career Paths
        $careerPaths = [
            [
                'title' => 'Full Stack Developer',
                'description' => 'Become a well-rounded developer proficient in both frontend and backend technologies',
                'industry' => 'Software Development',
                'required_experience' => 3,
                'salary_range_min' => 70000.00,
                'salary_range_max' => 150000.00,
                'growth_potential' => 'High',
                'market_demand' => 'Very High',
            ],
            [
                'title' => 'UI/UX Designer',
                'description' => 'Master the art and science of creating beautiful and functional user interfaces',
                'industry' => 'Design',
                'required_experience' => 2,
                'salary_range_min' => 60000.00,
                'salary_range_max' => 130000.00,
                'growth_potential' => 'High',
                'market_demand' => 'High',
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'Learn to automate and streamline development and deployment processes',
                'industry' => 'DevOps',
                'required_experience' => 4,
                'salary_range_min' => 80000.00,
                'salary_range_max' => 160000.00,
                'growth_potential' => 'Very High',
                'market_demand' => 'Very High',
            ],
        ];

        $createdCareerPaths = [];
        foreach ($careerPaths as $path) {
            $createdCareerPaths[] = CareerPath::create($path);
        }

        // Create Users with their skills, courses, and career goals
        $users = [
            [
                'name' => 'John Developer',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'skills' => [
                    ['id' => 1, 'proficiency_level' => 2, 'target_level' => 4], // PHP
                    ['id' => 2, 'proficiency_level' => 1, 'target_level' => 3], // Laravel
                    ['id' => 5, 'proficiency_level' => 4, 'target_level' => 5], // JavaScript
                ],
                'courses' => [
                    ['id' => 1, 'progress' => 75, 'status' => 'in_progress'],
                    ['id' => 2, 'progress' => 100, 'status' => 'completed'],
                ],
                'goals' => [
                    [
                        'career_path_id' => 1,
                        'target_completion_date' => now()->addMonths(6),
                        'status' => 'in_progress',
                        'progress' => 30,
                        'notes' => 'Focus on Laravel and Vue.js stack',
                    ],
                ],
            ],
            [
                'name' => 'Sarah Designer',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'skills' => [
                    ['id' => 14, 'proficiency_level' => 4, 'target_level' => 5], // UI/UX Design
                    ['id' => 3, 'proficiency_level' => 2, 'target_level' => 4], // Vue.js
                    ['id' => 5, 'proficiency_level' => 2, 'target_level' => 4], // JavaScript
                ],
                'courses' => [
                    ['id' => 2, 'progress' => 50, 'status' => 'in_progress'],
                    ['id' => 3, 'progress' => 25, 'status' => 'in_progress'],
                ],
                'goals' => [
                    [
                        'career_path_id' => 2,
                        'target_completion_date' => now()->addMonths(12),
                        'status' => 'not_started',
                        'progress' => 0,
                        'notes' => 'Focus on team leadership and advanced design principles',
                    ],
                ],
            ],
            [
                'name' => 'Mike DevOps',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'skills' => [
                    ['id' => 7, 'proficiency_level' => 4, 'target_level' => 5], // Docker
                    ['id' => 8, 'proficiency_level' => 2, 'target_level' => 4], // AWS
                    ['id' => 11, 'proficiency_level' => 4, 'target_level' => 5], // Git
                ],
                'courses' => [
                    ['id' => 4, 'progress' => 60, 'status' => 'in_progress'],
                    ['id' => 5, 'progress' => 30, 'status' => 'in_progress'],
                ],
                'goals' => [
                    [
                        'career_path_id' => 3,
                        'target_completion_date' => now()->addMonths(3),
                        'status' => 'in_progress',
                        'progress' => 40,
                        'notes' => 'Focus on AWS services and best practices',
                    ],
                ],
            ],
            [
                'name' => 'Admin Manager',
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'skills' => [
                    ['id' => 12, 'proficiency_level' => 4, 'target_level' => 5], // Agile
                    ['id' => 13, 'proficiency_level' => 4, 'target_level' => 5], // Scrum
                ],
                'goals' => [
                    [
                        'career_path_id' => 3,
                        'target_completion_date' => now()->addMonths(8),
                        'status' => 'in_progress',
                        'progress' => 20,
                        'notes' => 'Focus on Agile transformation and team training',
                    ],
                ],
            ],
        ];

        foreach ($users as $userData) {
            $skills = $userData['skills'];
            $courses = $userData['courses'] ?? [];
            $goals = $userData['goals'];
            
            unset($userData['skills'], $userData['courses'], $userData['goals']);
            
            $user = User::create($userData);
            
            foreach ($skills as $skill) {
                $user->skills()->attach($skill['id'], [
                    'proficiency_level' => $skill['proficiency_level'],
                    'target_level' => $skill['target_level'],
                ]);
            }
            
            foreach ($courses as $course) {
                $user->courses()->attach($course['id'], [
                    'progress' => $course['progress'],
                    'status' => $course['status'],
                    'completion_date' => $course['status'] === 'completed' ? now() : null,
                ]);
            }
            
            foreach ($goals as $goal) {
                $user->careerGoals()->create($goal);
            }
        }
    }
}
