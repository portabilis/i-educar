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
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

header('Content-type: text/xml; charset=UTF-8');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

$componentes = array();

// Seleciona os componentes de um curso ou série
if (is_numeric($_GET['cur']) || is_numeric($_GET['ser'])) {
  require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
  $mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();

  if (is_numeric($_GET['cur'])) {
    $componentes = $mapper->findComponentePorCurso($_GET['cur']);
  }
  elseif(is_numeric($_GET['ser'])) {
    $componentes = $mapper->findComponentePorSerie($_GET['ser']);
  }
}

// Seleciona os componentes de uma escola-série
if (is_numeric($_GET['esc']) && is_numeric($_GET['ser'])) {
  require_once 'App/Model/IedFinder.php';

  $componentes = App_Model_IedFinder::getEscolaSerieDisciplina($_GET['ser'],
    $_GET['esc']);
}

foreach ($componentes as $componente) {
  print sprintf(' <disciplina cod_disciplina="%d" carga_horaria="%d" docente_vinculado="%d">%s</disciplina>%s',
    $componente->id, $componente->cargaHoraria, $componente->docenteVinculado, $componente, PHP_EOL);
}

echo "</query>";