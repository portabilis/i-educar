<?php

use App\Models\City;
use iEducar\Legacy\InteractWithDatabase;

return new class extends clsDetalhe {
    use InteractWithDatabase;

    public $idmun;
    public $nome;
    public $sigla_uf;
    public $cod_ibge;

    public function model()
    {
        return City::class;
    }

    public function index()
    {
        return 'public_municipio_lst.php';
    }

    public function Gerar()
    {
        $this->titulo = 'Município - Detalhe';

        $this->idmun = $_GET['idmun'];

        $city = $this->find($this->idmun);

        $this->cod_ibge = $city->ibge_code;
        $this->nome = $city->name;

        if ($this->nome) {
            $this->addDetalhe(['Nome', $this->nome]);
        }
        if ($city->state->name) {
            $this->addDetalhe(['Estado', $city->state->name]);
        }
        if ($city->state->country->name) {
            $this->addDetalhe(['Pais', $city->state->country->name]);
        }
        if ($this->cod_ibge) {
            $this->addDetalhe(['Código INEP', $this->cod_ibge]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(755, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_municipio_cad.php';
            $this->url_editar = "public_municipio_cad.php?idmun={$this->idmun}";
        }

        $this->url_cancelar = 'public_municipio_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe do município', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Município';
        $this->processoAp = 755;
    }
};
