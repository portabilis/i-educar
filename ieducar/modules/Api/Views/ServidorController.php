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
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';

require_once 'include/pmieducar/clsPmieducarServidor.inc.php';

class ServidorController extends ApiCoreController
{

  protected function getServidores() {
    $sql = "SELECT cod_servidor,
                   carga_horaria,
                   nome
              FROM pmieducar.servidor
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = servidor.cod_servidor)
             INNER JOIN pmieducar.servidor_funcao ON (servidor_funcao.ref_cod_servidor = servidor.cod_servidor)
             INNER JOIN pmieducar.funcao ON (funcao.cod_funcao = servidor_funcao.ref_cod_funcao)
             WHERE servidor.ativo = 1
               AND funcao.professor = 1";

    $servidores = $this->fetchPreparedQuery($sql);
    return array('servidores' => $servidores);
  }

  protected function canGetServidoresDisciplinas() {
    return $this->validatesId('servidor');
  }

  protected function getServidoresDisciplinas() {
    if ($this->canGetServidoresDisciplinas()) {
      $sql = "SELECT ref_cod_disciplina,
                     ref_cod_servidor,
                     nome
                FROM pmieducar.servidor_disciplina
               INNER JOIN modules.componente_curricular ON (componente_curricular.id = ref_cod_disciplina)
               WHERE ref_cod_servidor = $1";

      $servidores = $this->fetchPreparedQuery($sql, $this->getRequest()->servidor_id);
      return array('servidores-disciplinas' => $servidores);
    }
  }

  protected function canGetServidoresEscolas() {
    return $this->validatesId('servidor');
  }

  protected function getServidoresEscolas() {
    if ($this->canGetServidoresEscolas()) {
      $sql = " SELECT ref_cod_escola,
                      ref_cod_servidor,
                      carga_horaria,
                      periodo
                 FROM pmieducar.servidor_alocacao
                WHERE ref_cod_servidor = $1
                  AND servidor_alocacao.ativo = 1";

      $servidores = $this->fetchPreparedQuery($sql, $this->getRequest()->servidor_id);
      return array('servidores-escolas' => $servidores);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'servidores'))
      $this->appendResponse($this->getServidores());
    elseif ($this->isRequestFor('get', 'servidores-disciplinas'))
      $this->appendResponse($this->getServidoresDisciplinas());
      elseif ($this->isRequestFor('get', 'servidores-escolas'))
        $this->appendResponse($this->getServidoresEscolas());
    else
      $this->notImplementedOperationError();
  }
}
