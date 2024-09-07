<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'task' => 'required|unique:tasks,task',
        ]);

        $task = Task::create(['task' => $request->task]);
        return response()->json($task);
    }

    public function update($id)
    {
        $task = Task::find($id);
        $task->is_completed = !$task->is_completed;
        $task->save();

        return response()->json($task);
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return response()->json(['success' => 'Task deleted successfully.']);
    }
}
