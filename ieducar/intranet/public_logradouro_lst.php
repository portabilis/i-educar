<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/urbano/clsUrbanoTipoLogradouro.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Logradouro");
        $this->processoAp = 757;
    }
}

class indice extends clsListagem
{
    public $__limite;
    public $__offset;
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

    public function Gerar()
    {
        $this->__titulo = 'Logradouro - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Código',
            'Tipo',
            'Nome',
            'Município',
            'Estado',
            'País'
        ]);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsPais();
        $lista = $objTemp->lista(false, false, false, false, false, 'nome ASC');

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
            }
        }

        $this->campoLista('idpais', 'País', $opcoes, $this->idpais, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        if ($this->idpais) {
            $objTemp = new clsUf();
            $lista = $objTemp->lista(false, false, $this->idpais, false, false, 'nome ASC');
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
                }
            }
        }

        $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        if ($this->sigla_uf) {
            $objTemp = new clsMunicipio();
            $lista = $objTemp->lista(false, $this->sigla_uf, false, false, false, false, false, false, false, false, false, 'nome ASC');
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['idmun']}"] = "{$registro['nome']}";
                }
            }
        }

        $this->campoLista('idmun', 'Município', $opcoes, $this->idmun, '', false, '', '', false, false);

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsUrbanoTipoLogradouro();
        $objTemp->setOrderby('descricao ASC');
        $lista = $objTemp->lista();
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
            }
        }

        $this->campoLista('idtlog', 'Tipo de Logradouro', $opcoes, $this->idtlog, '', false, '', '', false, false);
        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, false);

        $this->__limite = 20;
        $this->__offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->__limite - $this->__limite : 0;

        $obj_logradouro = new clsPublicLogradouro();
        $obj_logradouro->setOrderby('nome ASC');
        $obj_logradouro->setLimite($this->__limite, $this->__offset);

        $lista = $obj_logradouro->lista(
            $this->idtlog,
            $this->nome,
            $this->idmun,
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
            $this->idpais,
            $this->sigla_uf,
            $this->idlog
        );

        $total = $obj_logradouro->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $obj_idtlog = new clsUrbanoTipoLogradouro($registro['idtlog']);
                $det_idtlog = $obj_idtlog->detalhe();
                $registro['idtlog'] = $det_idtlog['descricao'];

                $this->addLinhas([
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['idlog']}</a>",
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['idtlog']}</a>",
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['nome']}</a>",
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['nm_municipio']}</a>",
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['nm_estado']}</a>",
                    "<a href=\"public_logradouro_det.php?idlog={$registro['idlog']}\">{$registro['nm_pais']}</a>"
                ]);
            }
        }

        $this->addPaginador2('public_logradouro_lst.php', $total, $_GET, $this->nome, $this->__limite);

        $this->largura = '100%';

        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(757, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("public_logradouro_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->breadcrumb('Listagem de logradouros', [
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

    var campoUf = document.getElementById('sigla_uf');
    campoUf.length = 1;
    campoUf.disabled = true;
    campoUf.options[0].text = 'Carregando estado...';

    var xml_uf = new ajax(getUf);
    xml_uf.envia("public_uf_xml.php?pais=" + campoPais);
  }

  function getUf (xml_uf) {
    var campoUf = document.getElementById('sigla_uf');
    var DOM_array = xml_uf.getElementsByTagName("estado");

    if (DOM_array.length) {
      campoUf.length = 1;
      campoUf.options[0].text = 'Selecione um estado';
      campoUf.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("sigla_uf"), false, false);
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
    xml_municipio.envia("public_municipio_xml.php?uf=" + campoUf);
  }

  function getMunicipio (xml_municipio) {
    var campoMunicipio = document.getElementById('idmun');
    var DOM_array = xml_municipio.getElementsByTagName("municipio");

    if (DOM_array.length) {
      campoMunicipio.length = 1;
      campoMunicipio.options[0].text = 'Selecione um município';
      campoMunicipio.disabled = false;

      for (var i = 0; i < DOM_array.length; i++) {
        campoMunicipio.options[campoMunicipio.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("idmun"), false, false);
      }
    } else {
      campoMunicipio.options[0].text = 'O estado não possui nenhum município';
    }
  }
</script>
