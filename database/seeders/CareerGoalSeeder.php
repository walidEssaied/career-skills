<?php

namespace Database\Seeders;

use App\Models\CareerGoal;
use App\Models\User;
use Illuminate\Database\Seeder;

class CareerGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            CareerGoal::create([
                'user_id' => $user->id,
                'title' => 'Learn Laravel Development',
                'description' => 'Master Laravel framework and its ecosystem',
                'target_date' => now()->addMonths(6),
                'progress' => 30,
                'status' => 'in_progress',
                'notes' => 'Following Laravel documentation and building projects'
            ]);

            CareerGoal::create([
                'user_id' => $user->id,
                'title' => 'Complete AWS Certification',
                'description' => 'Get AWS Solutions Architect certification',
                'target_date' => now()->addMonths(3),
                'progress' => 0,
                'status' => 'not_started',
                'notes' => 'Planning to start with AWS free tier'
            ]);
        }
    }
}
