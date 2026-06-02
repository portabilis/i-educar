<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<int, string> $fillable
 * @property string $name
 * @property string $description
 */
class RegionalType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];
}
