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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package   Ied_Public
 * @since     ?
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicDistrito.inc.php';

require_once 'CoreExt/View/Helper/UrlHelper.php';

/**
 * clsIndexBase class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Distrito');
    $this->processoAp = 759;
    $this->addEstilo('localizacaoSistema');
  }
}

/**
 * indice class.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Public
 * @since     ?
 * @version   @@package_version@@
 */
class indice extends clsListagem
{
  var $pessoa_logada;
  var $__titulo;
  var $__limite;
  var $__offset;

  var $idmun;
  var $geom;
  var $idbai;
  var $nome;
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

  function Gerar()
  {
    $this->__titulo = 'Distrito - Listagem';

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->addCabecalhos(array(
      'Nome',
      'Município',
      'Estado',
      'Pais'
    ));

    // Filtros de Foreign Keys
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
      echo "<!--\nErro\nClasse clsPais nao encontrada\n-->";
      $opcoes = array('' => 'Erro na geração');
    }

    $this->campoLista('idpais', 'Pais', $opcoes, $this->idpais, '', FALSE, '',
      '', FALSE, FALSE);

    $opcoes = array('' => 'Selecione');

    if (class_exists('clsUf')) {
      if ($this->idpais) {
        $objTemp = new clsUf();
        $lista = $objTemp->lista(FALSE, FALSE, $this->idpais, FALSE, FALSE,
          'nome ASC');

        if (is_array($lista) && count($lista)) {
          foreach ($lista as $registro) {
            $opcoes[$registro['sigla_uf']] = $registro['nome'];
          }
        }
      }
    }
    else {
      echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
      $opcoes = array('' => 'Erro na geração');
    }

    $this->campoLista('sigla_uf', 'Estado', $opcoes, $this->sigla_uf, '', FALSE,
      '', '', FALSE, FALSE);

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
      echo "<!--\nErro\nClasse clsMunicipio nao encontrada\n-->";
      $opcoes = array('' => 'Erro na geração');
    }

    $this->campoLista('idmun', 'Município', $opcoes, $this->idmun, '', FALSE,
      '', '', FALSE, FALSE);

    // Outros filtros
    $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, FALSE);

    // Paginador
    $this->__limite = 20;
    $this->__offset = ($_GET['pagina_' . $this->nome]) ?
      ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;
    
    $obj_distrito = new clsPublicDistrito();
    $obj_distrito->setOrderby('nome ASC');
    $obj_distrito->setLimite($this->__limite, $this->__offset);

    $lista = $obj_distrito->lista(
      $this->idmun,
      NULL,
      $this->nome,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      NULL,
      $this->idpais,
      $this->sigla_uf
    );

    $total = $obj_distrito->_total;

    // UrlHelper.
    $url = CoreExt_View_Helper_UrlHelper::getInstance();
    $options = array('query' => array('iddis' => NULL));

    // Monta a lista.
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {

        $options['query']['iddis'] = $registro['iddis'];

        $this->addLinhas(array(
          $url->l($registro['nome'], 'public_distrito_det.php', $options),
          $url->l($registro['nm_municipio'], 'public_distrito_det.php', $options),
          $url->l($registro['nm_estado'], 'public_distrito_det.php', $options),
          $url->l($registro['nm_pais'], 'public_distrito_det.php', $options)
        ));
      }
    }

    $this->addPaginador2('public_distrito_lst.php', $total, $_GET, $this->nome, $this->__limite);  

    $obj_permissao = new clsPermissoes();   

    if($obj_permissao->permissao_cadastra(759, $this->pessoa_logada,7,null,true))
    {
      $this->acao      = 'go("public_distrito_cad.php")';
      $this->nome_acao = 'Novo';
    }

    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_enderecamento_index.php"    => "Endereçamento",
         ""                                  => "Listagem de distritos"
    ));
    $this->enviaLocalizacao($localizacao->montar());    
  }
}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
<script type="text/javascript">
document.getElementById('idpais').onchange = function()
{
  var campoPais = document.getElementById('idpais').value;

  var campoUf= document.getElementById('sigla_uf');
  campoUf.length = 1;
  campoUf.disabled        = true;
  campoUf.options[0].text = 'Carregando estado...';

  var xml_uf = new ajax(getUf);
  xml_uf.envia('public_uf_xml.php?pais=' + campoPais);
}

function getUf(xml_uf)
{
  var campoUf   = document.getElementById('sigla_uf');
  var DOM_array = xml_uf.getElementsByTagName('estado');

  if (DOM_array.length) {
    campoUf.length          = 1;
    campoUf.options[0].text = 'Selecione um estado';
    campoUf.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoUf.options[campoUf.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('sigla_uf'),
        false, false
      );
    }
  }
  else {
    campoUf.options[0].text = 'O pais não possui nenhum estado';
  }
}

document.getElementById('sigla_uf').onchange = function()
{
  var campoUf = document.getElementById('sigla_uf').value;

  var campoMunicipio      = document.getElementById('idmun');
  campoMunicipio.length   = 1;
  campoMunicipio.disabled = true;

  campoMunicipio.options[0].text = 'Carregando município...';

  var xml_municipio = new ajax(getMunicipio);
  xml_municipio.envia('public_municipio_xml.php?uf=' + campoUf);
}

function getMunicipio(xml_municipio)
{
  var campoMunicipio = document.getElementById('idmun');
  var DOM_array      = xml_municipio.getElementsByTagName( "municipio" );

  if (DOM_array.length) {
    campoMunicipio.length          = 1;
    campoMunicipio.options[0].text = 'Selecione um município';
    campoMunicipio.disabled        = false;

    for (var i = 0; i < DOM_array.length; i++) {
      campoMunicipio.options[campoMunicipio.options.length] = new Option(
        DOM_array[i].firstChild.data, DOM_array[i].getAttribute('idmun'),
        false, false
      );
    }
  }
  else {
    campoMunicipio.options[0].text = 'O estado não possui nenhum município';
  }
}
</script>
