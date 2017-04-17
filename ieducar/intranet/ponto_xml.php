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
 * @license   @@license@@
 * @package   TransporteEscolar
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';

// Id do país na tabela public.pais
$id = isset($_GET['rota']) ? $_GET['rota'] : NULL;

header('Content-type: text/xml; charset=UTF-8');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {

  $obj = new clsModulesItinerarioTransporteEscolar();
  $obj->setOrderBy(' seq asc ');
  $pontos = $obj->listaPontos($id);

  $c=0;
  foreach ($pontos as $reg) {
    print sprintf('  <ponto cod_ponto="%s">%s</ponto>' . PHP_EOL,
      $reg['ref_cod_ponto_transporte_escolar'], $reg['descricao'].' - '.($reg['tipo']=='I'?'Ida':'Volta'));
  }
}

print '</query>';
