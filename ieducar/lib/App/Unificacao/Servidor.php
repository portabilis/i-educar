<?php

require_once 'CoreExt/Exception.php';
require_once 'App/Unificacao/Base.php';

class App_Unificacao_Servidor extends App_Unificacao_Base
{
    protected $chavesManterPrimeiroVinculo = array(
        array(
            'tabela' => 'pmieducar.servidor',
            'coluna' => 'cod_servidor'
        ),
        array(
            'tabela' => 'modules.educacenso_cod_docente',
            'coluna' => 'cod_servidor'
        ),
    );

    protected $chavesManterTodosVinculos = array(
        array(
            'tabela' => 'pmieducar.avaliacao_desempenho',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.falta_atraso',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.falta_atraso_compensado',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.servidor_formacao',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.servidor_afastamento',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.servidor_alocacao',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.servidor_funcao',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'modules.professor_turma',
            'coluna' => 'servidor_id'
        ),
        array(
            'tabela' => 'modules.docente_licenciatura',
            'coluna' => 'servidor_id'
        ),
        array(
            'tabela' => 'pmieducar.turma',
            'coluna' => 'ref_cod_regente'
        ),
    );

    protected $chavesDeletarDuplicados = array(
        array(
            'tabela' => 'pmieducar.servidor_curso_ministra',
            'coluna' => 'ref_cod_servidor'
        ),
        array(
            'tabela' => 'pmieducar.servidor_disciplina',
            'coluna' => 'ref_cod_servidor'
        ),
    );
}
