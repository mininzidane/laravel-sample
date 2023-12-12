<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @mixin \Eloquent
 */
final class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public static function getList() :array
    {
        /** @var self[] $models */
        $models = self::query()->orderBy('id', 'desc')->get();
        $output = [];
        foreach ($models as $model) {
            $output[$model->id] = $model->name;
        }

        return $output;
    }
}
