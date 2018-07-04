<?php

require_once 'lib/CoreExt/Exception.php';
require_once 'App/Unificacao/Base.php';
require_once 'App/Unificacao/Servidor.php';
require_once 'App/Unificacao/Aluno.php';
require_once 'App/Unificacao/Cliente.php';

class App_Unificacao_Pessoa extends App_Unificacao_Base
{

    protected $chavesManterPrimeiroVinculo = array(
        array(
            'tabela' => 'cadastro.fisica_deficiencia',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'cadastro.fisica_raca',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.fisica_foto',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'pmieducar.biblioteca_usuario',
            'coluna' => 'ref_cod_usuario'
        ),
        array(
            'tabela' => 'pmieducar.escola_usuario',
            'coluna' => 'ref_cod_usuario'
        ),
        array(
            'tabela' => 'pmieducar.usuario',
            'coluna' => 'cod_usuario'
        ),
        array(
            'tabela' => 'cadastro.fisica_sangue',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'cod_pessoa_transporte'
        ),
        array(
            'tabela' => 'pmieducar.aluno',
            'coluna' => 'ref_idpes'
        ),
    );

    protected $chavesManterTodosVinculos = array(
        array(
            'tabela' => 'acesso.usuario',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'acesso.usuario',
            'coluna' => 'idpes_sga'
        ),
        array(
            'tabela' => 'acesso.log_acesso',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'acesso.log_erro',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'acesso.pessoa_instituicao',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'alimentos.cliente',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'alimentos.pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.aviso_nome',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_mae'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_pai'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_responsavel'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_con'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.fisica',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.fisica_cpf',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.fone_pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.endereco_pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_exc'
        ),
        array(
            'tabela' => 'cadastro.raca',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_juridica'
        ),
        array(
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_fisica'
        ),
        array(
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.socio',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.juridica',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.religiao',
            'coluna' => 'idpes_exc'
        ),
        array(
            'tabela' => 'cadastro.religiao',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.cep_logradouro_bairro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.cep_logradouro_bairro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.bairro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.bairro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.cep_logradouro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.cep_logradouro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.documento',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.documento',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.documento',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.endereco_externo',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.endereco_pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_mae'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_pai'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_responsavel'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_con'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.fisica',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.fisica_cpf',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.fone_pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.funcionario',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.juridica',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.logradouro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.logradouro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_juridica'
        ),
        array(
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_fisica'
        ),
        array(
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.socio',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'historico.municipio',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'historico.municipio',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'modules.motorista',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'modules.empresa_transporte_escolar',
            'coluna' => 'ref_resp_idpes'
        ),
        array(
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'modules.pessoa_transporte',
            'coluna' => 'ref_idpes_destino'
        ),
        array(
            'tabela' => 'pmieducar.cliente',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'pmieducar.responsaveis_aluno',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'pmiotopic.grupopessoa',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'pmiotopic.topico',
            'coluna' => 'ref_idpes_cad'
        ),
        array(
            'tabela' => 'pmiotopic.topico',
            'coluna' => 'ref_idpes_exc'
        ),
        array(
            'tabela' => 'pmiotopic.notas',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'public.distrito',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'public.distrito',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'urbano.cep_logradouro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'urbano.cep_logradouro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'urbano.cep_logradouro_bairro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'urbano.cep_logradouro_bairro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'alimentos.unidade_atendida',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'alimentos.fornecedor',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.funcionario',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.historico_cartao',
            'coluna' => 'idpes_cidadao'
        ),
        array(
            'tabela' => 'cadastro.historico_cartao',
            'coluna' => 'idpes_emitiu'
        ),
        array(
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.documento',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'modules.rota_transporte_escolar',
            'coluna' => 'ref_idpes_destino'
        ),
        array(
            'tabela' => 'pmidrh.setor',
            'coluna' => 'ref_idpes_resp'
        ),
        array(
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes'
        ),
        array(
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_gestor'
        ),
        array(
            'tabela' => 'pmieducar.escola',
            'coluna' => 'ref_idpes_secretario_escolar'
        ),
        array(
            'tabela' => 'pmiotopic.participante',
            'coluna' => 'ref_ref_idpes'
        ),
        array(
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'cadastro.endereco_externo',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'cadastro.pessoa',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'public.bairro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'public.bairro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'public.logradouro',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'public.logradouro',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'public.municipio',
            'coluna' => 'idpes_rev'
        ),
        array(
            'tabela' => 'public.municipio',
            'coluna' => 'idpes_cad'
        ),
        array(
            'tabela' => 'pmicontrolesis.foto_evento',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ),
        array(
            'tabela' => 'pmieducar.candidato_reserva_vaga',
            'coluna' => 'ref_cod_pessoa_cad'
        ),
        array(
            'tabela' => 'pmieducar.candidato_fila_unica',
            'coluna' => 'ref_cod_pessoa_cad'
        ),
        array(
            'tabela' => 'pmieducar.candidato_fila_unica',
            'coluna' => 'ref_cod_pessoa_exc'
        ),
        array(
            'tabela' => 'pmiotopic.funcionario_su',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ),
        array(
            'tabela' => 'pmiotopic.grupomoderador',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ),
        array(
            'tabela' => 'portal.acesso',
            'coluna' => 'cod_pessoa'
        ),
        array(
            'tabela' => 'portal.agenda_compromisso',
            'coluna' => 'ref_ref_cod_pessoa_cad'
        ),
        array(
            'tabela' => 'portal.agenda_responsavel',
            'coluna' => 'ref_ref_cod_pessoa_fj'
        ),
    );

    protected $chavesDeletarDuplicados = array(
        array(
            'tabela' => 'consistenciacao.historico_campo',
            'coluna' => 'idpes'
        ),
        array(
            'tabela' => 'cadastro.pessoa_fonetico',
            'coluna' => 'idpes'
        )
    );

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

        $this->db->consulta("
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
            return TRUE;
        }
        $unificadorCliente = new App_Unificacao_Cliente(array_shift($codigoClientes), $codigoClientes, $this->codPessoaLogada, $this->db, $this->transacao);
        $unificadorCliente->unifica();
    }

    protected function validaParametros()
    {
        parent::validaParametros();
        $pessoas = implode(",", (array_merge(array($this->codigoUnificador),$this->codigosDuplicados)));
        $numeroAlunos = $this->db->CampoUnico("SELECT count(*) numero_alunos FROM pmieducar.aluno where ref_idpes IN ({$pessoas}) AND ativo = 1 ");
        if ($numeroAlunos > 1) {
            throw new CoreExt_Exception('Não é permitido unificar mais de uma pessoa vinculada com alunos. Efetue primeiro a unificação de alunos e tente novamente.');
        }
    }
}
