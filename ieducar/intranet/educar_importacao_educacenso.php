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
 * @author    Caroline Salib Canto <caroline@portabilis.com.br>
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
    $this->SetTitulo($this->_instituicao . ' i-Educar - Importação educacenso');
    $this->processoAp = 9998849;
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;

  var $arquivo;

  function Inicializar()
  {
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(9998849, $this->pessoa_logada, 7,
      'educar_index.php');
    $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "Início",
         "educar_educacenso_index.php" => "Educacenso",
         "" => "Importação educacenso"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return 'Editar';
  }

  function Gerar()
  {
    $this->campoArquivo('arquivo', 'Arquivo', $this->arquivo);
    $this->nome_url_sucesso = "Importar";
  }

  function Novo()
  {
    $this->Editar();
  }

  function Editar()
  {
    if (!$this->arquivo['tmp_name']){
      $this->mensagem = "Selecione um arquivo para a exportação.";
      return false;
    }

    $arquivo = file_get_contents($this->arquivo['tmp_name']);
    $registros = explode("\n", $arquivo);

    foreach ($registros as $registro) {
      $dadosRegistro = explode("|", $registro);
      $numeroRegistro = $dadosRegistro[0];

      switch ($numeroRegistro) {
        case '00':
          $this->ImportaRegistro00($dadosRegistro);
          break;
      }
    }
    return true;
  }

  function ImportaRegistro00() {
    $this->mensagem .= "Importação do registro 00";
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
