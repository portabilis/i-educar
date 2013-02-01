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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Exemplar" );
		$this->processoAp = "597";
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

	var $cod_exemplar_tipo;
	var $ref_cod_biblioteca;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_exemplar_tipo=$_GET["cod_exemplar_tipo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

		if( is_numeric( $this->cod_exemplar_tipo ) )
		{

			$obj = new clsPmieducarExemplarTipo( $this->cod_exemplar_tipo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				if ($this->cod_exemplar_tipo)
				{
					$obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
					$det_biblioteca = $obj_biblioteca->detalhe();
					$this->ref_cod_instituicao = $det_biblioteca["ref_cod_instituicao"];
					$this->ref_cod_escola = $det_biblioteca["ref_cod_escola"];
				}

				if( $obj_permissoes->permissao_excluir( 597, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_tipo_det.php?cod_exemplar_tipo={$registro["cod_exemplar_tipo"]}" : "educar_exemplar_tipo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_exemplar_tipo", $this->cod_exemplar_tipo );

		if ($this->cod_exemplar_tipo)
		{
			$instituicao_desabilitado = true;
			$escola_desabilitado = true;
			$biblioteca_desabilitado = true;
		}

		// foreign keys
		$get_escola     = 1;
		$escola_obrigatorio = false;
		$get_biblioteca = 1;
		$instituicao_obrigatorio = true;
		$biblioteca_obrigatorio = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_tipo", "Tipo Exemplar", $this->nm_tipo, 30, 255, true );
		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 60, 5, false );

		//-----------------------INICIO CLIENTE TIPO------------------------//

		if( class_exists( "clsPmieducarClienteTipo" ) )
		{
			$opcoes = array( "" => "Selecione" );
//			$todos_tipos_clientes = "tipo_cliente = new Array();\n";
			$todos_tipos_clientes .= "var editar_ = 0;\n";
			if($_GET['cod_exemplar_tipo'])
			{
				$todos_tipos_clientes .= "editar_ = {$_GET['cod_exemplar_tipo']};\n";
			}
			/*$objTemp = new clsPmieducarClienteTipo();
			$objTemp->setOrderby("nm_tipo ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					if ($this->cod_exemplar_tipo && $registro["cod_cliente_tipo"])
					{
						$obj_clt_tp_exp_tp = new clsPmieducarClienteTipoExemplarTipo( $registro["cod_cliente_tipo"], $this->cod_exemplar_tipo );
						$det_clt_tp_exp_tp = $obj_clt_tp_exp_tp->detalhe();
						$dias_emprestimo = $det_clt_tp_exp_tp["dias_emprestimo"];
						if($dias_emprestimo)
						{
							$todos_tipos_clientes .= "tipo_cliente[tipo_cliente.length] = new Array({$registro["cod_cliente_tipo"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_biblioteca"]}, {$dias_emprestimo});\n";
						}
					}
					else
						$todos_tipos_clientes .= "tipo_cliente[tipo_cliente.length] = new Array({$registro["cod_cliente_tipo"]},'{$registro["nm_tipo"]}', {$registro["ref_cod_biblioteca"]});\n";
				}

			}*/
			echo "<script>{$todos_tipos_clientes}{$script}</script>";

			// se o caso é EDITAR
			if ($this->ref_cod_biblioteca)
			{
				$objTemp = new clsPmieducarClienteTipo();
				$objTemp->setOrderby("nm_tipo ASC");
				$lista = $objTemp->lista(null,$this->ref_cod_biblioteca,null,null,null,null,null,null,null,null,1);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_cliente_tipo']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarClienteTipo n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoRotulo( "div_clientes", "Tipo Cliente", "<div id='clientes'></div>" );
		$this->acao_enviar = "Valida();";
		//-----------------------FIM CLIENTE TIPO------------------------
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

		$array_tipos = array();
		foreach ( $_POST AS $key => $cliente_tipo )
		{
			if(substr($key, 0, 5) == "tipo_")
			{
				$array_tipos[substr($key, 5)] = $cliente_tipo;
			}
		}

		$obj = new clsPmieducarExemplarTipo( null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{

		//-----------------------CADASTRA CLIENTE TIPO------------------------//
			if ($array_tipos)
			{
				foreach ( $array_tipos AS $cliente_tipo => $dias_emprestimo )
				{
					$obj = new clsPmieducarClienteTipoExemplarTipo( $cliente_tipo, $cadastrou, $dias_emprestimo );
					$cadastrou2  = $obj->cadastra();
					if ( !$cadastrou2 )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarClienteTipoExemplarTipo\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$cliente_tipo} ) && is_numeric( {$dias_emprestimo} )\n-->";
						return false;
					}
				}
			}
		//-----------------------FIM CADASTRA CLIENTE TIPO------------------------//

			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_exemplar_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarExemplarTipo\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_biblioteca ) && is_numeric( $this->pessoa_logada ) && is_string( $this->nm_tipo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );

		$array_tipos = array();
		foreach ( $_POST AS $key => $cliente_tipo )
		{
			if(substr($key, 0, 5) == "tipo_")
			{
				$array_tipos[substr($key, 5)] = $cliente_tipo;
			}
		}

		$obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
		$editou = $obj->edita();
		if( $editou )
		{

		//-----------------------EDITA CLIENTE TIPO------------------------//
			if ($array_tipos)
			{
				foreach ( $array_tipos AS $cliente_tipo => $dias_emprestimo )
				{
					$obj = new clsPmieducarClienteTipoExemplarTipo( $cliente_tipo, $this->cod_exemplar_tipo, $dias_emprestimo );
					$editou2  = $obj->edita();
					if ( !$editou2 )
					{
						$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
						echo "<!--\nErro ao editar clsPmieducarClienteTipoExemplarTipo\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_exemplar_tipo ) && is_numeric( {$this->pessoa_logada} )\n-->";
						return false;
					}
				}
			}
		//-----------------------FIM EDITA CLIENTE TIPO------------------------//

			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarExemplarTipo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_exemplar_tipo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 597, $this->pessoa_logada, 11,  "educar_exemplar_tipo_lst.php" );


		$obj = new clsPmieducarExemplarTipo($this->cod_exemplar_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_exemplar_tipo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarExemplarTipo\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_exemplar_tipo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

var scriptValida = "";
var retorno = 1;
var divClientes = document.getElementById( "tr_div_clientes" );
setVisibility ('tr_div_clientes', false);

function getClienteTipo()
{
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
  var exemplarTipoId  = document.getElementById('cod_exemplar_tipo').value;

	var xml1 = new ajax(getClienteTipo_XML);

	strURL = "educar_cliente_tipo_xml.php?bib="+campoBiblioteca+"&exemplar_tipo_id="+exemplarTipoId;
	xml1.envia(strURL);
}

function getClienteTipo_XML(xml)
{
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
	var clientes = document.getElementById('clientes');
	var nm_tipo_cliente = document.createElement( "input" );
	var span = document.createElement( "span" );
	var dias_tipo_cliente = document.createElement( "input" );
	var br = document.createElement( "br" );
	var tipos = false;

	clientes.innerHTML = "";
	scriptValida = "";

	var tipo_cliente = xml.getElementsByTagName( "cliente_tipo" );

	var aux = clientes.innerHTML;

	if(tipo_cliente.length)
		setVisibility ('tr_div_clientes', true);

	for (var j = 0; j < tipo_cliente.length; j++)
	{
//		if (tipo_cliente[j][2] == campoBiblioteca)
//		{
//			setVisibility ('tr_div_clientes', true);
			tipos = true;
			clientes.appendChild(nm_tipo_cliente);
			clientes.appendChild(span);
			clientes.appendChild(dias_tipo_cliente);
			clientes.appendChild(br);
			span.innerHTML = "Dias de Empréstimo";
			span.setAttribute( "class", "dias" );
			nm_tipo_cliente.setAttribute( "id", "teste"+j );
			nm_tipo_cliente.setAttribute( 'type', 'text' );
			nm_tipo_cliente.setAttribute( 'disabled', 'true' );
			nm_tipo_cliente.setAttribute( 'class', 'obrigatorio' );
			nm_tipo_cliente.setAttribute( 'style', 'margin: 2px;' );
//			nm_tipo_cliente.setAttribute( 'value', tipo_cliente[j][1] );
			nm_tipo_cliente.setAttribute( 'value', tipo_cliente[j].firstChild.data );

			dias_tipo_cliente.setAttribute( "id", "tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo") );
			dias_tipo_cliente.setAttribute( 'type', 'text' );
			dias_tipo_cliente.setAttribute( 'size', '3' );
			dias_tipo_cliente.setAttribute( 'autocomplete', 'off' );
			dias_tipo_cliente.setAttribute( 'style', 'margin: 2px;' );
			dias_tipo_cliente.setAttribute( 'maxlength', '3' );
			if(tipo_cliente[j].getAttribute("dias_emprestimo"))
				dias_tipo_cliente.setAttribute( 'value', tipo_cliente[j].getAttribute("dias_emprestimo") );
			dias_tipo_cliente.setAttribute( 'class', 'obrigatorio' );

			clientes.innerHTML += aux;

			scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').value )))\n";
			scriptValida +=	"{\n";
			scriptValida +=	"retorno = 0;\n";
			scriptValida +=	"mudaClassName( 'formdestaque', 'formlttd' );\n";
			scriptValida +=	"document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').className = \"formdestaque\";\n";
			scriptValida +=	"alert( 'Preencha o campo \""+tipo_cliente[j].firstChild.data+"\" corretamente!' );\n";
			scriptValida +=	"document.getElementById('tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")+"').focus();\n";
			scriptValida +=	"}\n\n";
			document.getElementById("tipo_"+tipo_cliente[j].getAttribute("cod_cliente_tipo")).name = dias_tipo_cliente.id;
//		}
	}
	if(!tipos)
	{
		setVisibility ('tr_div_clientes', false);
	}
}

function Valida()
{
	eval(scriptValida);
	if (retorno == 0)
	{
		retorno = 1;
		return false;
	}
	acao();
}

if(document.getElementById('ref_cod_biblioteca').type == 'hidden')
{
	getClienteTipo();

}
else
{
	document.getElementById('ref_cod_biblioteca').onchange = function()
	{
		getClienteTipo();
	}

}

if(editar_)
{
	getClienteTipo();
}

</script>
<style>
.dias
{
	padding: 6px;
}
</style>
