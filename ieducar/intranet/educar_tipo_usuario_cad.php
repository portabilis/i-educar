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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Usuario" );
		$this->processoAp = "554";
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

	var $cod_tipo_usuario;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $nm_tipo;
	var $descricao;
	var $nivel;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $permissoes;


	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		//** Verificacao de permissao para cadastro
		$obj_permissao = new clsPermissoes();

		$obj_permissao->permissao_cadastra(554, $this->pessoa_logada,1,"educar_tipo_usuario_lst.php",true);
		//**

		$this->cod_tipo_usuario=$_GET["cod_tipo_usuario"];

		if( is_numeric( $this->cod_tipo_usuario ) )
		{

			$obj = new clsPmieducarTipoUsuario( $this->cod_tipo_usuario );

			if(!$registro = $obj->detalhe()){
				header("location: educar_tipo_usuario_lst.php");
			}

			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;


				//** verificao de permissao para exclusao
				$this->fexcluir = $obj_permissao->permissao_excluir(554,$this->pessoa_logada,1,null,true);
				//**


				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_tipo_usuario_det.php?cod_tipo_usuario={$registro["cod_tipo_usuario"]}" : "educar_tipo_usuario_lst.php";
		$this->nome_url_cancelar = "Cancelar";

		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_tipo_usuario", $this->cod_tipo_usuario );

		// text
		$this->campoTexto( "nm_tipo", "Tipo de Usuário", $this->nm_tipo, 40, 255, true );

		$array_nivel = array( "8" => "Biblioteca",'4' => "Escola", '2' => "Institucional", "1" => "Poli-institucional");

		$this->campoLista( "nivel", "N&iacute;vel",$array_nivel, $this->nivel);

		$this->campoMemo( "descricao", "Descri&ccedil;&atilde;o", $this->descricao, 37, 5, false );
		//$this->campoRotulo("listagem_menu","<b>Permiss&otilde;es de acesso aos menus</b>","");
		//$this->campoQuebra();
		$this->campoRotulo("listagem_menu","<b>Permiss&otilde;es de acesso aos menus</b>","");
		//$this->campoQuebra();
		if( class_exists( "clsBanco" ) )
		{
			$objTemp = new clsBanco();
			$objTemp->Consulta("SELECT sub.cod_menu_submenu
								       ,sub.nm_submenu
								       ,m.nm_menu
								  FROM menu_submenu sub
								       ,menu_menu   m
								 WHERE sub.ref_cod_menu_menu  = m.cod_menu_menu
								   AND (m.cod_menu_menu       = 55 OR m.ref_cod_menu_pai = 55)
								 ORDER BY cod_menu_menu
								 	   ,upper(sub.nm_submenu)
							");
			while($objTemp->ProximoRegistro())
			{
				list ($codigo, $nome,$menu_pai) = $objTemp->Tupla();
				$opcoes[$menu_pai][$codigo] = $nome;
			}

		}
		else
		{
			echo "<!--\nErro\nClasse clsMenuSubmenu nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}

		$array_opcoes = array('' => "Selecione", 'M' => 'Marcar', 'U' => 'Desmarcar');
		$array_opcoes_ = array('' => "Selecione", 'M' => 'Marcar Todos', 'U' => 'Desmarcar Todos');

		$this->campoLista("todos","Op&ccedil;&otilde;es",$array_opcoes_,"","selAction('-','-',this)",false,"","",false,false);
		$script = "menu = new Array();\n";

		foreach ($opcoes as $id_pai => $menu)
		{
			$this->campoQuebra();
			$this->campoRotulo("$id_pai","<b>$id_pai</b>","");

			$this->campoLista("$id_pai 1","Op&ccedil;&otilde;es",$array_opcoes,"","selAction('$id_pai','visualiza',this)",true,"","",false,false);
			$this->campoLista("$id_pai 2","Op&ccedil;&otilde;es",$array_opcoes,"","selAction('$id_pai','cadastra',this)",true,"","",false,false);
			$this->campoLista("$id_pai 3","Op&ccedil;&otilde;es",$array_opcoes,"","selAction('$id_pai','exclui',this)",false,"","",false,false);

			$script .= "menu['$id_pai'] = new Array();\n";

			foreach ($menu as $id => $submenu)
			{
				$obj_menu_tipo_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$id);
				$obj_menu_tipo_usuario->setCamposLista("cadastra","visualiza","exclui");
				$obj_det = $obj_menu_tipo_usuario->detalhe();
				if($this->tipoacao == "Novo")
					$obj_det["visualiza"] = $obj_det["cadastra"] = $obj_det["exclui"] = 1;

				$script .= "menu['$id_pai'][menu['$id_pai'].length] = $id; \n";

				$this->campoCheck("permissoes[{$id}][visualiza]", $submenu, $obj_det["visualiza"],"Visualizar",true,false);
				$this->campoCheck("permissoes[{$id}][cadastra]", $submenu, $obj_det["cadastra"],"Cadastrar",true);
				$this->campoCheck("permissoes[{$id}][exclui]", $submenu, $obj_det["exclui"],"Excluir",false);

				$this->campoOculto("permissoes[{$id}][id]",$id);
			}

		}
		echo "<script>{$script}</script>";

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
//echo  "{$this->cod_tipo_usuario}, {$this->pessoa_logada}, null, {$this->nm_tipo}, {$this->descricao}, {$this->nivel}, null, null, 1";
		$obj = new clsPmieducarTipoUsuario( $this->cod_tipo_usuario, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, $this->nivel, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->cod_tipo_usuario =  $cadastrou;
			//**
			//echo "<pre>";
			//print_r($this->permissoes);die;
			if($this->permissoes)
			{
				/**
				 * LIMPA A TABELA
				 */
					$obj_menu_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$key,$valor['cadastra'],$valor['visualiza'],$valor['exclui']);
					$obj_menu_usuario->excluirTudo();
				/**
				 *
				 */

				foreach ($this->permissoes as $key => $valor)
				{
					$valor['cadastra'] = $valor['cadastra'] == "on" ? 1 : 0;
					$valor['visualiza'] = $valor['visualiza'] == "on" ? 1 : 0;
					$valor['exclui'] = $valor['exclui'] == "on" ? 1 : 0;

					if($valor['cadastra'] || $valor['visualiza'] || $valor['exclui'])
					{
						$obj_menu_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$key,$valor['cadastra'],$valor['visualiza'],$valor['exclui']);

					/*	if($obj_menu_usuario->detalhe())
						{
							$editou = $obj_menu_usuario->edita();
							if(!$editou){
								$this->mensagem .= "Erro ao editar acessos aos menus.<br>";
								return false;
							}
						}
						else
						{*/
							if(!$obj_menu_usuario->cadastra())
							{
								$this->mensagem .= "Erro ao cadastrar acessos aos menus.<br>";
								return false;
							}
						//}
					}
				}
			}
			//**

			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_tipo_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarTipoUsuario\nvalores obrigatorios\nis_numeric( $ref_funcionario_cad ) && is_string( $nm_tipo ) && is_numeric( $nivel ) && is_string( $data_cadastro ) && is_numeric( $ativo )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoUsuario($this->cod_tipo_usuario,null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->nivel, null, null, 1);
		$editou = $obj->edita();
		if( $editou )
		{
			//**
			//echo "<pre>";
			//print_r($this->permissoes);die;
			if($this->permissoes)
			{
				/**
				 * LIMPA A TABELA
				 */
					$obj_menu_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$key,$valor['cadastra'],$valor['visualiza'],$valor['exclui']);
					$obj_menu_usuario->excluirTudo();
				/**
				 *
				 */
				foreach ($this->permissoes as $key => $valor)
				{
					$valor['cadastra'] = $valor['cadastra'] == "on" ? 1 : 0;
					$valor['visualiza'] = $valor['visualiza'] == "on" ? 1 : 0;
					$valor['exclui'] = $valor['exclui'] == "on" ? 1 : 0;
					if($valor['cadastra'] || $valor['visualiza'] || $valor['exclui'])
					{
						$this->cod_tipo_usuario =  $this->cod_tipo_usuario == false ? "0" : $this->cod_tipo_usuario;
						$obj_menu_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$key,$valor['cadastra'],$valor['visualiza'],$valor['exclui']);

						/*if($obj_menu_usuario->detalhe())
						{
							$editou = $obj_menu_usuario->edita();
							if(!$editou){
								$this->mensagem .= "Erro ao editar acessos aos menus.<br>";
								return false;
							}
						}
						else
						{*/
							if(!$obj_menu_usuario->cadastra())
							{
								$this->mensagem .= "Erro ao cadastrar acessos aos menus.<br>";
								return false;
							}
						//}
					}
				}
			}
			//**

			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_tipo_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarTipoUsuario\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_usuario ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarTipoUsuario($this->cod_tipo_usuario, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, $this->nivel, null, null, 0);

		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";


			$obj_menu_usuario = new clsPmieducarMenuTipoUsuario($this->cod_tipo_usuario,$key,$valor['cadastra'],$valor['visualiza'],$valor['exclui']);
			$obj_menu_usuario->excluirTudo();



			header( "Location: educar_tipo_usuario_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarTipoUsuario\nvalores obrigatorios\nif( is_numeric( $this->cod_tipo_usuario ) && is_numeric( $this->ref_funcionario_exc ) )\n-->";
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
function selAction(menu_pai,tipo,acao){

	var element = document.getElementsByTagName('input');
	var state;

	switch(acao.value){
		case "M":
			state = true;
		break;
		case "U":
			state = false;
		break
		default:
			return false;
	}

	acao.selectedIndex = 0;

	if(menu_pai == "-" && tipo == "-"){
		for(var ct=0;ct< element.length;ct++){

			if(element[ct].getAttribute('type')=='checkbox')
			 	element[ct].checked = state;

		}
		return;
	}


	for(var ct=0;ct< menu[menu_pai].length;ct++){

		document.getElementsByName('permissoes[' + menu[menu_pai][ct]  + '][' + tipo + ']')[0].checked = state;

	}

}
</script>