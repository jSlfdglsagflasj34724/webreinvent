<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TasksController extends Controller
{
    public function index() {
        $tasks = Task::all();
        return view('welcome', compact('tasks'));
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tasks',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Task already exists!'], 400);
        }

        $task = Task::create([
            'name' => $request->name,
        ]);

        return response()->json(['task' => $task]);
    }

    public function update(Request $request, Task $task) {
        $task->status = $request->completed;
        $task->save();

        return response()->json(['success' => 'Task updated successfully!']);
    }

    public function destroy(Task $task) {
        $task->delete();

        return response()->json(['success' => 'Task deleted successfully!']);
    }
}
