<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Phone
 *
 * @property int $person_id
 * @property int $type_id
 * @property int $area_code
 * @property int $number
 * @property int $created_by
 * @property int $updated_by
 */
class LegacyPhone extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.fone_pessoa';

    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = 'data_rev';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'tipo',
        'ddd',
        'fone',
        'idpes_cad',
        'origem_gravacao',
        'operacao',
        'data_cad',
        'idpes_rev',
        'data_rev',
    ];

    /**
     * {@inheritDoc}
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->origem_gravacao = 'M';
            $model->data_cad = now();
            $model->operacao = 'I';
        });
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(LegacyPerson::class, 'idpes');
    }

    protected function number(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->fone) {
                    $ddd = $this->ddd ? sprintf('(%s)&nbsp;', $this->ddd) : null;

                    return $ddd . $this->fone;
                }

                return null;
            },
        );
    }
}
