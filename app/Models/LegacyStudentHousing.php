<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudentHousing extends Model
{
    protected $table = 'modules.moradia_aluno';

    protected $primaryKey = 'ref_cod_aluno';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ref_cod_aluno',
        'moradia',
        'material',
        'casa_outra',
        'moradia_situacao',
        'quartos',
        'sala',
        'copa',
        'banheiro',
        'garagem',
        'empregada_domestica',
        'automovel',
        'motocicleta',
        'geladeira',
        'fogao',
        'maquina_lavar',
        'microondas',
        'video_dvd',
        'televisao',
        'telefone',
        'recursos_tecnologicos',
        'quant_pessoas',
        'renda',
        'agua_encanada',
        'poco',
        'energia',
        'esgoto',
        'fossa',
        'lixo',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }
}
