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

/**
 * Class ServidorController
 * @deprecated Essa versão da API pública será descontinuada
 */
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
                 LEFT JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                 LEFT JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = p.idpes)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.idpes::varchar LIKE '%'||$1||'%'
                AND (CASE WHEN $2 = 0 THEN
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
                 LEFT JOIN cadastro.fisica f ON (p.idpes = f.idpes)
                 LEFT JOIN portal.funcionario fun ON (fun.ref_cod_pessoa_fj = f.idpes)
                INNER JOIN pmieducar.servidor s ON (s.cod_servidor = p.idpes)
                LEFT JOIN pmieducar.servidor_alocacao sa ON (s.cod_servidor = sa.ref_cod_servidor)

                WHERE p.nome ILIKE '%'||$1||'%'
                AND (CASE WHEN $2 = 0 THEN
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

      $sql = "SELECT pt.id as id,
                     s.cod_servidor as servidor_id,
                     p.nome as name,
                     pt.turma_id,
                     pt.permite_lancar_faltas_componente as permite_lancar_faltas_componente,
                     ptd.componente_curricular_id as disciplina_id,
                     CASE
                       WHEN ccae.tipo_nota IN (1,2) THEN
                         ccae.tipo_nota
                       ELSE
                         NULL
                     END AS tipo_nota,
                     pt.updated_at as updated_at,
                     s.ativo as ativo
              FROM pmieducar.servidor s
              INNER JOIN cadastro.pessoa p ON s.cod_servidor = p.idpes
              INNER JOIN modules.professor_turma pt ON s.cod_servidor = pt.servidor_id AND s.ref_cod_instituicao = pt.instituicao_id
              INNER JOIN modules.professor_turma_disciplina ptd ON pt.id = ptd.professor_turma_id
              INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
              INNER JOIN modules.componente_curricular_ano_escolar ccae ON (ccae.ano_escolar_id = t.ref_ref_cod_serie
                                                                            AND ccae.componente_curricular_id = ptd.componente_curricular_id)
              WHERE s.ref_cod_instituicao = $1
              AND pt.ano = $2
              GROUP BY pt.id, s.cod_servidor, p.nome, pt.turma_id, pt.permite_lancar_faltas_componente, ptd.componente_curricular_id, ccae.tipo_nota, s.ativo";

      $_servidores = $this->fetchPreparedQuery($sql, array($instituicaoId, $ano));

      $attrs = array('id', 'servidor_id', 'name', 'turma_id', 'permite_lancar_faltas_componente', 'disciplina_id','tipo_nota', 'updated_at', 'ativo');
      $_servidores = Portabilis_Array_Utils::filterSet($_servidores, $attrs);
      $servidores = array();
      $__servidores = array();

      foreach ($_servidores as $servidor) {
        $__servidores[$servidor['id']]['id'] = $servidor['id'];
        $__servidores[$servidor['id']]['servidor_id'] = $servidor['servidor_id'];
        $__servidores[$servidor['id']]['name'] = Portabilis_String_Utils::toUtf8($servidor['name']);
        $__servidores[$servidor['id']]['updated_at'] = $servidor['updated_at'];
        $__servidores[$servidor['id']]['ativo'] = $servidor['ativo'];
        $__servidores[$servidor['id']]['disciplinas_turmas'][] = array(
          'turma_id' => $servidor['turma_id'],
          'disciplina_id' => $servidor['disciplina_id'],
          'permite_lancar_faltas_componente' => $servidor['permite_lancar_faltas_componente'],
          'tipo_nota' => $servidor['tipo_nota']
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

    protected function getEscolaridade() {
        $idesco = $this->getRequest()->idesco;
        $sql = "SELECT * FROM cadastro.escolaridade where idesco = $1 ";
        $escolaridade = $this->fetchPreparedQuery($sql, array($idesco), TRUE, 'first-row');
        $escolaridade['descricao'] = Portabilis_String_Utils::toUtf8($escolaridade['descricao']);
        return array('escolaridade' => $escolaridade);
    }

  public function Gerar() {
    if ($this->isRequestFor('get', 'servidor-search'))
      $this->appendResponse($this->search());
    elseif ($this->isRequestFor('get', 'escolaridade'))
      $this->appendResponse($this->getEscolaridade());
    elseif ($this->isRequestFor('get', 'servidores-disciplinas-turmas'))
      $this->appendResponse($this->getServidoresDisciplinasTurmas());
    else
      $this->notImplementedOperationError();
  }

}
