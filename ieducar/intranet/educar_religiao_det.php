<?php

use App\Models\Religion;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_religiao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_religiao;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Religiao - Detalhe';

        $this->cod_religiao = $_GET['cod_religiao'];

        $registro = Religion::findOrFail(id: $this->cod_religiao, columns: ['id', 'name'])?->getAttributes();

        if (!$registro) {
            $this->simpleRedirect(url: 'educar_religiao_lst.php');
        }

        if ($registro['id']) {
            $this->addDetalhe(detalhe: ['Religião', "{$registro['id']}"]);
        }
        if ($registro['name']) {
            $this->addDetalhe(detalhe: ['Nome Religião', "{$registro['name']}"]);
        }

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(int_processo_ap: 579, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3)) {
            $this->url_novo = 'educar_religiao_cad.php';
            $this->url_editar = "educar_religiao_cad.php?cod_religiao={$registro['id']}";
        }
        //**

        $this->url_cancelar = 'educar_religiao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da religião', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Religiao';
        $this->processoAp = '579';
    }
};
