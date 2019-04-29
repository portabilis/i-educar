<?php

require_once 'lib/CoreExt/Exception.php';
require_once 'App/Unificacao/Base.php';
require_once 'App/Unificacao/Servidor.php';
require_once 'App/Unificacao/Aluno.php';
require_once 'App/Unificacao/Cliente.php';

class App_Unificacao_Pessoa extends App_Unificacao_Base
{
    protected $chavesManterPrimeiroVinculo = [
        [
            'tabela' => 'cadastro.fisica_deficiencia',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'cadastro.fisica_raca',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.fisica_foto',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'pmieducar.biblioteca_usuario',
            'coluna' => 'ref_cod_usuario'
        ],
        [
            'tabela' => 'pmieducar.escola_usuario',
            'coluna' => 'ref_cod_usuario'
        ],
        [
            'tabela' => 'pmieducar.usuario',
            'coluna' => 'cod_usuario'
        ],
        [
            'tabela' => 'cadastro.fisica_sangue',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'cod_pessoa_transporte'
        ],
        [
            'tabela' => 'pmieducar.aluno',
            'coluna' => 'ref_idpes'
        ],
    ];

    protected $chavesManterTodosVinculos = [
        [
            'tabela' => 'acesso.usuario',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'acesso.usuario',
            'coluna' => 'idpes_sga'
        ],
        [
            'tabela' => 'acesso.log_acesso',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'acesso.log_erro',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'acesso.pessoa_instituicao',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'alimentos.cliente',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'alimentos.pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.aviso_nome',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_mae'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_pai'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_responsavel'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_con'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_exc'
        ],
        [
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_juridica'
        ],
        [
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_fisica'
        ],
        [
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.religiao',
            'coluna' => 'idpes_exc'
        ],
        [
            'tabela' => 'cadastro.religiao',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.cep_logradouro_bairro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.cep_logradouro_bairro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.bairro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.bairro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.cep_logradouro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.cep_logradouro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.documento',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.documento',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.documento',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_mae'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_pai'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_responsavel'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_con'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.logradouro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.logradouro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_juridica'
        ],
        [
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_fisica'
        ],
        [
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'historico.municipio',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'historico.municipio',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'modules.motorista',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_resp_idpes'
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes_destino'
        ],
        [
            'tabela' => 'pmieducar.cliente',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'pmiotopic.grupopessoa',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'pmiotopic.topico',
            'coluna' => 'ref_idpes_cad'
        ],
        [
            'tabela' => 'pmiotopic.topico',
            'coluna' => 'ref_idpes_exc'
        ],
        [
            'tabela' => 'pmiotopic.notas',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'public.distrito',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'public.distrito',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'urbano.cep_logradouro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'urbano.cep_logradouro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'urbano.cep_logradouro_bairro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'urbano.cep_logradouro_bairro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'alimentos.unidade_atendida',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'alimentos.fornecedor',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.historico_cartao',
            'coluna' => 'idpes_cidadao'
        ],
        [
            'tabela' => 'cadastro.historico_cartao',
            'coluna' => 'idpes_emitiu'
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'modules.rota_transporte_escolar',
            'coluna' => 'ref_idpes_destino'
        ],
        [
            'tabela' => 'pmidrh.setor',
            'coluna' => 'ref_idpes_resp'
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes'
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_gestor'
        ],
        [
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_secretario_escolar'
        ],
        [
            'tabela' => 'pmiotopic.participante',
            'coluna' => 'ref_ref_idpes'
        ],
        [
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'public.bairro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'public.bairro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'public.logradouro',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'public.logradouro',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'public.municipio',
            'coluna' => 'idpes_rev'
        ],
        [
            'tabela' => 'public.municipio',
            'coluna' => 'idpes_cad'
        ],
        [
            'tabela' => 'pmicontrolesis.foto_evento',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ],
        [
            'tabela' => 'pmieducar.candidato_reserva_vaga',
            'coluna' => 'ref_cod_pessoa_cad'
        ],
        [
            'tabela' => 'pmiotopic.funcionario_su',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ],
        [
            'tabela' => 'pmiotopic.grupomoderador',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ],
        [
            'tabela' => 'portal.acesso',
            'coluna' => 'cod_pessoa'
        ],
        [
            'tabela' => 'portal.agenda_compromisso',
            'coluna' => 'ref_ref_cod_pessoa_cad'
        ],
        [
            'tabela' => 'portal.agenda_responsavel',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ],
    ];

    protected $chavesDeletarDuplicados = [
        [
            'tabela' => 'consistenciacao.historico_campo',
            'coluna' => 'idpes'
        ],
        [
            'tabela' => 'cadastro.pessoa_fonetico',
            'coluna' => 'idpes'
        ]
    ];

    public function __construct($codigoUnificador, $codigosDuplicados, $codPessoaLogada, clsBanco $db, bool $transacao = true)
    {
        parent::__construct($codigoUnificador, $codigosDuplicados, $codPessoaLogada, $db, $transacao);

        if (is_dir(base_path('ieducar/intranet/filaunica'))) {
            $this->chavesManterTodosVinculos = array_merge($this->chavesManterTodosVinculos, [
                'tabela' => 'pmieducar.responsaveis_aluno',
                'coluna' => 'ref_idpes'
            ], [
                'tabela' => 'pmieducar.candidato_fila_unica',
                'coluna' => 'ref_cod_pessoa_cad'
            ], [
                'tabela' => 'pmieducar.candidato_fila_unica',
                'coluna' => 'ref_cod_pessoa_exc'
            ]);
        }
    }

    public function unifica()
    {
        $this->unificaClientes();
        $unificadorServidor = new App_Unificacao_Servidor($this->codigoUnificador, $this->codigosDuplicados, $this->codPessoaLogada, $this->db, $this->transacao);
        $unificadorServidor->unifica();
        parent::unifica();
    }

    protected function unificaClientes()
    {
        $chavesConsultar = $this->codigosDuplicados;
        $chavesConsultar[] = $this->codigoUnificador;
        $chavesConsultarString = implode(',', $chavesConsultar);

        $this->db->consulta(
            "
            SELECT cod_cliente
                FROM pmieducar.cliente
                WHERE ref_idpes in ({$chavesConsultarString})
                ORDER BY ref_idpes = {$this->codigoUnificador} DESC"
        );

        $codigoClientes = [];

        while ($this->db->ProximoRegistro()) {
            $reg = $this->db->Tupla();
            $codigoClientes[] = $reg['cod_cliente'];
        }
        if (COUNT($codigoClientes) < 2) {
            return true;
        }
        $unificadorCliente = new App_Unificacao_Cliente(array_shift($codigoClientes), $codigoClientes, $this->codPessoaLogada, $this->db, $this->transacao);
        $unificadorCliente->unifica();
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
