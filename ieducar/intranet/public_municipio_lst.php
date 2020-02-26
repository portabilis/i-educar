<?php

use App\Models\City;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
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

class indice extends clsListagem
{
    use InteractWithDatabase, SelectOptions;

    public $__limite;
    public $__offset;
    public $idmun;
    public $nome;
    public $sigla_uf;
    public $cod_ibge;
    public $geom;
    public $tipo;
    public $idpes_rev;
    public $idpes_cad;
    public $data_rev;
    public $data_cad;
    public $origem_gravacao;
    public $operacao;
    public $idpais;

    public function model()
    {
        return City::class;
    }

    public function Gerar()
    {
        $this->__titulo = 'Município - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Estado'
        ]);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf, '', false, '', '', false, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->with('state');
            $query->orderBy('name');
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->when($this->iduf, function ($query) {
                $query->where('state_id', $this->iduf);
            });
        });

        foreach ($data as $item) {
            $this->addLinhas([
                "<a href=\"public_municipio_det.php?idmun={$item->id}\">{$item->name}</a>",
                "<a href=\"public_municipio_det.php?idmun={$item->id}\">{$item->state->abbreviation}</a>"
            ]);
        }

        $this->addPaginador2('public_municipio_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(755, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_municipio_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de municípios', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script>
  document.getElementById('idpais').onchange = function () {
    var campoPais = document.getElementById('idpais').value;

    var campoUf = document.getElementById('iduf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia("public_uf_xml.php?pais=" + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('iduf');
    var DOM_array = xml_uf.getElementsByTagName("estado");

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("id"), false, false);
      }
    } else
      campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
</script>
