<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacyCourse
 *
 * @property string $name Nome do curso
 */
class LegacyDocument extends Model
{
    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'cadastro.documento';

    /**
     * @var string
     */
    protected $primaryKey = 'idpes';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes',
        'rg',
        'certidao_nascimento',
        'operacao',
        'origem_gravacao',
    ];

    protected function casts(): array
    {
        return [
            'data_exp_rg' => 'date',
            'data_emissao_cert_civil' => 'date',
        ];
    }

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->origem_gravacao = 'M';
            $model->operacao = 'I';
        });
    }

    public function issuingBody(): BelongsTo
    {
        return $this->belongsTo(LegacyIssuingBody::class, 'idorg_exp_rg');
    }

    public function issuingBodyWithUf(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->issuingBody->sigla . '/'. $this->sigla_uf_exp_rg;
            },
        );
    }

    public function birthRegistration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->tipo_cert_civil === 91) {
                    $data = collect();
                    $this->num_termo && $data->push('Termo: ' . $this->num_termo);
                    $this->num_livro && $data->push('Livro: ' . $this->num_livro);
                    $this->num_folha && $data->push('Folha: ' . $this->num_folha);

                    return $data->implode(' ');
                }

                return $this->certidao_nascimento;
            },
        );
    }
}
