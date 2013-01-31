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

class UnauthorizedController extends Portabilis_Controller_ErrorCoreController
{
  protected $_titulo = 'Acesso n&atilde;o autorizado';

  protected function setHeader() {
    header("HTTP/1.1 403 Forbidden");
  }

  public function Gerar() {
    $linkToSupport = $GLOBALS['coreExt']['Config']->modules->error->link_to_support;

    echo "
      <div id='error' class='small'>
        <div class='content'>
         <h1>Acesso n&atilde;o autorizado</h1>

         <p class='explanation'>
          Seu usu&aacute;rio n&atilde;o possui autoriza&ccedil;&atilde;o para realizar esta a&ccedil;&atilde;o,
          <strong> tente seguir as etapas abaixo:</strong>

          <ol>
            <li><a href='/intranet/index.php'>Volte para o sistema</a></li>
            <li>Solicite ao respons&aacute;vel pelo sistema, para adicionar ao seu usu&aacute;rio a permiss&atilde;o necess&aacute;ria e tente novamente</li>
            <li>Caso o erro persista, por favor, <a target='_blank' href='$linkToSupport'>solicite suporte</a>.</li>
          </ol>
        </p>

        </div>
      </div>";
  }
}
