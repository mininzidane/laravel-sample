<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

final class TaskController extends Controller
{
    public function index(Request $request, TaskManager $taskManager)
    {
        if ($request->isMethod('post')) {
            $taskManager->bulkChangePriorities($request->get('taskPriorities'));
        }

        return view('task.index', [
            'projectId' => (int) $request->get('project_id'),
            'projects' => Project::getList(),
            'tasks' => Task::getList((int) $request->get('project_id')),
        ]);
    }

    public function create(Request $request, TaskManager $taskManager)
    {
        if ($request->isMethod('post') && $this->validateRequest($request)) {
            $task = $taskManager->create($request->get('name'), (int)$request->get('priority', 1), (int)$request->get('project_id'));
            Session::flash(TaskManager::ALERT_FLASH_KEY, $task !== null);
            return redirect(route('task_list'));
        }

        return view('task.create', [
            'projects' => Project::getList(),
        ]);
    }

    public function edit(Request $request, int $id, TaskManager $taskManager)
    {
        $task = Task::find($id);
        if ($request->isMethod('post') && $this->validateRequest($request)) {
            Session::flash(
                TaskManager::ALERT_FLASH_KEY,
                $taskManager->update($task, $request->get('name'), (int)$request->get('project_id'))
            );
            return redirect(route('task_list'));
        }

        return view('task.edit', [
            'projects' => Project::getList(),
            'task' => $task,
        ]);
    }

    public function delete(int $id, TaskManager $taskManager)
    {
        $task = Task::find($id);
        Session::flash(TaskManager::ALERT_FLASH_KEY, $taskManager->delete($task));

        return redirect(route('task_list'));
    }

    private function validateRequest(Request $request): bool
    {
        try {
            $request->validate([
                'project_id' => 'required|integer',
                'name' => 'required',
            ]);
        } catch (\Throwable) {
            Session::flash(TaskManager::ALERT_FLASH_KEY, false);
            return false;
        }

        return true;
    }
}
