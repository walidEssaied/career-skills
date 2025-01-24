<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Skill;
use App\Models\CareerPath;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Complete Web Development Bootcamp',
                'description' => 'Become a full-stack web developer with this comprehensive course covering HTML, CSS, JavaScript, Node.js, React, and more.',
                'provider' => 'Udemy',
                'url' => 'https://www.udemy.com/course/web-development-bootcamp',
                'duration' => 63.5,
                'difficulty_level' => 'beginner',
                'price' => 89.99,
                'language' => 'English',
                'rating' => 4.7,
                'reviews_count' => 15840,
                'certificate_offered' => true,
                'metadata' => [
                    'prerequisites' => ['Basic computer skills'],
                    'topics_covered' => ['HTML', 'CSS', 'JavaScript', 'Node.js', 'React', 'MongoDB'],
                    'projects_included' => 15
                ]
            ],
            [
                'title' => 'Machine Learning A-Z™',
                'description' => 'Learn and master Machine Learning using Python, R, and cutting-edge techniques like Deep Learning and Natural Language Processing.',
                'provider' => 'Udemy',
                'url' => 'https://www.udemy.com/course/machine-learning-a-z',
                'duration' => 44.5,
                'difficulty_level' => 'intermediate',
                'price' => 94.99,
                'language' => 'English',
                'rating' => 4.5,
                'reviews_count' => 12670,
                'certificate_offered' => true,
                'metadata' => [
                    'prerequisites' => ['Basic Python', 'Mathematics'],
                    'topics_covered' => ['Regression', 'Classification', 'Clustering', 'Deep Learning'],
                    'projects_included' => 10
                ]
            ],
            [
                'title' => 'AWS Certified Solutions Architect',
                'description' => 'Prepare for the AWS Solutions Architect Associate certification with hands-on labs and real-world scenarios.',
                'provider' => 'A Cloud Guru',
                'url' => 'https://acloud.guru/aws-certified-solutions-architect-associate',
                'duration' => 32.0,
                'difficulty_level' => 'intermediate',
                'price' => 149.99,
                'language' => 'English',
                'rating' => 4.8,
                'reviews_count' => 8940,
                'certificate_offered' => true,
                'metadata' => [
                    'prerequisites' => ['Basic IT knowledge'],
                    'topics_covered' => ['EC2', 'S3', 'VPC', 'RDS', 'Lambda'],
                    'certification_exam_included' => true
                ]
            ],
            [
                'title' => 'UI/UX Design Fundamentals',
                'description' => 'Master the principles of user interface and user experience design using industry-standard tools.',
                'provider' => 'Coursera',
                'url' => 'https://www.coursera.org/ui-ux-design-fundamentals',
                'duration' => 28.5,
                'difficulty_level' => 'beginner',
                'price' => 49.99,
                'language' => 'English',
                'rating' => 4.6,
                'reviews_count' => 5630,
                'certificate_offered' => true,
                'metadata' => [
                    'prerequisites' => ['None'],
                    'topics_covered' => ['Design Thinking', 'Wireframing', 'Prototyping', 'User Research'],
                    'software_used' => ['Figma', 'Adobe XD']
                ]
            ],
            [
                'title' => 'Advanced Data Structures and Algorithms',
                'description' => 'Deep dive into complex data structures and algorithms with practical implementations in Python.',
                'provider' => 'edX',
                'url' => 'https://www.edx.org/advanced-dsa',
                'duration' => 40.0,
                'difficulty_level' => 'advanced',
                'price' => 199.99,
                'language' => 'English',
                'rating' => 4.9,
                'reviews_count' => 3240,
                'certificate_offered' => true,
                'metadata' => [
                    'prerequisites' => ['Python programming', 'Basic algorithms'],
                    'topics_covered' => ['Trees', 'Graphs', 'Dynamic Programming', 'Advanced Sorting'],
                    'includes_interview_prep' => true
                ]
            ]
        ];

        foreach ($courses as $courseData) {
            $metadata = $courseData['metadata'];
            unset($courseData['metadata']);
            
            $course = Course::create(array_merge($courseData, [
                'metadata' => json_encode($metadata)
            ]));

            // Attach relevant skills based on course content
            $skillKeywords = collect($metadata['topics_covered'])->map(function($topic) {
                return strtolower($topic);
            });

            $relevantSkills = Skill::whereIn(DB::raw('LOWER(name)'), $skillKeywords)->get();
            foreach ($relevantSkills as $skill) {
                $course->skills()->attach($skill->id, [
                    'importance_level' => rand(1, 5),
                    'skill_level_gained' => rand(1, 5)
                ]);
            }

            // Attach relevant career paths based on skills
            $careerPaths = CareerPath::whereHas('skills', function($query) use ($relevantSkills) {
                $query->whereIn('skills.id', $relevantSkills->pluck('id'));
            })->get();

            foreach ($careerPaths as $index => $careerPath) {
                $course->careerPaths()->attach($careerPath->id, [
                    'order' => $index + 1,
                    'required' => rand(0, 1) === 1
                ]);
            }
        }
    }
}
