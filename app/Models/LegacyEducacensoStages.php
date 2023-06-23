<?php

namespace App\Models;

class LegacyEducacensoStages extends LegacyModel
{
    protected $table = 'modules.etapas_educacenso';

    protected $primaryKey = 'id';

    protected $fillable = [
        'nome',
    ];

    public array $legacy = [
        'name' => 'nome',
    ];

    public $timestamps = false;

    public static function getDescriptiveValues(): array
    {
        return static::query()->pluck('nome', 'id')->toArray();
    }
}
