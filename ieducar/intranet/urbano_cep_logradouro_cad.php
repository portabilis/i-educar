<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/urbano/geral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Cep Logradouro');
        $this->processoAp = 758;
    }
}

class indice extends clsCadastro
{
    public $idlog;
    public $nroini;
    public $nrofin;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idpais;
    public $sigla_uf;
    public $idmun;
    public $tab_cep = [];
    public $cep;
    public $idbai;
    public $retorno;

    public function Inicializar()
    {
        $this->retorno = 'Novo';

        $this->idlog = $_GET['idlog'];

        if (is_numeric($this->idlog)) {
            $obj_cep_logradouro = new clsUrbanoCepLogradouro();
            $obj_cep_logradouro->_campo_order_by = 'cep';
            $lst_cep_logradouro = $obj_cep_logradouro->lista(
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
                $this->idlog
            );
            if ($lst_cep_logradouro) {
                $registro = $lst_cep_logradouro[0];
            }
            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->retorno = 'Editar';
                $this->tab_cep = $this->getListCepBairro();
            }
        } else {
            $this->tab_cep[] = [];
        }

        $this->url_cancelar = $this->retorno == 'Editar'
            ? 'urbano_cep_logradouro_det.php?idlog=' . $registro['idlog']
            : 'urbano_cep_logradouro_lst.php';

        $this->nome_url_cancelar = 'Cancelar';
        $nomeMenu = $this->retorno == 'Editar' ? $this->retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} CEP", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $this->retorno;
    }

    public function Gerar()
    {
        $habilitaCampo = ($this->retorno == 'Editar');

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idpais']] = $registro['nome'];
            }
        }

        $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', false, '', '', $habilitaCampo);

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

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf, '', false, '', '', $habilitaCampo);

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

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun, '', false, '', '', $habilitaCampo);
        $opcoes = ['' => 'Selecione'];

        if ($this->idmun) {
            $objTemp = new clsLogradouro();
            $lista = $objTemp->lista(
                false,
                false,
                $this->idmun,
                false,
                false,
                false,
                false,
                'nome ASC'
            );
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['idlog']] = $registro['nome'];
                }
            }
        }

        $this->campoLista('idlog', 'Logradouro', $opcoes, $this->idlog, '', false, '', '', $habilitaCampo);
        $this->campoTabelaInicio('tab_cep', 'Tabela de CEP', ['CEP', 'Bairro'], $this->tab_cep, 400);

        $opcoes_bairro = ['' => 'Selecione'];

        if ($this->idmun) {
            $obj_bairro = new clsBairro();
            $lst_bairro = $obj_bairro->lista(
                $this->idmun,
                false,
                false,
                false,
                false,
                'nome ASC'
            );
            if ($lst_bairro) {
                foreach ($lst_bairro as $campo) {
                    $opcoes_bairro[$campo['idbai']] = $campo['nome'];
                }
            }
        }
        $this->campoCep('cep', 'CEP', $this->cep, true);
        $this->campoLista('idbai', 'Bairro', $opcoes_bairro, $this->idbai);
        $this->campoTabelaFim();

        $scripts = [
            '/modules/Portabilis/Assets/Javascripts/Utils.js',
            '/modules/Portabilis/Assets/Javascripts/ClientApi.js'
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function Novo()
    {
        $this->Editar();
    }

    public function Editar()
    {
        $this->idlog = !$this->idlog ? $_GET['idlog'] : $this->idlog;

        $tab_cep_aux = $this->getListCepBairro();

        if (($this->idbai[0] != '') && ($this->cep[0] != '')) {
            foreach ($this->cep as $id => $cep) {
                $cep = idFederal2int($cep);
                $obj = new clsUrbanoCepLogradouro(
                    $cep,
                    $this->idlog,
                    null,
                    null,
                    null,
                    null,
                    'U',
                    $this->pessoa_logada,
                    null,
                    'I',
                    null,
                    9
                );
                if (!$obj->existe()) {
                    if (!$obj->cadastra()) {
                        $this->mensagem = 'Cadastro não realizado.<br>';

                        return false;
                    }
                }
                $obj_cep_log_bairro = new clsUrbanoCepLogradouroBairro(
                    $this->idlog,
                    $cep,
                    $this->idbai[$id],
                    null,
                    null,
                    'U',
                    $this->pessoa_logada,
                    null,
                    'I',
                    null,
                    9
                );

                if (!$obj_cep_log_bairro->existe()) {
                    if ($id >= count($tab_cep_aux)) {
                        if (!$obj_cep_log_bairro->cadastra()) {
                            $this->mensagem = 'Cadastro não realizado.<br>';

                            return false;
                        }
                    } else {
                        $cepOld = idFederal2int($tab_cep_aux[$id][0]);
                        $bairroOld = $tab_cep_aux[$id][1];
                        if (!$obj_cep_log_bairro->editaCepBairro($cepOld, $bairroOld)) {
                            $this->mensagem = 'Cadastro não realizado.<br>';

                            return false;
                        }
                    }
                }
            }
            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('urbano_cep_logradouro_lst.php');
        } else {
            $this->simpleRedirect('urbano_cep_logradouro_lst.php');
        }
    }

    public function Excluir()
    {
        $obj = new clsUrbanoCepLogradouro(
            $this->cep,
            $this->idlog,
            $this->nroini,
            $this->nrofin,
            $this->idpes_rev,
            $this->data_rev,
            $this->origem_gravacao,
            $this->idpes_cad,
            $this->data_cad,
            $this->operacao
        );
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('urbano_cep_logradouro_lst.php');
        }
        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }

    public function getListCepBairro()
    {
        $tab_cep = [];

        $obj_cep_logradouro_bairro = new clsCepLogradouroBairro();
        $lst_cep_logradouro_bairro = $obj_cep_logradouro_bairro->lista(
            $this->idlog,
            false,
            false,
            'cep ASC',
            null,
            null
        );
        if ($lst_cep_logradouro_bairro) {
            foreach ($lst_cep_logradouro_bairro as $cep) {
                $tab_cep[] = [int2CEP($cep['cep']->cep), $cep['idbai']->idbai];
            }
        }

        return $tab_cep;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();

?>

<script type="text/javascript" charset="toLatin1">
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
      campoUf.options[0].text = 'O pa\u00eds n\u00e3o possui nenhum estado';
    }
  }

  document.getElementById('sigla_uf').onchange = function () {
    var campoUf = document.getElementById('sigla_uf').value;
    var campoMunicipio = document.getElementById('idmun');
    campoMunicipio.length = 1;
    campoMunicipio.disabled = true;
    campoMunicipio.options[0].text = 'Carregando munic\u00edpio...';
    var xml_municipio = new ajax(getMunicipio);
    xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
  }

  function getMunicipio (xml_municipio) {
    var campoMunicipio = document.getElementById('idmun');
    var DOM_array = xml_municipio.getElementsByTagName('municipio');
    if (DOM_array.length) {
      campoMunicipio.length = 1;
      campoMunicipio.options[0].text = 'Selecione um munic\u00edpio';
      campoMunicipio.disabled = false;
      for (var i = 0; i < DOM_array.length; i++) {
        campoMunicipio.options[campoMunicipio.options.length] = new Option(DOM_array[i].firstChild.data,
          DOM_array[i].getAttribute('idmun'), false, false);
      }
    } else {
      campoMunicipio.options[0].text = 'O estado n\u00e3o possui nenhum munic\u00edpio';
    }
  }

  document.getElementById('idmun').onchange = function () {
    var campoMunicipio = document.getElementById('idmun').value;
    var campoLogradouro = document.getElementById('idlog');
    campoLogradouro.length = 1;
    campoLogradouro.disabled = true;
    campoLogradouro.options[0].text = 'Carregando logradouro...';
    var xml_logradouro = new ajax(getLogradouro);
    xml_logradouro.envia('public_logradouro_xml.php?mun=' + campoMunicipio);
    for (var i = 0; i < tab_add_1.id; i++) {
      var campoBairro = document.getElementById('idbai[' + i + ']');
      campoBairro.length = 1;
      campoBairro.disabled = true;
      campoBairro.options[0].text = 'Carregando bairro...';
    }
    var xml_bairro = new ajax(getBairro);
    xml_bairro.envia('public_bairro_xml.php?mun=' + campoMunicipio);
  }

  function getLogradouro (xml_logradouro) {
    var campoLogradouro = document.getElementById('idlog');
    var DOM_array = xml_logradouro.getElementsByTagName('logradouro');
    if (DOM_array.length) {
      campoLogradouro.length = 1;
      campoLogradouro.options[0].text = 'Selecione um logradouro';
      campoLogradouro.disabled = false;
      for (var i = 0; i < DOM_array.length; i++) {
        if (DOM_array[i].firstChild) {
          campoLogradouro.options[campoLogradouro.options.length] = new Option(DOM_array[i].firstChild.data,
            DOM_array[i].getAttribute('idlog'), false, false);
        }
      }
    } else {
      campoLogradouro.options[0].text = 'O munic\u00edpio n\u00e3o possui nenhum logradouro';
    }
  }

  function getBairro (xml_bairro) {
    var DOM_array = xml_bairro.getElementsByTagName('bairro');
    for (var i = 0; i < tab_add_1.id; i++) {
      var campoBairro = document.getElementById('idbai[' + i + ']');
      if (DOM_array.length) {
        campoBairro.length = 1;
        campoBairro.options[0].text = 'Selecione um bairro';
        campoBairro.disabled = false;
        for (var j = 0; j < DOM_array.length; j++) {
          campoBairro.options[campoBairro.options.length] = new Option(DOM_array[j].firstChild.data,
            DOM_array[j].getAttribute('idbai'), false, false);
        }
      } else {
        campoBairro.options[0].text = 'O munic\u00edpio n\u00e3o possui nenhum bairro';
      }
    }
  }

  document.getElementById('btn_add_tab_add_1').onclick = function () {
    tab_add_1.addRow();
    var campoMunicipio = document.getElementById('idmun').value;
    var pos = tab_add_1.id - 1;
    var campoBairro = document.getElementById('idbai[' + pos + ']');
    campoBairro.length = 1;
    campoBairro.disabled = true;
    campoBairro.options[0].text = 'Carregando bairro...';
    var xml_bairro = new ajax(getBairroUnico);
    xml_bairro.envia('public_bairro_xml.php?mun=' + campoMunicipio);
  }

  function getBairroUnico (xml_bairro) {
    var pos = tab_add_1.id - 1;
    var campoBairro = document.getElementById('idbai[' + pos + ']');
    var DOM_array = xml_bairro.getElementsByTagName('bairro');
    if (DOM_array.length) {
      campoBairro.length = 1;
      campoBairro.options[0].text = 'Selecione um bairro';
      campoBairro.disabled = false;
      for (var j = 0; j < DOM_array.length; j++) {
        campoBairro.options[campoBairro.options.length] = new Option(DOM_array[j].firstChild.data,
          DOM_array[j].getAttribute('idbai'), false, false);
      }
    } else {
      campoBairro.options[0].text = 'O munic\u00edpio n\u00e3o possui nenhum bairro';
    }
  }

  $j(document).ready(function () {

    for (var i = 0; i < tab_add_1.id; i++) {

      var valorCep = $j("input[id='cep[" + i + "]']").val();
      var idBairro = $j("select[id='idbai[" + i + "]'] option:selected").val();

      if (idBairro == '') continue;

      //Remove evento de click antigo
      $j("a[id='link_remove[" + i + "]']").attr('onclick', '').unbind('click');
      //Adiciona novo evento de click para excluir via Ajax

      $j("a[id='link_remove[" + i + "]']").click({
        cep: valorCep,
        bairro: idBairro,
        button: document.getElementById('link_remove[' + i + ']')
      }, onclickExcluirCepBairro);
    }

    function onclickExcluirCepBairro (event) {
      if (!confirm("Tem certeza que deseja excluir este CEP?")) return false;

      var idLog = $j("select[id='idlog'] option:selected").val();

      var options = {
        url: deleteResourceUrlBuilder.buildUrl('/module/Api/endereco', 'delete_endereco'),
        dataType: 'json',
        data: {
          cep: event.data.cep,
          id_bairro: event.data.bairro,
          id_log: idLog
        },
        success: function (dataResponse) {
          if (!dataResponse.any_error_msg)
            tab_add_1.removeRow(event.data.button);

          handleDeleteCepBairro(dataResponse);
        }
      };
      deleteResource(options)
    }

    var handleDeleteCepBairro = function (dataResponse) {
      handleMessages(dataResponse.msgs);
    }

  });

</script>
