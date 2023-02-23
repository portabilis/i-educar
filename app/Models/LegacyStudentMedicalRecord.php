<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyStudentMedicalRecord extends Model
{
    public $table = 'modules.ficha_medica_aluno';

    public $timestamps = false;

    public $primaryKey = 'ref_cod_aluno';

    public $incrementing = false;

    public $fillable = [
        'ref_cod_aluno',
        'grupo_sanguineo',
        'fator_rh',
        'alergia_medicamento',
        'desc_alergia_medicamento',
        'alergia_alimento',
        'desc_alergia_alimento',
        'doenca_congenita',
        'desc_doenca_congenita',
        'fumante',
        'doenca_caxumba',
        'doenca_sarampo',
        'doenca_rubeola',
        'doenca_catapora',
        'doenca_escarlatina',
        'doenca_coqueluche',
        'doenca_outras',
        'epiletico',
        'epiletico_tratamento',
        'hemofilico',
        'hipertenso',
        'asmatico',
        'diabetico',
        'insulina',
        'tratamento_medico',
        'desc_tratamento_medico',
        'medicacao_especifica',
        'desc_medicacao_especifica',
        'acomp_medico_psicologico',
        'desc_acomp_medico_psicologico',
        'restricao_atividade_fisica',
        'desc_restricao_atividade_fisica',
        'fratura_trauma',
        'desc_fratura_trauma',
        'plano_saude',
        'desc_plano_saude',
        'responsavel',
        'responsavel_parentesco',
        'responsavel_parentesco_telefone',
        'responsavel_parentesco_celular',
        'observacao',
        'aceita_hospital_proximo',
        'desc_aceita_hospital_proximo',
    ];

    public function student()
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }
}
