<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/pmieducar/clsPmieducarFuncionarioVinculo.inc.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Servidores - Servidor Alocação');
        $this->processoAp = 635;
    }
}

class indice extends clsCadastro
{
    public $pessoa_logada;
    public $cod_servidor_alocacao;
    public $ref_ref_cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_escola;
    public $ref_cod_servidor;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $carga_horaria_alocada;
    public $carga_horaria_disponivel;
    public $hora_inicial;
    public $hora_final;
    public $hora_atividade;
    public $horas_excedentes;
    public $periodo;
    public $ref_cod_funcionario_vinculo;
    public $ano;
    public $data_admissao;
    public $data_saida;
    public $alocacao_array          = [];
    public $alocacao_excluida_array = [];

    public static $escolasPeriodos = [];
    public static $periodos = [];

    public function Inicializar()
    {
        $retorno = 'Novo';


        $ref_cod_servidor        = $_GET['ref_cod_servidor'];
        $ref_ref_cod_instituicao = $_GET['ref_cod_instituicao'];
        $cod_servidor_alocacao   = $_GET['cod_servidor_alocacao'];

        if (is_numeric($cod_servidor_alocacao)) {
            $this->cod_servidor_alocacao = $cod_servidor_alocacao;

            $servidorAlocacao = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao);
            $servidorAlocacao = $servidorAlocacao->detalhe();

            $this->ref_ref_cod_instituicao     = $servidorAlocacao['ref_ref_cod_instituicao'];
            $this->ref_cod_servidor            = $servidorAlocacao['ref_cod_servidor'];
            $this->ref_cod_escola              = $servidorAlocacao['ref_cod_escola'];
            $this->periodo                     = $servidorAlocacao['periodo'];
            $this->carga_horaria_alocada       = $servidorAlocacao['carga_horaria'];
            $this->cod_servidor_funcao         = $servidorAlocacao['ref_cod_servidor_funcao'];
            $this->ref_cod_funcionario_vinculo = $servidorAlocacao['ref_cod_funcionario_vinculo'];
            $this->ativo                       = $servidorAlocacao['ativo'];
            $this->ano                         = $servidorAlocacao['ano'];
            $this->data_admissao               = $servidorAlocacao['data_admissao'];
            $this->data_saida                  = $servidorAlocacao['data_saida'];
            $this->hora_inicial                = $servidorAlocacao['hora_inicial'];
            $this->hora_final                  = $servidorAlocacao['hora_final'];
            $this->hora_atividade              = $servidorAlocacao['hora_atividade'];
            $this->horas_excedentes            = $servidorAlocacao['horas_excedentes'];
        } elseif (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
            $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
            $this->ref_cod_servidor        = $ref_cod_servidor;
            $this->ref_cod_instituicao = $ref_ref_cod_instituicao;
        } else {
            $this->simpleRedirect('educar_servidor_lst.php');
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635, $this->pessoa_logada, 7, 'educar_servidor_lst.php'
        );

        if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
            $this->fexcluir = true;
        }

        $this->url_cancelar = sprintf(
            'educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_ref_cod_instituicao
        );
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Alocar servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $obj_inst = new clsPmieducarInstituicao($this->ref_ref_cod_instituicao);
        $inst_det = $obj_inst->detalhe();

        $this->campoRotulo('nm_instituicao', 'Instituição', $inst_det['nm_instituicao']);
        $this->campoOculto('ref_ref_cod_instituicao', $this->ref_ref_cod_instituicao);
        $this->campoOculto('cod_servidor_alocacao', $this->cod_servidor_alocacao);

        // Dados do servidor
        $objTemp = new clsPmieducarServidor(
            $this->ref_cod_servidor,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_ref_cod_instituicao
        );

        $det = $objTemp->detalhe();

        if ($det) {
            $this->carga_horaria_disponivel = $det['carga_horaria'];
        }

        if ($this->ref_cod_servidor) {
            $objTemp = new clsPessoaFisica($this->ref_cod_servidor);
            $detalhe = $objTemp->detalhe();
            $nm_servidor = $detalhe['nome'];
        }

        $this->campoRotulo('nm_servidor', 'Servidor', $nm_servidor);

        $this->campoOculto('ref_cod_servidor', $this->ref_cod_servidor);

        // Carga horária
        $carga = $this->carga_horaria_disponivel;
        $this->campoRotulo('carga_horaria_disponivel', 'Carga horária do servidor', $carga . ':00');

        $this->inputsHelper()->integer('ano', ['value' => $this->ano, 'max_length' => 4]);

        $this->inputsHelper()->dynamic('escola');

        // Períodos
        $periodo = [
            1  => 'Matutino',
            2  => 'Vespertino',
            3  => 'Noturno'
        ];

        self::$periodos = $periodo;

        $this->campoLista('periodo', 'Período', $periodo, $this->periodo, null, false, '', '', false, true);

        $options = [
            'label' => 'Data de admissão',
            'placeholder' => 'dd/mm/yyyy',
            'hint' => 'A data deve estar em branco ou dentro do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
            'value' => $this->data_admissao,
            'required' => false,
        ];

        $this->inputsHelper()->date('data_admissao', $options);

        $options = [
            'label' => 'Data de saída',
            'placeholder' => 'dd/mm/yyyy',
            'hint' => 'A data deve estar em branco ou fora do período de datas da exportação para o Educacenso, para o servidor ser exportado.',
            'value' => $this->data_saida,
            'required' => false,
        ];

        $this->inputsHelper()->date('data_saida', $options);

        // Funções
        $obj_funcoes = new clsPmieducarServidorFuncao();

        $lista_funcoes = $obj_funcoes->funcoesDoServidor($this->ref_ref_cod_instituicao, $this->ref_cod_servidor);

        $opcoes = ['' => 'Selecione'];

        if ($lista_funcoes) {
            foreach ($lista_funcoes as $funcao) {
                $opcoes[$funcao['cod_servidor_funcao']] = (!empty($funcao['matricula']) ? "{$funcao['funcao']} - {$funcao['matricula']}" : $funcao['funcao']);
            }
        }

        $this->campoLista('cod_servidor_funcao', 'Função', $opcoes, $this->cod_servidor_funcao, '', false, '', '', false, false);

        // Vínculos
        $objFuncionarioVinculo = new clsPmieducarFuncionarioVinculo;
        $opcoes = ['' => 'Selecione'] + $objFuncionarioVinculo->lista();

        $this->campoLista('ref_cod_funcionario_vinculo', 'V&iacute;nculo', $opcoes, $this->ref_cod_funcionario_vinculo, null, false, '', '', false, false);

        $this->campoRotulo('informacao_carga_horaria','<b>Informações sobre carga horária</b>');
        $this->campoHora('hora_inicial', 'Hora de início', $this->hora_inicial);
        $this->campoHora('hora_final', 'Hora de término', $this->hora_final);
        $this->campoHoraServidor('carga_horaria_alocada', 'Carga horária', $this->carga_horaria_alocada, true);
        $this->campoHora('hora_atividade', 'Hora atividade', $this->hora_atividade);
        $this->campoHora('horas_excedentes', 'Horas excedentes', $this->horas_excedentes);
    }

    public function Novo()
    {

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            "educar_servidor_alocacao_lst.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_ref_cod_instituicao}"
        );

        $dataAdmissao = $this->data_admissao ? Portabilis_Date_Utils::brToPgSql($this->data_admissao) : null;
        $dataSaida = $this->data_saida ? Portabilis_Date_Utils::brToPgSql($this->data_saida) : null;

        $servidorAlocacao = new clsPmieducarServidorAlocacao(
            $this->cod_servidor_alocacao,
            $this->ref_ref_cod_instituicao,
            null,
            null,
            null,
            $this->ref_cod_servidor,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $this->ano,
            $dataAdmissao
        );

        $carga_horaria_disponivel = $this->hhmmToMinutes($this->carga_horaria_disponivel);
        $carga_horaria_alocada    = $this->hhmmToMinutes($this->carga_horaria_alocada);
        $carga_horaria_alocada   += $this->hhmmToMinutes($servidorAlocacao->getCargaHorariaAno());

        if ($carga_horaria_disponivel >= $carga_horaria_alocada) {
            $obj_novo = new clsPmieducarServidorAlocacao(
                $this->cod_servidor_alocacao,
                $this->ref_ref_cod_instituicao,
                null,
                $this->pessoa_logada,
                $this->ref_cod_escola,
                $this->ref_cod_servidor,
                null,
                null,
                $this->ativo,
                $this->carga_horaria_alocada,
                $this->periodo,
                $this->cod_servidor_funcao,
                $this->ref_cod_funcionario_vinculo,
                $this->ano,
                $dataAdmissao,
                $this->hora_inicial,
                $this->hora_final,
                $this->hora_atividade,
                $this->horas_excedentes,
                $dataSaida
            );

            if ($obj_novo->periodoAlocado()) {
                $this->mensagem = 'Período informado já foi alocado. Por favor, selecione outro.<br />';

                return false;
            }

            $cadastrou = $obj_novo->cadastra();

            if (!$cadastrou) {
                $this->mensagem = 'Cadastro não realizado.<br />';

                return false;
            }

            // Excluí alocação existente
            if ($this->cod_servidor_alocacao) {
                $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
                $obj_tmp->excluir();
            }

            // Atualiza código da alocação
            $this->cod_servidor_alocacao = $cadastrou;
        } else {
            $this->mensagem = 'Não é possível alocar quantidade superior de horas do que o disponível.<br />';
            $this->alocacao_array = null;

            return false;
        }

        $this->mensagem .= 'Cadastro efetuado com sucesso.<br />';
        $this->simpleRedirect(sprintf('educar_servidor_alocacao_det.php?cod_servidor_alocacao=%d', $this->cod_servidor_alocacao));

    }

    public function Editar()
    {
        return false;
    }

    public function Excluir()
    {


        if ($this->cod_servidor_alocacao) {
            $obj_tmp = new clsPmieducarServidorAlocacao($this->cod_servidor_alocacao, null, $this->pessoa_logada);
            $excluiu = $obj_tmp->excluir();

            if ($excluiu) {
                $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
                $this->simpleRedirect(sprintf('educar_servidor_alocacao_lst.php?ref_cod_servidor=%d&ref_cod_instituicao=%d', $this->ref_cod_servidor, $this->ref_ref_cod_instituicao));
            }
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function hhmmToMinutes($hhmm)
    {
        list($hora, $minuto) = explode(':', $hhmm);

        return (((int)$hora * 60) + $minuto);
    }

    public function arrayHhmmToMinutes($array)
    {
        $total = 0;
        foreach ($array as $key => $value) {
            $total += $this->hhmmToMinutes($value);
        }

        return $total;
    }
}

$pagina = new clsIndexBase();
$pagina->addForm(new indice());
$pagina->MakeAll();
