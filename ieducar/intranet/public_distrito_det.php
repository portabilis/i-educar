<?php

use App\Models\District;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

return new class extends clsDetalhe {
    use InteractWithDatabase, SelectOptions;

    public $idmun;
    public $geom;
    public $iddis;
    public $nome;

    public function model()
    {
        return District::class;
    }

    public function index()
    {
        return 'public_distrito_lst.php';
    }

    public function Gerar()
    {
        $this->titulo = 'Distrito - Detalhe';
        $this->iddis = $_GET['iddis'];

        $district = $this->find($this->iddis);

        if ($district->name) {
            $this->addDetalhe(['Nome', $district->name]);
        }

        if ($district->city->name) {
            $this->addDetalhe(['Município', $district->city->name]);
        }

        if ($district->city->state->name) {
            $this->addDetalhe(['Estado', $district->city->state->name]);
        }

        if ($district->city->state->country->name) {
            $this->addDetalhe(['Pais', $district->city->state->country->name]);
        }

        if ($district->ibge_code) {
            $this->addDetalhe(['Código INEP', $district->ibge_code]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(759, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_distrito_cad.php';
            $this->url_editar = 'public_distrito_cad.php?iddis=' . $this->iddis;
        }

        $this->url_cancelar = 'public_distrito_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do distrito', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distrito';
        $this->processoAp = 759;
    }
};
