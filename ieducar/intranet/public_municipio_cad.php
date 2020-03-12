<?php

use App\Models\City;
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
        $this->SetTitulo($this->_instituicao . ' Município');
        $this->processoAp = 755;
    }
}

class indice extends clsCadastro
{
    use InteractWithDatabase, SelectOptions;

    public $idmun;
    public $nome;
    public $iduf;
    public $cod_ibge;
    public $idpais;

    public function model()
    {
        return City::class;
    }

    public function index()
    {
        return 'public_municipio_lst.php';
    }

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->idmun = $_GET['idmun'];

        if (is_numeric($this->idmun)) {
            $city = $this->find($this->idmun);

            $this->cod_ibge = $city->ibge_code;
            $this->nome = $city->name;
            $this->iduf = $city->state_id;
            $this->idpais = $city->state->country_id;

            $retorno = 'Editar';
        }
        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_municipio_det.php?idmun=' . $this->idmun
            : 'public_municipio_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} município", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idmun', $this->idmun);

        $opcoes = ['' => 'Selecione'] + $this->getCountries();

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $opcoes += $this->getStates($this->idpais);
        }

        $this->campoLista('iduf', 'Estado', $opcoes, $this->iduf);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 60, true);
        $this->campoNumero('cod_ibge', 'Código INEP', $this->cod_ibge, 7, 7);
    }

    public function Novo()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido cadastro de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->create([
            'name' => request('nome'),
            'state_id' => request('iduf'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Editar()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido edição de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->update($this->idmun, [
            'name' => request('nome'),
            'state_id' => request('iduf'),
            'ibge_code' => request('cod_ibge'),
        ]);
    }

    public function Excluir()
    {
        if ($this->idpais == App_Model_Pais::BRASIL && $this->nivelAcessoPessoaLogada() != App_Model_NivelAcesso::POLI_INSTITUCIONAL) {
            $this->mensagem = 'Não é permitido exclusão de municípios brasileiros, pois já estão previamente cadastrados.<br>';

            return false;
        }

        return $this->delete($this->idmun);
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
};

function getUf(xml_uf) {
  var campoUf = document.getElementById('iduf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoUf.options[campoUf.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('id'),
        false, false);
    }
  } else {
    campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
}
</script>
