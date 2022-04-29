<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

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
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Calendário Dia Motivo - Detalhe';

        $this->cod_calendario_dia_motivo=$_GET['cod_calendario_dia_motivo'];

        $tmp_obj = new clsPmieducarCalendarioDiaMotivo($this->cod_calendario_dia_motivo);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_calendario_dia_motivo_lst.php');
        }

        $obj_cod_escola = new clsPmieducarEscola($registro['ref_cod_escola']);
        $obj_cod_escola_det = $obj_cod_escola->detalhe();
        $registro['ref_cod_escola'] = $obj_cod_escola_det['nome'];

        $cod_instituicao = $obj_cod_escola_det['ref_cod_instituicao'];
        $obj_instituicao = new clsPmieducarInstituicao($cod_instituicao);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $nm_instituicao = $obj_instituicao_det['nm_instituicao'];

        if ($nm_instituicao) {
            $this->addDetalhe([ 'Instituição', "{$nm_instituicao}" ]);
        }
        if ($registro['ref_cod_escola']) {
            $this->addDetalhe([ 'Escola', "{$registro['ref_cod_escola']}"]);
        }
        if ($registro['nm_motivo']) {
            $this->addDetalhe([ 'Motivo', "{$registro['nm_motivo']}"]);
        }
        if ($registro['sigla']) {
            $this->addDetalhe([ 'Sigla', "{$registro['sigla']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descricão', "{$registro['descricao']}"]);
        }
        if ($registro['tipo']) {
            if ($registro['tipo'] == 'e') {
                $registro['tipo'] = 'extra';
            } elseif ($registro['tipo'] == 'n') {
                $registro['tipo'] = 'não-letivo';
            }
            $this->addDetalhe([ 'Tipo', "{$registro['tipo']}"]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(576, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_calendario_dia_motivo_cad.php';
            $this->url_editar = "educar_calendario_dia_motivo_cad.php?cod_calendario_dia_motivo={$registro['cod_calendario_dia_motivo']}";
        }
        $this->url_cancelar = 'educar_calendario_dia_motivo_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do motivo de dias do calendário', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Calendário Dia Motivo';
        $this->processoAp = '576';
    }
};
