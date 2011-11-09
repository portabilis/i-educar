<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *           <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */

#TODO remover includes desnecessarios
require_once 'CoreExt/View/Helper/UrlHelper.php';
require_once 'CoreExt/View/Helper/TableHelper.php';
require_once 'Core/Controller/Page/ListController.php';
require_once 'App/Model/IedFinder.php';
#require_once 'Avaliacao/Model/NotaAlunoDataMapper.php';
#require_once 'Avaliacao/Model/FaltaAlunoDataMapper.php';
#require_once 'Avaliacao/Service/Boletim.php';
#require_once 'RegraAvaliacao/Model/TipoPresenca.php';
#require_once 'App/Model/MatriculaSituacao.php';

require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

#require_once 'include/pmieducar/clsPmieducarEscola.inc.php';
#require_once 'include/pmieducar/clsPmieducarMatricula.inc.php';
#require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
#require_once 'include/pmieducar/clsPmieducarTurma.inc.php';
#require_once 'include/pmieducar/clsPmieducarAluno.inc.php';

#require_once 'include/portabilis_utils.php';

class DiarioController extends Core_Controller_Page_ListController
{
  protected $_dataMapper = 'Avaliacao_Model_NotaAlunoDataMapper';
  protected $_titulo   = 'Lan&ccedil;amento por turma';
  protected $_processoAp = 644;
  protected $_formMap  = array();

  protected function setVars()
  {
    $this->ref_cod_aluno = $_GET['aluno_id'];
    $this->ref_cod_instituicao = $_GET['instituicao_id'];
    $this->ref_cod_escola = $_GET['escola_id'];
    $this->ref_cod_curso = $_GET['curso_id'];
    $this->ref_cod_turma = $_GET['turma_id'];
    $this->ref_ref_cod_serie = $this->ref_cod_serie = $_GET['serie_id'];
    $this->ano_escolar = $_GET['ano_escolar'];
    $this->ref_cod_componente_curricular = $_GET['componente_curricular_id'];
    $this->etapa = $_GET['etapa'];

    if ($this->ref_cod_aluno)
    {
      $nome_aluno_filtro = new clsPmieducarAluno();
      $nome_aluno_filtro = $nome_aluno_filtro->lista($int_cod_aluno = $this->ref_cod_aluno);
      $this->nm_aluno = $nome_aluno_filtro[0]['nome_aluno'];
    }
  }


  protected function setSelectionFields()
  {

    #variaveis usadas pelo modulo /intranet/include/pmieducar/educar_campo_lista.php
    $this->verificar_campos_obrigatorios = True;
    $this->add_onchange_events = True;

    $get_escola = $escola_obrigatorio = $listar_escolas_alocacao_professor = TRUE;
    $get_ano_escolar = $ano_escolar_obrigatorio = TRUE;
    $get_curso = $curso_obrigatorio = $listar_somente_cursos_funcao_professor = TRUE;
    $get_escola_curso_serie = $escola_curso_serie_obrigatorio = TRUE;
    $get_turma = $turma_obrigatorio = $listar_turmas_periodo_alocacao_professor = TRUE;
    $get_componente_curricular = $componente_curricular_obrigatorio = $listar_componentes_curriculares_professor = TRUE;
    $get_etapa = $etapa_obrigatorio = TRUE;
    include 'include/pmieducar/educar_campo_lista.php';

    $this->campoTexto('nm_aluno', 'Aluno', $this->nm_aluno, 30, 255, FALSE,
      FALSE, FALSE, '', "<img border=\"0\" onclick=\"pesquisa_aluno();\" id=\"ref_cod_aluno_lupa\" name=\"ref_cod_aluno_lupa\" src=\"imagens/lupa.png\"\/>", '', '', TRUE);

    $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);
  }

  
  public function Gerar()
  {

    $this->setVars();
    $this->setSelectionFields();

    $this->rodape = "<strong>N&atilde;o est&aacute; sendo listado as op&ccedil;&otilde;es de filtro que voc&ecirc; espera ?</strong> solicite a(o) secret&aacute;rio(a) da escola que verifique a aloca&ccedil;&atilde;o do seu usu&aacute;rio.";

    $this->largura = '100%';

    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.js"></script>');
    $this->appendOutput('<script type="text/javascript" src="scripts/jquery/jquery.form.js"></script>');
    $this->appendOutput('<link type="text/javascript" rel="stylesheet" href="/modules/Avaliacao/Static/styles/diarioController.css"></script>');
    $this->appendOutput('<script type="text/javascript" src="/modules/Avaliacao/Static/scripts/diarioController.js"></script>');
  }
}
?>

