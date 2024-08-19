<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $caminho
 */
class LegacyIndividualPicture extends Model
{
    public const CREATED_AT = null;

    public const UPDATED_AT = 'updated_at';

    protected $table = 'cadastro.fisica_foto';

    protected $primaryKey = 'idpes';

    public $fillable = [
        'idpes',
        'caminho',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->caminho,
            set: fn ($value) => [
                'caminho' => $value,
            ],
        );
    }
}
