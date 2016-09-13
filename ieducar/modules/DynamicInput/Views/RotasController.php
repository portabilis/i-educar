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
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

/**
 * CursoController class.
 *
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão ?
 * @version     @@package_version@@
 */
class RotasController extends ApiCoreController
{

   protected function canGetRotas() {
    return $this->validatesPresenceOf('ano_rota');
  }

  protected function getRotas() {
    if ($this->canGetRotas()) {
      $anoRota = $this->getRequest()->ano_rota;

      $sql = "SELECT descricao,
                     cod_rota_transporte_escolar
              FROM modules.rota_transporte_escolar
              WHERE ano = $1";

      $rotas = $this->fetchPreparedQuery($sql, array($anoRota));
      $options = array();

      foreach ($rotas as $rota)
        $options['__' . $rota['cod_rota_transporte_escolar']] = $this->toUtf8($rota['descricao']);

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'rotas'))
        $this->appendResponse($this->getRotas());
    else
      $this->notImplementedOperationError();
  }
}