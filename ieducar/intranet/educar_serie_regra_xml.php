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
 * Retorna um XML com todas as regras de avaliação para uma determinada
 * instituição.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @todo      Refatorar para um design pattern como Service Layer em conjunto
 *   com um controller que permita respostas em JSON/XML.
 * @version   $Id$
 */

header('Content-type: text/xml; charset=UTF-8');

require_once 'include/clsBanco.inc.php';
require_once 'RegraAvaliacao/Model/RegraDataMapper.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (isset($_GET['ins']) && is_numeric($_GET['ins'])) {
  $mapper = new RegraAvaliacao_Model_RegraDataMapper();

  $regras = $mapper->findAll(
    array('id', 'nome'),
    array('instituicao' => $_GET['ins'])
  );

  foreach ($regras as $regra) {
    print sprintf('  <regra id="%d">%s</regra>%s', $regra->id, $regra->nome, PHP_EOL);
  }
}
print '</query>';