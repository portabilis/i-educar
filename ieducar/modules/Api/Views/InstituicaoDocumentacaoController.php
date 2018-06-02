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
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'intranet/include/clsBanco.inc.php';

/**
 * Class InstituicaoDocumentacaoController
 * @deprecated Essa versão da API pública será descontinuada
 */
class InstituicaoDocumentacaoController extends ApiCoreController
{

  protected function insertDocuments() {

    $var1 = $this->getRequest()->instituicao_id;
    $var2 = $this->getRequest()->titulo_documento;
    $var3 = $this->getRequest()->url_documento;
    $var4 = $this->getRequest()->ref_usuario_cad;
    $var5 = $this->getRequest()->ref_cod_escola;

    $sql = "INSERT INTO pmieducar.instituicao_documentacao (instituicao_id, titulo_documento, url_documento, ref_usuario_cad, ref_cod_escola) VALUES ($var1, '$var2', '$var3', $var4, $var5)";
    $this->fetchPreparedQuery($sql);

    $sql = "SELECT MAX(id) FROM pmieducar.instituicao_documentacao WHERE instituicao_id = $var1";

    $novoId = $this->fetchPreparedQuery($sql);

    return array('id' => $novoId[0][0]);
  }

  protected function getDocuments() {

    $var1 = $this->getRequest()->instituicao_id;

    $sql = "SELECT * FROM pmieducar.instituicao_documentacao WHERE instituicao_id = $var1 ORDER BY id DESC";

    $instituicao = $this->fetchPreparedQuery($sql);

    $attrs = array('id', 'titulo_documento', 'url_documento', 'ref_usuario_cad', 'ref_cod_escola');
    $instituicao = Portabilis_Array_Utils::filterSet($instituicao, $attrs);

    return array('documentos' => $instituicao);
  }

  protected function deleteDocuments() {

    $var1 = $this->getRequest()->id;

    $sql = "DELETE FROM pmieducar.instituicao_documentacao WHERE id = $var1";

    $instituicao = $this->fetchPreparedQuery($sql);

    return $instituicao;
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'insertDocuments'))
      $this->appendResponse($this->insertDocuments());
    elseif ($this->isRequestFor('get', 'getDocuments'))
      $this->appendResponse($this->getDocuments());
    elseif ($this->isRequestFor('get', 'deleteDocuments'))
      $this->appendResponse($this->deleteDocuments());
  }
}
