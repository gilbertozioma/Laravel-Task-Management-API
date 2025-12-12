<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
            'priority' => 'nullable|in:low,medium,high',
            'due_date' => 'nullable|date_format:Y-m-d H:i:s|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.max' => 'Task title cannot exceed 255 characters',
            'status.in' => 'Status must be one of: pending, in-progress, completed',
            'priority.in' => 'Priority must be one of: low, medium, high',
            'due_date.date_format' => 'Due date must be in format: YYYY-MM-DD HH:MM:SS',
            'due_date.after' => 'Due date must be a future date',
        ];
    }
}