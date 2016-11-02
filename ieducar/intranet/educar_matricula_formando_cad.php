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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Matricula Turma" );
		$this->processoAp = "578";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	var $ref_cod_matricula;

	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $formando;

	function Inicializar()
	{
	//	print_r($_POST);die;
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//$this->ref_cod_turma=$_GET["ref_cod_turma"];


		foreach ($_GET as $key =>$value) {
			$this->$key = $value;
		}

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php" );

		if( is_numeric( $this->ref_cod_matricula ) && is_numeric($this->formando))
		{

			$obj = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,null,null,null,null,null,null,null,$this->formando);
			$registro  = $obj->detalhe();
			if( $registro )
			{

				if(!$obj->edita())
				{
					echo "erro ao cadastrar";
					die;
				}
				$des = "";
				if(!$this->formando)
					$des = "des";
				echo "<script>alert('Matrícula {$des}marcada como formando com sucesso!'); window.location='educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}';</script>";

			}

		}

		header("location: educar_matricula_lst.php");;
		die;

	}

	function Gerar()
	{

		die;

	}

	function Novo()
	{

	}

	function Editar()
	{

	}

	function Excluir()
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