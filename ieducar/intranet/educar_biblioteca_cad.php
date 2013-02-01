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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Biblioteca" );
		$this->processoAp = "591";
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

	var $cod_biblioteca;
	var $ref_cod_instituicao;
	var $ref_cod_escola;
	var $nm_biblioteca;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $biblioteca_usuario;
	var $ref_cod_usuario;
	var $incluir_usuario;
	var $excluir_usuario;
	var $tombo_automatico;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		$this->tipo_biblioteca = $_SESSION['biblioteca']['tipo_biblioteca'];
		@session_write_close();

		$this->cod_biblioteca=$_GET["cod_biblioteca"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );

		if( is_numeric( $this->cod_biblioteca ) )
		{

			$obj = new clsPmieducarBiblioteca( $this->cod_biblioteca );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 591, $this->pessoa_logada, 3 ) )
				{
					$this->fexcluir = true;
				}
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_biblioteca_det.php?cod_biblioteca={$registro["cod_biblioteca"]}" : "educar_biblioteca_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_biblioteca", $this->cod_biblioteca );

		if( $_POST )
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;

		// foreign keys
		$instituicao_obrigatorio = true;
		$get_escola = true;
		include("include/pmieducar/educar_campo_lista.php");

		// text
		$this->campoTexto( "nm_biblioteca", "Biblioteca", $this->nm_biblioteca, 30, 255, true );
		/*if ($this->tombo_automatico)
			$this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", $this->tombo_automatico);
		else 
			$this->campoBoolLista("tombo_automatico", "Biblioteca possui tombo automático", "t");*/
//		$this->campoCheck("tombo_automatico", "Biblioteca possui tombo automático", dbBool($this->tombo_automatico));

	//-----------------------INCLUI USUARIOS------------------------//
		$this->campoQuebra();

		if ( $_POST["biblioteca_usuario"] )
			$this->biblioteca_usuario = unserialize( urldecode( $_POST["biblioteca_usuario"] ) );
		if( is_numeric( $this->cod_biblioteca ) && !$_POST )
		{
			$obj = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca );
			$registros = $obj->lista( $this->cod_biblioteca );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$this->biblioteca_usuario["ref_cod_usuario_"][] = $campo["ref_cod_usuario"];
				}
			}
		}
		if ( $_POST["ref_cod_usuario"] )
		{
			$this->biblioteca_usuario["ref_cod_usuario_"][] = $_POST["ref_cod_usuario"];
			unset( $this->ref_cod_usuario );
		}

		$this->campoOculto( "excluir_usuario", "" );
		unset($aux);

		if ( $this->biblioteca_usuario )
		{
			foreach ( $this->biblioteca_usuario as $key => $campo )
			{
				if($campo)
				{
					foreach ($campo as $chave => $usuarios)
					{
						if ( $this->excluir_usuario == $usuarios )
						{
							$this->biblioteca_usuario[$chave] = null;
							$this->excluir_usuario = null;
						}
						else
						{
							$obj_cod_usuario = new clsPessoa_( $usuarios );
							$obj_usuario_det = $obj_cod_usuario->detalhe();
							$nome_usuario = $obj_usuario_det['nome'];
							$this->campoTextoInv( "ref_cod_usuario_{$usuarios}", "", $nome_usuario, 30, 255, false, false, false, "", "<a href='#' onclick=\"getElementById('excluir_usuario').value = '{$usuarios}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>" );
							$aux["ref_cod_usuario_"][] = $usuarios;
						}
					}
				}
			}
			unset($this->biblioteca_usuario);
			$this->biblioteca_usuario = $aux;
		}

		$this->campoOculto( "biblioteca_usuario", serialize( $this->biblioteca_usuario ) );


		$opcoes = array( "" => "Selecione" );
		if ($this->ref_cod_instituicao)
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$objTemp = new clsPmieducarUsuario();
				$objTemp->setOrderby("nivel ASC");
				$lista = $objTemp->lista(null,null,$this->ref_cod_instituicao,null,null,null,null,null,null,null,1);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
						$obj_usuario_det = $obj_cod_usuario->detalhe();
						$nome_usuario = $obj_usuario_det['nome'];
						$opcoes["{$registro['cod_usuario']}"] = "{$nome_usuario}";
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarUsuario n&atilde;o encontrada\n-->";
				$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
		}
		/*if( class_exists( "clsPmieducarUsuario" ) )
		{
			// cria array com todos os usuarios escola (nivel 4)
			$usuarios_escola = "user_escola = new Array();\n";
			$objTemp = new clsPmieducarUsuario();
			$objTemp->setOrderby("nivel ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,4);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
					$obj_usuario_det = $obj_cod_usuario->detalhe();
					$nome_usuario = $obj_usuario_det['nome'];
					$usuarios_escola .= "user_escola[user_escola.length] = new Array({$registro["cod_usuario"]},'{$nome_usuario}', {$registro["ref_cod_instituicao"]}, '{$registro["ref_cod_escola"]}');\n";
				}
			}
			echo "<script>{$usuarios_escola}</script>";

			// cria array com todos os usuarios biblioteca (nivel 8)
			$usuarios_biblioteca = "user_biblioteca = new Array();\n";
			$objTemp = new clsPmieducarUsuario();
			$objTemp->setOrderby("nivel ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,null,1,8);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
					$obj_usuario_det = $obj_cod_usuario->detalhe();
					$nome_usuario = $obj_usuario_det['nome'];
					$usuarios_biblioteca .= "user_biblioteca[user_biblioteca.length] = new Array({$registro["cod_usuario"]},'{$nome_usuario}', {$registro["ref_cod_instituicao"]}, '{$registro["ref_cod_escola"]}');\n";
				}
			}
			echo "<script>{$usuarios_biblioteca}</script>";
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarUsuario n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}*/
		$this->campoLista( "ref_cod_usuario", "Usu&aacute;rio", $opcoes, $this->ref_cod_usuario,"",false,"","<a href='#' onclick=\"getElementById('incluir_usuario').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false);

		$this->campoOculto( "incluir_usuario", "" );
//		$this->campoRotulo( "bt_incluir_usuario", "Usu&aacute;rio", "<a href='#' onclick=\"getElementById('incluir_usuario').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_incluir2.gif' title='Incluir' border=0></a>" );

		$this->campoQuebra();
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );
		/*if ($this->tombo_automatico == "on")
			$this->tombo_automatico = "TRUE";
		else
			$this->tombo_automatico = "FALSE";*/
		$obj = new clsPmieducarBiblioteca( null, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
		//-----------------------CADASTRA USUARIOS------------------------//
			$this->biblioteca_usuario = unserialize( urldecode( $this->biblioteca_usuario ) );
			if ($this->biblioteca_usuario)
			{
				foreach ( $this->biblioteca_usuario AS $campo )
				{
					for ($i = 0; $i < sizeof($campo) ; $i++)
					{
						$obj = new clsPmieducarBibliotecaUsuario( $cadastrou, $campo[$i] );
						$cadastrou2  = $obj->cadastra();
						if ( !$cadastrou2 )
						{
							$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
							echo "<!--\nErro ao cadastrar clsPmieducarBibliotecaUsuario\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$campo[$i]} ) \n-->";
							return false;
						}
					}
				}
			}
		//-----------------------FIM CADASTRA USUARIOS------------------------//

			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_biblioteca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarBiblioteca\nvalores obrigatorios\nis_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_cod_escola ) && is_string( $this->nm_biblioteca )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );
		$obj = new clsPmieducarBiblioteca($this->cod_biblioteca, $this->ref_cod_instituicao, $this->ref_cod_escola, $this->nm_biblioteca, null, null, null, null, null, null, 1, null);
		$editou = $obj->edita();
		if( $editou )
		{

		//-----------------------EDITA USUARIOS------------------------//
			$this->biblioteca_usuario = unserialize( urldecode( $this->biblioteca_usuario ) );
			$obj  = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca );
			$excluiu = $obj->excluirTodos();
			if ( $excluiu )
			{
				if ($this->biblioteca_usuario)
				{
					foreach ( $this->biblioteca_usuario AS $campo )
					{
						for ($i = 0; $i < sizeof($campo) ; $i++)
						{
							$obj = new clsPmieducarBibliotecaUsuario( $this->cod_biblioteca, $campo[$i]);
							$cadastrou3  = $obj->cadastra();
							if ( !$cadastrou3 )
							{
								$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
								echo "<!--\nErro ao editar clsPmieducarBibliotecaUsuario\nvalores obrigat&oacute;rios\nis_numeric( $this->cod_biblioteca ) && is_numeric( {$campo[$i]} ) \n-->";
								return false;
							}
						}
					}
				}
			}
		//-----------------------FIM EDITA USUARIOS------------------------//

			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_biblioteca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarBiblioteca\nvalores obrigatorios\nif( is_numeric( $this->cod_biblioteca ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 591, $this->pessoa_logada, 3,  "educar_biblioteca_lst.php" );


		$obj = new clsPmieducarBiblioteca($this->cod_biblioteca, null,null,null,null,null,null,null,null,null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_biblioteca_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarBiblioteca\nvalores obrigatorios\nif( is_numeric( $this->cod_biblioteca ) )\n-->";
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
/*
function getUsuarios(selecao)
{
	var campoUsuario = document.getElementById('ref_cod_usuario');

	campoUsuario.length = 1;
	if (selecao == 1)
	{
		var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

		for (var j = 0; j < user_escola.length; j++)
		{
			if (user_escola[j][2] == campoInstituicao)
			{
				campoUsuario.options[campoUsuario.options.length] = new Option( user_escola[j][1], user_escola[j][0],false,false);
			}
		}
		for (var j = 0; j < user_biblioteca.length; j++)
		{
			if (user_biblioteca[j][2] == campoInstituicao)
			{
				campoUsuario.options[campoUsuario.options.length] = new Option( user_biblioteca[j][1], user_biblioteca[j][0],false,false);
			}
		}
	}
	else if (selecao == 2)
	{
		var campoEscola = document.getElementById('ref_cod_escola').value;

		for (var j = 0; j < user_escola.length; j++)
		{
			if (user_escola[j][3] == campoEscola)
			{
				campoUsuario.options[campoUsuario.options.length] = new Option( user_escola[j][1], user_escola[j][0],false,false);
			}
		}
		for (var j = 0; j < user_biblioteca.length; j++)
		{
			if (user_biblioteca[j][3] == campoEscola)
			{
				campoUsuario.options[campoUsuario.options.length] = new Option( user_biblioteca[j][1], user_biblioteca[j][0],false,false);
			}
		}
	}
}
*/
function getUsuario(xml_usuario)
{
	var campoUsuario = document.getElementById('ref_cod_usuario');
	var DOM_array = xml_usuario.getElementsByTagName( "usuario" );

	if(DOM_array.length)
	{
		campoUsuario.length = 1;
		campoUsuario.options[0].text = 'Selecione um usuário';
		campoUsuario.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoUsuario.options[campoUsuario.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_usuario"),false,false);
		}
	}
	else
		campoUsuario.options[0].text = 'A instituição não possui nenhum usuário';
}

/*
document.getElementById('ref_cod_instituicao').onchange = function()
{
	getUsuarios();
}
*/
/*
before_getEscola = function()
{
	getUsuarios(1);
}
*/
//document.getElementById('ref_cod_instituicao').onchange = function()
before_getEscola = function()
{
//	getUsuarios(1);
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

	var campoUsuario = document.getElementById('ref_cod_usuario');
	campoUsuario.length = 1;
	campoUsuario.disabled = true;
	campoUsuario.options[0].text = 'Carregando usuário';

	var xml_usuario = new ajax( getUsuario );
	xml_usuario.envia( "educar_usuario_xml.php?ins="+campoInstituicao );
}

document.getElementById('ref_cod_escola').onchange = function()
{
//	getUsuarios(2);
	var campoEscola = document.getElementById('ref_cod_escola').value;

	var campoUsuario = document.getElementById('ref_cod_usuario');
	campoUsuario.length = 1;
	campoUsuario.disabled = true;
	campoUsuario.options[0].text = 'Carregando usuário';

	var xml_usuario = new ajax( getUsuario );
	xml_usuario.envia( "educar_usuario_xml.php?esc="+campoEscola );
}
/*
after_getEscola = function()
{
	getUsuarios(2);
}
*/
</script>
