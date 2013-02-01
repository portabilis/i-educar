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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Usu&aacute;rio" );
		$this->processoAp = "555";
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

	var $cod_usuario;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_tipo_usuario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $nivel_usuario_;

	var $ref_cod_instituicao_;
	var $cod_usuario_;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();


		if($_POST)
		{
			$this->cod_usuario=$_POST["cod_usuario_"];
		}
		else
		{
			$this->cod_usuario=$_GET["cod_usuario"];
		}

		if( is_numeric( $this->cod_usuario ) )
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario);
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_permissoes = new clsPermissoes();
				$this->fexcluir = $obj_permissoes->permissao_excluir( 555, $this->pessoa_logada,7, "educar_usuario_lst.php", true );
				$retorno = "Editar";
			}
		}

		$this->url_cancelar = ($retorno == "Editar") ? "educar_usuario_det.php?cod_usuario={$registro["cod_usuario"]}" : "educar_usuario_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		$obj_permissao = new clsPermissoes();
		$obj_permissao->permissao_cadastra(555,$this->pessoa_logada,7,"educar_usuario_lst.php", true);

		// primary keys
		$this->campoOculto( "cod_usuario", $this->cod_usuario );
		// foreign keys
		$opcoes = array( "" => "Pesquise o funcion&aacute;rio clicando na lupa ao lado" );
		if( $this->cod_usuario )
		{
			$objTemp = new clsFuncionario( $this->cod_usuario );
			$detalhe = $objTemp->detalhe();
			$detalhe = $detalhe["idpes"]->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "cod_usuario_", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "cod_usuario_", "Usu&aacute;rio", $opcoes, $this->cod_usuario, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoUsuario" ) )
		{
			$objTemp = new clsPmieducarTipoUsuario();
			$objTemp->setOrderby('nm_tipo ASC');

			$obj_libera_menu = new clsMenuFuncionario($this->pessoa_logada,false,false,0);
			$obj_super_usuario = $obj_libera_menu->detalhe();

			// verifica se pessoa logada é super-usuario
			if ($obj_super_usuario) {
				$lista = $objTemp->lista(null,null,null,null,null,null,null,null,1);
			}else{
				$lista = $objTemp->lista(null,null,null,null,null,null,null,null,1,$obj_permissao->nivel_acesso($this->pessoa_logada));
			}

			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
					$opcoes_["{$registro['cod_tipo_usuario']}"] = "{$registro['nivel']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoUsuario n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na geração" );
		}
		$tamanho = sizeof($opcoes_);
		echo "<script>\nvar cod_tipo_usuario = new Array({$tamanho});\n";
		foreach ($opcoes_ as $key => $valor)
			echo "cod_tipo_usuario[{$key}] = {$valor};\n";
		echo "</script>";

		$this->campoLista( "ref_cod_tipo_usuario", "Tipo Usu&aacute;rio", $opcoes, $this->ref_cod_tipo_usuario,"",null,null,null,null,true );

		$nivel = $obj_permissao->nivel_acesso($this->cod_usuario);

		$this->campoOculto("nivel_usuario_",$nivel);

		$get_biblioteca			= false;
		$get_escola 			= true;

		$cad_usuario = true;
		include( "include/pmieducar/educar_campo_lista.php" );

		$this->acao_enviar = "valida()";
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		// verifica se usuario é escolar
		if ($this->ref_cod_instituicao && $this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario_, $this->ref_cod_escola, $this->ref_cod_instituicao, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		} // verifica se usuario é institucional
		else if ($this->ref_cod_instituicao && !$this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario_, null, $this->ref_cod_instituicao, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		} // verifica se usuario é poli-institucional
		else if (!$this->ref_cod_instituicao && !$this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario_, null, null, $this->pessoa_logada,  $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		}
		if($obj->existe())
			$cadastrou = $obj->edita();
		else
			$cadastrou = $obj->cadastra();

		// cadastra os menus que o usuario tem acesso
		$obj_menu_func = new clsMenuFuncionario($this->cod_usuario_);
		$obj_menu_func->exclui_todos(55);
		$obj_menu_func->exclui_todos(57);

		//echo $this->cod_usuario;


		$obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario();
		$obj_menu_tipo_ususario_lst = $obj_menu_tipo_usuario->lista($this->ref_cod_tipo_usuario);

		foreach ( $obj_menu_tipo_ususario_lst as $menu )
		{
			$obj_menu_func = new clsMenuFuncionario($this->cod_usuario_,$menu["cadastra"],$menu["exclui"],$menu["ref_cod_menu_submenu"]);
			$obj_menu_func->cadastra();
		}

		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarUsuario\nvalores obrigat&oacute;rios\n is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_tipo_usuario )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		// verifica se usuario é escolar
		if ($this->ref_cod_instituicao && $this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario, $this->ref_cod_escola, $this->ref_cod_instituicao, null, $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		} // verifica se usuario é institucional
		else if ($this->ref_cod_instituicao && !$this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario, null, $this->ref_cod_instituicao, null, $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		} // verifica se usuario é poli-institucional
		else if (!$this->ref_cod_instituicao && !$this->ref_cod_escola)
		{
			$obj = new clsPmieducarUsuario( $this->cod_usuario, null, null, null, $this->pessoa_logada, $this->ref_cod_tipo_usuario,null,null,1 );
		}
		$editou = $obj->edita();

		// cadastra os menus que o usuario tem acesso
		$obj_menu_func = new clsMenuFuncionario($this->cod_usuario);
		$obj_menu_func->exclui_todos(55);
		$obj_menu_func->exclui_todos(57);

		//echo $this->cod_usuario;


		$obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario();
		$obj_menu_tipo_ususario_lst = $obj_menu_tipo_usuario->lista($this->ref_cod_tipo_usuario);
		foreach ( $obj_menu_tipo_ususario_lst as $menu )
		{
			$obj_menu_func = new clsMenuFuncionario($this->cod_usuario,$menu["cadastra"],$menu["exclui"],$menu["ref_cod_menu_submenu"]);
			$obj_menu_func->cadastra();
		}

		if($this->nivel_usuario_ == 8)
		{
			$obj_tipo = new clsPmieducarTipoUsuario($this->ref_cod_tipo_usuario);
			$det_tipo = $obj_tipo->detalhe();
			if($det_tipo['nivel'] != 8){
				$obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
				$lista_bibliotecas_usuario = $obj_usuario_bib->lista(null,$this->pessoa_logada);

				if ($lista_bibliotecas_usuario) {

					foreach ($lista_bibliotecas_usuario as $usuario)
					{
						$obj_usuario_bib = new clsPmieducarBibliotecaUsuario($usuario['ref_cod_biblioteca'],$this->pessoa_logada);
						if(!$obj_usuario_bib->excluir()){
							echo "<!--\nErro ao excluir usuarios biblioteca\n-->";
							return false;
						}
					}
				}
			}
		}

		if($this->ref_cod_instituicao != $this->ref_cod_instituicao_)
		{
			$obj_biblio = new clsPmieducarBiblioteca();
			$lista_biblio_inst = $obj_biblio->lista(null,$this->ref_cod_instituicao_);
			if($lista_biblio_inst)
			{
				foreach ($lista_biblio_inst as $biblioteca) {
					$obj_usuario_bib = new clsPmieducarBibliotecaUsuario($biblioteca['cod_biblioteca'],$this->pessoa_logada);
					$obj_usuario_bib->excluir();
				}
			}
		}

		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarUsuario\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_usuario ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarUsuario($this->cod_usuario, null, null, null, $this->pessoa_logada,null,null,null,0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{


			// cadastra os menus que o usuario tem acesso
			$obj_menu_func = new clsMenuFuncionario($this->cod_usuario);
			$obj_menu_func->exclui_todos(55);
			$obj_menu_func->exclui_todos(57);

			$obj_usuario_bib = new clsPmieducarBibliotecaUsuario();
			$lista_bibliotecas_usuario = $obj_usuario_bib->lista(null,$this->cod_usuario);

			if ($lista_bibliotecas_usuario) {

				foreach ($lista_bibliotecas_usuario as $usuario)
				{
					$obj_usuario_bib = new clsPmieducarBibliotecaUsuario($usuario['ref_cod_biblioteca'],$this->pessoa_logada);
					if(!$obj_usuario_bib->excluir()){
						echo "<!--\nErro ao excluir usuarios biblioteca\n-->";
						return false;
					}
				}
			}

			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarUsuario\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_usuario ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

//var campo_tipo_usuario = document.getElementById("ref_cod_tipo_usuario");
//var campo_instituicao = document.getElementById("ref_cod_instituicao");
//var campo_escola = document.getElementById("ref_cod_escola");
//var campo_biblioteca = document.getElementById("ref_cod_biblioteca");
//
//campo_instituicao.disabled = true;
//campo_escola.disabled = true;
//campo_biblioteca.disabled = true;

var campo_tipo_usuario = document.getElementById("ref_cod_tipo_usuario");
var campo_instituicao = document.getElementById("ref_cod_instituicao");
var campo_escola = document.getElementById("ref_cod_escola");
//var campo_biblioteca = document.getElementById("ref_cod_biblioteca");

if(  campo_tipo_usuario.value == "" )
{
	campo_instituicao.disabled = true;
	campo_escola.disabled = true;
	//campo_biblioteca.disabled = true;

}
else if( cod_tipo_usuario[campo_tipo_usuario.value] == 1 )
{
	campo_instituicao.disabled = true;
	campo_escola.disabled = true;
//	campo_biblioteca.disabled = true;
}
else if( cod_tipo_usuario[campo_tipo_usuario.value] == 2 )
{
	campo_instituicao.disabled = false;
	campo_escola.disabled = true;
//	campo_biblioteca.disabled = true;
}
else if( cod_tipo_usuario[campo_tipo_usuario.value] == 4 )
{
	campo_instituicao.disabled = false;
	campo_escola.disabled = false;
	//campo_biblioteca.disabled = true;
}
else if( cod_tipo_usuario[campo_tipo_usuario.value] == 8 )
{
	campo_instituicao.disabled = false;
	campo_escola.disabled = false;
	//campo_biblioteca.disabled = false;
}

document.getElementById('ref_cod_tipo_usuario').onchange = function()
{
	habilitaCampos();
}

//function getEscola()
//{
//	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
//	var campoEscola = document.getElementById('ref_cod_escola');
//
//	campoEscola.length = 1;
//	for (var j = 0; j < escola.length; j++)
//	{
//		if (escola[j][2] == campoInstituicao)
//		{
//			campoEscola.options[campoEscola.options.length] = new Option( escola[j][1], escola[j][0],false,false);
//		}
//	}
//}

function habilitaCampos()
{
	if( cod_tipo_usuario[campo_tipo_usuario.value] == 1 )
	{
		campo_instituicao.disabled = true;
		campo_escola.disabled = true;
		//campo_biblioteca.disabled = true;
	}
	else if( cod_tipo_usuario[campo_tipo_usuario.value] == 2 )
	{
		campo_instituicao.disabled = false;
		campo_escola.disabled = true;
		//campo_biblioteca.disabled = true;
	}
	else if( cod_tipo_usuario[campo_tipo_usuario.value] == 4 )
	{
		campo_instituicao.disabled = false;
		campo_escola.disabled = false;
		//campo_biblioteca.disabled = true;
	}
	else if( cod_tipo_usuario[campo_tipo_usuario.value] == 8 )
	{
		campo_instituicao.disabled = false;
		campo_escola.disabled = false;
		//campo_biblioteca.disabled = false;
	}
//	else if( campo == "ref_cod_instituicao" &&
//			 cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 4 )
//	{
//		campo_escola.disabled = false;
//		campo_biblioteca.disabled = true;
//		getEscola();
//	}
//	else if( campo == "ref_cod_instituicao" &&
//			 cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 8 )
//	{
//		campo_escola.disabled = false;
//		campo_biblioteca.disabled = false;
//		getEscola();
//	}

}

//function habilitaCampos()
//{
////	var campo_tipo_usuario = document.getElementById("ref_cod_tipo_usuario");
////	var campo_instituicao = document.getElementById("ref_cod_instituicao");
////	var campo_escola = document.getElementById("ref_cod_escola");
////	var campo_biblioteca = document.getElementById("ref_cod_biblioteca");
//
//	if(  campo_tipo_usuario == "" )
//	{
//		campo_instituicao.disabled = true;
//		campo_escola.disabled = true;
//		campo_biblioteca.disabled = true;
//
//	}
//	else if( campo == "ref_cod_tipo_usuario" )
//	{
//		if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 1 ||
//			cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == null )
//		{
//			campo_instituicao.disabled = true;
//			campo_escola.disabled = true;
//			campo_biblioteca.disabled = true;
//		}
//		else if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 2 )
//		{
//			campo_instituicao.disabled = false;
//			campo_escola.disabled = true;
//			campo_biblioteca.disabled = true;
//		}
//		else if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 4  )
//		{
//			campo_instituicao.disabled = false;
//			campo_escola.disabled = false;
//			campo_biblioteca.disabled = true;
//			getEscola();
//		}
//		else if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 8 )
//		{
//			campo_instituicao.disabled = false;
//			campo_escola.disabled = false;
//			campo_biblioteca.disabled = false;
//			getEscola();
//		}
//	}
//	else if( campo == "ref_cod_instituicao" &&
//			 cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 4 )
//	{
//		campo_escola.disabled = false;
//		campo_biblioteca.disabled = true;
//		getEscola();
//	}
//	else if( campo == "ref_cod_instituicao" &&
//			 cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 8 )
//	{
//		campo_escola.disabled = false;
//		campo_biblioteca.disabled = false;
//		getEscola();
//	}
//
//}

function valida()
{
	var campo_tipo_usuario = document.getElementById("ref_cod_tipo_usuario");
	var campo_instituicao = document.getElementById("ref_cod_instituicao");
	var campo_escola = document.getElementById("ref_cod_escola");

	if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 2)
	{
		if( campo_instituicao.options[campo_instituicao.selectedIndex].value == "" )
		{
			alert("É obrigatório a escolha de uma Instituição!");
			return false;
		}
	}
	else if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 4 || campo_tipo_usuario.value == 6)
	{
		if( campo_instituicao.options[campo_instituicao.selectedIndex].value == "" )
		{
			alert("É obrigatório a escolha de uma Instituição!");
			return false;
		}
		else if( cod_tipo_usuario[campo_instituicao.options[campo_instituicao.selectedIndex].value] != "")
		{
			if( campo_escola.options[campo_escola.selectedIndex].value == "" && campo_tipo_usuario.value != 6)
			{
				alert("É obrigatório a escolha de uma Escola!");
				return false;
			}
		}
	}
	else if( cod_tipo_usuario[campo_tipo_usuario.options[campo_tipo_usuario.selectedIndex].value] == 8)
	{
		if( campo_instituicao.options[campo_instituicao.selectedIndex].value == "" )
		{
			alert("É obrigatório a escolha de uma Instituição! ");
			return false;
		}
	}
	if(!acao())
		return;
	document.forms[0].submit();
}

</script>