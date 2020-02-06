<?php

use App\Models\District;
use iEducar\Legacy\InteractWithDatabase;
use iEducar\Legacy\SelectOptions;

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'App/Model/Pais.php';
require_once 'App/Model/NivelAcesso.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Distrito');
        $this->processoAp = 759;
    }
}

class indice extends clsCadastro
{
    use InteractWithDatabase, SelectOptions;

    public $idmun;
    public $geom;
    public $iddis;
    public $nome;
    public $cod_ibge;
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

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->iddis = $_GET['iddis'];

        if (is_numeric($this->iddis)) {
            $district = $this->find($this->iddis);

            $this->nome = $district->name;
            $this->idmun = $district->city_id;
            $this->iduf = $district->city->state_id;
            $this->idpais = $district->city->state->country_id;
            $this->cod_ibge = $district->ibge_code;

            $retorno = 'Editar';
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_distrito_det.php?iddis=' . $this->iddis
            : 'public_distrito_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} distrito", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('iddis', $this->iddis);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf);

        $opcoes = ['' => 'Selecione'];

        if ($this->iduf) {
            $opcoes += $this->getCities($this->iduf);
        }

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);
        $this->campoTexto('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7, null, null, null, 'Somente números');
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->create([
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
            'city_id' => request('idmun'),
        ]);
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->update($this->iddis, [
            'name' => request('nome'),
            'ibge_code' => request('cod_ibge'),
            'city_id' => request('idmun'),
        ]);
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de distritos brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->delete($this->iddis);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>
<script type='text/javascript'>
document.getElementById('idpais').onchange = function () {
  var campoPais = document.getElementById('idpais').value;

  var campoUf = document.getElementById('iduf');
  campoUf.length = 1;
  campoUf.disabled = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax(getUf);
  xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
};

function getUf(xml_uf) {
  var campoUf = document.getElementById('iduf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data,
        DOM_array[i].getAttribute('id'), false, false);
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
};

function getMunicipio(xml_municipio) {
  var campoMunicipio = document.getElementById('idmun');
  var DOM_array = xml_municipio.getElementsByTagName('municipio');

  if (DOM_array.length) {
    campoMunicipio.length = 1;
    campoMunicipio.options[0].text = 'Selecione um município';
    campoMunicipio.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoMunicipio.options[campoMunicipio.options.length] = new Option(DOM_array[i].firstChild.data,
        DOM_array[i].getAttribute('idmun'), false, false);
    }
  } else {
    campoMunicipio.options[0].text = 'O estado não possui nenhum município';
  }
}
</script>
