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


/**
 * Portabilis_View_Helper_DynamicInput_Turma class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_Turma extends Portabilis_View_Helper_DynamicInput_CoreSelect {

  protected function inputName() {
    return 'ref_cod_turma';
  }

  protected function inputOptions($options) {
    $resources     = $options['resources'];
    $instituicaoId = $this->getInstituicaoId($options['instituicaoId']);
    $escolaId      = $this->getEscolaId($options['escolaId']);
    $serieId       = $this->getSerieId($options['serieId']);
    $ano           = $this->viewInstance->ano;

    $userId        = $this->getCurrentUserId();
    $isProfessor   = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

    if ($escolaId and $serieId and empty($resources) and $isProfessor) {
      $turmas    = Portabilis_Business_Professor::turmasAlocado($escolaId, $serieId, $userId);
      $resources = Portabilis_Array_Utils::setAsIdValue($turmas, 'id', 'nome');
    }
    elseif ($escolaId && $serieId && empty($resources))
      $resources = App_Model_IedFinder::getTurmas($escolaId, $serieId);


    // caso no letivo esteja definido para filtrar turmas por ano,
    // somente exibe as turmas do ano letivo.

    if ($escolaId && $ano && $this->turmasPorAno($escolaId, $ano)) {
      foreach ($resources as $id => $nome) {
        $turma            = new clsPmieducarTurma();
        $turma->cod_turma = $id;
        $turma            = $turma->detalhe();

        if ($turma['ano'] != $ano)
          unset($resources[$id]);
      }
    }

    return $this->insertOption(null, "Selecione uma turma", $resources);
  }

  protected function turmasPorAno($escolaId, $ano) {
    $anoLetivo                 = new clsPmieducarEscolaAnoLetivo();
    $anoLetivo->ref_cod_escola = $escolaId;
    $anoLetivo->ano            = $ano;
    $anoLetivo                 = $anoLetivo->detalhe();

    return ($anoLetivo['turmas_por_ano'] == 1);
  }

  public function turma($options = array()) {
    parent::select($options);
  }
}