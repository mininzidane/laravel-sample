<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Partner
 *
 * @property int $id
 * @property string $name
 * @property int $balance
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 * @mixin \Eloquent
 */
class Partner extends Model
{
    use HasFactory;

    protected $table = 'partner';

    protected $fillable = [
        'name',
        'balance',
    ];

    protected $casts = [
        'balance' => 'int',
    ];
}
