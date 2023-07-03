<?php

use App\Models\LegacyRace;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_raca;

    public $idpes_exc;

    public $idpes_cad;

    public $nm_raca;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $pessoa_logada;

    public function Gerar()
    {
        $this->titulo = 'Raça - Detalhe';

        $this->cod_raca = $_GET['cod_raca'];

        $race = LegacyRace::query()->find(id: $this->cod_raca);

        if (!$race) {
            $this->simpleRedirect(url: 'educar_raca_lst.php');
        }

        if ($race->nm_raca) {
            $this->addDetalhe(detalhe: ['Raça', $race->nm_raca]);
        }

        $obj_permissao = new clsPermissoes();
        if ($obj_permissao->permissao_cadastra(int_processo_ap: 678, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->url_novo = 'educar_raca_cad.php';
            $this->url_editar = "educar_raca_cad.php?cod_raca=$race->cod_raca";
        }

        $this->url_cancelar = 'educar_raca_lst.php';
        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Detalhe da raça', breadcrumbs: [
            url(path: 'intranet/educar_pessoas_index.php') => 'Pessoas',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Raça';
        $this->processoAp = '678';
    }
};
