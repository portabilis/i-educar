<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyEducacensoStages extends Model
{
    use HasFactory;

    protected $table = 'modules.etapas_educacenso';

    public static function getDescriptiveValues(): array
    {
        return static::query()->pluck('nome', 'id')->toArray();
    }
}
