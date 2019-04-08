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
require_once 'include/public/clsPublicSetorBai.inc.php';

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
    $this->SetTitulo($this->_instituicao . ' Setor');
    $this->processoAp = 760;
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

  var $idsetorbai;
  var $nome;

  function Gerar()
  {
    $this->__titulo = 'Setor - Listagem';

    // Passa todos os valores obtidos no GET para atributos do objeto
    foreach ($_GET as $var => $val) {
      $this->$var = ($val === '') ? NULL : $val;
    }

    $this->addBanner('imagens/nvp_top_intranet.jpg',
      'imagens/nvp_vert_intranet.jpg', 'Intranet');

    $this->addCabecalhos(array(
      'Código',
      'Nome'
    ));
    
    // Outros filtros
    $this->campoNumero('idsetorbai', 'Código', $this->idsetorbai, 5, 5, FALSE);
    $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, FALSE);

    // Paginador
    $this->__limite = 20;
    $this->__offset = ($_GET['pagina_' . $this->nome]) ?
      ($_GET['pagina_' . $this->nome] * $this->__limite - $this->__limite) : 0;
    
    $obj_setor = new clsPublicSetorBai();
    $obj_setor->setOrderby('nome ASC');
    $obj_setor->setLimite($this->__limite, $this->__offset);

    $lista = $obj_setor->lista(
      $this->idsetorbai,
      $this->nome
    );

    $total = $obj_setor->_total;

    $url = CoreExt_View_Helper_UrlHelper::getInstance();
    $options = array('query' => array('idsetorbai' => NULL));

    // Monta a lista.
    if (is_array($lista) && count($lista)) {
      foreach ($lista as $registro) {
        $options['query']['idsetorbai'] = $registro['idsetorbai'];
        $this->addLinhas(array(
          $url->l($registro['idsetorbai'], 'public_setor_det.php', $options),
          $url->l($registro['nome'], 'public_setor_det.php', $options),         
        ));
      }
    }

    $this->addPaginador2('public_setor_lst.php', $total, $_GET, $this->nome, $this->__limite);

    $obj_permissao = new clsPermissoes();

    if($obj_permissao->permissao_cadastra(760, $this->pessoa_logada,7,null,true))
    {
      $this->acao      = 'go("public_setor_cad.php")';
      $this->nome_acao = 'Novo';
    }

    $this->largura = '100%';

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_enderecamento_index.php"    => "Endereçamento",
         ""                                  => "Listagem de setores"
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
