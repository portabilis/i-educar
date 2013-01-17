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
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';

class MatriculaController extends ApiCoreController
{
  protected $_dataMapper  = null;

  #TODO definir este valor com mesmo código cadastro de tipo de exemplar?
  protected $_processoAp  = 0;
  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_saveOption  = FALSE;
  protected $_deleteOption  = FALSE;
  protected $_titulo   = '';

  // search options

  protected function searchOptions() {
    return array('sqlParams'    => array($this->getRequest()->escola_id, $this->getRequest()->ano),
                 'selectFields' => array('aluno_id'));
  }

  protected function sqlsForNumericSearch() {

    return "select distinct ON (aluno.cod_aluno) aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, pessoa.nome as name from pmieducar.matricula,
            pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
            pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
            matricula.ativo = 1 and matricula.ref_ref_cod_escola = $2 and
            (matricula.cod_matricula like $1 or matricula.ref_cod_aluno like $1) and
            matricula.aprovado in (1, 2, 3, 7, 8, 9) and ano = $3 limit 15";
  }


  protected function sqlsForStringSearch() {
    return "select distinct ON (aluno.cod_aluno) aluno.cod_aluno as aluno_id,
            matricula.cod_matricula as id, pessoa.nome as name from pmieducar.matricula,
            pmieducar.aluno, cadastro.pessoa where aluno.cod_aluno = matricula.ref_cod_aluno and
            pessoa.idpes = aluno.ref_idpes and aluno.ativo = matricula.ativo and
            matricula.ativo = 1 and matricula.ref_ref_cod_escola = $2 and
            lower(to_ascii(pessoa.nome)) like lower(to_ascii($1))||'%' and matricula.aprovado in (1, 2, 3, 7, 8, 9)
            and ano = $3 limit 15";
  }


  protected function formatResourceValue($resource) {
    $alunoId = $resource['aluno_id'];
    $nome    = $this->toUtf8($resource['name'], array('transform' => true));

    return $resource['id'] . " - ($alunoId) $nome";
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'matricula-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
