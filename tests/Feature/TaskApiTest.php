<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    protected function createAuthenticatedUser()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;
        return [$user, $token];
    }

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'token',
            ]);
    }

    public function test_user_can_create_task()
    {
        [$user, $token] = $this->createAuthenticatedUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/tasks', [
                'title' => 'Test Task',
                'description' => 'Test Description',
                'status' => 'pending',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Task created successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_view_own_tasks()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'user_id'],
                ],
            ]);
    }

    public function test_user_can_filter_tasks_by_status()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        
        Task::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        Task::factory()->create(['user_id' => $user->id, 'status' => 'completed']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/tasks?status=pending');

        $response->assertStatus(200);
        $this->assertTrue(
            collect($response->json('data'))->every(fn($task) => $task['status'] === 'pending')
        );
    }

    public function test_user_can_update_task()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/tasks/{$task->id}", [
                'title' => 'Updated Title',
                'status' => 'completed',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task updated successfully',
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'status' => 'completed',
        ]);
    }

    public function test_user_can_delete_task()
    {
        [$user, $token] = $this->createAuthenticatedUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_user_cannot_access_other_users_tasks()
    {
        [$user1, $token1] = $this->createAuthenticatedUser();
        $user2 = User::factory()->create();
        
        $task = Task::factory()->create(['user_id' => $user2->id]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->getJson("/api/tasks/{$task->id}");

        $response->assertStatus(403);
    }
}