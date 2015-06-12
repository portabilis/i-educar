<?php

// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
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
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

/**
 * clsIndexBase class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar - S&eacute;rie');
    $this->processoAp = '586';
    $this->addEstilo("localizacaoSistema");
  }
}

/**
 * indice class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class indice extends clsCadastro{
  var $pessoa_logada;
  var $cod_turma;
  

  function Inicializar(){
    $retorno = 'Novo';
    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    @session_write_close();

    Portabilis_View_Helper_Application::loadJavascript($this, array('/modules/Cadastro/Assets/Javascripts/OrdenarAlunosTurma.js'));

    $this->cod_turma=$_GET['cod_turma'];

    if (is_numeric($this->cod_turma)) {
      $matriculasTurma = new clsPmieducarMatriculaTurma();
      $matriculasTurma->setOrderby('sequencial_fechamento, nome_ascii');
      $matriculasTurma = $matriculasTurma->lista(null, $this->cod_turma, null, null, null, null, null, null, 1);
      if ($matriculasTurma) {
        foreach ($matriculasTurma as $campo => $val) {
          $this->campoTexto('nome_aluno_' . $val['ref_cod_matricula'], '', $val['nome_ascii'], 60, false, false, false, true, '', '', '', '', true);
          $matricula = $val['ref_cod_matricula'];
          $this->campoTexto("sequencia[$matricula]", '', ($campo+1), 5, null, false, false, false);
        }
        $retorno = 'Editar';
      }
    }

    $this->url_cancelar = "educar_turma_det.php?cod_serie={$this->cod_turma}";

    $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";
    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "{$nomeMenu} ordem da turma"             
    ));
    $this->enviaLocalizacao($localizacao->montar());      

    $this->nome_url_cancelar = "Cancelar";

    return $retorno;
  }

  function Gerar(){
     return true;
  }

  function Novo(){
    return false;
  }

  function Editar(){
    $cod_turma = $_GET['cod_turma'];
    foreach($this->sequencia as $matricula => $sequencial){
      $retorno = Portabilis_Utils_Database::fetchPreparedQuery('UPDATE pmieducar.matricula_turma 
                                                                   SET sequencial_fechamento = $1 
                                                                 WHERE ref_cod_matricula = $2 
                                                                   AND ref_cod_turma = $3', 
                                                                 array('params' => array($sequencial, $matricula, $cod_turma)));
    }
    header("location: educar_turma_det.php?cod_turma={$cod_turma}");
    return true;
  }

  function Excluir(){
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
