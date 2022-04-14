<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsCadastro {
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_calendario_anotacao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_anotacao;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public $dia;
    public $mes;
    public $ano;

    public $ref_ref_cod_calendario_ano_letivo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_calendario_anotacao=$_GET['cod_calendario_anotacao'];
        $this->dia=$_GET['dia'];
        $this->mes=$_GET['mes'];
        $this->ano=$_GET['ano'];
        $this->ref_ref_cod_calendario_ano_letivo=$_GET['ref_cod_calendario_ano_letivo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_anotacao_lst.php');
        if (!is_numeric($this->ref_ref_cod_calendario_ano_letivo) || !is_numeric($this->dia) || !is_numeric($this->mes)) {
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_ano_letivo_lst.php')
            );
        }
        if (is_numeric($this->cod_calendario_anotacao)) {
            $obj = new clsPmieducarCalendarioAnotacao($this->cod_calendario_anotacao);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $retorno = 'Editar';
            }
        }
        //$this->url_cancelar = ($retorno == "Editar") ? "educar_calendario_anotacao_lst.php?cod_calendario_anotacao={$registro["cod_calendario_anotacao"]}" : "educar_calendario_anotacao_lst.php";
        $this->url_cancelar =  "educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_ref_cod_calendario_ano_letivo}";
        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoRotulo('info', '-', "Anota&ccedil;&otilde;es Calend&aacute;rio do dia <b>{$this->dia}/{$this->mes}/{$this->ano}</b>");
        $this->campoOculto('cod_calendario_anotacao', $this->cod_calendario_anotacao);

        $this->campoOculto('dia', $this->dia);
        $this->campoOculto('mes', $this->mes);
        $this->campoOculto('ano', $this->ano);
        $this->campoOculto('ref_ref_cod_calendario_ano_letivo', $this->ref_ref_cod_calendario_ano_letivo);

        // text
        $this->campoTexto('nm_anotacao', 'Anota&ccedil;&atilde;o', $this->nm_anotacao, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);

        // data
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_anotacao_lst.php');

        $obj_dia = new clsPmieducarCalendarioDia($this->ref_ref_cod_calendario_ano_letivo, $this->mes, $this->dia);
        if (!$obj_dia->existe()) {
            $obj_dia = new clsPmieducarCalendarioDia($this->ref_ref_cod_calendario_ano_letivo, $this->mes, $this->dia, null, $this->pessoa_logada, null, null, null, null, 1);
            $ref_cod_dia_letivo = $obj_dia->cadastra();
            if (!$ref_cod_dia_letivo) {
                return false;
            }
        }
        $obj = new clsPmieducarCalendarioAnotacao($this->cod_calendario_anotacao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_anotacao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $this->cod_calendario_anotacao = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj_anotacao_dia = new clsPmieducarCalendarioDiaAnotacao($this->dia, $this->mes, $this->ref_ref_cod_calendario_ano_letivo, $cadastrou);
            $cadastrado = $obj_anotacao_dia->cadastra();
            if ($cadastrado) {
                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect("educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_ref_cod_calendario_ano_letivo}");
            }

            return false;
        }
        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(620, $this->pessoa_logada, 7, 'educar_calendario_anotacao_lst.php');

        $obj = new clsPmieducarCalendarioAnotacao($this->cod_calendario_anotacao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_anotacao, $this->descricao, $this->data_cadastro, $this->data_exclusao, $this->ativo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            throw new HttpResponseException(
                new RedirectResponse("educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_cod_calendario_ano_letivo}")
            );
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(620, $this->pessoa_logada, 7, 'educar_calendario_anotacao_lst.php');

        $obj = new clsPmieducarCalendarioAnotacao($this->cod_calendario_anotacao, $this->pessoa_logada, $this->pessoa_logada, $this->nm_anotacao, $this->descricao, $this->data_cadastro, $this->data_exclusao, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            throw new HttpResponseException(
                new RedirectResponse("educar_calendario_anotacao_lst.php?dia={$this->dia}&mes={$this->mes}&ano={$this->ano}&ref_cod_calendario_ano_letivo={$this->ref_ref_cod_calendario_ano_letivo}")
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Calendario Anotacao';
        $this->processoAp = '620';
    }
};
