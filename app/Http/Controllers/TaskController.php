<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Services\WeatherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private WeatherService $weatherService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tasks = Auth::user()->tasks()->latest()->paginate(10);
        $weather = $this->weatherService->getCurrentWeather();

        return view('tasks.index', compact('tasks', 'weather'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        Auth::user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): View
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task): View
    {
        $this->authorize('update', $task);

        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): RedirectResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted successfully.');
    }

    /**
     * Toggle the completion status of the task.
     */
    public function toggle(Task $task): RedirectResponse
    {
        $this->authorize('update', $task);

        $task->update([
            'completed' => ! $task->completed,
        ]);

        $status = $task->completed ? 'completed' : 'marked as pending';

        return redirect()->back()
            ->with('success', "Task {$status} successfully.");
    }
}
