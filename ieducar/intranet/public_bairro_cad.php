<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicDistrito.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'App/Model/ZonaLocalizacao.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Bairro');
        $this->processoAp = 756;
    }
}

class indice extends clsCadastro
{
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
    public $zona_localizacao;
    public $iddis;
    public $idpais;
    public $sigla_uf;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->idbai = $_GET['idbai'];

        if (is_numeric($this->idbai)) {
            $obj_bairro = new clsPublicBairro();
            $lst_bairro = $obj_bairro->lista(
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->idbai
            );

            if ($lst_bairro) {
                $registro = $lst_bairro[0];
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
            'public_bairro_det.php?idbai=' . $registro['idbai'] :
            'public_bairro_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} bairro", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idbai', $this->idbai);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idpais']] = $registro['nome'];
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $objTemp = new clsUf();

            $lista = $objTemp->lista(false, false, $this->idpais, false, false, 'nome ASC');

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['sigla_uf']] = $registro['nome'];
                }
            }
        }

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf);

        $opcoes = ['' => 'Selecione'];

        if ($this->sigla_uf) {
            $objTemp = new clsMunicipio();
            $lista = $objTemp->lista(
                false,
                $this->sigla_uf,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                false,
                'nome ASC'
            );

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['idmun']] = $registro['nome'];
                }
            }
        }

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun);

        $opcoes = ['' => 'Selecione'];

        if ($this->idmun) {
            $objTemp = new clsPublicDistrito();
            $objTemp->setOrderBy(' nome asc ');
            $lista = $objTemp->lista($this->idmun);

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['iddis']] = $registro['nome'];
                }
            }
        }

        $this->campoLista('iddis', 'Distrito', $opcoes, $this->iddis);

        $zona = App_Model_ZonaLocalizacao::getInstance();
        $this->campoLista(
            'zona_localizacao',
            'Zona Localização',
            $zona->getEnums(),
            $this->zona_localizacao
        );

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 80, true);
    }

    public function Novo()
    {
        $obj = new clsPublicBairro(
            $this->idmun,
            null,
            null,
            $this->nome,
            null,
            null,
            'U',
            $this->pessoa_logada,
            null,
            'I',
            null,
            9,
            $this->zona_localizacao,
            $this->iddis
        );

        $cadastrou = $obj->cadastra();

        if ($cadastrou) {
            $enderecamento = new clsPublicBairro();
            $enderecamento->idbai = $cadastrou;
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Bairro', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('public_bairro_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $enderecamentoDetalhe = new clsPublicBairro(null, null, $this->idbai);
        $enderecamentoDetalhe->cadastrou = $this->idbai;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicBairro(
            $this->idmun,
            null,
            $this->idbai,
            $this->nome,
            $this->pessoa_logada,
            null,
            'U',
            null,
            null,
            'I',
            null,
            9,
            $this->zona_localizacao,
            $this->iddis
        );

        $editou = $obj->edita();

        if ($editou) {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Bairro', $this->pessoa_logada, $this->idbai);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('public_bairro_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPublicBairro(null, null, $this->idbai, null, $this->pessoa_logada);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_bairro_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
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

    var campoUf = document.getElementById('sigla_uf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('sigla_uf');
    var DOM_array = xml_uf.getElementsByTagName('estado');

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data,
          DOM_array[i].getAttribute('sigla_uf'), false, false);
      }
    } else {
      campoUf.options[0].text = 'O pais não possui nenhum estado';
    }
  }

  document.getElementById('sigla_uf').onchange = function () {
    var campoUf = document.getElementById('sigla_uf').value;

    var campoMunicipio = document.getElementById('idmun');
    campoMunicipio.length = 1;
    campoMunicipio.disabled = true;
    campoMunicipio.options[0].text = 'Carregando município...';

    var xml_municipio = new ajax(getMunicipio);
    xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
  }

  function getMunicipio (xml_municipio) {
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

  document.getElementById('idmun').onchange = function () {
    var campoMunicipio = document.getElementById('idmun').value;

    var campoDistrito = document.getElementById('iddis');
    campoDistrito.length = 1;
    campoDistrito.disabled = true;

    campoDistrito.options[0].text = 'Carregando distritos...';

    var xml_distrito = new ajax(getDistrito);
    xml_distrito.envia('public_distrito_xml.php?idmun=' + campoMunicipio);
  }

  function getDistrito (xml_distrito) {
    var campoDistrito = document.getElementById('iddis');
    var DOM_array = xml_distrito.getElementsByTagName("distrito");
    console.log(DOM_array);

    if (DOM_array.length) {
      campoDistrito.length = 1;
      campoDistrito.options[0].text = 'Selecione um distrito';
      campoDistrito.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoDistrito.options[campoDistrito.options.length] = new Option(
          DOM_array[i].firstChild.data, DOM_array[i].getAttribute('iddis'),
          false, false
        );
      }
    } else {
      campoDistrito.options[0].text = 'O município não possui nenhum distrito';
    }
  }
</script>
