<?php

use App\Models\City;
use iEducar\Legacy\InteractWithDatabase;

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Município");
        $this->processoAp = 755;
    }
}

class indice extends clsDetalhe
{
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
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
