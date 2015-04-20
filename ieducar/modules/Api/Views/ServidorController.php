<?php

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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

class ServidorController extends ApiCoreController
{

  protected function searchOptions() {
    $escolaId = $this->getRequest()->escola_id ? $this->getRequest()->escola_id : 0;
    return array('sqlParams'    => array($escolaId));

  }

  protected function formatResourceValue($resource) {
    $nome    = $this->toUtf8($resource['nome'], array('transform' => true));

    return $nome;
  }

  protected function canGetServidoresDisciplinasTurmas() {
    return  $this->validatesPresenceOf('ano') &&
            $this->validatesPresenceOf('instituicao_id');
  }

  protected function sqlsForNumericSearch() {

    $sqls[] = "SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                INNER JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                INNER JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = fun.ref_cod_pessoa_fj)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.idpes LIKE '%'||$1||'%'
                AND (CASE WHEN $2 = NULL OR $2 = 0 THEN
                      1 = 1
                    ELSE
                      sa.ref_cod_escola = $2
                    END)
                LIMIT 15";

    return $sqls;
  }

  protected function sqlsForStringSearch() {

    $sqls[] = "SELECT p.idpes as id, p.nome
                FROM cadastro.pessoa p
                INNER JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                INNER JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = fun.ref_cod_pessoa_fj)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.nome ILIKE '%'||$1||'%'
                AND (CASE WHEN $2 = NULL OR $2 = 0 THEN
                      1 = 1
                    ELSE
                      sa.ref_cod_escola = $2
                    END)
                LIMIT 15";

    return $sqls;
  }

  protected function getServidoresDisciplinasTurmas() {
    if($this->canGetServidoresDisciplinasTurmas()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $ano = $this->getRequest()->ano;

      $sql = "SELECT s.cod_servidor as id, p.nome as name, pt.turma_id, ptd.componente_curricular_id as disciplina_id

              FROM pmieducar.servidor s
              INNER JOIN cadastro.pessoa p ON s.cod_servidor = p.idpes
              INNER JOIN modules.professor_turma pt ON s.cod_servidor = pt.servidor_id AND s.ref_cod_instituicao = pt.instituicao_id
              INNER JOIN modules.professor_turma_disciplina ptd ON pt.id = ptd.professor_turma_id

              WHERE s.ref_cod_instituicao = $1
              AND pt.ano = $2
              GROUP BY s.cod_servidor, p.nome, pt.turma_id, ptd.componente_curricular_id ";

      $_servidores = $this->fetchPreparedQuery($sql, array($instituicaoId, $ano));

      $attrs = array('id', 'name', 'turma_id', 'disciplina_id');
      $_servidores = Portabilis_Array_Utils::filterSet($_servidores, $attrs);
      $servidores = array();
      $__servidores = array();

      foreach ($_servidores as $servidor) {
        $__servidores[$servidor['id']]['id'] = $servidor['id'];
        $__servidores[$servidor['id']]['name'] = Portabilis_String_Utils::toUtf8($servidor['name']);
        $__servidores[$servidor['id']]['disciplinas_turmas'][] = array(
          'turma_id' => $servidor['turma_id'],
          'disciplina_id' => $servidor['disciplina_id']
        );
      }

      foreach ($__servidores as $servidor) {
        $servidores[] = $servidor;
      }

      $attrs = array('id', 'name', 'disciplinas_turmas');
      $_servidores = Portabilis_Array_Utils::filterSet($_servidores, $attrs);

      return array('servidores' => $servidores);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'servidor-search'))
      $this->appendResponse($this->search());
    elseif ($this->isRequestFor('get', 'servidores-disciplinas-turmas'))
      $this->appendResponse($this->getServidoresDisciplinasTurmas());
    else
      $this->notImplementedOperationError();
  }

}
