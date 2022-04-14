<?php

use App\Models\State;
use iEducar\Legacy\InteractWithDatabase;

return new class extends clsDetalhe {
    use InteractWithDatabase;

    public $id;
    public $sigla_uf;
    public $nome;
    public $cod_ibge;
    public $idpais;

    public function model()
    {
        return State::class;
    }

    public function index()
    {
        return 'public_uf_lst.php';
    }

    public function Gerar()
    {
        $this->titulo = 'Uf - Detalhe';

        $this->id = $_GET['id'];

        $model = $this->find($this->id);

        $this->nome = $model->name;
        $this->sigla_uf = $model->abbreviation;
        $this->idpais = $model->country_id;
        $this->cod_ibge = $model->ibge_code;

        if ($this->sigla_uf) {
            $this->addDetalhe(['Sigla Uf', $this->sigla_uf]);
        }
        if ($this->nome) {
            $this->addDetalhe(['Nome', $this->nome]);
        }
        if ($this->idpais) {
            $this->addDetalhe(['Pais', $model->country->name]);
        }
        if ($this->cod_ibge) {
            $this->addDetalhe(['Código INEP', $this->cod_ibge]);
        }

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(754, $this->pessoa_logada, 7, null, true)) {
            $this->url_novo = 'public_uf_cad.php';
            $this->url_editar = "public_uf_cad.php?id={$this->id}";
        }

        $this->url_cancelar = 'public_uf_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da UF', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Uf';
        $this->processoAp = 754;
    }
};
