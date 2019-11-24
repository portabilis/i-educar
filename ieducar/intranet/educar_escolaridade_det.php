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
 * @author      Adriano Erik Weiguert Nagasava <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  Escolaridade
 * @since       Arquivo disponível desde a versão 1.0.0
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/Geral.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' Servidores - Escolaridade');
    $this->processoAp = '632';
  }
}

class indice extends clsDetalhe
{
  /**
   * Referência a usuário da sessão
   * @var int
   */
  var $pessoa_logada = NULL;

  /**
   * Título no topo da página
   * @var string
   */
  var $titulo = '';

  var $idesco;
  var $descricao;

  function Gerar()
  {
    $this->titulo = 'Escolaridade - Detalhe';


    $this->idesco = $_GET['idesco'];

    $tmp_obj = new clsCadastroEscolaridade($this->idesco);
    $registro = $tmp_obj->detalhe();

    if (! $registro) {
        $this->simpleRedirect('educar_escolaridade_lst.php');
    }

    if ($registro['descricao']) {
      $this->addDetalhe(array('Descri&ccedil;&atilde;o', $registro['descricao']));
    }

    $obj_permissoes = new clsPermissoes();
    if ($obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 3)) {
      $this->url_novo   = 'educar_escolaridade_cad.php';
      $this->url_editar = 'educar_escolaridade_cad.php?idesco=' . $registro['idesco'];
    }

    $this->url_cancelar = 'educar_escolaridade_lst.php';
    $this->largura      = '100%';

    $this->breadcrumb('Detalhe da escolaridade', [
        url('intranet/educar_servidores_index.php') => 'Servidores',
    ]);
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
