<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
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
 * @package   Biblioteca
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
#require_once 'Core/Controller/Page/EditController.php';
#require_once 'Avaliacao/Model/NotaComponenteDataMapper.php';
#require_once 'Avaliacao/Service/Boletim.php';
#require_once 'App/Model/MatriculaSituacao.php';
#require_once 'RegraAvaliacao/Model/TipoPresenca.php';
#require_once 'RegraAvaliacao/Model/TipoParecerDescritivo.php';
#require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
#require_once 'include/portabilis/dal.php';
#require_once 'include/pmieducar/clsPmieducarHistoricoEscolar.inc.php';
#require_once 'include/pmieducar/clsPmieducarHistoricoDisciplinas.inc.php';

class ReservaApiController extends ApiCoreController
{
  protected $_dataMapper  = '';#Avaliacao_Model_NotaComponenteDataMapper';
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_BIBLIOTECA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  #TODO setar código processoAP, copiar da funcionalidade de reserva existente?
  protected $_processoAp  = 0;


  protected function getExpectedAtts() {
    return array('exemplares');
  }


  protected function getExpectedOpers() {
    return array('get');
  }


  protected function getExemplares() {
    return "#TODO implementar getExemplares";
  }


  public function Gerar(){
    if ($this->getRequest()->oper == 'get')
      $this->appendResponse('exemplares', $this->getExemplares());
    else
      $this->notImplementedError();
  }
}
