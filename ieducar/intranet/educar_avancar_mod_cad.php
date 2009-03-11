<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itajaí								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software Público Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  é  software livre, você pode redistribuí-lo e/ou	 *
	*	modificá-lo sob os termos da Licença Pública Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a versão 2 da	 *
	*	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.	 *
	*																		 *
	*	Este programa  é distribuído na expectativa de ser útil, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-	 *
	*	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU	 *
	*	junto  com  este  programa. Se não, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar" );
		$this->processoAp = "561";
	}
}

class indice extends clsCadastro
{

	var $pessoa_logada;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		return $retorno;
	}

	function Gerar()
	{
		$instituicao_obrigatorio = true;
		$escola_obrigatorio = true;
		$curso_obrigatorio = true;
		$escola_curso_serie_obrigatorio = true;
		$turma_obrigatorio = true;
		$get_escola = true;
		$get_curso = true;
		$get_escola_curso_serie = true;
		$get_turma = true;
		$get_cursos_nao_padrao = true;
		include("include/pmieducar/educar_campo_lista.php");
	}

	function Novo()
	{

		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		
		$db = new clsBanco();
		$db2 = new clsBanco();
		
		$ano = $db2->CampoUnico("SELECT MAX(ano) FROM pmieducar.escola_ano_letivo WHERE ref_cod_escola = {$this->ref_cod_escola} AND andamento=1");
		if (!is_numeric($ano))
			$ano = date("Y");
		//aprovados
		$db->Consulta("SELECT cod_matricula, ref_cod_aluno FROM pmieducar.matricula m, pmieducar.matricula_turma WHERE aprovado = '1' AND m.ativo = '1' AND ref_ref_cod_escola = '{$this->ref_cod_escola}' AND ref_ref_cod_serie='{$this->ref_ref_cod_serie}' AND ref_cod_curso = '$this->ref_cod_curso' AND cod_matricula = ref_cod_matricula AND ref_cod_turma = '$this->ref_cod_turma' ");
		while ($db->ProximoRegistro()) 
		{
			list($cod_matricula, $ref_cod_aluno) = $db->Tupla();
			
			$prox_mod = $db2->campoUnico("SELECT ref_serie_destino FROM pmieducar.sequencia_serie WHERE ref_serie_origem = '{$this->ref_ref_cod_serie}' AND ativo = '1' ");
			
						
			if(is_numeric($prox_mod))
			{
				//aqui localizar o proximo curso
				$ref_cod_curso = $db2->CampoUnico("SELECT ref_cod_curso FROM pmieducar.serie WHERE cod_serie = {$prox_mod}");
				$db2->Consulta("UPDATE pmieducar.matricula SET ultima_matricula = '0' WHERE cod_matricula = '$cod_matricula'");
//				$ano = date("Y");

				$db2->Consulta("INSERT INTO 
									pmieducar.matricula 
									(ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula) 
									VALUES 
									('{$this->ref_cod_escola}', '$prox_mod', '{$this->pessoa_logada}', '$ref_cod_aluno', '3', 'NOW()', '$ano', '{$ref_cod_curso}', '1' )
									");
			}
			
		}
		
		//reprovados
		$db->Consulta("SELECT cod_matricula, ref_cod_aluno, ref_ref_cod_serie FROM pmieducar.matricula, pmieducar.matricula_turma WHERE aprovado = '2' AND ref_ref_cod_escola = '{$this->ref_cod_escola}' AND ref_ref_cod_serie='{$this->ref_ref_cod_serie}' AND cod_matricula = ref_cod_matricula AND ref_cod_turma = '$this->ref_cod_turma'");
		while ($db->ProximoRegistro()) 
		{
			list($cod_matricula, $ref_cod_aluno, $ref_cod_serie) = $db->Tupla();
			$db2->Consulta("UPDATE pmieducar.matricula SET ultima_matricula = '0' WHERE cod_matricula = '$cod_matricula'");
//			$ano = date("Y");
			$db2->Consulta("INSERT INTO 
									pmieducar.matricula 
									(ref_ref_cod_escola, ref_ref_cod_serie, ref_usuario_cad, ref_cod_aluno, aprovado, data_cadastro, ano, ref_cod_curso, ultima_matricula) 
									VALUES 
									('{$this->ref_cod_escola}', '$ref_cod_serie', '{$this->pessoa_logada}', '$ref_cod_aluno', '3', 'NOW()', '$ano', '{$this->ref_cod_curso}', '1' )
									");
		}
		$this->mensagem = "Rematrícula efetuada com sucesso!";
		return true;
	}

	function Editar()
	{

		
	}
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
<script>

document.getElementById('ref_cod_escola').onchange = function()
{
	getEscolaCurso();
}

document.getElementById('ref_cod_curso').onchange = function()
{
	getEscolaCursoSerie();
}

document.getElementById('ref_ref_cod_serie').onchange = function()
{
	getTurma();
}

</script>
