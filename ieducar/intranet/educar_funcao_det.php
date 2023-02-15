<?php

use App\Models\LegacyQualification;
use App\Models\LegacyRole;

return new class extends clsDetalhe {
    public $pessoa_logada;
    public $cod_funcao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_funcao;
    public $abreviatura;
    public $professor;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Servidores -  Funções do servidor';

        $this->cod_funcao = $_GET['cod_funcao'];

        $registro = LegacyRole::find($this->cod_funcao)?->getAttributes();
        if (! $registro) {
            $this->simpleRedirect('educar_funcao_lst.php');
        }
        if ($registro['ref_cod_instituicao']) {
            $obj_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
            $obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
            $registro['ref_cod_instituicao'] = $obj_cod_instituicao_det['nm_instituicao'];

            $this->addDetalhe([ 'Instituição', "{$registro['ref_cod_instituicao']}"]);
        }
        $this->addDetalhe([ 'Nome', "{$registro['nm_funcao']}"]);
        $this->addDetalhe([ 'Abreviatura', "{$registro['abreviatura']}"]);
        $professor = $registro['professor'] == 1 ? 'Sim' : 'Não';
        $this->addDetalhe([ 'Professor', "{$professor}"]);

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 573, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_funcao_cad.php';
            $this->url_editar = "educar_funcao_cad.php?cod_funcao={$registro['cod_funcao']}";
        }
        $this->url_cancelar = 'educar_funcao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da função', breadcrumbs: [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores -  Funções do servidor';
        $this->processoAp = '634';
    }
};
