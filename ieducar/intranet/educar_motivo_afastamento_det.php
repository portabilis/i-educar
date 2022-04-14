<?php

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_motivo_afastamento;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_motivo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    //var $ref_cod_escola;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Motivo Afastamento - Detalhe';

        $this->cod_motivo_afastamento=$_GET['cod_motivo_afastamento'];

        $tmp_obj = new clsPmieducarMotivoAfastamento($this->cod_motivo_afastamento);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            throw new HttpResponseException(
                new RedirectResponse('educar_motivo_afastamento_lst.php')
            );
        }

        $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
        $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();

        $obj_permissao = new clsPermissoes();
        $nivel_usuario = $obj_permissao->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($det_ref_cod_instituicao['nm_instituicao']) {
                $this->addDetalhe([ 'Institui&ccedil;&atilde;o', "{$det_ref_cod_instituicao['nm_instituicao']}"]);
            }
        }
        if ($registro['nm_motivo']) {
            $this->addDetalhe([ 'Motivo de Afastamento', "{$registro['nm_motivo']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe([ 'Descri&ccedil;&atilde;o', "{$registro['descricao']}"]);
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(633, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_motivo_afastamento_cad.php';
            $this->url_editar = "educar_motivo_afastamento_cad.php?cod_motivo_afastamento={$registro['cod_motivo_afastamento']}";
        }

        $this->url_cancelar = 'educar_motivo_afastamento_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do motivo de afastamento', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores - Motivo Afastamento';
        $this->processoAp = '633';
    }
};
