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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Envio Mensal Escola " );
		$this->processoAp = "10005";
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

	var $ideme;
	var $ref_escola;
	var $ano;
	var $mes;
	var $alunos;
	var $dt_cadastro;
	var $pesoouvolume;
	var $dias;
	var $refeicoes;
	var $javascript;
	var $acaodropdonw;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		$this->ideme=$_GET["ideme"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 10005, $this->pessoa_logada,3, "alimentacao_envio_mensal_escola_lst.php" );

		if( is_numeric( $this->ideme ) )
		{
			$obj_envio = new clsAlimentacaoEnvioMensalEscola();
			$lst = $obj_envio->lista($this->ideme);
			
			if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
						$this->$campo = $val;

					//** verificao de permissao para exclusao
					$this->fexcluir = $obj_permissoes->permissao_excluir(10005,$this->pessoa_logada,3);
					//**

					$retorno = "Editar";
				}else{
					header( "Location: alimentacao_envio_mensal_escola_lst.php" );
					die();
				}
			}
		}
		else
		{
			$obj_envio_mensal_padroes = new clsAlimentacaoEnvioMensalPadroes();
			$lista = $obj_envio_mensal_padroes->lista();
			if( is_array( $lista ) && count( $lista ) )
			{
				$this->acaodropdonw = "trocaAnoMes()";
				$this->javascript = '<script language="javascript"> var envioPadroes = new Array();';
				foreach ( $lista AS $registro )
				{
					$this->javascript .= 'envioPadroes['.$registro["ano"].$registro["mes"].']=new Array("'.$registro["dias"].'","'.$registro["refeicoes"].'");';				
				}
				$this->javascript .= '</script>';
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "alimentacao_envio_mensal_escola_det.php?ideme={$registro["ideme"]}" : "alimentacao_envio_mensal_escola_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "ideme", $this->ideme );

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
		for ($i = 2008; $i <= date("Y");$i++)
		{
			$opcoes[$i] = $i;
		}
		$this->campoLista( "ano", "Ano", $opcoes, $this->ano,$this->acaodropdonw,false,"","","",true );
		
		$obj_envio = new clsAlimentacaoEnvioMensalEscola();
		
		$this->campoLista( "mes", "Mês", $obj_envio->getArrayMes(), $this->mes,$this->acaodropdonw,false,"","","",true );
		
		$this->campoNumero( "alunos", "Alunos", $this->alunos, 30, 255, true );
		
		$this->campoNumero( "dias", "Dias", $this->dias, 30, 255, true );
		
		$this->campoNumero( "refeicoes", "Refeições por dia", $this->refeicoes, 30, 255, true );
		
		$obj = new clsAlimentacaoEnvioMensalEscolaProduto();
		$filtro_envio = 0;
		if($this->ideme!="")
		{
			$filtro_envio = $this->ideme;
		}
		$registros = $obj->lista($filtro_envio);
		
		$teste = "<table>";
		$teste .= "<tr><td class='formmdtd'><span class='form'><b>Produto</b></span></td><td class='formmdtd'><span class='form'><b>Quantidade</b></span></td></tr>";
		if( $registros )
		{
			foreach ( $registros AS $campo )
			{
				$prod_cad = "0";
				if ( $campo["ref_envio_mensal_escola"] != "" )
				{
					$prod_cad = "1";
				}
				$campo["pesoouvolume"] = number_format($campo["pesoouvolume"],2,",","");
				$teste .= "<tr><td class='formmdtd'><span class='form'>".$campo["nm_produto"]."</span></td><td><input size='6' maxlength='6' onkeypress='return aceitaNumeros(this)' onKeyup='formataMonetario(this, event);' class='obrigatorio' type='text' name='produto[".$campo["idpro"]."]' value='".$campo["pesoouvolume"]."'><input type='hidden' name='produto_cad[".$campo["idpro"]."]' value='".$prod_cad."'> ".$campo["unidade"]."</td></tr>";
				
			}
		}
		$teste .= "</table>";
		
		$this->campoRotulo("produto","Quantidade por produto",$teste);
		
		
		
		

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoEnvioMensalEscola();
		
		$lst = $obj->lista(null,$this->ref_escola,$this->ano,$this->mes);
		if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					$this->mensagem = "Cadastro não realizado.<br>Envio já cadastrado para a escola e período informado.";
					echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalEscola\n-->";
					return false;
				}
				
			}
		
		$obj->ideme = $this->ideme;
		$obj->dt_cadastro = "NOW()";
		$obj->ref_escola = $this->ref_escola;
		$obj->ano = $this->ano;
		$obj->mes = $this->mes;
		$obj->dias = $this->dias;
		$obj->refeicoes = $this->refeicoes;
		$obj->alunos = $this->alunos;
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$obj = new clsAlimentacaoEnvioMensalEscolaProduto();
			
			if( is_array( $this->produto ) && count( $this->produto ) )
			{
				foreach ( $this->produto AS $campo => $val )
				{
					if($val != "")
					{
						$obj->ref_envio_mensal_escola = $cadastrou;
						$obj->ref_produto = $campo;
						$obj->pesoouvolume = $val;
						$obj->cadastra();
					}
				}			
			
			}
			
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: alimentacao_envio_mensal_escola_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não realizado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalEscola\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		$obj = new clsAlimentacaoEnvioMensalEscola();
		
		$lst = $obj->lista(null,$this->ref_escola,$this->ano,$this->mes);
		if (is_array($lst))
			{
				$registro = array_shift($lst);
				if( $registro )
				{
					if($registro["ideme"]!=$this->ideme)
					{
						$this->mensagem = "Cadastro não realizado.<br>Envio já cadastrado para a escola e período informado.";
						echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalEscola\n-->";
						return false;
					}
				}
				
			}
		
		
		$obj = new clsAlimentacaoEnvioMensalEscola();
		$obj->ideme = $this->ideme;
		$obj->ref_escola = $this->ref_escola;
		$obj->ano = $this->ano;
		$obj->mes = $this->mes;
		$obj->dias = $this->dias;
		$obj->refeicoes = $this->refeicoes;
		$obj->alunos = $this->alunos;
		$cadastrou = $obj->edita();
		if( $cadastrou )
		{
			$obj = new clsAlimentacaoEnvioMensalEscolaProduto();
			
			if( is_array( $this->produto ) && count( $this->produto ) )
			{
				foreach ( $this->produto AS $campo => $val )
				{
					$obj->ref_envio_mensal_escola = $this->ideme;
					$obj->ref_produto = $campo;
					if($val != "")
					{
						$obj->pesoouvolume = str_replace(",",".",$val);
						if($this->produto_cad[$campo]=="1")
						{
							$obj->edita();
						}
						else
						{
							$obj->cadastra();
						}
					}
					else
					{
						$obj->exclui();
					}
				}			
			
			}
			
			$this->mensagem .= "Cadastro editado com sucesso.<br>";
			header( "Location: alimentacao_envio_mensal_escola_det.php?ideme={$this->ideme}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro não editado.<br>";
		echo "<!--\nErro ao cadastrar clsAlimentacaoEnvioMensalEscola\n-->";
		return false;

		
	}

	function Excluir()
	{
		@session_start();
	    $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		
		
		$obj = new clsAlimentacaoEnvioMensalEscolaProduto();
		$obj->ref_envio_mensal_escola = $this->ideme;
		$obj->excluiTudo();
		$obj = new clsAlimentacaoEnvioMensalEscola();
		$obj->ideme = $this->ideme;
		$obj->exclui();

		header( "Location: alimentacao_envio_mensal_escola_lst.php" );
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
<script language="javascript">
function trocaAnoMes()
{
	var ano = document.getElementById("ano").value;
	var mes = document.getElementById("mes").value;
	if(envioPadroes[ano+""+mes])
	{
		document.getElementById("dias").value = envioPadroes[ano+""+mes][0];
		document.getElementById("refeicoes").value = envioPadroes[ano+""+mes][1];
	}
}

function aceitaNumeros(){  
         
       if (document.all) // Internet Explorer  
               var tecla = event.keyCode;  
       else if(document.layers) // Nestcape  
               var tecla = e.which;  
     
       if ((tecla > 47 && tecla < 58)) // numeros de 0 a 9  
           return true;  
       else {  
           if (tecla != 8) // backspace  
               //event.keyCode = 0;  
               return false;  
           else  
               return true;  
       }  
}  
</script>

<?
echo $miolo->javascript;
?>