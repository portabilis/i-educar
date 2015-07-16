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


  protected function getTabelasDeArredondamento() {
    if($this->canGetTabelasDeArredondamento()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $ano = $this->getRequest()->ano;

      $sql = "SELECT ta.id, tav.nome, tav.descricao, tav.valor_maximo
                FROM modules.tabela_arredondamento ta
                INNER JOIN modules.tabela_arredondamento_valor tav ON tav.tabela_arredondamento_id = ta.id
                WHERE ta.instituicao_id = $1";

      $tabelas = $this->fetchPreparedQuery($sql, array($instituicaoId));

      $attrs = array('id', 'nome', 'descricao', 'valor_maximo');
      $tabelas = Portabilis_Array_Utils::filterSet($tabelas, $attrs);

      foreach ($tabelas as &$tabela) {
        $tabela['nome'] = Portabilis_String_Utils::toUtf8($tabela['nome']);
        $tabela['descricao'] = Portabilis_String_Utils::toUtf8($tabela['descricao']);
        $tabela['valor_maximo'] = Portabilis_String_Utils::toUtf8($tabela['valor_maximo']);
      }

      return array('tabelas' => $tabelas);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'tabelas-de-arredondamento'))
      $this->appendResponse($this->getTabelasDeArredondamento());
    else
      $this->notImplementedOperationError();
  }

}
