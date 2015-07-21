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

class RegraController extends ApiCoreController
{

  protected function canGetTabelasDeArredondamento() {
    return $this->validatesPresenceOf('instituicao_id');
  }

  protected function canGetRegras() {
    return $this->validatesPresenceOf('instituicao_id')
      && $this->validatesPresenceOf('ano');
  }

  protected function getTabelasDeArredondamento() {
    if($this->canGetTabelasDeArredondamento()){
      $instituicaoId = $this->getRequest()->instituicao_id;

      $sql = "SELECT ta.id, ta.nome, tav.nome as rotulo, tav.descricao, tav.valor_maximo
                FROM modules.tabela_arredondamento ta
                INNER JOIN modules.tabela_arredondamento_valor tav ON tav.tabela_arredondamento_id = ta.id
                WHERE ta.instituicao_id = $1";

      $tabelas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'rotulo', 'descricao', 'valor_maximo');
      $tabelas = Portabilis_Array_Utils::filterSet($tabelas, $attrs);
      $_tabelas = array();

      foreach ($tabelas as $tabela) {
        $_tabelas[$tabela['id']]['id'] = $tabela['id'];
        $_tabelas[$tabela['id']]['nome'] = Portabilis_String_Utils::toUtf8($tabela['nome']);
        $_tabelas[$tabela['id']]['valores'][] = array(
          'rotulo' => Portabilis_String_Utils::toUtf8($tabela['rotulo']),
          'descricao' => Portabilis_String_Utils::toUtf8($tabela['descricao']),
          'valor_maximo' => Portabilis_String_Utils::toUtf8($tabela['valor_maximo'])
        );
      }

      return array('tabelas' => $_tabelas);
    }
  }

  protected function getRegras() {
    if($this->canGetRegras()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $ano = $this->getRequest()->ano;

      $sql = "SELECT *,
                id, tabela_arredondamento_id, tipo_nota, tipo_presenca, parecer_descritivo, cod_turma as turma_id
                FROM modules.regra_avaliacao ra
                LEFT JOIN pmieducar.serie s ON s.regra_avaliacao_id = ra.id
                LEFT JOIN pmieducar.turma t ON t.ref_ref_cod_serie = s.cod_serie
                WHERE s.ativo = 1
                AND t.ativo = 1
                AND ra.instituicao_id = $1
                AND t.ano = $2";

      $_regras = $this->fetchPreparedQuery($sql, array($instituicaoId, $ano));

      $attrs = array('id', 'tabela_arredondamento_id', 'tipo_nota', 'tipo_presenca', 'parecer_descritivo', 'turma_id');
      $_regras = Portabilis_Array_Utils::filterSet($_regras, $attrs);
      $regras = array();
      $__regras = array();

      foreach ($_regras as $regra) {
        $__regras[$regra['id']]['id'] = $regra['id'];
        $__regras[$regra['id']]['tabela_arredondamento_id'] = $regra['tabela_arredondamento_id'];
        $__regras[$regra['id']]['tipo_nota'] = $regra['tipo_nota'];
        $__regras[$regra['id']]['tipo_presenca'] = $regra['tipo_presenca'];
        $__regras[$regra['id']]['parecer_descritivo'] = $regra['parecer_descritivo'];
        $__regras[$regra['id']]['turmas'][] = array(
          'turma_id' => $regra['turma_id']
        );
      }

      foreach ($__regras as $regra) {
        $regras[] = $regra;
      }

      return array('regras' => $regras);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'tabelas-de-arredondamento'))
      $this->appendResponse($this->getTabelasDeArredondamento());
    elseif ($this->isRequestFor('get', 'regras'))
      $this->appendResponse($this->getRegras());
    else
      $this->notImplementedOperationError();
  }

}
