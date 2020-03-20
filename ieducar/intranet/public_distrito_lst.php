<?php

use App\Models\District;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Distrito');
        $this->processoAp = 759;
    }
}

class indice extends clsListagem
{
    use InteractWithDatabase, SelectOptions;

    public $__limite;
    public $__offset;
    public $idmun;
    public $geom;
    public $idbai;
    public $nome;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idpais;
    public $iduf;

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
        $this->__titulo = 'Distrito - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome',
            'Município',
            'Estado',
            'Pais'
        ]);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista(
            'idpais',
            'Pais',
            $opcoes,
            $this->idpais,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista(
            'iduf',
            'Estado',
            $opcoes,
            $this->iduf,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $opcoes = ['' => 'Selecione'];

        if ($this->iduf) {
            $opcoes += $this->getCities($this->iduf);
        }

        $this->campoLista(
            'idmun',
            'Município',
            $opcoes,
            $this->idmun,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);

        $this->__limite = 20;
        $this->__offset = ($_GET['pagina_' . $this->nome]) ? ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;

        [$data, $total] = $this->paginate($this->__limite, $this->__offset, function ($query) {
            $query->with('city.state.country');
            $query->orderBy('name');
            $query->when($this->nome, function ($query) {
                $query->whereUnaccent('name', $this->nome);
            });
            $query->when($this->idmun, function ($query) {
                $query->where('city_id', $this->idmun);
            });
        });

        $url = CoreExt_View_Helper_UrlHelper::getInstance();
        $options = ['query' => ['iddis' => null]];

        foreach ($data as $district) {
            $options['query']['iddis'] = $district->id;

            $this->addLinhas([
                $url->l($district->name, 'public_distrito_det.php', $options),
                $url->l($district->city->name, 'public_distrito_det.php', $options),
                $url->l($district->city->state->name, 'public_distrito_det.php', $options),
                $url->l($district->city->state->country->name, 'public_distrito_det.php', $options)
            ]);
        }

    $this->addPaginador2('public_distrito_lst.php', $total, $_GET, $this->nome, $this->__limite);

    $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(759, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_distrito_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Listagem de distritos', [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>
<script type="text/javascript">
  document.getElementById('idpais').onchange = function () {
    var campoPais = document.getElementById('idpais').value;

    var campoUf = document.getElementById('iduf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('iduf');
    var DOM_array = xml_uf.getElementsByTagName('estado');

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(
          DOM_array[i].firstChild.data, DOM_array[i].getAttribute('id'),
          false, false
        );
      }
    } else {
      campoUf.options[0].text = 'O pais não possui nenhum estado';
    }
  }

  document.getElementById('iduf').onchange = function () {
    var campoUf = document.getElementById('iduf').value;

    var campoMunicipio = document.getElementById('idmun');
    campoMunicipio.length = 1;
    campoMunicipio.disabled = true;

    campoMunicipio.options[0].text = 'Carregando município...';

    var xml_municipio = new ajax(getMunicipio);
    xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
  }

  function getMunicipio (xml_municipio) {
    var campoMunicipio = document.getElementById('idmun');
    var DOM_array = xml_municipio.getElementsByTagName("municipio");

    if (DOM_array.length) {
      campoMunicipio.length = 1;
      campoMunicipio.options[0].text = 'Selecione um município';
      campoMunicipio.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoMunicipio.options[campoMunicipio.options.length] = new Option(
          DOM_array[i].firstChild.data, DOM_array[i].getAttribute('idmun'),
          false, false
        );
      }
    } else {
      campoMunicipio.options[0].text = 'O estado não possui nenhum município';
    }
  }
</script>
