<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';

/**
 * @author    Paula Bonot <bonot@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     ?
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - Nova exporta&ccedil;&atilde;o');
    $this->processoAp = 999869;
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ano;
  var $ref_cod_instituicao;

  function Inicializar()
  {


    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(999869, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $this->breadcrumb('Exportação de usuários', [
        url('intranet/educar_configuracoes_index.php') => 'Configurações',
    ]);

    return 'Nova exporta&ccedil;&atilde;o';
  }

  function Gerar()
  {

    $this->inputsHelper()->dynamic(array('instituicao'));
    $this->inputsHelper()->dynamic('escola', array('required' =>  false));

    $resourcesStatus = array(1  => 'Ativo',
                       0  => 'Inativo');
    $optionsStatus   = array('label' => 'Status', 'resources' => $resourcesStatus, 'value' => 1);
    $this->inputsHelper()->select('status', $optionsStatus);

    $opcoes = array( "" => "Selecione" );

    $objTemp = new clsPmieducarTipoUsuario();
    $objTemp->setOrderby('nm_tipo ASC');

    $lista = $objTemp->lista(null,null,null,null,null,null,null,null,1);

    if ( is_array( $lista ) && count( $lista ) )
    {
      foreach ( $lista as $registro )
      {
        $opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
        $opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
      }
    }

    $tamanho = sizeof($opcoes_);
    echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";
    foreach ($opcoes_ as $key => $valor)
      echo "cod_tipo_usuario[{$key}] = {$valor};\n";
    echo "</script>";

    $this->campoLista( "ref_cod_tipo_usuario", "Tipo usu&aacute;rio", $opcoes, $this->ref_cod_tipo_usuario,"",null,null,null,null,false );

    Portabilis_View_Helper_Application::loadJavascript($this, '/modules/ExportarUsuarios/exportarUsuarios.js');

    $this->nome_url_sucesso = "Exportar";
    $this->acao_enviar      = " ";
  }

}
// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
