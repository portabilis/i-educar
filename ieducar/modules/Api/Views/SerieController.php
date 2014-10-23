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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class SerieController extends ApiCoreController
{

  protected function canGetSeries(){
    return $this->validatesPresenceOf('instituicao_id') && $this->validatesPresenceOf('escola_id') && $this->validatesPresenceOf('curso_id');
  }

  protected function getSeries(){
    if ($this->canGetSeries()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $escolaId = $this->getRequest()->escola_id;
      $cursoId = $this->getRequest()->curso_id;

      if(is_array($escolaId))
        $escolaId = implode(",", $escolaId);

      if(is_array($cursoId))
        $cursoId = implode(",", $cursoId);

      $sql = "SELECT distinct s.cod_serie, s.nm_serie
                FROM pmieducar.serie s
                INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                INNER JOIN pmieducar.curso c ON s.ref_cod_curso = c.cod_curso
                WHERE es.ativo = 1
                AND s.ativo = 1
                AND c.ativo = 1
                AND es.ref_cod_escola IN ({$escolaId})
                AND c.ref_cod_instituicao = $1
                AND c.cod_curso IN ({$cursoId})
                ORDER BY s.nm_serie ASC ";
      
      $params     = array($this->getRequest()->instituicao_id);

      $series = $this->fetchPreparedQuery($sql, $params);

      foreach ($series as &$serie) {
        $serie['nm_serie'] = Portabilis_String_Utils::toUtf8($serie['nm_serie']);
      }

      $attrs = array(
        'cod_serie'       => 'id',
        'nm_serie'        => 'nome'
      );

      $series = Portabilis_Array_Utils::filterSet($series, $attrs);

      return array('series' => $series );
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'series'))
      $this->appendResponse($this->getSeries());
    else
      $this->notImplementedOperationError();
  }
}
