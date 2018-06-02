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
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */
require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'intranet/include/clsBanco.inc.php';
require_once 'intranet/include/clsBase.inc.php';

/**
 * Class UsuarioExportController
 * @deprecated Essa versão da API pública será descontinuada
 */
class UsuarioExportController extends ApiCoreController
{

  protected function exportUsers(){
    $instituicao = $this->getRequest()->instituicao;
    $escola      = $this->getRequest()->escola;
    $status      = $this->getRequest()->status;
    $tipoUser    = $this->getRequest()->tipoUsuario;
    $getUsers = new clsPmieducarUsuario();
    $getUsers->setOrderby("nome ASC");
    $lstUsers = $getUsers->listaExportacao($escola,
                                           $instituicao,
                                           $tipoUser,
                                           $status);

    //Linhas do cabeçalho
    $csv .= 'Nome,';
    $csv .= 'Matricula,';
    $csv .= 'E-mail,';
    $csv .= 'Status,';
    $csv .= Portabilis_String_Utils::toLatin1('Tipo_usuário,');
    $csv .= Portabilis_String_Utils::toLatin1('Instituição,');
    $csv .= 'Escola,';
    $csv .= PHP_EOL;
    foreach ($lstUsers as $row) {
        $csv .= '"' . $row['nome'] . '",';
        $csv .= '"' . $row['matricula'] . '",';
        $csv .= '"' . $row['email'] . '",';
        $csv .= '"' . $row['status'] . '",';
        $csv .= '"' . $row['nm_tipo'] . '",';
        $csv .= '"' . $row['nm_instituicao'] . '",';
        $csv .= '"' . $row['nm_escola'] . '",';
        $csv .= PHP_EOL;
    }
    return array('conteudo' => Portabilis_String_Utils::toUtf8($csv));
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'exportarDados'))
      $this->appendResponse($this->exportUsers());
    else
      $this->notImplementedOperationError();
  }
}
