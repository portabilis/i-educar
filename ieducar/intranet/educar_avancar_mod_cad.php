<?php

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
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @license     http://creativecommons.org/licenses/GPL/2.0/legalcode.pt  CC GNU GPL
 * @package     Core
 * @subpackage  pmieducar
 * @subpackage  Matricula
 * @subpackage  Rematricula
 * @since       Arquivo disponível desde a versão 1.0.0
 * @todo        Refatorar a lógica de indice::Novo() para uma classe na camada de domínio
 * @version     $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

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
         "educar_index.php"                  => "Escola",
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
    $this->inputsHelper()->date('data_matricula', array('label' => 'Data da matricula', 'placeholder' => 'dd/mm/yyyy'));

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
          $mensagem .= "</br> As enturmações podem ser realizadas em: Movimentação > Enturmação.</span>";
          if (count($alunosComSaidaDaEscola) > 0) {

            $mensagem .= "</br></br><span>O(s) seguinte(s) aluno(s) não foram rematriculados, pois possuem saída na escola: </br></br>";
            foreach ($alunosComSaidaDaEscola as $nome) {
              $mensagem .= "{$nome} </br>";
            }
          }
        }
        $this->mensagem = $mensagem;
      }elseif (count($alunosSemInep) > 0) {
        $this->mensagem .= "<span>Não foi possível realizar a rematrícula, pois o(s) seguinte(s) aluno(s) não possuem o INEP cadastrado: </br></br>";
        foreach ($alunosSemInep as $nome) {
          $this->mensagem .= "{$nome} </br>";
        }
        $this->mensagem .= "</br>Por favor, cadastre o INEP do(s) aluno(s) em: Cadastros > Aluno > Alunos > Campo: Código INEP.";
      }elseif ($this->existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $this->ano_letivo)) {
        $this->mensagem = "<span class='notice'>Nenhum aluno rematriculado. Certifique-se que a turma possui alunos aprovados ou reprovados em ".($this->ano_letivo-1).".</span>";
      }else{
        $this->mensagem = Portabilis_String_Utils::toLatin1("<span class='notice'>Os alunos desta série já encontram-se rematriculados, sendo assim, favor verificar se as enturmações já foram efetuadas em Movimentação > Enturmação.</span>");
      }
    }elseif(empty($this->mensagem))
      $this->mensagem = "Ocorreu algum erro inesperado durante as rematrículas, por favor, tente novamente.";

    return $result;
  }

  protected function getAlunosSemInep($escolaId, $cursoId, $serieId, $turmaId, $ano){
    //Pega todas as matriculas
    $objMatricula = new clsPmieducarMatriculaTurma();
    $objMatricula->setOrderby("nome");
    $anoAnterior = $this->ano_letivo  - 1;
    $lstMatricula = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $ano);
    //Verifica o parametro na série pra exigir inep
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
      $this->mensagem = "Erro ao selecionar matrículas ano anterior: $anoAnterior";
      error_log("Erro ao selecionar matrículas ano anterior, no processo rematrícula automática:" . $e->getMessage());
      return false;
    }

    return true;
  }

  protected function existeMatriculasAprovadasReprovadas($escolaId, $cursoId, $serieId, $turmaId, $ano) {
    $objMatricula = new clsPmieducarMatriculaTurma();
    $objMatricula->setOrderby("nome");
    $anoAnterior = $this->ano_letivo - 1;
    $matriculas = $objMatricula->lista4($escolaId, $cursoId, $serieId, $turmaId, $anoAnterior);
    $qtdMatriculasAprovadasReprovadas = 0;

    foreach ($matriculas as $m) {
      if (in_array($m['aprovado'], array(1, 2, 12, 13)))
        $qtdMatriculasAprovadasReprovadas++;
    }

    //var_dump(($qtdMatriculasAprovadasReprovadas != 0) ? true : false);die;

    return ($qtdMatriculasAprovadasReprovadas == 0) ? true : false;
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
        $this->mensagem = "A série de destino não está configurada na escola. Favor efetuar o cadastro em Cadastro > Série > Escola-Série";
      }
    }
    else{
      $this->mensagem = "Não foi possível obter a próxima série da sequência de enturmação";
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
      $this->mensagem = "Erro durante matrícula do aluno: $alunoId";
      error_log("Erro durante a matrícula do aluno $alunoId, no processo de rematrícula automática:" . $e->getMessage());
      return false;
    }
    
    $this->auditarMatriculas($escolaId, $cursoId, $serieId, $ano, $alunoId);

    return true;
  }

  protected function auditarMatriculas($escolaId, $cursoId, $serieId, $ano, $alunoId) {
    $objMatricula = new clsPmieducarMatricula();
    $matricula    = $objMatricula->lista(null, null, $escolaId, $serieId, null, null, $alunoId, null, null, null, null, null, 1, $ano, null, null, null, null, null, null, null, null, null, null, $cursoId);

    $matriculaId = $matricula[0]['cod_matricula'];
    $objMatricula->cod_matricula =  $matriculaId;

    $detalhe = $objMatricula->detalhe();

    $auditoria = new clsModulesAuditoriaGeral("matricula", $this->pessoa_logada, $matriculaId);
    $auditoria->inclusao($detalhe);
    
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

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúddo
$miolo = new indice();

// Atribui o conteúdo dá página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
?>
