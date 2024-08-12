<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, string> $fillable
 * @property int $count
 */
class ReportsCount extends Model
{
    public $timestamps = false;

    public $fillable = [
        'render',
        'template',
        'success',
        'date',
        'count',
        'authenticated',
    ];

    protected static function booted(): void
    {
        static::saving(function (ReportsCount $report) {
            $report->count++;
        });
    }
}
