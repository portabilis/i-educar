<?php

use App\Models\LegacyEducationLevel;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_nivel_ensino;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_nivel;

    public $descricao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $ref_cod_instituicao;

    public function Gerar()
    {
        $this->titulo = 'Nível Ensino - Detalhe';

        $this->cod_nivel_ensino = $_GET['cod_nivel_ensino'];

        $registro = LegacyEducationLevel::find(id: $this->cod_nivel_ensino)?->getAttributes();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_nivel_ensino_lst.php');
        }

        $obj_instituicao = new clsPmieducarInstituicao(cod_instituicao: $registro['ref_cod_instituicao']);
        $obj_instituicao_det = $obj_instituicao->detalhe();
        $registro['ref_cod_instituicao'] = $obj_instituicao_det['nm_instituicao'];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);
        if ($nivel_usuario == 1) {
            if ($registro['ref_cod_instituicao']) {
                $this->addDetalhe(detalhe: ['Instituição', "{$registro['ref_cod_instituicao']}"]);
            }
        }
        if ($registro['nm_nivel']) {
            $this->addDetalhe(detalhe: ['Nível Ensino', "{$registro['nm_nivel']}"]);
        }
        if ($registro['descricao']) {
            $this->addDetalhe(detalhe: ['Descrição', "{$registro['descricao']}"]);
        }

        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 571, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_nivel_ensino_cad.php';
            $this->url_editar = "educar_nivel_ensino_cad.php?cod_nivel_ensino={$registro['cod_nivel_ensino']}";
        }
        $this->url_cancelar = 'educar_nivel_ensino_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe do nível de ensino', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Nivel Ensino';
        $this->processoAp = '571';
    }
};
