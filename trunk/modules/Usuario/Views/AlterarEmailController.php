<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Core/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';

class AlterarEmailController extends Core_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo   = 'Alterar e-mail';
  protected $_processoAp = 0;

  protected $_formMap = array(
    'matricula' => array(
      'label'  => 'Matr&iacute;cula',
      'help'   => '',
    ),
    'email' => array(
      'label'  => 'E-mail',
      'help'   => 'E-mail utilizado para recuperar senha.',
    ),
  );


  public function Gerar()
  {
    if (filter_var($this->getEntity()->email, FILTER_VALIDATE_EMAIL) == false)
      $this->mensagem = "Por favor informe um e-mail v&aacute;lido, para ser usado caso voc&ecirc; esque&ccedil;a sua senha.";

    $this->campoRotulo('matricula', $this->_getLabel('matricula'), $this->getEntity()->matricula);
    $this->campoTexto('email', $this->_getLabel('email'), $this->getEntity()->email,
      50, 50, TRUE, FALSE, FALSE, $this->_getHelp('email'));

    $this->url_cancelar = '/intranet/index.php';

    if (! $hasValidEmail)
      $this->nome_url_cancelar = 'Deixar para depois';
  }


  protected function _initNovo()
  {
    return FALSE;
  }


  protected function _initEditar()
  {
    try {
      $this->setEntity($this->getDataMapper()->find($this->getSession()->id_pessoa));
    } catch(Exception $e) {
      $this->mensagem = $e;
      return FALSE;
    }
    return TRUE;
  }


  public function Novo()
  {
    return FALSE;
  }


  public function Editar()
  {
    if ($this->_save())
      header("Location: /intranet/index.php");
      
    return FALSE;
  }


  function Excluir()
  {
    return FALSE;
  }


  protected function _save()
  {
    $entity = $this->setEntity($this->getDataMapper()->find($this->getSession()->id_pessoa));

    if (isset($entity))
      $this->getEntity()->setOptions(array('email' => $_POST['email']));

    try {
      $this->getDataMapper()->save($this->getEntity());
      return TRUE;
    }
    catch (Exception $e) {
      $this->mensagem = "E-mail n&atilde;o alterado, por favor, tente novamente.";
      return FALSE;
    }
  }
}
?>

