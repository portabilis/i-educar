<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/urbano/clsUrbanoTipoLogradouro.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Logradouro");
        $this->processoAp = 757;
    }
}

class indice extends clsCadastro
{
    public $idlog;
    public $idtlog;
    public $nome;
    public $idmun;
    public $geom;
    public $idpes_rev;
    public $data_rev;
    public $origem_gravacao;
    public $idpes_cad;
    public $data_cad;
    public $operacao;
    public $idpais;
    public $sigla_uf;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->idlog = $_GET['idlog'];

        if (is_numeric($this->idlog)) {
            $obj_logradouro = new clsPublicLogradouro();
            $lst_logradouro = $obj_logradouro->lista(
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
                null,
                null,
                $this->idlog
            );

            if ($lst_logradouro) {
                $registro = $lst_logradouro[0];
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar' ?
            'public_logradouro_det.php?idlog=' . $registro['idlog'] :
            'public_logradouro_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} logradouro", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idlog', $this->idlog);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idpais']] = $registro['nome'];
            }
        }

        $this->campoLista('idpais', 'País', $opcoes, $this->idpais);

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

        $objTemp = new clsUrbanoTipoLogradouro();
        $objTemp->setOrderby('descricao ASC');
        $lista = $objTemp->lista();

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idtlog']] = $registro['descricao'];
            }
        }

        $this->campoLista('idtlog', 'Tipo de Logradouro', $opcoes, $this->idtlog);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 150, true);
    }

    public function Novo()
    {
        $obj = new clsPublicLogradouro(
            null,
            $this->idtlog,
            $this->nome,
            $this->idmun,
            null,
            'S',
            null,
            null,
            'U',
            $this->pessoa_logada,
            null,
            'I',
            null,
            9
        );

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $enderecamento = new clsPublicLogradouro($cadastrou);
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Logradouro', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('public_logradouro_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $enderecamentoDetalhe = new clsPublicLogradouro(null, null, $this->idlog);
        $enderecamentoDetalhe->cadastrou = $this->idlog;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicLogradouro(
            $this->idlog,
            $this->idtlog,
            $this->nome,
            $this->idmun,
            null,
            'S',
            $this->pessoa_logada,
            null,
            'U',
            null,
            null,
            'I',
            null,
            9
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('public_logradouro_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPublicLogradouro(
            $this->idlog,
            $this->idtlog,
            $this->nome,
            $this->idmun,
            null,
            'S',
            $this->pessoa_logada
        );

        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_logradouro_lst.php');
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

<script type="text/javascript">
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
      campoUf.options[0].text = 'O país não possui nenhum estado';
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
</script>
