<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Task;

final class TaskManager
{
    public const ALERT_FLASH_KEY = '__alert';

    public function bulkChangePriorities(array $data): void
    {
        foreach ($data as $taskId => $priority) {
            \DB::update(
                'UPDATE tasks SET priority = ? WHERE id = ?',
                [$priority, $taskId]
            );
        }
    }

    public function create(string $name, int $priority, int $project_id): ?Task
    {
        $task = new Task();
        $task->name = $name;
        $task->priority = $priority;
        $task->project_id = $project_id;
        if (!$task->save()) {
            return null;
        }

        return $task;
    }

    public function update(Task $task, string $name, int $project_id): bool
    {
        $task->name = $name;
        $task->project_id = $project_id;

        return $task->save();
    }

    public function delete(Task $task): bool
    {
        return (bool)$task->delete();
    }
}
