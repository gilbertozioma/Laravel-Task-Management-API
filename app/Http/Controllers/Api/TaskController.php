<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tasks = Task::where('user_id', $request->user()->id)
                ->status($request->query('status'))
                ->priority($request->query('priority'))
                ->search($request->query('search'))
                ->orderBy($request->query('sort_by', 'created_at'), $request->query('sort_order', 'desc'))
                ->paginate(15);

            if ($tasks->isEmpty()) {
                Log::info('No tasks found for user ID: ' . $request->user()->id);

                return response()->json([
                    'message' => 'No tasks found.',
                ], 200);
            }

            return response()->json($tasks);

        } catch (\Exception $e) {
            Log::error('Error retrieving tasks for user ID: ' . $request->user()->id, [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error retrieving tasks'], 500);
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status ?? 'pending',
                'priority' => $request->priority ?? 'medium',
                'due_date' => $request->due_date,
                'user_id' => $request->user()->id,
            ]);

            Log::info('Task created successfully. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task,
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating task for user ID: ' . $request->user()->id, ['error' => $e->getMessage(),]);
            return response()->json(['message' => 'Error creating task'], 500);
        }
    }

    public function show(Request $request, ?Task $task)
    {
        try {
            // Check if task exists
            if (!$task) {
                Log::warning('Task not found. User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Task not found',
                ], 404);
            }

            // Check if user owns the task
            if ($task->user_id !== $request->user()->id) {
                Log::warning('Unauthorized access attempt. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            return response()->json($task);

        } catch (\Exception $e) {
            Log::error('Error retrieving task for user ID: ' . $request->user()->id, [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error retrieving task'], 500);
        }
    }

    public function update(UpdateTaskRequest $request, ?Task $task)
    {
        try {
            // Check if task exists
            if (!$task) {
                Log::warning('Task not found for update. User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Task not found',
                ], 404);
            }

            // Check if user owns the task
            if ($task->user_id !== $request->user()->id) {
                Log::warning('Unauthorized update attempt. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            $task->update($request->validated());
            Log::info('Task updated successfully. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);

            return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task,
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating task for user ID: ' . $request->user()->id, [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error updating task'], 500);
        }
    }

    public function destroy(Request $request, ?Task $task)
    {
        try {
            // Check if task exists
            if (!$task) {
                Log::warning('Task not found for deletion. User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Task not found',
                ], 404);
            }

            // Check if user owns the task
            if ($task->user_id !== $request->user()->id) {
                Log::warning('Unauthorized deletion attempt. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);
                return response()->json([
                    'message' => 'Unauthorized',
                ], 403);
            }

            $task->delete();
            Log::info('Task deleted successfully. Task ID: ' . $task->id . ', User ID: ' . $request->user()->id);

            return response()->json([
                'message' => 'Task deleted successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting task for user ID: ' . $request->user()->id, [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Error deleting task'], 500);
        }
    }
}