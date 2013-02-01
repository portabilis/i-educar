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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Calendario Ano Letivo" );
		$this->processoAp = "620";
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

	var $cod_calendario_ano_letivo;
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ano;
	var $data_cadastra;
	var $data_exclusao;
	var $ativo;
	var $inicio_ano_letivo;
	var $termino_ano_letivo;

	var $ref_cod_instituicao;


	function Inicializar()
	{
		$retorno = "Novo";

		@session_start();
			$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_calendario_ano_letivo=$_GET["cod_calendario_ano_letivo"];
		$this->ref_cod_escola=$_GET["ref_cod_escola"];
		$this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_ano_letivo_lst.php" );
	//	$this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
		//$this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

		if( is_numeric( $this->cod_calendario_ano_letivo ) )
		{
			$obj = new clsPmieducarCalendarioAnoLetivo( $this->cod_calendario_ano_letivo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_escola = new clsPmieducarEscola($this->ref_cod_escola);
				$obj_det = $obj_escola->detalhe();

				/*
				$this->inicio_ano_letivo = dataFromPgToBr( $this->inicio_ano_letivo );
				$this->termino_ano_letivo = dataFromPgToBr( $this->termino_ano_letivo );
				*/
				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 620, $this->pessoa_logada, 7 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}

		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro["cod_calendario_ano_letivo"]}" : "educar_calendario_ano_letivo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_calendario_ano_letivo", $this->cod_calendario_ano_letivo );

		/*$obj_anos = new clsPmieducarEscolaAnoLetivo();
		$lista_ano = $obj_anos->lista(null,null,null,null,2,null,null,null,null,1);
		if($lista_ano)
		{
			$script = "<script>
					  var ar_anos = new Array();
						";

			foreach ($lista_ano as $ano) {

				$script .= "ar_anos[ar_anos.length] = new Array('{$ano['ref_cod_escola']}','{$ano['ano']}');\n";
			}

			echo $script .= "</script>";
		}*/

		if($_GET)
		{
			$this->ref_cod_escola=$_GET["ref_cod_escola"];
			$this->ref_cod_instituicao=$_GET["ref_cod_instituicao"];
		}
		$get_escola = 1;
		$obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		$this->url_cancelar = ($retorno == "Editar") ? "educar_calendario_ano_letivo_det.php?cod_calendario_ano_letivo={$registro["cod_calendario_ano_letivo"]}" : "educar_calendario_ano_letivo_lst.php";

//		$ano_array = array();
		$ano_array = array( "" => "Selecione um ano" );
		if($this->ref_cod_escola)
		{
			$obj_anos = new clsPmieducarEscolaAnoLetivo();
			$lista_ano = $obj_anos->lista($this->ref_cod_escola,null,null,null,2,null,null,null,null,1);
			if($lista_ano)
			{
				foreach ($lista_ano as $ano)
				{
					$ano_array["{$ano['ano']}"] = $ano['ano'];
				}
			}
		}
		else
			$ano_array = array( "" => "Selecione uma escola" );
		// text
//		$conc = ",";
//		$anos = array( "" => "Selecione" );
//		$ano_atual = date("Y");
//		$lim = 5;
//		for($a = date('Y') ; $a < $ano_atual + $lim ; $a++ )
//			if(!key_exists($a,$ano_array))
//				$anos["{$a}"] = "{$a}";
//			else
//				$lim++;


		$this->campoLista( "ano", "Ano",$ano_array, $this->ano,"",false );

	//	if($this->ref_cod_escola)
	//		$this->campoOculto("ref_cod_escola",$this->ref_cod_escola);

		//if($this->ref_cod_instituicao)
	//	$this->campoOculto("ref_cod_instituicao",$this->ref_cod_instituicao);

		// data
//		$this->campoData( "inicio_ano_letivo", "Inicio Ano Letivo", $this->inicio_ano_letivo, true );
//		$this->campoData( "termino_ano_letivo", "Termino Ano Letivo", $this->termino_ano_letivo, true );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_ano_letivo_lst.php" );

		$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
		$data_inicio = $obj_ano_letivo_modulo->menorData( $this->ano, $this->ref_cod_escola );
		$data_fim = $obj_ano_letivo_modulo->maiorData( $this->ano, $this->ref_cod_escola );

		if ( $data_inicio && $data_fim)
		{
			$obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo();
			$lst_calend_ano_letivo = $obj_calend_ano_letivo->lista( null,$this->ref_cod_escola,null,null,$this->ano );
			if ($lst_calend_ano_letivo)
			{
				$det_calend_ano_letivo = array_shift($lst_calend_ano_letivo);

				$obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo( $det_calend_ano_letivo['cod_calendario_ano_letivo'], $this->ref_cod_escola, $this->pessoa_logada, null, $this->ano, null, null, 1/*, $data_inicio,$data_fim*/ );
				if( $obj_calend_ano_letivo->edita() )
				{
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_calendario_ano_letivo_lst.php" );
					die();
					return true;
				}

				$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
				echo "<!--\nErro ao editar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nif( is_numeric( {$det_calend_ano_letivo['cod_calendario_ano_letivo']} ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
				return false;
			}
			else
			{
				$obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo( null, $this->ref_cod_escola, null, $this->pessoa_logada, $this->ano, null, null, 1/*, $data_inicio,$data_fim*/ );
				if( $obj_calend_ano_letivo->cadastra() )
				{
					$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
					header( "Location: educar_calendario_ano_letivo_lst.php?ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}" );
					die();
					return true;
				}

				$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
				echo "<!--\nErro ao cadastrar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ano ) && is_string( $data_inicio ) && is_string( $data_fim )\n-->";
				return false;
			}

		}

		echo "<script> alert( 'Não foi possível definir as datas de início e fim do ano letivo.' ) </script>";
		return false;

		/*
		$obj = new clsPmieducarCalendarioAnoLetivo();
		$lista = $obj->lista( null,$this->ref_cod_escola,null,null,$this->ano );
		if($lista)
		{
			echo "<script>alert('Calend&aacute;rio j&aacute; cadastrado para essa escola');</script>";
			return false;
		}
		else
		{
			$inicio  = explode("/", $this->inicio_ano_letivo);
			$termino = explode("/", $this->termino_ano_letivo);
			if(($inicio[2] != $this->ano) && ($termino[2] != $this->ano)){
				echo "<script>alert('Verifique o inicio e o termino do ano letivo!\\n Possivel causa: Ano das datas diferem do ano');</script>";
				return false;
			}elseif(($inicio[1] > $termino[1]) || ($inicio[1] == $termino[1] && ($inicio[0] < $termino[0]))){
				echo "<script>alert('Verifique o inicio e o termino do ano letivo!\\n Possivel causa: Data final anterior a data inicial');</script>";
				return false;
			}
		}
		$obj = new clsPmieducarCalendarioAnoLetivo( $this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, $this->pessoa_logada, $this->ano, $this->data_cadastra, $this->data_exclusao, $this->ativo, $this->inicio_ano_letivo, $this->termino_ano_letivo );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_calendario_ano_letivo_lst.php?ref_cod_escola={$this->ref_cod_escola}&ref_cod_instituicao={$this->ref_cod_instituicao}&ano={$this->ano}" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nis_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ano ) && is_string( $this->inicio_ano_letivo ) && is_string( $this->termino_ano_letivo )\n-->";
		return false;
		*/
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 620, $this->pessoa_logada, 7,  "educar_calendario_ano_letivo_lst.php" );

		$obj_ano_letivo_modulo = new clsPmieducarAnoLetivoModulo();
		$data_inicio = $obj_ano_letivo_modulo->menorData( $this->ano, $this->ref_cod_escola );
		$data_fim = $obj_ano_letivo_modulo->maiorData( $this->ano, $this->ref_cod_escola );

		if ( $data_inicio && $data_fim)
		{
			$obj_calend_ano_letivo = new clsPmieducarCalendarioAnoLetivo( $this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, null, $this->ano, null, null, 1/*, $data_inicio,$data_fim*/ );
			if( $obj_calend_ano_letivo->edita() )
			{
				$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
				header( "Location: educar_calendario_ano_letivo_lst.php" );
				die();
				return true;
			}

			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nif( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
			return false;
		}

		echo "<script> alert( 'Não foi possível definir as datas de início e fim do ano letivo.' ) </script>";
		return false;

		/*
		$obj = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, $this->pessoa_logada, $this->ano, $this->data_cadastra, $this->data_exclusao, $this->ativo, $this->inicio_ano_letivo, $this->termino_ano_letivo);
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_calendario_ano_letivo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nif( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
		*/
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 620, $this->pessoa_logada, 7,  "educar_calendario_ano_letivo_lst.php" );


		$obj = new clsPmieducarCalendarioAnoLetivo($this->cod_calendario_ano_letivo, $this->ref_cod_escola, $this->pessoa_logada, $this->pessoa_logada, $this->ano, $this->data_cadastra, $this->data_exclusao, 0 );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_calendario_ano_letivo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarCalendarioAnoLetivo\nvalores obrigatorios\nif( is_numeric( $this->cod_calendario_ano_letivo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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
<script>
<? echo "var anoAtual=".date("Y").";\n"; ?>

after_getEscola = function()
{
	var campoAno = document.getElementById('ano').length = 1;
}

document.getElementById('ref_cod_escola').onchange = function geraAnos()
{
	var campoEscola = document.getElementById('ref_cod_escola');

//		campoAno.length = 1;
	//	campoAno.options[0].value = anoAtual;
	//	campoAno.options[0].text = anoAtual;

	var campoAno = document.getElementById('ano');
	campoAno.length = 1;
	campoAno.disabled = true;
	campoAno.options[0].text = 'Carregando ano';

	if(campoEscola.value == '')
		return;

	var xml1 = new ajax(loadFromXML);
	strURL = "educar_escola_ano_letivo_xml.php?esc="+campoEscola.value+"&lim=5&ano_atual="+anoAtual;
	xml1.envia(strURL);
}

function loadFromXML(xml)
{
	var campoAno = document.getElementById('ano');

	//var num_anos = 5;
	//var achou;
	//for(var ct = anoAtual ;ct < anoAtual + num_anos;ct++){
		//achou = false;
		/*
		var ar_anos = xml.getElementsByTagName( "ano" );
		for(var c = 0; c < ar_anos.length;c++)
		{
			//if(ar_anos[c][1] == ct){
			campoAno.options[campoAno.length] = new Option( ar_anos[c].firstChild.nodeValue, ar_anos[c].firstChild.nodeValue, false, false );
				//num_anos++;
				//achou = true;
			//}
		}
		if(campoAno.length == 1)
		{
			campoAno.options[0].text = 'Escola não possui anos letivos';
		}
		///if(!achou)
			//campoAno.options[campoAno.length] = new Option( ct, ct, false, false );
	//}
	*/
	var DOM_array = xml.getElementsByTagName( "ano" );

	if(DOM_array.length)
	{
		campoAno.length = 1;
		campoAno.options[0].text = 'Selecione um ano';
		campoAno.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoAno.options[campoAno.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].firstChild.data,false,false);
		}
	}
	else
		campoAno.options[0].text = 'A escola não possui nenhum ano letivo';
}
</script>
