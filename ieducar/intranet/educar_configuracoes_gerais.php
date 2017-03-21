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
 * @author    Caroline Salib <caroline@portabilis.com.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Configura&ccedil;&otilde;es gerais');
    $this->processoAp = 999873;
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $ref_cod_instituicao;
  var $permite_relacionamento_posvendas;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(999873, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Administrativo",
         ""                                  => "Configura&ccedil;&otilde;es gerais"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return 'Editar';
  }

  function Gerar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao);
    $configuracoes = $configuracoes->detalhe();

    $this->permite_relacionamento_posvendas = $configuracoes['permite_relacionamento_posvendas'];

    $this->inputsHelper()->checkbox('permite_relacionamento_posvendas', array('label' => 'Permite relacionamento direto no pós-venda?', 'value' => $this->permite_relacionamento_posvendas));
  }

  function Editar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $permiteRelacionamentoPosvendas = ($this->permite_relacionamento_posvendas == 'on' ? 1 : 0);

    $configuracoes = new clsPmieducarConfiguracoesGerais($ref_cod_instituicao, $permiteRelacionamentoPosvendas);
    $editou = $configuracoes->edita();

    if( $editou )
    {
      $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
      header( "Location: index.php" );
      die();
      return true;
    }

    $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
    return false;
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
