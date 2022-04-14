<?php

class App_Unificacao_Servidor extends App_Unificacao_Base
{
    protected $chavesManterPrimeiroVinculo = [
        [
            'tabela' => 'pmieducar.servidor',
            'coluna' => 'cod_servidor'
        ],
        [
            'tabela' => 'modules.educacenso_cod_docente',
            'coluna' => 'cod_servidor'
        ],
    ];

    protected $chavesManterTodosVinculos = [
        [
            'tabela' => 'pmieducar.avaliacao_desempenho',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.falta_atraso',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.falta_atraso_compensado',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.servidor_formacao',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.servidor_afastamento',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.servidor_alocacao',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.servidor_funcao',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'modules.professor_turma',
            'coluna' => 'servidor_id'
        ],
        [
            'tabela' => 'modules.docente_licenciatura',
            'coluna' => 'servidor_id'
        ],
        [
            'tabela' => 'pmieducar.turma',
            'coluna' => 'ref_cod_regente'
        ],
    ];

    protected $chavesDeletarDuplicados = [
        [
            'tabela' => 'pmieducar.servidor_curso_ministra',
            'coluna' => 'ref_cod_servidor'
        ],
        [
            'tabela' => 'pmieducar.servidor_disciplina',
            'coluna' => 'ref_cod_servidor'
        ],
    ];
}
