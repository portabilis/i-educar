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
#require_once 'intranet/include/pmieducar/clsPmieducarEtapa.inc.php';

/**
 * EtapaController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class EtapaController extends ApiCoreController
{

  protected function canGetEtapas() {
    return $this->validatesId('escola') &&
           $this->validatesId('curso') &&
           $this->validatesId('turma') &&
           $this->validatesPresenceOf('ano');
  }
  protected function canGetEtapasEscola() {
    return $this->validatesId('escola');
  }

  protected function getEtapas() {
    if ($this->canGetEtapas()) {
      $cursoId = $this->getRequest()->curso_id;

      $sql             = "select padrao_ano_escolar from pmieducar.curso where cod_curso = $1 and ativo = 1";
      $padraoAnoLetivo = $this->fetchPreparedQuery($sql, array($cursoId), true, 'first-field');

      if ($padraoAnoLetivo == 1) {
        $escolaId = $this->getRequest()->escola_id;
        $ano      = $this->getRequest()->ano;

        $sql = "select padrao.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.ano_letivo_modulo
                as padrao, pmieducar.modulo where padrao.ref_ano = $1 and padrao.ref_ref_cod_escola = $2
                and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial";

        $etapas = $this->fetchPreparedQuery($sql, array($ano, $escolaId));
      }

      else {
        $sql = "select turma.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.turma_modulo as turma,
                pmieducar.modulo where turma.ref_cod_turma = $1 and turma.ref_cod_modulo = modulo.cod_modulo
                and modulo.ativo = 1 order by turma.sequencial";

        $etapas = $this->fetchPreparedQuery($sql, $this->getRequest()->turma_id);
      }

      $options = array();
      foreach ($etapas as $etapa)
        $options['__' . $etapa['etapa']] = $etapa['etapa'] . 'º ' . mb_strtoupper($etapa['nome'], 'UTF-8');

      return array('options' => $options);
    }
  }

    protected function getEtapasEscola() {
    if ($this->canGetEtapasEscola()) {


        $escolaId = $this->getRequest()->escola_id;
        $ano      = $this->getRequest()->ano;

        $sql = "select padrao.sequencial as etapa, modulo.nm_tipo as nome from pmieducar.ano_letivo_modulo
                as padrao, pmieducar.modulo where padrao.ref_ano = $1 and padrao.ref_ref_cod_escola = $2
                and padrao.ref_cod_modulo = modulo.cod_modulo and modulo.ativo = 1 order by padrao.sequencial";

        $etapas = $this->fetchPreparedQuery($sql, array($ano, $escolaId));




      $options = array();
      foreach ($etapas as $etapa)
        $options['__' . $etapa['etapa']] = $etapa['etapa'] . 'º ' . $this->toUtf8($etapa['nome']);

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'etapas'))
      $this->appendResponse($this->getEtapas());
    else if ($this->isRequestFor('get', 'etapasEscola'))
      $this->appendResponse($this->getEtapasEscola());
    else
      $this->notImplementedOperationError();
  }
}
