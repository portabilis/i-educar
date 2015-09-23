<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

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
 * @package   Portabilis
 * @subpackage  lib
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ErrorCoreController.php';

class UnexpectedController extends Portabilis_Controller_ErrorCoreController
{
  protected $_titulo = 'Erro inesperado';

  protected function setHeader() {
    header("HTTP/1.1 500 Internal Server Error");
  }

  public function Gerar() {
    $linkToSupport = $GLOBALS['coreExt']['Config']->modules->error->link_to_support;

    if ($GLOBALS['coreExt']['Config']->modules->error->show_details) {
      $detail  = "<br /><h3>Erro app</h3>{$this->getSession()->last_error_message}";
      $detail .= "<br /><h3>Erro php</h3>{$this->getSession()->last_php_error_message}";
      $detail .= "<br /><h3>Arquivo</h3>(linha: {$this->getSession()->last_php_error_line}) ";
      $detail .= "{$this->getSession()->last_php_error_file}";

      unset($this->getSession()->last_error_message);
      unset($this->getSession()->last_php_error_message);
      unset($this->getSession()->last_php_error_line);
      unset($this->getSession()->last_php_error_file);

      if (! $detail)
        $detail = 'Sem detalhes do erro.';

      $detail = "<h2>Detalhes:</h2>
                  <p>$detail</p>";
    }
    else
      $detail = '<p>Visualiza&ccedil;&atilde;o de detalhes do erro desativada.</p>';

    echo "
      <div id='error' class='small'>
        <div class='content'>
         <h1>Erro inesperado</h1>

         <p class='explanation'>
          Desculpe-nos, algum erro inesperado ocorreu,
          <strong> tente seguir as etapas abaixo:</strong>

          <ol>
            <li><a href='/intranet/index.php'>Tente novamente</a></li>
            <li><a href='/intranet/logof.php'>Fa&ccedil;a logoff do sistema</a> e tente novamente</li>
            <li>Caso o erro persista, por favor, <a target='_blank' href='$linkToSupport'>solicite suporte</a>.</li>
          </ol>
        </p>

        <div class='detail'>
          $detail
        </div>

        </div>
      </div>";
  }
}
