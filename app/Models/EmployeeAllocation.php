<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAllocation extends Model
{
    protected $primaryKey = 'cod_servidor_alocacao';

    protected $table = 'pmieducar.servidor_alocacao';

    protected $fillable = ['ativo', 'carga_horaria', 'periodo', 'hora_final',
        'hora_inicial', 'dia_seaman', 'ano', 'data_admissao', 'ref_ref_cod_instituicao', 'ref_usuario_exc',
        'ref_usuario_cad', 'ref_cod_escola', 'ref_cod_servidor', 'ref_cod_servidor_funcao', 'ref_cod_funcionario_vinculo', 'hora_atividade',
        'horas_excedentes', 'data_saida'];

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = 'data_exclusao';
}
