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
class CursoController extends ApiCoreController
{

  protected function canGetCursos() {
    return $this->validatesId('instituicao') &&
           $this->validatesId('escola');
  }

  protected function getCursos() {
    if ($this->canGetCursos()) {
      $userId        = $this->getSession()->id_pessoa;
      $instituicaoId = $this->getRequest()->instituicao_id;
      $escolaId      = $this->getRequest()->escola_id;

      $isProfessor   = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

      if ($isProfessor)
        $cursos = Portabilis_Business_Professor::cursosAlocado($instituicaoId, $escolaId, $userId);

      else {
        $sql    = "select c.cod_curso as id, c.nm_curso as nome FROM pmieducar.curso c,
                   pmieducar.escola_curso ec WHERE ec.ref_cod_escola = $1 AND ec.ref_cod_curso =
                   c.cod_curso AND ec.ativo = 1 AND c.ativo = 1 ORDER BY c.nm_curso ASC";

        $cursos = $this->fetchPreparedQuery($sql, $escolaId);
      }

      $options = array();
      foreach ($cursos as $curso)
        $options['__' . $curso['id']] = $this->toUtf8($curso['nome']);

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'cursos'))
      $this->appendResponse($this->getCursos());
    else
      $this->notImplementedOperationError();
  }
}