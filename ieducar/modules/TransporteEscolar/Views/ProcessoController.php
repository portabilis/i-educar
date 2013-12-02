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
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   TransporteEscolar
 * @subpackage  Modules
 * @since     Arquivo disponível desde a versão ?
 * @version   $Id$
 */
require_once 'lib/Portabilis/Controller/Page/EditController.php';
require_once 'Usuario/Model/FuncionarioDataMapper.php';
require_once 'Avaliacao/Views/DiarioApiController.php';
require_once 'include/pmieducar/clsPmieducarMatriculaTurma.inc.php';
require_once 'include/pmieducar/clsPmieducarEscola.inc.php';

class ProcessoController extends Portabilis_Controller_Page_EditController
{
  protected $_dataMapper = 'Usuario_Model_FuncionarioDataMapper';
  protected $_titulo     = 'i-Educar - Processo';

  protected $_nivelAcessoOption = App_Model_NivelAcesso::SOMENTE_ESCOLA;
  protected $_processoAp        = 21236;
  protected $_deleteOption      = true;

  protected $_formMap    = array(
  );


  protected function _preConstruct()
  {
  }


  protected function _initNovo() {
    return false;
  }


  protected function _initEditar() {
    return false;
  }


  public function Gerar()
  {

    
   /* $objEscola = new clsPmieducarEscola();

    $listaEscola = $objEscola->lista($int_cod_escola = NULL, $int_ref_usuario_cad = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_cod_instituicao = NULL,
    $int_ref_cod_escola_localizacao = NULL, $int_ref_cod_escola_rede_ensino = NULL,
    $int_ref_idpes = NULL, $str_sigla = NULL, $date_data_cadastro = NULL,
    $date_data_exclusao = NULL, $int_ativo = 1, $str_nome = NULL,
    $escola_sem_avaliacao = NULL);

    foreach ($listaEscola as $regEscola) {
      */
      $objMat = new clsPmieducarMatriculaTurma();
      $objMat->setOrderBy(' ref_cod_turma ASC ');
      $lista = $objMat->lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
            $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
            $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
            $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = 1,
            $int_ref_cod_serie = NULL, $int_ref_cod_curso = NULL, $int_ref_cod_escola = 13170, //$regEscola['cod_escola'],
            $int_ref_cod_instituicao = NULL, $int_ref_cod_aluno = NULL, $mes = NULL,
            $aprovado = 3, $mes_menor_que = NULL, $int_sequencial = NULL,
            $int_ano_matricula = 2013, $tem_avaliacao = NULL, $bool_get_nome_aluno = FALSE,
            $bool_aprovados_reprovados = NULL, $int_ultima_matricula = 1,
            $bool_matricula_ativo = TRUE, $bool_escola_andamento = TRUE,
            $mes_matricula_inicial = FALSE, $get_serie_mult = FALSE,
            $int_ref_cod_serie_mult = NULL, $int_semestre = NULL,
            $pegar_ano_em_andamento = TRUE, $parar=NULL);
  
      foreach ($lista as $reg) {
        $objDiario = null;
        $objDiario = new DiarioApiController();
        $objDiario->_currentMatriculaId = $reg['ref_cod_matricula'];
        $objDiario->processaNotasNecessarias($reg['ref_cod_matricula']);              
      }
      
      die('lol fez todas de uma escola *_*');
    //}
    die('Notas de exame de todas as matrículas processadas.');

  }

}
?>