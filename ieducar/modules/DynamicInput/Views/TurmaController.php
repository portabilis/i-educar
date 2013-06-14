<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

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
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

/**
 * CursoController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class TurmaController extends ApiCoreController
{

  protected function canGetTurmas() {
    return $this->validatesId('instituicao') &&
           $this->validatesId('escola') &&
           $this->validatesId('serie');
  }

  protected function turmasPorAno($escolaId, $ano) {
    $anoLetivo                 = new clsPmieducarEscolaAnoLetivo();
    $anoLetivo->ref_cod_escola = $escolaId;
    $anoLetivo->ano            = $ano;
    $anoLetivo                 = $anoLetivo->detalhe();

    return ($anoLetivo['turmas_por_ano'] == 1);
  }

  protected function getTurmas() {
    if ($this->canGetTurmas()) {
      $userId        = $this->getSession()->id_pessoa;
      $instituicaoId = $this->getRequest()->instituicao_id;
      $escolaId      = $this->getRequest()->escola_id;
      $serieId       = $this->getRequest()->serie_id;
      $ano           = $this->getRequest()->ano;

      $isProfessor   = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

      if ($isProfessor)
        $turmas = Portabilis_Business_Professor::turmasAlocado($escolaId, $serieId, $userId);

      else {
        $sql    = "select cod_turma as id, nm_turma as nome from pmieducar.turma where ref_ref_cod_escola = $1
                   and (ref_ref_cod_serie = $2 or ref_ref_cod_serie_mult = $2) and ativo = 1 and
                   visivel != 'f' order by nm_turma asc";

        $turmas = $this->fetchPreparedQuery($sql, array($escolaId, $serieId));
      }

      // caso no ano letivo esteja definido para filtrar turmas por ano,
      // somente retorna as turmas do ano letivo.

      if ($ano && $this->turmasPorAno($escolaId, $ano)) {
        foreach ($turmas as $index => $t) {
          $turma            = new clsPmieducarTurma();
          $turma->cod_turma = $t['id'];
          $turma            = $turma->detalhe();

          if ($turma['ano'] != $ano)
            unset($turmas[$index]);
        }
      }

      $options = array();
      foreach ($turmas as $turma)
        $options['__' . $turma['id']] = $this->toUtf8($turma['nome']);

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'turmas'))
      $this->appendResponse($this->getTurmas());
    else
      $this->notImplementedOperationError();
  }
}