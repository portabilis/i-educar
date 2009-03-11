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

	var $ref_cod_turma_origem;
	var $ref_cod_turma_destino;
	var $ref_cod_curso;

	var $sequencial;

	function Inicializar()
	{
	//	print_r($_POST);die;
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//$this->ref_cod_turma=$_GET["ref_cod_turma"];
		if(!$_POST)
		{
			header("location: educar_matricula_lst.php");
			die;
		}

		foreach ($_POST as $key =>$value) {
			$this->$key = $value;
		}

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php" );

		if( is_numeric( $this->ref_cod_matricula ) )
		{
			//echo "$obj = is_numeric( $this->ref_cod_turma_origem ) new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $this->ref_cod_turma_destino,$this->pessoa_logada,$this->pessoa_logada,null,null,1 );";die;
			if(is_numeric( $this->ref_cod_turma_origem ) )
			{
				$obj_matricula_turma = new clsPmieducarMatriculaTurma();
				$lst_matricula_turma = $obj_matricula_turma->lista( $this->ref_cod_matricula, null, null, null, null, null, null, null, 1 );
				if( $lst_matricula_turma ) 
				{
					foreach( $lst_matricula_turma AS $matricula ) 
					{
						$obj = new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $matricula['ref_cod_turma'], $this->pessoa_logada,null,null,null,0,null,$matricula['sequencial']/*,$this->ref_cod_turma_destino*/ );
						$registro  = $obj->detalhe();
						if( $registro )
						{
		
							if(!$obj->edita())
							{
								echo "erro ao cadastrar";
								die;
							}
							//header("location: educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
		
						}

					}
				}
								//else
			//	{

					$obj = new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $this->ref_cod_turma_destino,$this->pessoa_logada,$this->pessoa_logada,null,null,1 );
					$cadastrou = $obj->cadastra();
					if( $cadastrou )
					{
						$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
						header("location: educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
						die();
					}

				//}

			}
			else
				{
					$obj = new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $this->ref_cod_turma_destino,$this->pessoa_logada,$this->pessoa_logada,null,null,1 );
					$cadastrou = $obj->cadastra();
					if( $cadastrou )
					{
						$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
						header("location: educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
						die();

					}

				}
		}

		header("location: educar_matricula_lst.php");;
		die;
		//$this->url_cancelar = ($retorno == "Editar") ? "educar_matricula_turma_det.php?ref_cod_matricula={$registro["ref_cod_matricula"]}&ref_cod_turma={$registro["ref_cod_turma"]}" : "educar_matricula_turma_lst.php";
		//$this->nome_url_cancelar = "Cancelar";
		//return $retorno;
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