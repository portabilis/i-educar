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

    public $cod_calendario_dia_motivo;
    public $ref_cod_escola;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $sigla;
    public $descricao;
    public $tipo;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_motivo;

    public $ref_cod_instituicao;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_calendario_dia_motivo=$_GET['cod_calendario_dia_motivo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(576, $this->pessoa_logada, 7, 'educar_calendario_dia_motivo_lst.php');

        if (is_numeric($this->cod_calendario_dia_motivo)) {
            $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $this->fexcluir = $obj_permissoes->permissao_excluir(576, $this->pessoa_logada, 7);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_calendario_dia_motivo_det.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}" : 'educar_calendario_dia_motivo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' motivo de dias do calendário', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_calendario_dia_motivo', $this->cod_calendario_dia_motivo);

        if ($this->cod_calendario_dia_motivo) {
            $obj_calendario_dia_motivo = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
            $obj_calendario_dia_motivo_det = $obj_calendario_dia_motivo->detalhe();
            $this->ref_cod_escola = $obj_calendario_dia_motivo_det['ref_cod_escola'];
            $obj_ref_cod_escola = new clsPmieducarEscola($this->ref_cod_escola);
            $det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
            $this->ref_cod_instituicao = $det_ref_cod_escola['ref_cod_instituicao'];
        }

        // foreign keys
        $obrigatorio = true;
        $get_escola = true;
        // foreign keys

        // text
        $this->inputsHelper()->dynamic(['instituicao','escola']);
        $this->campoTexto('nm_motivo', 'Motivo', $this->nm_motivo, 30, 255, true);
        $this->campoTexto('sigla', 'Sigla', $this->sigla, 15, 15, true);
        $this->campoMemo('descricao', 'Descric&atilde;o', $this->descricao, 60, 5, false);

        $opcoes = [ '' => 'Selecione', 'e' => 'extra', 'n' => 'n&atilde;o-letivo' ];
        $this->campoLista('tipo', 'Tipo', $opcoes, $this->tipo);
    }

    public function Novo()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo(null, $this->ref_cod_escola, null, $this->pessoa_logada, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo);
        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst.php')
            );
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, $this->ref_cod_escola, $this->pessoa_logada, null, $this->sigla, $this->descricao, $this->tipo, null, null, 1, $this->nm_motivo);
        $editou = $obj->edita();
        if ($editou) {
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';

            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst.php')
            );
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo, null, $this->pessoa_logada, null, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            throw new HttpResponseException(
                new RedirectResponse('educar_calendario_dia_motivo_lst')
            );
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Formular()
    {
        $this->title = 'Calend&aacute;rio Dia Motivo';
        $this->processoAp = '576';
    }
};
