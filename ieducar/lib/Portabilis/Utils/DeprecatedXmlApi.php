<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/Utils/User.php';

/**
 * Portabilis_Utils_DeprecatedXmlApi class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Utils_DeprecatedXmlApi {

  public static function returnEmptyQueryUnlessUserIsLoggedIn($xmlns = 'sugestoes', $rootNodeName = 'query') {
    if (Portabilis_Utils_User::loggedIn() != true)
      Portabilis_Utils_DeprecatedXmlApi::returnEmptyQuery($xmlns, $rootNodeName, 'Login required');
  }

  public static function returnEmptyQueryForDisabledApi($xmlns = 'sugestoes', $rootNodeName = 'query'){
    Portabilis_Utils_DeprecatedXmlApi::returnEmptyQuery($xmlns, $rootNodeName, 'Disabled API');
  }

  public static function returnEmptyQuery($xmlns = 'sugestoes', $rootNodeName = 'query', $comment = ''){
    $emptyQuery = "<?xml version='1.0' encoding='ISO-8859-15'?>" .
                  "<!-- $comment -->" .
                  "<$rootNodeName xmlns='$xmlns'></$rootNodeName>";

    die($emptyQuery);
  }
}
