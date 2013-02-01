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

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'lib/Portabilis/Utils/Database.php';

/**
 * Portabilis_View_Helper_DynamicInput_AnoLetivo class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_AnoLetivo extends Portabilis_View_Helper_DynamicInput_CoreSelect {

  // subscreve para não acrescentar '_id' no final
  protected function inputName() {
    return 'ano';
  }

  protected function filtroSituacao() {
    $tiposSituacao  = array('nao_iniciado' => 0, 'em_andamento' => 1, 'finalizado' => 2);
    $situacaoIn     = array();

    foreach ($tiposSituacao as $nome => $flag) {
      if (in_array("$nome", $this->options['situacoes']))
        $situacaoIn[] = $flag;
    }

    return (empty($situacaoIn) ? '' : 'and andamento in ('. implode(',', $situacaoIn) . ')');
  }

  protected function inputOptions($options) {
    $resources = $options['resources'];
    $escolaId  = $this->getEscolaId($options['escolaId']);

    if ($escolaId && empty($resources)) {
      $sql       = "select ano from pmieducar.escola_ano_letivo as al where ref_cod_escola = $1
                    and ativo = 1 {$this->filtroSituacao()} order by ano desc";

      $resources = Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => $escolaId));
      $resources = Portabilis_Array_Utils::setAsIdValue($resources, 'ano', 'ano');
    }

    return $this->insertOption(null, "Selecione um ano letivo", $resources);
  }

  protected function defaultOptions() {
    return array('escolaId' => null, 'situacoes' => array('em_andamento', 'nao_iniciado', 'finalizado'));
  }

  public function anoLetivo($options = array()) {
    parent::select($options);

    foreach ($this->options['situacoes'] as $situacao)
      $this->viewInstance->appendOutput("<input type='hidden' name='situacoes_ano_letivo' value='$situacao' />");
  }
}