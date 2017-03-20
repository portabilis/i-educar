<?php

/**
 * i-Educar - Sistema de gest�o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja�
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo
 * sob os termos da Licen�a P�blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio)
 * qualquer vers�o posterior.
 *
 * Este programa � distribu��do na expectativa de que seja �til, por�m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl��cita de COMERCIABILIDADE OU
 * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral
 * do GNU para mais detalhes.
 *
 * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral do GNU junto
 * com este programa; se n�o, escreva para a Free Software Foundation, Inc., no
 * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Eriksen Costa Paix�o <eriksen.paixao_bs@cobra.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     TabelaArredondamento
 * @subpackage  Modules
 * @since       Arquivo dispon�vel desde a vers�o 1.2.0
 * @version     $Id$
 */

require_once dirname(__FILE__) . '/../../../includes/bootstrap.php';
require_once 'include/clsBanco.inc.php';
require_once 'TabelaArredondamento/Model/TabelaDataMapper.php';

$tabelas = array();

if (isset($_GET['tipoNota'])) {
  $tabela = new TabelaArredondamento_Model_TabelaDataMapper();
  $tabelas = $tabela->findAll(array(), array('tipoNota' => (int) $_GET['tipoNota']));
}

header('Content-type: text/xml');

echo "<?xml version=\"1.0\" encoding=\"5\"?>\n<query xmlns=\"sugestoes\">\n";

foreach ($tabelas as $tabela) {
  echo sprintf('<tabela id="%d">%s</tabela>', $tabela->id, $tabela->nome);
}

echo '</query>';
