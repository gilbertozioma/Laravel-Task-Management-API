<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users
        $gilbert = User::create([
            'name' => 'Gilbert Ozioma',
            'email' => 'gilbertozioma0@gmail.com',
            'password' => bcrypt('11111111'),
        ]);

        $user2 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $user3 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create tasks for Gilbert
        Task::create([
            'title' => 'Complete Laravel Project Documentation',
            'description' => 'Write comprehensive README with API documentation and setup instructions',
            'status' => 'in-progress',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'user_id' => $gilbert->id,
        ]);

        Task::create([
            'title' => 'Implement User Authentication',
            'description' => 'Set up Laravel Sanctum for token-based authentication',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => now()->subDays(1)->format('Y-m-d H:i:s'),
            'user_id' => $gilbert->id,
        ]);

        Task::create([
            'title' => 'Create Task CRUD Operations',
            'description' => 'Build complete Create, Read, Update, Delete functionality for tasks',
            'status' => 'completed',
            'priority' => 'medium',
            'due_date' => now()->subDays(2)->format('Y-m-d H:i:s'),
            'user_id' => $gilbert->id,
        ]);

        Task::create([
            'title' => 'Add Filtering and Pagination',
            'description' => 'Implement task filtering by status and pagination support',
            'status' => 'completed',
            'priority' => 'low',
            'due_date' => now()->subDays(3)->format('Y-m-d H:i:s'),
            'user_id' => $gilbert->id,
        ]);

        Task::create([
            'title' => 'Write Automated Tests',
            'description' => 'Create comprehensive feature tests for all API endpoints',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(14)->format('Y-m-d H:i:s'),
            'user_id' => $gilbert->id,
        ]);

        // Create tasks for John
        Task::create([
            'title' => 'Review Code Quality',
            'description' => 'Perform code review and ensure PSR-12 standards',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(10)->format('Y-m-d H:i:s'),
            'user_id' => $user2->id,
        ]);

        Task::create([
            'title' => 'Setup CI/CD Pipeline',
            'description' => 'Configure GitHub Actions for automated testing',
            'status' => 'in-progress',
            'priority' => 'medium',
            'due_date' => now()->addDays(20)->format('Y-m-d H:i:s'),
            'user_id' => $user2->id,
        ]);

        Task::create([
            'title' => 'Database Optimization',
            'description' => 'Add indexes and optimize query performance',
            'status' => 'pending',
            'priority' => 'low',
            'due_date' => now()->addDays(30)->format('Y-m-d H:i:s'),
            'user_id' => $user2->id,
        ]);

        // Create tasks for Jane
        Task::create([
            'title' => 'API Documentation',
            'description' => 'Generate OpenAPI/Swagger documentation',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
            'user_id' => $user3->id,
        ]);

        Task::create([
            'title' => 'Security Audit',
            'description' => 'Perform security review and penetration testing',
            'status' => 'in-progress',
            'priority' => 'high',
            'due_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'user_id' => $user3->id,
        ]);

        // Create additional random tasks using factory
        Task::factory(5)->create(['user_id' => $gilbert->id]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Demo users created:');
        $this->command->info('- Gilbert Ozioma (gilbertozioma0@gmail.com) - Password: 11111111');
        $this->command->info('- John Doe (john@example.com) - Password: password');
        $this->command->info('- Jane Smith (jane@example.com) - Password: password');
    }
}
