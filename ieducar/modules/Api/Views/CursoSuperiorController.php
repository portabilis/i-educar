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

class CursoSuperiorController extends ApiCoreController
{
  // search options

  protected function sqlsForStringSearch() {

    $sqls[] = "SELECT   id,
                        (nome || ' / ' || (case grau_academico
                                               when 1 then 'Tecnologo'
                                               when 2 then 'Licenciatura'
                                               when 3 then 'Bacharelado' end)) as name
                from modules.educacenso_curso_superior 
                WHERE nome ILIKE '%'||$1||'%'
                LIMIT 15";

    return $sqls;
  }

  protected function sqlsForNumericSearch() {

    $sqls[] = "SELECT   id,
                        (nome || ' / ' || (case grau_academico
                                               when 1 then 'Tecnologo'
                                               when 2 then 'Licenciatura'
                                               when 3 then 'Bacharelado' end)) as name
                from modules.educacenso_curso_superior
                WHERE id = $1
                LIMIT 15";

    return $sqls;
  }

  protected function searchOptions() {
    return array('namespace' => 'modules', 'table' => 'educacenso_curso_superior', 'idAttr' => 'id');
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'cursosuperior-search'))
      $this->appendResponse($this->search());
    else
      $this->notImplementedOperationError();
  }
}
