<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LegacyCourse
 *
 * @property string $tipo_cert_civil
 * @property string $num_termo
 * @property string $num_livro
 * @property string $num_folha
 * @property string $certidao_nascimento
 * @property string $sigla_uf_exp_rg
 * @property LegacyIssuingBody $issuingBody
 */
class LegacyDocument extends Model
{
    public const CREATED_AT = 'data_cad';

    public const UPDATED_AT = null;

    protected $table = 'cadastro.documento';

    protected $primaryKey = 'idpes';

    protected $fillable = [
        'idpes',
        'rg',
        'certidao_nascimento',
        'operacao',
        'origem_gravacao',
        'data_exp_rg',
        'sigla_uf_exp_rg',
        'tipo_cert_civil',
        'num_termo',
        'num_livro',
        'num_folha',
        'data_emissao_cert_civil',
        'sigla_uf_cert_civil',
        'cartorio_cert_civil',
        'num_cart_trabalho',
        'serie_cart_trabalho',
        'data_emissao_cart_trabalho',
        'sigla_uf_cart_trabalho',
        'num_tit_eleitor',
        'zona_tit_eleitor',
        'secao_tit_eleitor',
        'idorg_exp_rg',
        'idpes_rev',
        'data_rev',
        'origem_gravacao',
        'idpes_cad',
        'data_cad',
        'operacao',
        'certidao_nascimento',
        'cartorio_cert_civil_inep',
        'certidao_casamento',
        'passaporte',
        'comprovante_residencia',
        'declaracao_trabalho_autonomo',
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

    /**
     * @return BelongsTo<LegacyIssuingBody, $this>
     */
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
