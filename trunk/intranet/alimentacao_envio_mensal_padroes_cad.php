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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Envio Mensal Padrões " );
		$this->processoAp = "10011";
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

	var $idemp;
	var $ano;
	var $mes;
	var $dias;
	var $refeicoes;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->idemp=$_GET["idemp"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10011, $this->pessoa_logada,3, "alimentacao_envio_mensal_padroes_lst.php" );

		if( is_numeric( $this->idemp ) )
		{
			$obj_envio_mensal_padroes = new clsAlimentacaoEnvioMensalPadroes();
			$lst = $obj_envio_mensal_padroes->lista($this->idemp);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					$this->fexcluir = $obj_permissoes->permissao_excluir(10011,$this->pessoa_logada,3);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_envio_mensal_padroes_lst.php" );
					die();
				}
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_envio_mensal_padroes_det.php?idemp={$registro["idemp"]}" : "alimentacao_envio_mensal_padroes_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idemp", $this->idemp );		
		
		
		$opcoes = array();
		for ($i = 2008; $i <= date("Y");$i++)
		{
			$opcoes[$i] = $i;
		}
		$this->campoLista( "ano", "Ano", $opcoes, $this->ano,"",false,"","","",true );
		
		$obj_envio_mensal_padroes = new clsAlimentacaoEnvioMensalPadroes();
		
		$this->campoLista( "mes", "Mês", $obj_envio_mensal_padroes->getArrayMes(), $this->mes,"",false,"","","",true );
		
		$this->campoNumero( "dias", "Dias", $this->dias, 30, 255, true );

		$this->campoNumero( "refeicoes", "Refeições por dia", $this->refeicoes, 30, 255, true );
		
		

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		

		$obj = new clsAlimentacaoEnvioMensalPadroes();
		$obj->ano = $this->ano;
		$obj->mes = $this->mes;
		$obj->dias = $this->dias;
		$obj->refeicoes = $this->refeicoes;		
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_envio_mensal_padroes_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalPadroes\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoEnvioMensalPadroes();
		$obj->idemp = $this->idemp;
		$obj->ano = $this->ano;
		$obj->mes = $this->mes;
		$obj->dias = $this->dias;
		$obj->refeicoes = $this->refeicoes;	
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_envio_mensal_padroes_det.php?idemp={$this->idemp}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalPadroes\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsAlimentacaoEnvioMensalPadroes();
		$obj->idemp= $this->idemp;
		$excluiu = $obj->exclui();
		if( $excluiu )
		{
			$this->mensagem .= "Cadastro excluído com sucesso.<br>";
			header( "Location: alimentacao_envio_mensal_padroes_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não excluído.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalPadroes\n-->";
		return false;

		return false;
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
