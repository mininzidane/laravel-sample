<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property int $priority
 * @property int $project_id
 */
final class Task extends Model
{
    protected $fillable = [
        'name',
        'priority',
        'project_id',
    ];

    public function project(): HasOne
    {
        return $this->hasOne(Project::class, 'project_id', 'id');
    }

    public static function getList(?int $projectId = null, int $page = 1): Collection
    {
        return Task::query()
            ->when($projectId > 0, fn($query) => $query->where('project_id', $projectId))
            ->forPage($page)
            ->orderBy('priority')
            ->get()
        ;
    }
}
