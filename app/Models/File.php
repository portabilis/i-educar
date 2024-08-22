<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, string> $fillable
 */
class File extends Model
{
    protected $table = 'public.files';

    protected $fillable = [
        'url',
        'size',
        'original_name',
        'extension',
    ];
}
