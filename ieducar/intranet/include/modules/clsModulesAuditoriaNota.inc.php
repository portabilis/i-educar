<?php
// error_reporting(E_ERROR);
// ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestÃ£o escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de ItajaÃ­
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa Ã© software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo
 * sob os termos da LicenÃ§a PÃºblica Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versÃ£o 2 da LicenÃ§a, como (a seu critÃ©rio)
 * qualquer versÃ£o posterior.
 *
 * Este programa Ã© distribuÃ­Â­do na expectativa de que seja Ãºtil, porÃ©m, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implÃ­Â­cita de COMERCIABILIDADE OU
 * ADEQUAÃÃO A UMA FINALIDADE ESPECÃFICA. Consulte a LicenÃ§a PÃºblica Geral
 * do GNU para mais detalhes.
 *
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral do GNU junto
 * com este programa; se nÃ£o, escreva para a Free Software Foundation, Inc., no
 * endereÃ§o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2015
 * @version   $Id$
 */

require_once( "include/pmieducar/geral.inc.php" );

/**
 * clsModulesAuditoria class.
 *
 * @author    Gabriel Matos de Souza <gabriel@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2015
 * @version   @@package_version@@
 */

class clsModulesAuditoriaNota {
	var $notaAntiga;
	var $notaNova;
	var $stringNotaAntiga;
	var $stringNotaNova;
	var $usuario;
	var $operacao;
	var $rotina;
	var $dataHora;
	var $turma;

	const OPERACAO_INCLUSAO = 1;
	const OPERACAO_ALTERACAO = 2;
	const OPERACAO_EXCLUSAO = 3;

	function clsModulesAuditoriaNota($notaAntiga, $notaNova, $turmaId){

		//Foi necessÃ¡rio enviar turma pois nÃ£o Ã© possÃ­vel saber a turma atual somente atravÃ©s da matrÃ­cula
		$this->turma = $turmaId;

		$this->usuario = $this->getUsuarioAtual();
		$this->rotina = "notas";

		$this->notaAntiga = $notaAntiga;
		$this->notaNova = $notaNova;

		if(!is_null($this->notaAntiga)){
			$this->stringNotaAntiga = $this->montaStringInformacoes($this->montaArrayInformacoes($this->notaAntiga));
		}

		if(!is_null($this->notaNova)){
			$this->stringNotaNova = $this->montaStringInformacoes($this->montaArrayInformacoes($this->notaNova));
		}

		$this->dataHora = date('Y-m-d H:i:s');

	}

	public function cadastra(){

		$db = new clsBanco();
		$this->_schema = "modules.";
		$this->_tabela = "{$this->_schema}auditoria";
		$separador = "";
		$valores = "";

		if(!is_null($this->stringNotaAntiga) && !is_null($this->stringNotaNova)){
			$this->operacao = self::OPERACAO_ALTERACAO;
		}elseif(!is_null($this->stringNotaAntiga) && is_null($this->stringNotaNova)){
			$this->operacao = self::OPERACAO_EXCLUSAO;
		}elseif(is_null($this->stringNotaAntiga) && !is_null($this->stringNotaNova)){
			$this->operacao = self::OPERACAO_INCLUSAO;
		}

		if(is_string($this->usuario)){
			$campos .= "{$separador}usuario";
			$valores .= "{$separador}'{$this->usuario}'";
			$separador = ", ";
		}

		$campos .= "{$separador}operacao";
		$valores .= "{$separador}'{$this->operacao}'";
		$separador = ", ";

		$campos .= "{$separador}rotina";
		$valores .= "{$separador}'{$this->rotina}'";
		$separador = ", ";

		if(is_string($this->stringNotaAntiga)){
			$campos .= "{$separador}valor_antigo";
			$valores .= "{$separador}'{$this->stringNotaAntiga}'";
			$separador = ", ";
		}

		if(is_string($this->stringNotaNova)){
			$campos .= "{$separador}valor_novo";
			$valores .= "{$separador}'{$this->stringNotaNova}'";
			$separador = ", ";
		}

		$campos .= "{$separador}data_hora";
		$valores .= "{$separador}'{$this->dataHora}'";
		$separador = ", ";

		$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );

	}


	private function montaStringInformacoes($arrayInformacoes){
		if(empty($arrayInformacoes)){return null;}

		$stringDados = "";
		$separadorDados = ",";
		$separadorInformacoes = ":";
		$inicioString = "{";
		$fimString = "}";

		$stringDados .= $inicioString;

		foreach($arrayInformacoes as $campo => $valor){
			$stringDados .= $campo;
			$stringDados .= $separadorInformacoes;
			$stringDados .= $valor;
			$stringDados .= $separadorDados;
		}

		//remove o Ãºltimo valor, qual seria uma vÃ­rgula
		$stringDados = substr($stringDados, 0, -1);

		$stringDados .= $fimString;

		return $stringDados;
	}

	private function montaArrayInformacoes($nota){

		if(!($nota instanceof Avaliacao_Model_NotaComponente)){return null;}
			$componenteCurricularId = $nota->get('componenteCurricular');
			$componenteCurricular = $this->getNomeComponenteCurricular($componenteCurricularId);

			$notaAlunoId = $nota->get('notaAluno');

			$arrayInformacoes = $this->getInfosMatricula($notaAlunoId);

			$arrayInformacoes += array("nota" => $nota->notaArredondada,
																 "etapa" => $nota->etapa,
																 "componenteCurricular" => $componenteCurricular);

			return $arrayInformacoes;

	}

	private function getNomeComponenteCurricular($componenteCurricularId){
		$mapper = new ComponenteCurricular_Model_ComponenteDataMapper();
		$componenteCurricular = $mapper->find($componenteCurricularId)->nome;

		return $componenteCurricular;
	}

	private function getInfosMatricula($notaAlunoId){
		$mapper = new Avaliacao_Model_NotaAlunoDataMapper();
		$matriculaId = $mapper->find($notaAlunoId)->matricula;

		$objMatricula = new clsPmieducarMatricula($matriculaId);
		$detMatricula = $objMatricula->detalhe();

		$instituicaoId = $detMatricula["ref_cod_instituicao"];
		$escolaId = $detMatricula["ref_ref_cod_escola"];
		$cursoId = $detMatricula["ref_cod_curso"];
		$serieId = $detMatricula["ref_ref_cod_serie"];
		$alunoId = $detMatricula["ref_cod_aluno"];
		$turmaId = $this->turma;

		$nomeInstitucao = $this->getNomeInstituicao($instituicaoId);
		$nomeEscola = $this->getNomeEscola($escolaId);
		$nomeCurso = $this->getNomeCurso($cursoId);
		$nomeSerie = $this->getNomeSerie($serieId);
		$nomeAluno = $this->getNomeAluno($alunoId);
		$nomeTurma = $this->getNomeTurma($turmaId);

		return array("instituicao" => $nomeInstitucao,
								 "instituicao_id" => $instituicaoId,
								 "escola" => $nomeEscola,
								 "escola_id" => $escolaId,
								 "curso" => $nomeCurso,
								 "curso_id" => $cursoId,
								 "serie" => $nomeSerie,
								 "serie_id" => $serieId,
								 "turma" => $nomeTurma,
								 "turma_id" => $turmaId,
								 "aluno" => $nomeAluno,
								 "aluno_id" => $alunoId);

	}
	private function getNomeInstituicao($instituicaoId){
		$objInstituicao = new clsPmieducarInstituicao($instituicaoId);
		$detInstituicao = $objInstituicao->detalhe();
		$nomeInstitucao = $detInstituicao["nm_instituicao"];

		return $nomeInstitucao;
	}
	private function getNomeEscola($escolaId){
		$objEscola = new clsPmieducarEscola($escolaId);
		$detEscola = $objEscola->detalhe();
		$nomeEscola = $detEscola["nome"];

		return $nomeEscola;
	}
	private function getNomeCurso($cursoId){
		$objCurso = new clsPmieducarCurso($cursoId);
		$detCurso = $objCurso->detalhe();
		$nomeCurso = $detCurso["nm_curso"];

		return $nomeCurso;
}
	private function getNomeSerie($serieId){
		$objSerie = new clsPmieducarSerie($serieId);
		$detSerie = $objSerie->detalhe();
		$nomeSerie = $detSerie["nm_serie"];

		return $nomeSerie;
	}

	private function getNomeAluno($alunoId){
		$objAluno = new clsPmieducarAluno($alunoId);
		$detAluno = $objAluno->detalhe();
		$pessoaId = $detAluno["ref_idpes"];

		$objPessoa = new clsPessoa_($pessoaId);
		$detPessoa = $objPessoa->detalhe();
		$nomePessoa = $detPessoa["nome"];

		$nomePessoa = Portabilis_String_Utils::toLatin1($nomePessoa);

		return $nomePessoa;

	}
	private function getNomeTurma($turmaId){
		$objTurma = new clsPmieducarTurma($turmaId);
		$detTurma = $objTurma->detalhe();
		$nomeTurma = $detTurma["nm_turma"];

		return $nomeTurma;
	}
	private function getUsuarioAtual(){
		@session_start();
   	$pessoaId = $_SESSION['id_pessoa'];
   	@session_write_close();
   	$objFuncionario = new clsFuncionario($pessoaId);
   	$detFuncionario = $objFuncionario->detalhe();
   	$matricula = $detFuncionario["matricula"];

   	return $pessoaId . " - " . $matricula;
	}

}
?>