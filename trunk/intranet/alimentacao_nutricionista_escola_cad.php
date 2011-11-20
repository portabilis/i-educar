<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	*																	     *
	*	@author Prefeitura Municipal de Itaja?								 *
	*	@updated 29/03/2007													 *
	*   Pacote: i-PLB Software P?blico Livre e Brasileiro					 *
	*																		 *
	*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja?			 *
	*						ctima@itajai.sc.gov.br					    	 *
	*																		 *
	*	Este  programa  ?  software livre, voc? pode redistribu?-lo e/ou	 *
	*	modific?-lo sob os termos da Licen?a P?blica Geral GNU, conforme	 *
	*	publicada pela Free  Software  Foundation,  tanto  a vers?o 2 da	 *
	*	Licen?a   como  (a  seu  crit?rio)  qualquer  vers?o  mais  nova.	 *
	*																		 *
	*	Este programa  ? distribu?do na expectativa de ser ?til, mas SEM	 *
	*	QUALQUER GARANTIA. Sem mesmo a garantia impl?cita de COMERCIALI-	 *
	*	ZA??O  ou  de ADEQUA??O A QUALQUER PROP?SITO EM PARTICULAR. Con-	 *
	*	sulte  a  Licen?a  P?blica  Geral  GNU para obter mais detalhes.	 *
	*																		 *
	*	Voc?  deve  ter  recebido uma c?pia da Licen?a P?blica Geral GNU	 *
	*	junto  com  este  programa. Se n?o, escreva para a Free Software	 *
	*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
	*	02111-1307, USA.													 *
	*																		 *
	* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once( "include/alimentacao/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Nutricionista Escola " );
		$this->processoAp = "10001";
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

	var $ref_escola;
	var $ref_usuario;
	var $dt_cadastro;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->ref_usuario=$_GET["ref_usuario"];
		$this->ref_escola=$_GET["ref_escola"];


		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10001, $this->pessoa_logada,3, "alimentacao_nutricionista_escola_lst.php" );

		if( is_numeric( $this->ref_usuario ) && is_numeric( $this->ref_escola ) )
		{
			$obj_nutricionista_escola = new clsAlimentacaoNutricionistaEscola();
			$lst = $obj_nutricionista_escola->lista($this->ref_usuario,$this->ref_escola);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					$this->fexcluir = $obj_permissoes->permissao_excluir(10001,$this->pessoa_logada,3);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_nutricionista_escola_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_nutricionista_escola_det.php?ref_escola={$registro["ref_escola"]}&ref_usuario={$registro["ref_usuario"]}" : "alimentacao_nutricionista_escola_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$opcoes = array();
		$obj_escola = new clsPmieducarEscola();
		$lista = $obj_escola->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["cod_escola"]] = $registro["nome"];
			}			
		}
		
		$this->campoLista( "ref_escola", "Escola", $opcoes, $this->ref_escola,"",false,"","","",true );
		
		$opcoes = array();
		$obj_usuario = new clsPmieducarUsuario();
		$lista = $obj_usuario->lista(null,null,null,null,null,7);
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$obj_pessoa = new clsPessoa_($registro["cod_usuario"]);
				$det_pessoa = $obj_pessoa->detalhe();
				$nm_pessoa = $det_pessoa["nome"];
				$opcoes[$registro["cod_usuario"]] = $nm_pessoa;
			}
			
		}
		
		$this->campoLista( "ref_usuario", "Nutricionista", $opcoes, $this->ref_usuario,"",false,"","","",true );
		
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		if(false)
		{
			$this->mensagem = "Cadastro não realizado.<br>Dados já cadastrados.";
			echo "<!--\nErro ao cadastrar clsAlimentacaoNutricionistaEscola\Dados já cadastrados-->";
			return false;
		}

		$obj = new clsAlimentacaoNutricionistaEscola();
		$obj->dt_cadastro = "NOW()";
		$obj->ref_usuario= $this->ref_usuario;
		$obj->ref_escola = $this->ref_escola;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_nutricionista_escola_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoNutricionistaEscola\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$this->mensagem = "Cadastro não editado.<br>Este cadastro não pode ser editado.";
		echo "<!--\nErro ao cadastrar clsAlimentacaoNutricionistaEscola\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoNutricionistaEscola();
		$obj->ref_usuario= $this->ref_usuario;
		$obj->ref_escola = $this->ref_escola;
		$cadastrou = $obj->exclui();
		
		$this->mensagem = "Cadastro excluído com sucesso.<br>";
		header( "Location: alimentacao_nutricionista_escola_lst.php" );
		die();
		return true;
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
