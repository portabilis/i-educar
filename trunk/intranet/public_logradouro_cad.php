<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  public
 * @subpackage  Enderecamento
 * @subpackage  Logradouro
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/urbano/clsUrbanoTipoLogradouro.inc.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo( "{$this->_instituicao} Logradouro" );
    $this->processoAp = "757";
  }
}

class indice extends clsCadastro
{
  /**
   * Referência a usuário da sessão.
   * @var int
   */
  var $pessoa_logada;

  var $idlog;
  var $idtlog;
  var $nome;
  var $idmun;
  var $geom;
  var $ident_oficial;
  var $idpes_rev;
  var $data_rev;
  var $origem_gravacao;
  var $idpes_cad;
  var $data_cad;
  var $operacao;
  var $idsis_rev;
  var $idsis_cad;

  var $idpais;
  var $sigla_uf;

  function Inicializar()
  {
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $this->idlog = $_GET['idlog'];


    if (is_numeric($this->idlog)) {
      $obj_logradouro = new clsPublicLogradouro();
      $lst_logradouro = $obj_logradouro->lista(NULL, NULL, NULL, NULL, NULL,
        NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL,
        $this->idlog);

      if ($lst_logradouro) {
        $registro = $lst_logradouro[0];
      }

      if ($registro) {
        foreach($registro as $campo => $val) {
          $this->$campo = $val;
        }

        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = ($retorno == 'Editar') ?
      'public_logradouro_det.php?idlog=' . $registro['idlog'] :
      'public_logradouro_lst.php';

    $this->nome_url_cancelar = 'Cancelar';

    return $retorno;
  }

  function Gerar()
  {
    // primary keys
    $this->campoOculto('idlog', $this->idlog);

    // foreign keys
    $opcoes = array('' => 'Selecione');
    if (class_exists('clsPais')) {
      $objTemp = new clsPais();
      $lista = $objTemp->lista(FALSE, FALSE, FALSE, FALSE, FALSE, 'nome ASC');

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['idpais']] = $registro['nome'];
        }
      }
    }
    else {
      echo '<!--\nErro\nClasse clsPais nao encontrada\n-->';
      $opcoes = array('' => 'Erro na geracao');
    }
    $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais);

    $opcoes = array('' => 'Selecione');
    if (class_exists('clsUf')) {
      if ($this->idpais) {
        $objTemp = new clsUf();
        $lista = $objTemp->lista(FALSE, FALSE, $this->idpais, FALSE, FALSE, 'nome ASC');

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $opcoes[$registro['sigla_uf']] = $registro['nome'];
          }
        }
      }
    }
    else {
      echo '<!--\nErro\nClasse clsUf nao encontrada\n-->';
      $opcoes = array('' => 'Erro na geracao');
    }
    $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf);

    $opcoes = array('' => 'Selecione');
    if (class_exists('clsMunicipio')) {
      if ($this->sigla_uf) {
        $objTemp = new clsMunicipio();
        $lista = $objTemp->lista(FALSE, $this->sigla_uf, FALSE, FALSE, FALSE,
          FALSE, FALSE, FALSE, FALSE, FALSE, FALSE, 'nome ASC');

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $opcoes[$registro['idmun']] = $registro['nome'];
          }
        }
      }
    }
    else {
      echo '<!--\nErro\nClasse clsMunicipio nao encontrada\n-->';
      $opcoes = array('' => 'Erro na geracao');
    }
    $this->campoLista('idmun', 'Munic&iacute;pio', $opcoes, $this->idmun);

    $opcoes = array('' => 'Selecione');
    if (class_exists('clsUrbanoTipoLogradouro')) {
      $objTemp = new clsUrbanoTipoLogradouro();
      $objTemp->setOrderby('descricao ASC');
      $lista = $objTemp->lista();

      if (is_array($lista) && count($lista)) {
        foreach ($lista as $registro) {
          $opcoes[$registro['idtlog']] = $registro['descricao'];
        }
      }
    }
    else {
      echo '<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->';
      $opcoes = array('' => 'Erro na geracao');
    }
    $this->campoLista('idtlog', 'Tipo de Logradouro', $opcoes, $this->idtlog);

    $this->campoTexto('nome', 'Nome', $this->nome, 30, 150, true);
  }

  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsPublicLogradouro(NULL, $this->idtlog, $this->nome, $this->idmun,
      NULL, 'S', NULL, NULL, 'U', $this->pessoa_logada, NULL, 'I', NULL, 9);

    $cadastrou = $obj->cadastra();
    if ($cadastrou) {
      $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
      header('Location: public_logradouro_lst.php');
      die();
    }

    $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';
    echo "<!--\nErro ao cadastrar clsPublicLogradouro\nvalores obrigatorios\nis_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idmun ) && is_string( $this->origem_gravacao ) && is_string( $this->operacao ) && is_numeric( $this->idsis_cad )\n-->";

    return FALSE;
  }

  function Editar()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsPublicLogradouro($this->idlog, $this->idtlog, $this->nome,
      $this->idmun, NULL, 'S', $this->pessoa_logada, NULL, 'U', NULL, NULL, 'I', NULL, 9);

    $editou = $obj->edita();
    if ($editou) {
      $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
      header('Location: public_logradouro_lst.php');
      die();
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
    echo "<!--\nErro ao editar clsPublicLogradouro\nvalores obrigatorios\nif( is_numeric( $this->idlog ) )\n-->";

    return FALSE;
  }

  function Excluir()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj = new clsPublicLogradouro($this->idlog, $this->idtlog, $this->nome,
      $this->idmun, NULL, 'S', $this->pessoa_logada);

    $excluiu = $obj->excluir();
    if ($excluiu) {
      $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
      header('Location: public_logradouro_lst.php');
      die();
    }

    $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
    echo '<!--\nErro ao excluir clsPublicLogradouro\nvalores obrigatorios\nif( is_numeric( $this->idlog ) )\n-->';

    return FALSE;
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>

<script type="text/javascript">
document.getElementById('idpais').onchange = function() {
  var campoPais = document.getElementById('idpais').value;

  var campoUf= document.getElementById('sigla_uf');
  campoUf.length = 1;
  campoUf.disabled = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax( getUf );
  xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
}

function getUf(xml_uf) {
  var campoUf = document.getElementById('sigla_uf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled = false;

    for (var i = 0; i < DOM_array.length; i++ ) {
      campoUf.options[campoUf.options.length] = new Option(DOM_array[i].firstChild.data,
        DOM_array[i].getAttribute('sigla_uf'), false, false);
    }
  }
  else {
    campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
}

document.getElementById('sigla_uf').onchange = function() {
  var campoUf = document.getElementById('sigla_uf').value;

  var campoMunicipio= document.getElementById('idmun');
  campoMunicipio.length = 1;
  campoMunicipio.disabled = true;
  campoMunicipio.options[0].text = 'Carregando município...';

  var xml_municipio = new ajax( getMunicipio );
  xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
}

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
  }
  else {
    campoMunicipio.options[0].text = 'O estado não possui nenhum município';
  }
}
</script>