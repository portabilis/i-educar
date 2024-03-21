<?php

class App_Unificacao_Pessoa extends App_Unificacao_Base
{
    protected $chavesManterPrimeiroVinculo = [
        [
            'tabela' => 'cadastro.fisica_deficiencia',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'cadastro.fisica_raca',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'cadastro.fisica_foto',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'public.person_has_place',
            'coluna' => 'person_id',
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'pmieducar.escola_usuario',
            'coluna' => 'ref_cod_usuario',
        ],
        [
            'tabela' => 'pmieducar.usuario',
            'coluna' => 'cod_usuario',
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'cod_pessoa_transporte',
        ],
        [
            'tabela' => 'pmieducar.aluno',
            'coluna' => 'ref_idpes',
        ],
    ];

    protected $chavesManterTodosVinculos = [
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_mae',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_pai',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_responsavel',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_con',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_rev',
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_rev',
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_exc',
        ],
        [
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes',
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_rev',
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'modules.motorista',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_resp_idpes',
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes_destino',
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_rev',
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'modules.rota_transporte_escolar',
            'coluna' => 'ref_idpes_destino',
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_gestor',
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_secretario_escolar',
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_cad',
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_rev',
        ],
        [
            'tabela' => 'pmieducar.candidato_reserva_vaga',
            'coluna' => 'ref_cod_pessoa_cad',
        ],
        [
            'tabela' => 'portal.acesso',
            'coluna' => 'cod_pessoa',
        ],
        [
            'tabela' => 'portal.agenda_compromisso',
            'coluna' => 'ref_ref_cod_pessoa_cad',
        ],
        [
            'tabela' => 'portal.agenda',
            'coluna' => 'ref_ref_cod_pessoa_own',
        ],
        [
            'tabela' => 'portal.agenda',
            'coluna' => 'ref_ref_cod_pessoa_cad',
        ],
        [
            'tabela' => 'portal.agenda',
            'coluna' => 'ref_ref_cod_pessoa_exc',
        ],
        [
            'tabela' => 'portal.agenda_responsavel',
            'coluna' => 'ref_ref_cod_pessoa_fj',
        ],
        [
            'tabela' => 'portal.funcionario',
            'coluna' => 'ref_ref_cod_pessoa_fj',
        ],
        [
            'tabela' => 'pmieducar.aluno_excluidos',
            'coluna' => 'ref_idpes',
        ],
        [
            'tabela' => 'public.school_managers',
            'coluna' => 'employee_id',
        ],
    ];

    protected $chavesDeletarDuplicados = [
        [
            'tabela' => 'portal.funcionario',
            'coluna' => 'ref_cod_pessoa_fj',
        ],
    ];

    protected $triggersNecessarias = [
        [
            'tabela' => 'pmieducar.aluno',
            'trigger' => 'trigger_when_deleted_pmieducar_aluno',
        ],
    ];

    public function unifica()
    {
        $unificadorServidor = new App_Unificacao_Servidor($this->codigoUnificador, $this->codigosDuplicados, $this->codPessoaLogada, $this->db, $this->unificationId);
        $unificadorServidor->unifica();
        parent::unifica();
    }

    protected function validaParametros()
    {
        parent::validaParametros();
        $pessoas = implode(',', (array_merge([$this->codigoUnificador], $this->codigosDuplicados)));
        $numeroAlunos = $this->db->CampoUnico("SELECT count(*) numero_alunos FROM pmieducar.aluno where ref_idpes IN ({$pessoas}) AND ativo = 1 ");
        if ($numeroAlunos > 1) {
            throw new CoreExt_Exception('Não é permitido unificar mais de uma pessoa vinculada com alunos. Efetue primeiro a unificação de alunos e tente novamente.');
        }
    }
}
