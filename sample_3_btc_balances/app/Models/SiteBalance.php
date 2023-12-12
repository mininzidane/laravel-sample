<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SiteBalance
 *
 * @property int $id
 * @property int $balance
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 * @mixin \Eloquent
 */
class SiteBalance extends Model
{
    use HasFactory;

    protected $table = 'site_balance';

    protected $casts = [
        'balance' => 'int',
    ];

    protected $fillable = [
        'balance',
    ];

    public static function changeBalance(int $amount): bool
    {
        $model = self::first();
        $model->balance += $amount;

        return $model->save();
    }

    public static function getBalance(): int
    {
        return self::first()->balance;
    }
}
