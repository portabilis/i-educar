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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matricula Turma" );
		$this->processoAp = "578";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $ref_cod_matricula;
	var $ref_cod_turma;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_serie;
	var $ref_cod_escola;
	var $ref_cod_turma_origem;
	var $ref_cod_curso;

	var $sequencial;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Matricula Turma - Detalhe";
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		foreach ($_POST as $key =>$value) {
			$this->$key = $value;
		}
		//print_r($_POST);
		$obj_mat_turma = new clsPmieducarMatriculaTurma();
		$det_mat_turma = $obj_mat_turma->lista($this->ref_cod_matricula,null,null,null,null,null,null,null,1);

		if($det_mat_turma){
			$det_mat_turma = array_shift($det_mat_turma);
			$obj_turma = new clsPmieducarTurma($det_mat_turma['ref_cod_turma']);
			$det_turma = $obj_turma->detalhe();
			$this->nm_turma = $det_turma['nm_turma'];

			$this->ref_cod_turma_origem = $det_turma['cod_turma'];
			$this->sequencial = $det_mat_turma['sequencial'];
		}
		$tmp_obj = new clsPmieducarMatriculaTurma( );
		$lista = $tmp_obj->lista(null,$this->ref_cod_turma,null,null,null,null,null,null,1);

		$total_alunos = 0;
		if( $lista )
		{
			$total_alunos = count($lista);
		}


		$tmp_obj = new clsPmieducarTurma();
		$lst_obj = $tmp_obj->lista( $this->ref_cod_turma );
		$registro = array_shift($lst_obj);

		$this->ref_cod_curso = $registro['ref_cod_curso'];

		if( ! $registro  || !$_POST)
		{
			header( "location: educar_matricula_lst.php" );
			die();
		}

		if( class_exists( "clsPmieducarTurmaTipo" ) )
		{
			$obj_ref_cod_turma_tipo = new clsPmieducarTurmaTipo( $registro["ref_cod_turma_tipo"] );
			$det_ref_cod_turma_tipo = $obj_ref_cod_turma_tipo->detalhe();
			$registro["ref_cod_turma_tipo"] = $det_ref_cod_turma_tipo["nm_tipo"];
		}
		else
		{
			$registro["ref_cod_turma_tipo"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarTurmaTipo\n-->";
		}

		if( class_exists( "clsPmieducarInstituicao" ) )
		{
			$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
			$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
			$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
		}
		else
		{
			$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
		}

		if( class_exists( "clsPmieducarEscola" ) )
		{
			$obj_ref_cod_escola = new clsPmieducarEscola( $registro["ref_ref_cod_escola"] );
			$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
			$registro["ref_ref_cod_escola"] = $det_ref_cod_escola["nome"];
		}
		else
		{
			$registro["ref_cod_escola"] = "Erro na gera&ccedil;&atilde;o";
			echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarEscola\n-->";
		}

		if( class_exists( "clsPmieducarCurso" ) )
		{
			$obj_ref_cod_curso = new clsPmieducarCurso( $registro["ref_cod_curso"] );
			$det_ref_cod_curso = $obj_ref_cod_curso->detalhe();
			$registro["ref_cod_curso"] = $det_ref_cod_curso["nm_curso"];
			$padrao_ano_escolar = $det_ref_cod_curso["padrao_ano_escolar"];
		}
		else
		{
			$registro["ref_cod_curso"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarCurso\n-->";
		}

		if( class_exists( "clsPmieducarSerie" ) )
		{
			$obj_ser = new clsPmieducarSerie( $registro["ref_ref_cod_serie"] );
			$det_ser = $obj_ser->detalhe();
			$registro["ref_ref_cod_serie"] = $det_ser["nm_serie"];
		}
		else
		{
			$registro["ref_ref_cod_serie"] = "Erro na geracao";
			echo "<!--\nErro\nClasse nao existente: clsPmieducarSerie\n-->";
		}

		/**
		 * Busca dados da matricula
		 */
			if( class_exists( "clsPmieducarMatricula" ) )
			{
				$obj_ref_cod_matricula = new clsPmieducarMatricula();
				$detalhe_aluno = array_shift($obj_ref_cod_matricula->lista($this->ref_cod_matricula));
			}
			else
			{
				$registro["ref_cod_matricula"] = "Erro na geracao";
				echo "<!--\nErro\nClasse nao existente: clsPmieducarMatricula\n-->";
			}

			$obj_aluno = new clsPmieducarAluno();
			$det_aluno = array_shift($det_aluno = $obj_aluno->lista($detalhe_aluno['ref_cod_aluno'],null,null,null,null,null,null,null,null,null,1));

			$obj_escola = new clsPmieducarEscola($this->ref_cod_escola,null,null,null,null,null,null,null,null,null,1);
			$det_escola = $obj_escola->detalhe();


			$this->addDetalhe(array("Nome do Aluno",$det_aluno['nome_aluno']));

		/**
		 *
		 */

			$objTemp = new clsPmieducarTurma($this->ref_cod_turma);
			$det_turma = $objTemp->detalhe();


		if( $registro["ref_ref_cod_escola"] )
		{
			$this->addDetalhe( array( "Escola", "{$registro["ref_ref_cod_escola"]}") );
		}
		if( $registro["ref_cod_curso"] )
		{
			$this->addDetalhe( array( "Curso", "{$registro["ref_cod_curso"]}") );
		}
		if( $registro["ref_ref_cod_serie"] )
		{
			$this->addDetalhe( array( "S&eacute;rie", "{$registro["ref_ref_cod_serie"]}") );
		}

		$this->addDetalhe(array("Turma atual",$this->nm_turma));

		if( $registro["nm_turma"] )
		{
			$this->addDetalhe( array( "Turma destino" , "{$registro["nm_turma"]}") );
		}
		if( $registro["max_aluno"] )
		{
			$this->addDetalhe( array( "Total de vagas", "{$registro["max_aluno"]}") );
		}

		if( is_numeric($total_alunos))
		{
			$this->addDetalhe( array( "Alunos nesta turma", "{$total_alunos}") );

			$this->addDetalhe( array( "Vagas restantes", $registro["max_aluno"] - $total_alunos) );
		}

		$this->addDetalhe(array("-","
			<form name='formcadastro' method='post' action='educar_matricula_turma_cad.php'>
				<input type='hidden' name='ref_cod_matricula' value=''>
				<input type='hidden' name='ref_cod_serie' value=''>
				<input type='hidden' name='ref_cod_escola' value=''>
				<input type='hidden' name='ref_cod_turma_origem' value='{$this->ref_cod_turma_origem}'>
				<input type='hidden' name='ref_cod_turma_destino' value=''>
				<input type='hidden' name='sequencial' value='$this->sequencial'>
			</form>
		"));

		if($registro["max_aluno"] - $total_aluno <= 0)
		{
			$valida = "if(!confirm('Atenção,\\nturma sem vagas (lotada)!!\\nDeseja continuar com a enturmação mesmo assim?')) return false";
		}
		else
		{
			$valida = "if(!confirm('Atenção,\\nConfirma a enturmação?')) return false";
		}
		$script = "<script>

			function enturmar(ref_cod_matricula,ref_cod_turma_destino){
			{$valida}
				document.formcadastro.ref_cod_matricula.value = ref_cod_matricula;
				document.formcadastro.ref_cod_turma_destino.value = ref_cod_turma_destino;

				document.formcadastro.submit();

			}
			</script>";
		echo $script;
		$script = "";
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7 ) )
		{

			$script = "enturmar({$this->ref_cod_matricula},{$this->ref_cod_turma})";
			$this->array_botao = array('Transferir Aluno');
			$this->array_botao_url_script = array("{$script}");

		}

			$this->array_botao[] = 'Voltar';
			$this->array_botao_url_script[] = "go(\"educar_matricula_turma_lst.php?ref_cod_matricula={$this->ref_cod_matricula}\");";

		$this->largura = "100%";
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

