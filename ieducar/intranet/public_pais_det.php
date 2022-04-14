<?php

use App\Models\Country;
use iEducar\Legacy\InteractWithDatabase;

return new class extends clsDetalhe {
    use InteractWithDatabase;

    public $idpais;
    public $nome;
    public $geom;

    public function model()
    {
        return Country::class;
    }

    public function index()
    {
        return 'public_pais_lst.php';
    }

    public function Gerar()
    {
        $this->titulo = 'Pais - Detalhe';

        $this->idpais = $_GET['idpais'];

        $country = $this->find($this->idpais);

        if ($country->name) {
            $this->addDetalhe(['Nome', "{$country->name}"]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(753, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_pais_cad.php';
            $this->url_editar = "public_pais_cad.php?idpais={$country->id}";
        }

        $this->url_cancelar = 'public_pais_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do país', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Pais';
        $this->processoAp = 753;
    }
};
