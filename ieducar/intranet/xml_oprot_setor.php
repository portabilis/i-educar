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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

header('Content-type: text/xml');

require_once 'include/pmidrh/geral.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo '<?xml version="1.0" encoding="ISO-8859-15"?>' . "\n";
echo '<query xmlns="sugestoes">' . "\n";

if (isset($_GET['setor_pai'])) {
  $obj = new clsSetor();
  $lista = $obj->lista($_GET['setor_pai']);

  if ($lista) {
    foreach ($lista as $linha)  {
      echo '  <item>' . $linha['sgl_setor'] . '</item>' . "\n";
      echo '  <item>' . $linha['cod_setor'] . '</item>' . "\n";
    }
  }
}

echo '</query>';