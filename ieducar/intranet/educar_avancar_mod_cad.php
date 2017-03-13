<?php

/**
 * i-Educar - Sistema de gest? escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itaja? *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa ?software livre; voc?pode redistribu?lo e/ou modific?lo
 * sob os termos da Licen? P?blica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a vers? 2 da Licen?, como (a seu crit?io)
 * qualquer vers? posterior.
 *
 * Este programa ?distribu?do na expectativa de que seja ?til, por?, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia impl?cita de COMERCIABILIDADE OU
 * ADEQUA?O A UMA FINALIDADE ESPEC?ICA. Consulte a Licen? P?blica Geral
 * do GNU para mais detalhes.
 *
 * Voc?deve ter recebido uma c?ia da Licen? P?blica Geral do GNU junto
 * com este programa; se n?, escreva para a Free Software Foundation, Inc., no
 * endere? 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author      Prefeitura Municipal de Itaja?<ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pmieducar
 * @subpackage  Matricula
 * @subpackage  Rematricula
 * @since       Arquivo dispon?el desde a vers? 1.0.0
 * @todo        Refatorar a l?ica de indice::Novo() para uma classe na camada de dom?io
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
  function Formular()
  {
    $this->SetTitulo($this->_instituicao . ' i-Educar');
    $this->processoAp = '845';
    $this->addEstilo('localizacaoSistema');
  }
}

class indice extends clsCadastro
{
  var $pessoa_logada;
  var $data_matricula;

  function Inicializar()
  {
    $retorno = 'Novo';
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $obj_permissao = new clsPermissoes();
    $obj_permissao->permissao_cadastra(845, $this->pessoa_logada, 7, 'educar_index.php');

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "i-Educar - Escola",
         ""        => "Rematr&iacute;cula autom&aacute;tica"
    ));
    $this->enviaLocalizacao($localizacao->montar());

    return $retorno;
  }

  function Gerar() {
    // inputs
    $anoLetivoHelperOptions = array('situacoes' => array('em_andamento', 'nao_iniciado'));

    $this->inputsHelper()->dynamic('ano');
    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'curso', 'serie'));
    $this->inputsHelper()->dynamic('turma', array('label' => 'Selecione a turma do ano anterior', 'required' => FALSE));
    $this->inputsHelper()->dynamic('anoLetivo', array('label' => 'Ano destino'), $anoLetivoHelperOptions);
    $this->inputsHelper()->date('data_matricula', array('label' => 'Data da matr?ula', 'placeholder' => 'dd/mm/yyyy'));

    $scripts = array('/modules/Cadastro/Assets/Javascripts/RematriculaAutomatica.js');
    Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
  }

  /**
   * @todo Refatorar a l?ica para uma classe na camada de dom?io.
   */
  function Novo()
  {
    session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $anoLetivo = new clsPmieducarEscolaAnoLetivo();
    $anoLetivo = $anoLetivo->lista($this->ref_cod_escola, null, null, null, 1);

    if (count($anoLetivo) > 1) {
      $this->mensagem = "<span class='notice'>Nenhum aluno rematriculado. Certifique-se que somente um ano letivo encontra-se em aberto.</span>";
      return false;
    }

    $this->db  = new clsBanco();
    $this->db2 = new clsBanco();
    $this->db3 = new clsBanco();
    $this->data_matricula = Portabilis_Date_Utils::brToPgSQL($this->data_matricula);

    $result = $this->rematricularAlunos($this->ref_cod_escola, $this->ref_cod_curso,
                                        $this->ref_cod_serie, $this->ref_cod_turma, $_POST['ano']);

    return $result;
  }


  function Editar() {
    return TRUE;
  }


  protected function rematricularAlunos($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    $result           = $this->selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $this->ano_letivo);
    $alunosSemInep    = $this->getAlunosSemInep($escolaId, $cursoId, $serieId, $turmaId, $ano);
    $alunosComSaidaDaEscola = $this->getAlunosComSaidaDaEscola($escolaId, $cursoId, $serieId, $turmaId, $ano);
    $count            = 0;
    $nomesAlunos;

    if (count($alunosSemInep) == 0) {
      while ($result && $this->db->ProximoRegistro()) {
        list($matriculaId, $alunoId, $situacao, $nomeAluno) = $this->db->Tupla();

        $this->db2->Consulta("UPDATE pmieducar.matricula
                                 SET ultima_matricula = '0'
                               WHERE cod_matricula = $matriculaId");

        $resultApDep = $this->db2->Consulta("SELECT ref_ref_cod_serie
                                               FROM pmieducar.matricula
                                              WHERE aprovado = 12
                                                AND ref_cod_aluno = '{$alunoId}'
                                                AND ano < '{$ano}'
                                                AND ativo = 1
                                                AND dependencia = FALSE");

        if ($result && $situacao == 1 || $situacao == 12 || $situacao == 13)
          $result = $this->rematricularAlunoAprovado($escolaId, $serieId, $this->ano_letivo, $alunoId);
        elseif ($result && $situacao == 2 || $situacao == 14)
          $result = $this->rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $this->ano_letivo, $alunoId);

          $nomesAlunos[] = $nomeAluno;
          $count += 1;

        if (! $result)
          break;
      }
    }

    if ($result && empty($this->mensagem)){
      if ($count > 0){
        $mensagem = "";
        if($count > 0){
          $mensagem .= "<span class='success'>Rematriculado os seguinte(s) $count aluno(s) com sucesso em $this->ano_letivo: </br></br>";
          foreach ($nomesAlunos as $nome) {
            $mensagem .= "{$nome} </br>";
          }
          $mensagem .= "</br> As enturma?es podem ser realizadas em: Movimenta?o > Enturma?o.</span>";
          if (count($alunosComSaidaDaEscola) > 0) {

            $mensagem .= "</br></br><span>O(s) seguinte(s) aluno(s) n? foram rematriculados, pois possuem sa?a na escola: </br></br>";
            foreach ($alunosComSaidaDaEscola as $nome) {
              $mensagem .= "{$nome} </br>";
            }
          }
        }
        $this->mensagem = $mensagem;
      }elseif (count($alunosSemInep) > 0) {
        $this->mensagem .= "<span>N? foi poss?el realizar a rematr?ula, pois o(s) seguinte(s) aluno(s) n? possuem o INEP cadastrado: </br></br>";
        foreach ($alunosSemInep as $nome) {
          $this->mensagem .= "{$nome} </br>";
        }
        $this->mensagem .= "</br>Por favor, cadastre o INEP do(s) aluno(s) em: Cadastros > Aluno > Alunos > Campo: C?igo INEP.";
      }elseif ($this->existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $this->ano_letivo)) {
        $this->mensagem = "<span class='notice'>Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados em ".($this->ano_letivo-1).".</span>";
      }else{
        $this->mensagem = Portabilis_String_Utils::toLatin1("<span class='notice'>Os alunos desta série já encontram-se rematriculados, sendo assim, favor verificar se as enturmações já foram efetuadas em Movimentação > Enturmação.</span>");
      }
    }elseif(empty($this->mensagem))
      $this->mensagem = "Ocorreu algum erro inesperado durante as rematr?ulas, por favor, tente novamente.";

    return $result;
  }

  protected function getAlunosSemInep($escolaId, $cursoId, $serieId, $turmaId, $ano){
    //Pega todas as matriculas
    $objMatricula = new clsPmieducarMatriculaTurma();
    $objMatricula->setOrderby("nome");
    $anoAnterior = $this->ano_letivo  - 1;
    $lstMatricula = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano);
    //Verifica o parametro na s?ie pra exigir inep
    $objSerie = new clsPmieducarSerie($serieId);
    $serieDet = $objSerie->detalhe();
    $exigeInep = $serieDet['exigir_inep'] == "t";
    //Retorna alunos sem inep
    $alunosSemInep = array();
    $objAluno = new clsPmieducarAluno();

    foreach ($lstMatricula as $matricula) {
      $alunoInep = $objAluno->verificaInep($matricula['ref_cod_aluno']);
      if (!$alunoInep && $exigeInep) {
        $alunosSemInep[] = strtoupper($matricula['nome']);
      }
    }
    return $alunosSemInep;
  }

  protected function getAlunosComSaidaDaEscola($escolaId, $cursoId, $serieId, $turmaId, $ano){

    $objMatricula = new clsPmieducarMatriculaTurma();
    $objMatricula->setOrderby("nome");
    $anoAnterior = $this->ano_letivo  - 1;
    $alunosComSaidaDaEscola = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano, TRUE);
    $alunos = array();

    foreach ($alunosComSaidaDaEscola as $a) {
      $alunos[] = strtoupper($a['nome']);
    }
    return $alunos;
  }

  protected function selectMatriculas($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    try {
      $anoAnterior = $this->ano_letivo  - 1;

      $sql = "SELECT cod_matricula,
                     ref_cod_aluno,
                     aprovado,
                     (SELECT upper(nome)
                        FROM cadastro.pessoa,
                             pmieducar.aluno
                       WHERE pessoa.idpes = aluno.ref_idpes
                         AND aluno.cod_aluno = ref_cod_aluno) as nome
                FROM pmieducar.matricula m, pmieducar.matricula_turma
               WHERE aprovado in (1, 2, 12, 13, 14)
                 AND m.ativo = 1
                 AND ref_ref_cod_escola = $escolaId
                 AND ref_ref_cod_serie = $serieId
                 AND ref_cod_curso = $cursoId
                 AND cod_matricula = ref_cod_matricula
                 AND matricula_turma.ativo = 1
                 AND ano  = $anoAnterior
                 AND m.dependencia = FALSE
                 AND m.saida_escola = FALSE
                 AND NOT EXISTS(SELECT 1
                                  FROM pmieducar.matricula m2
                                 WHERE m2.ref_cod_aluno = m.ref_cod_aluno
                                   AND m2.ano = $this->ano_letivo
                                   AND m2.ativo = 1
                                   AND m2.ref_ref_cod_escola = m.ref_ref_cod_escola)
                 AND NOT EXISTS(SELECT 1
                                  FROM pmieducar.matricula m2
                                 WHERE m2.ref_cod_aluno = m.ref_cod_aluno
                                   AND m2.ano = $this->ano_letivo
                                   AND m2.ativo = 1
                                   AND m2.ref_ref_cod_serie = (SELECT ref_serie_destino
                                                                 FROM pmieducar.sequencia_serie
                                                                WHERE ref_serie_origem = $serieId
                                                                  AND ativo = 1))";

      if ($turmaId)
        $sql .= "AND ref_cod_turma = $turmaId
                 ORDER BY nome";

      $this->db->Consulta($sql);
    }
    catch (Exception $e) {
      $this->mensagem = "Erro ao selecionar matr?ulas ano anterior: $anoAnterior";
      error_log("Erro ao selecionar matr?ulas ano anterior, no processo rematr?ula autom?ica:" . $e->getMessage());
      return false;
    }

    return true;
  }

  protected function existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    $objMatricula = new clsPmieducarMatriculaTurma();
    $objMatricula->setOrderby("nome");
    $anoAnterior = $this->ano_letivo - 1;
    $matriculas = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano, FALSE);
    $qtdMatriculasAprovadasReprovadas = 0;

    foreach ($matriculas as $m) {
      if (in_array($m['aprovado'], array(1, 2, 12, 13)))
        $qtdMatriculasAprovadasReprovadas++;
    }

    return ($qtdMatriculasAprovadasReprovadas != 0) ? true : false;
  }

  protected function rematricularAlunoAprovado($escolaId, $serieId, $ano, $alunoId) {
    $nextSerieId = $this->db2->campoUnico("SELECT ref_serie_destino FROM pmieducar.sequencia_serie
                                           WHERE ref_serie_origem = $serieId AND ativo = 1");

    if (is_numeric($nextSerieId)) {
      $nextCursoId = $this->db2->CampoUnico("SELECT ref_cod_curso FROM pmieducar.serie
                                            WHERE cod_serie = $nextSerieId");

      if ($this->escolaSerieConfigurada($escolaId, $nextSerieId)){
      	 return $this->matricularAluno($escolaId, $nextCursoId, $nextSerieId, $this->ano_letivo, $alunoId);
      }
      else{
      	$this->mensagem = "A s?ie de destino n? est?configurada na escola. Favor efetuar o cadastro em Cadastro > S?ie > Escola-S?ie";
      }
    }
    else{
      $this->mensagem = "N? foi poss?el obter a pr?ima s?ie da sequ?cia de enturma?o";
    }

    return false;
  }


  protected function rematricularAlunoReprovado($escolaId, $cursoId, $serieId, $ano, $alunoId) {
    return $this->matricularAluno($escolaId, $cursoId, $serieId, $this->ano_letivo, $alunoId);
  }


  protected function matricularAluno($escolaId, $cursoId, $serieId, $ano, $alunoId) {
    try {
      $this->db2->Consulta(sprintf("INSERT INTO pmieducar.matricula
        (ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula, data_matricula) VALUES ('%d', '%d', '%d', '%d', '3', 'NOW()', '%d', '%d', '1','{$this->data_matricula}')",
      $escolaId, $serieId, $this->pessoa_logada, $alunoId, $this->ano_letivo, $cursoId));
    }
    catch (Exception $e) {
      $this->mensagem = "Erro durante matr?ula do aluno: $alunoId";
      error_log("Erro durante a matr?ula do aluno $alunoId, no processo de rematr?ula autom?ica:" . $e->getMessage());
      return false;
    }

    return true;
  }

  protected function escolaSerieConfigurada($escolaId, $serieId){

  	$escolaSerie = new clsPmieducarEscolaSerie($escolaId, $serieId);

  	$escolaSerie = $escolaSerie->detalhe();
  	if(count($escolaSerie) > 0){
  		if($escolaSerie["ativo"] == '1'){
  			return true;
  		}
  	}
  	return false;
  }
}

// Instancia objeto de p?ina
$pagina = new clsIndexBase();

// Instancia objeto de conte?do
$miolo = new indice();

// Atribui o conte?do ? p?ina
$pagina->addForm($miolo);

// Gera o c?igo HTML
$pagina->MakeAll();
?>