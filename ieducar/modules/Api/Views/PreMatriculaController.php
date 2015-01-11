<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
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
 * @author     Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category   i-Educar
 * @license    @@license@@
 * @package    Api
 * @subpackage Modules
 * @since      Arquivo disponível desde a versão ?
 * @version    $Id$
 */

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';
require_once 'include/pmieducar/geral.inc.php';

class PreMatriculaController extends ApiCoreController
{

  protected function canMatricularCandidato(){
    return $this->validatesPresenceOf('ano_letivo') && $this->validatesPresenceOf('curso_id')
        && $this->validatesPresenceOf('serie_id') && $this->validatesPresenceOf('escola_id')
        && $this->validatesPresenceOf('turma_id') && $this->validatesPresenceOf('nome_aluno')
        && $this->validatesPresenceOf('data_nasc_aluno') && $this->validatesPresenceOf('sexo_aluno')
        && $this->validatesPresenceOf('cep') && $this->validatesPresenceOf('rua')
        && $this->validatesPresenceOf('numero') && $this->validatesPresenceOf('bairro')
        && $this->validatesPresenceOf('cidade') && $this->validatesPresenceOf('estado') && $this->validatesPresenceOf('pais');
  }

  protected function matricularCandidato(){
    if ($this->canMatricularCandidato()){
      // Dados da matrícula
      $anoLetivo = $this->getRequest()->ano_letivo;
      $cursoId = $this->getRequest()->curso_id;
      $serieId = $this->getRequest()->serie_id;
      $escolaId = $this->getRequest()->escola_id;
      $turmaId = $this->getRequest()->turma_id;

      // Dados do aluno
      $nomeAluno = Portabilis_String_utils::toLatin1($this->getRequest()->nome_aluno);
      $dataNascAluno = $this->getRequest()->data_nasc_aluno;
      $deficiencias = $this->getRequest()->deficiencias;
      $sexoAluno = $this->getRequest()->sexo_aluno;

      // Dados responsaveis
      $nomeMae = Portabilis_String_utils::toLatin1($this->getRequest()->nome_mae);
      $cpfMae = $this->getRequest()->cpf_mae;
      $nomeResponsavel = Portabilis_String_utils::toLatin1($this->getRequest()->nome_responsavel);
      $cpfResponsavel = $this->getRequest()->cpf_responsavel;

      // Dados do endereço
      $cep = $this->getRequest()->cep;
      $rua = Portabilis_String_utils::toLatin1($this->getRequest()->rua);
      $numero = $this->getRequest()->numero;
      $complemento = Portabilis_String_utils::toLatin1($this->getRequest()->complemento);
      $bairro = Portabilis_String_utils::toLatin1($this->getRequest()->bairro);
      $cidade = Portabilis_String_utils::toLatin1($this->getRequest()->cidade);
      $estado = Portabilis_String_utils::toLatin1($this->getRequest()->estado);
      $pais = Portabilis_String_utils::toLatin1($this->getRequest()->pais);

      $pessoaAlunoId = $this->createPessoa($nomeAluno);
      $pessoaMaeId = null;
      $pessoaResponsavelId = null;

      if(is_numeric($cpfMae)){
        $pessoaMaeId = $this->createOrUpdatePessoaResponsavel($cpfMae, $nomeMae);
        $this->createOrUpdatePessoaFisicaResponsavel($pessoaMaeId, $cpfMae);
      }

      if(is_numeric($cpfResponsavel)){
        $pessoaResponsavelId = $this->createOrUpdatePessoaResponsavel($cpfResponsavel, $nomeResponsavel);
        $this->createOrUpdatePessoaFisicaResponsavel($pessoaResponsavelId, $cpfResponsavel);
      }

      $this->createOrUpdatePessoaFisica($pessoaAlunoId, $pessoaResponsavelId, $pessoaMaeId, $dataNascimento, $sexoAluno);

      $alunoId = $this->createOrUpdateAluno($pessoaAlunoId);

      if(is_array($deficiencias))
        $this->updateDeficiencias($pessoaAlunoId, $deficiencias);

      if($this->_maxAlunosTurma($turmaId) <= $this->_alunosMatriculadosTurma($turmaId)){
      	// $this->messenger->append("max alunos turma: " . $this->_maxAlunosTurma($turmaId) . "alunos matriculados na turma: " . $this->_alunosMatriculadosTurma($turmaId));
      	$this->messenger->append("Aparentemente não existem vagas disponíveis para a seleção informada. Altere a seleção e tente novamente.");
      	return array("cod_matricula" => 0);
  	  }

      return array("cod_matricula" => $this->cadastraMatricula($escolaId, $serieId, $anoLetivo, $cursoId, $alunoId, $turmaId));

      // @TODO CRIAR/GRAVAR ENDEREÇO
    }
  }

  protected function cadastraMatricula($escolaId, $serieId, $anoLetivo, $cursoId, $alunoId, $turmaId){
    $obj = new clsPmieducarMatricula(NULL, NULL,
        $escolaId, $serieId, NULL,
        1, $alunoId, 3, NULL, NULL, 1, $anoLetivo,
        1, NULL, NULL, NULL, NULL, $cursoId,
        NULL, NULL, date('Y-m-d'));

    $matriculaId = $obj->cadastra();
    $enturmacao = new clsPmieducarMatriculaTurma($matriculaId,
                                                 $turmaId,
                                                1,
                                                 1,
                                                 NULL,
                                                 NULL,
                                                 1);
    $enturmacao->data_enturmacao = date('Y-m-d');
    $enturmacao->cadastra();
    return $matriculaId;
  }

  protected function updateDeficiencias($pessoaId, $deficiencias) {
    $sql = "delete from cadastro.fisica_deficiencia where ref_idpes = $1";
    $this->fetchPreparedQuery($sql, $pessoaId, false);

    foreach ($deficiencias as $id) {
      if (! empty($id)) {
        $deficiencia = new clsCadastroFisicaDeficiencia($pessoaId, $id);
        $deficiencia->cadastra();
      }
    }
  }

  protected function createPessoa($nome) {
    $pessoa        = new clsPessoa_();
    $pessoa->nome  = addslashes($nome);

    $pessoa->tipo      = 'F';

    return $pessoa->cadastra();
  }

  protected function createOrUpdatePessoaResponsavel($cpf, $nome) {
    $pessoa        = new clsPessoa_();
    $pessoa->nome  = addslashes($nome);

    $sql = "select idpes from cadastro.fisica WHERE cpf = $1 limit 1";
    $pessoaId = Portabilis_Utils_Database::selectField($sql, $cpf);

    if (! $pessoaId || !$pessoaId > 0) {
      $pessoa->tipo      = 'F';
      $pessoaId          = $pessoa->cadastra();
    }
    else {
      $pessoa->idpes = $pessoaId;
      $pessoa->data_rev  = date('Y-m-d H:i:s', time());
      $pessoa->edita();
    }

    return $pessoaId;
  }

  protected function createOrUpdatePessoaFisica($pessoaId, $pessoaResponsavelId, $pessoaMaeId, $dataNascimento, $sexo) {
    $fisica                       = new clsFisica();
    $fisica->idpes                = $pessoaId;
    $fisica->data_nasc            = $dataNascimento;
    $fisica->idpes_cad            = 1;
    $fisica->sexo                 = strtoupper($sexo);

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if(is_numeric($pessoaResponsavelId))
      $fisica->idpes_responsavel = $pessoaResponsavelId;
    elseif(is_numeric($pessoaMaeId)){
      $fisica->idpes_mae = $pessoaMaeId;
      $fisica->idpes_responsavel = $pessoaMaeId;
    }

    if(is_numeric($pessoaResponsavelId) && is_numeric($pessoaMaeId))
      $fisica->idpes_mae = $pessoaMaeId;

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();
  }

  protected function createOrUpdatePessoaFisicaResponsavel($pessoaId, $cpf) {
    $fisica                       = new clsFisica();
    $fisica->idpes                = $pessoaId;
    $fisica->cpf                  = $cpf;

    $sql = "select 1 from cadastro.fisica WHERE idpes = $1 limit 1";

    if (Portabilis_Utils_Database::selectField($sql, $pessoaId) != 1)
      $fisica->cadastra();
    else
      $fisica->edita();
  }

  protected function createOrUpdateAluno($pessoaId) {
    $aluno                       = new clsPmieducarAluno();
    $aluno->ref_idpes            = $pessoaId;

    $detalhe = $aluno->detalhe();

    if (!$detalhe)
      $retorno = $aluno->cadastra();
    else
      $retorno = $detalhe['cod_aluno'];

    return $retorno;
  }

  protected function _maxAlunosTurma($turmaId){
  	$obj_t = new clsPmieducarTurma($turmaId);
    $det_t = $obj_t->detalhe();
    $maxAlunosTurma = $det_t['max_aluno'];
    return $maxAlunosTurma;
  }

  protected function _alunosMatriculadosTurma($turmaId){
  	$obj_mt = new clsPmieducarMatriculaTurma($turmaId);

    return count(array_filter(($obj_mt->lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = $turmaId,
              								  $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
              								  $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
              								  $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = 1,
              								  $int_ref_cod_serie = $this->ref_cod_serie, $int_ref_cod_curso = NULL,
              								  $int_ref_cod_escola = NULL,
              								  $int_ref_cod_instituicao = $this->getRequest()->instituicao_id))));
  }

  public function Gerar() {
    if ($this->isRequestFor('post', 'matricular-candidato'))
      $this->appendResponse($this->matricularCandidato());
    else
      $this->notImplementedOperationError();
  }
}