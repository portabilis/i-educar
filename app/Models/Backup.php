<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property array<int, string> $fillable
 */
class Backup extends Model
{
    public $table = 'pmieducar.backup';

    public const CREATED_AT = 'data_backup';

    public const UPDATED_AT = null;

    protected $fillable = [
        'caminho',
    ];
}
