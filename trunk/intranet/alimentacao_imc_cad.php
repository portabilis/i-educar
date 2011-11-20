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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - IMC " );
		$this->processoAp = "10006";
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

	var $idimc;
	var $ref_aluno;
	var $ref_serie;
	var $ref_escola;
	var $altura;
	var $peso;
	var $imc;
	var $dt_cadastro;
	var $observacao;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idimc=$_GET["idimc"];
		$this->ref_aluno=$_GET["ref_aluno"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10006, $this->pessoa_logada,3, "alimentacao_imc_lst_lst.php" );

		if( is_numeric( $this->idimc ) )
		{
			$obj_imc = new clsAlimentacaoIMC();
			$lst = $obj_imc->lista($this->idimc);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					$this->fexcluir = $obj_permissoes->permissao_excluir(10006,$this->pessoa_logada,3);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_imc_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_imc_det.php?idimc={$this->idimc}&ref_aluno={$this->ref_aluno}" : "alimentacao_cardapio_lst_lst.php?ref_aluno={$this->ref_aluno}";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idimc", $this->idimc );
		
		$obj_imc = new clsAlimentacaoIMC();

		$lista = $obj_imc->listaAluno($this->ref_aluno);
		if(is_array($lista))
		{
			$nm_aluno = $lista[0]["nome"];
		}
		$this->campoOculto( "ref_aluno", $this->ref_aluno );
		$this->campoRotulo("nm_aluno","Aluno","{$nm_aluno}");
		
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
		$obj_serie = new clsPmieducarSerie();
		$lista = $obj_serie->lista();
		
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$opcoes[$registro["cod_serie"]] = $registro["nm_serie"];
			}			
		}
		
		$this->campoLista( "ref_serie", "Série", $opcoes, $this->ref_serie,"",false,"","","",true );

		$this->campoMonetario( "altura", "Altura(Mt).", $this->altura, 30, 255, true );

		$this->campoMonetario( "peso", "Peso(Kg).", $this->peso, 30, 255, true );
		
		$this->campoMemo( "observacao", "Observações", $this->observacao, 70, 3, false );


	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$caminho  = "arquivos/cardapios/";

		$this->peso = str_replace(",",".",$this->peso);
		$this->altura = str_replace(",",".",$this->altura);
		$obj = new clsAlimentacaoIMC();
		$obj->dt_cadastro = "NOW()";
		$obj->ref_aluno = $this->ref_aluno;
		$obj->ref_escola = $this->ref_escola;
		$obj->ref_serie = $this->ref_serie;
		$obj->altura = $this->altura;
		$obj->peso = $this->peso;
		$obj->observacao = $this->observacao;
		$obj->imc = number_format(($this->peso / ($this->altura * $this->altura)),2,".","");
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_imc_lst_lst.php?ref_aluno={$this->ref_aluno}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoIMC\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoIMC();
		$obj->idimc = $this->idimc;
		$this->peso = str_replace(",",".",$this->peso);
		$this->altura = str_replace(",",".",$this->altura);
		$obj->ref_escola = $this->ref_escola;
		$obj->ref_serie = $this->ref_serie;
		$obj->altura = $this->altura;
		$obj->peso = $this->peso;
		$obj->observacao = $this->observacao;
		$obj->imc = number_format(($this->peso / ($this->altura * $this->altura)),2,".","");
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_imc_det.php?idimc={$this->idimc}&ref_aluno={$ref_aluno}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoIMC\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoIMC();
		$obj->idimc = $this->idimc;
		$obj->exclui();
		
			$this->mensagem .= "Cadastro excluído com sucesso.<br>";
			header( "Location: alimentacao_imc_lst_lst.php?ref_aluno={$ref_aluno}" );
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
