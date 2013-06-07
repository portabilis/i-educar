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
require_once "include/clsBase.inc.php";
require_once "include/clsCadastro.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Obras" );
		$this->processoAp = "598";
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

	var $cod_acervo;
	var $ref_cod_exemplar_tipo;
	var $ref_cod_acervo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_acervo_colecao;
	var $ref_cod_acervo_idioma;
	var $ref_cod_acervo_editora;
	var $titulo_livro;
	var $sub_titulo;
	var $cdu;
	var $cutter;
	var $volume;
	var $num_edicao;
	var $ano;
	var $num_paginas;
	var $isbn;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	var $checked;

	var $acervo_autor;
	var $ref_cod_acervo_autor;
	var $principal;
	var $incluir_autor;
	var $excluir_autor;

	var $colecao;
	var $editora;
	var $idioma;
	var $autor;

  protected function setSelectionFields()
  {

  }

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_acervo=$_GET["cod_acervo"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );

		if( is_numeric( $this->cod_acervo ) )
		{

			$obj = new clsPmieducarAcervo( $this->cod_acervo );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
				$obj_det = $obj_biblioteca->detalhe();

				$this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
				$this->ref_cod_escola = $obj_det["ref_cod_escola"];


				$obj_permissoes = new clsPermissoes();
				if( $obj_permissoes->permissao_excluir( 598, $this->pessoa_logada, 11 ) )
				{
					$this->fexcluir = true;
				}

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_acervo_det.php?cod_acervo={$registro["cod_acervo"]}" : "educar_acervo_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		if( $_POST )
		{
			foreach( $_POST AS $campo => $val )
				$this->$campo = ( $this->$campo ) ? $this->$campo : $val;
		}
		if(is_numeric($this->colecao))
		{
			$this->ref_cod_acervo_colecao = $this->colecao;
		}
		if(is_numeric($this->editora))
		{
			$this->ref_cod_acervo_editora = $this->editora;
		}
		if(is_numeric($this->idioma))
		{
			$this->ref_cod_acervo_idioma = $this->idioma;
		}
		if(is_numeric($this->autor))
		{
			$this->ref_cod_acervo_autor = $this->autor;
		}

		// primary keys
		$this->campoOculto( "cod_acervo", $this->cod_acervo );
		$this->campoOculto( "colecao", "" );
		$this->campoOculto( "editora", "" );
		$this->campoOculto( "idioma", "" );
		$this->campoOculto( "autor", "" );

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca', 'bibliotecaTipoExemplar'));

    // Obra referência
		$opcoes = array( "NULL" => "Selecione" );

		if( $this->ref_cod_acervo && $this->ref_cod_acervo != "NULL")
		{
			$objTemp = new clsPmieducarAcervo($this->ref_cod_acervo);
			$detalhe = $objTemp->detalhe();
			if ( $detalhe )
			{
				$opcoes["{$detalhe['cod_acervo']}"] = "{$detalhe['titulo']}";
			}
		}

		$this->campoLista("ref_cod_acervo","Obra Refer&ecirc;ncia",$opcoes,$this->ref_cod_acervo,"",false,"","<img border=\"0\" onclick=\"pesquisa();\" id=\"ref_cod_acervo_lupa\" name=\"ref_cod_acervo_lupa\" src=\"imagens/lupa.png\"\/>",false,false);

    // Coleção
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarAcervoColecao" ) )
		{
			$objTemp = new clsPmieducarAcervoColecao();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_acervo_colecao']}"] = "{$registro['nm_colecao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarAcervoColecao nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_acervo_colecao", "Cole&ccedil;&atilde;o", $opcoes, $this->ref_cod_acervo_colecao,"",false,"","<img id='img_colecao' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(500, 200,'educar_acervo_colecao_cad_pop.php',[], 'Coleção')\" />",false,false );

    // Idioma
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarAcervoIdioma" ) )
		{
			$objTemp = new clsPmieducarAcervoIdioma();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_acervo_idioma']}"] = "{$registro['nm_idioma']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarAcervoIdioma nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_acervo_idioma", "Idioma", $opcoes, $this->ref_cod_acervo_idioma, "", false, "", "<img id='img_idioma' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(400, 150,'educar_acervo_idioma_cad_pop.php',[], 'Idioma')\" />" );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarAcervoEditora" ) )
		{
			$objTemp = new clsPmieducarAcervoEditora();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_acervo_editora']}"] = "{$registro['nm_editora']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarAcervoEditora nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_acervo_editora", "Editora", $opcoes, $this->ref_cod_acervo_editora, "", false, "", "<img id='img_editora' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(400, 320,'educar_acervo_editora_cad_pop.php',[], 'Editora')\" />" );


		//-----------------------INCLUI AUTOR------------------------//
		$this->campoQuebra();

		if ( $_POST["acervo_autor"] )
			$this->acervo_autor = unserialize( urldecode( $_POST["acervo_autor"] ) );
		if( is_numeric( $this->cod_acervo ) && !$_POST )
		{
			$obj = new clsPmieducarAcervoAcervoAutor();
			$registros = $obj->lista( null, $this->cod_acervo );
			if( $registros )
			{
				foreach ( $registros AS $campo )
				{
					$aux["ref_cod_acervo_autor_"]= $campo["ref_cod_acervo_autor"];
					$aux["principal_"]= $campo["principal"];
					$this->acervo_autor[] = $aux;
				}

				// verifica se ja existe um autor principal
				if ( is_array($this->acervo_autor) )
				{
					foreach ($this->acervo_autor AS $autores)
					{
						if ($autores["principal_"] == 1)
						{
							$this->checked = 1;
							$this->campoOculto( "checked", $this->checked );
						}
					}
				}
			}
		}

		unset($aux);

		if ( $_POST["ref_cod_acervo_autor"] )
		{
			if ( $_POST["principal"] )
			{
				$this->checked = 1;
				$this->campoOculto( "checked", $this->checked );
			}
			$aux["ref_cod_acervo_autor_"] = $_POST["ref_cod_acervo_autor"];
			$aux["principal_"] = $_POST["principal"];
			$this->acervo_autor[] = $aux;

//			echo "<pre>";print_r($this->acervo_autor);

			// verifica se ja existe um autor principal
			if ( is_array($this->acervo_autor) )
			{
				foreach ($this->acervo_autor AS $autores)
				{
					if ($autores["principal_"] == 'on')
					{
						$this->checked = 1;
						$this->campoOculto( "checked", $this->checked );
					}
				}
			}
			unset( $this->ref_cod_acervo_autor );
			unset( $this->principal );
		}

		$this->campoOculto( "excluir_autor", "" );
		unset($aux);

		if ( $this->acervo_autor )
		{
			foreach ( $this->acervo_autor as $key => $autor)
			{
				if ( $this->excluir_autor == $autor["ref_cod_acervo_autor_"] )
				{
					unset($this->acervo_autor[$key]);
					unset($this->excluir_autor);
				}
				else
				{
					$obj_acervo_autor = new clsPmieducarAcervoAutor($autor["ref_cod_acervo_autor_"]);
					$det_acervo_autor = $obj_acervo_autor->detalhe();
					$nm_autor = $det_acervo_autor["nm_autor"];
					$this->campoTextoInv( "ref_cod_exemplar_tipo_{$autor["ref_cod_acervo_autor_"]}", "", $nm_autor, 30, 255, false, false, true );
					$this->campoCheck( "principal_{$autor["ref_cod_acervo_autor_"]}", "", $autor['principal_'], "<a href='#' onclick=\"getElementById('excluir_autor').value = '{$autor["ref_cod_acervo_autor_"]}'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bola_xis.gif' title='Excluir' border=0></a>", false, false, false );
					$aux["ref_cod_acervo_autor_"] = $autor["ref_cod_acervo_autor_"];
					$aux["principal_"] = $autor['principal_'];
				}
			}
		}
		$this->campoOculto( "acervo_autor", serialize( $this->acervo_autor ) );

		if( class_exists( "clsPmieducarAcervoAutor" ) )
		{
			$opcoes = array( "" => "Selecione" );
			$objTemp = new clsPmieducarAcervoAutor();
			$objTemp->setOrderby("nm_autor ASC");
			$lista = $objTemp->lista(null,null,null,null,null,null,null,null,null,1);
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_acervo_autor']}"] = "{$registro['nm_autor']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarAcervoAutor n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		if ( is_array($this->acervo_autor) )
		{
			$qtd_autor = count($this->acervo_autor);
		}
		// não existe um autor principal nem autor
		if ( ($this->checked != 1) && ( !$qtd_autor || ($qtd_autor == 0) ) )
		{
//			die("1");
			$this->campoLista( "ref_cod_acervo_autor", "Autor", $opcoes, $this->ref_cod_acervo_autor,null,true,"","",false,true );

		 	$this->campoCheck( "principal", "&nbsp;&nbsp;<img id='img_autor' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"showExpansivelImprimir(500, 250,'educar_acervo_autor_cad_pop.php',[], 'Autor')\" />", $this->principal,"<a href='#' onclick=\"getElementById('incluir_autor').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );
		}
		// não existe um autor principal, mas existe um autor
		else if ( ($this->checked != 1) && ($qtd_autor > 0) )
		{
			$this->campoLista( "ref_cod_acervo_autor", "Autor", $opcoes, $this->ref_cod_acervo_autor,null,true,null, null,null,false);
		 	$this->campoCheck( "principal", "&nbsp;&nbsp;<img src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"pesquisa_valores_popless( 'educar_acervo_autor_cad_pop.php' )\" />", $this->principal,"<a href='#' onclick=\"getElementById('incluir_autor').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>" );
		}
		// existe um autor principal
		else
		{
//			die("3");
			$this->campoLista( "ref_cod_acervo_autor", "Autor", $opcoes, $this->ref_cod_acervo_autor,"",false,"","<img src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"pesquisa_valores_popless( 'educar_acervo_autor_cad_pop.php' )\" />&nbsp;&nbsp;<a href='#' onclick=\"getElementById('incluir_autor').value = 'S'; getElementById('tipoacao').value = ''; {$this->__nome}.submit();\"><img src='imagens/nvp_bot_adiciona.gif' title='Incluir' border=0></a>",false,false);
		}

		$this->campoOculto( "incluir_autor", "" );

		$this->campoQuebra();
		//-----------------------FIM AUTOR------------------------//

		// text
		$this->campoTexto( "titulo", "T&iacute;tulo", $this->titulo, 40, 255, true );
		$this->campoTexto( "sub_titulo", "Subt&iacute;tulo", $this->sub_titulo, 40, 255, false );
		$this->campoTexto( "estante", "Estante", $this->estante, 20, 15, false );
		$this->campoTexto( "cdd", "CDD", $this->cdd, 20, 15, false );
		$this->campoTexto( "cdu", "CDU", $this->cdu, 20, 15, false );
		$this->campoTexto( "cutter", "Cutter", $this->cutter, 20, 15, false );
		$this->campoNumero( "volume", "Volume", $this->volume, 20, 255, true );
		$this->campoNumero( "num_edicao", "N&uacute;mero Edic&atilde;o", $this->num_edicao, 20, 255, true );
		$this->campoNumero( "ano", "Ano", $this->ano, 5, 4, true );
		$this->campoNumero( "num_paginas", "N&uacute;mero P&aacute;ginas", $this->num_paginas, 5, 255, true );
		$this->campoNumero( "isbn", "ISBN", $this->isbn, 20, 13, false );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );

		$this->acervo_autor = unserialize( urldecode( $this->acervo_autor ) );
		if ($this->acervo_autor)
		{
			$obj = new clsPmieducarAcervo( null, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, null, $this->pessoa_logada, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante );
			$cadastrou = $obj->cadastra();
			if( $cadastrou )
			{
			//-----------------------CADASTRA AUTOR------------------------//
				foreach ( $this->acervo_autor AS $autor )
				{
          $autorPrincipal = $_POST["principal_{$autor['ref_cod_acervo_autor_']}"];
          $autor["principal_"] = is_null($autorPrincipal) ? 0 : 1;

					$obj = new clsPmieducarAcervoAcervoAutor( $autor["ref_cod_acervo_autor_"], $cadastrou, $autor["principal_"] );
					$cadastrou2  = $obj->cadastra();
					if ( !$cadastrou2 )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao cadastrar clsPmieducarAcervoAcervoAutor\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$autor["ref_cod_acervo_autor_"]} ) && is_numeric( {$autor["principal_"]} )\n-->";
						return false;
					}
				}
				$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
				header( "Location: educar_acervo_lst.php" );
				die();
				return true;
			//-----------------------FIM CADASTRA AUTOR------------------------//
			}
			$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
			echo "<!--\nErro ao cadastrar clsPmieducarAcervo\nvalores obrigatorios\nis_numeric( $this->ref_cod_exemplar_tipo ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_acervo_colecao ) && is_numeric( $this->ref_cod_acervo_idioma ) && is_numeric( $this->ref_cod_acervo_editora ) && is_string( $this->titulo ) && is_numeric( $this->volume ) && is_numeric( $this->num_edicao ) && is_numeric( $this->ano ) && is_numeric( $this->num_paginas ) && is_numeric( $this->isbn )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Autor') </script>";
		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );

		$this->acervo_autor = unserialize( urldecode( $this->acervo_autor ) );
		if ($this->acervo_autor)
		{
			$obj = new clsPmieducarAcervo($this->cod_acervo, $this->ref_cod_exemplar_tipo, $this->ref_cod_acervo, $this->pessoa_logada, null, $this->ref_cod_acervo_colecao, $this->ref_cod_acervo_idioma, $this->ref_cod_acervo_editora, $this->titulo, $this->sub_titulo, $this->cdu, $this->cutter, $this->volume, $this->num_edicao, $this->ano, $this->num_paginas, $this->isbn, null, null, 1, $this->ref_cod_biblioteca, $this->cdd, $this->estante);
			$editou = $obj->edita();
			if( $editou )
			{
			//-----------------------EDITA AUTOR------------------------//

				$obj  = new clsPmieducarAcervoAcervoAutor( null, $this->cod_acervo );
				$excluiu = $obj->excluirTodos();
				if ( $excluiu )
				{
					foreach ( $this->acervo_autor AS $autor )
					{
            $autorPrincipal = $_POST["principal_{$autor['ref_cod_acervo_autor_']}"];
            $autor["principal_"] = is_null($autorPrincipal) ? 0 : 1;

						$obj = new clsPmieducarAcervoAcervoAutor( $autor["ref_cod_acervo_autor_"], $this->cod_acervo, $autor["principal_"] );
						$cadastrou2  = $obj->cadastra();
						if ( !$cadastrou2 )
						{
							$this->mensagem = "Editar n&atilde;o realizado.<br>";
							echo "<!--\nErro ao editar clsPmieducarAcervoAcervoAutor\nvalores obrigat&oacute;rios\nis_numeric( $cadastrou ) && is_numeric( {$autor["ref_cod_acervo_autor_"]} ) && is_numeric( {$autor["principal_"]} )\n-->";
							return false;
						}
					}
					$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
					header( "Location: educar_acervo_lst.php" );
					die();
					return true;
				}
			//-----------------------FIM EDITA AUTOR------------------------//
			}
			$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
			echo "<!--\nErro ao editar clsPmieducarAcervo\nvalores obrigatorios\nif( is_numeric( $this->cod_acervo ) && is_numeric( $this->ref_usuario_exc ) )\n-->";
			return false;
		}
		echo "<script> alert('É necessário adicionar pelo menos 1 Autor') </script>";
		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_excluir( 598, $this->pessoa_logada, 11,  "educar_acervo_lst.php" );


		$obj = new clsPmieducarAcervo($this->cod_acervo, null, null, $this->pessoa_logada, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, 0, $this->ref_cod_biblioteca);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_acervo_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarAcervo\nvalores obrigatorios\nif( is_numeric( $this->cod_acervo ) && is_numeric( $this->pessoa_logada ) )\n-->";
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

document.getElementById('ref_cod_acervo_colecao').disabled = true;
document.getElementById('ref_cod_acervo_colecao').options[0].text = 'Selecione uma biblioteca';

document.getElementById('ref_cod_acervo_editora').disabled = true;
document.getElementById('ref_cod_acervo_editora').options[0].text = 'Selecione uma biblioteca';

document.getElementById('ref_cod_acervo_idioma').disabled = true;
document.getElementById('ref_cod_acervo_idioma').options[0].text = 'Selecione uma biblioteca';

var tempExemplarTipo;
var tempColecao;
var tempIdioma;
var tempEditora;

if(document.getElementById('ref_cod_biblioteca').value == "")
{
	setVisibility(document.getElementById('img_colecao'), false);
	setVisibility(document.getElementById('img_editora'), false);
	setVisibility(document.getElementById('img_idioma'), false);
	setVisibility(document.getElementById('img_autor'), false);
	//tempExemplarTipo = null;
	tempColecao = null;
	tempIdioma = null;
	tempEditora = null;
}
else
{
	ajaxBiblioteca('novo');
}

function getColecao( xml_acervo_colecao )
{
	var campoColecao = document.getElementById('ref_cod_acervo_colecao');
	var DOM_array = xml_acervo_colecao.getElementsByTagName( "acervo_colecao" );

	if(DOM_array.length)
	{
		campoColecao.length = 1;
		campoColecao.options[0].text = 'Selecione uma coleção';
		campoColecao.disabled = false;

		for( var i=0; i<DOM_array.length; i++)
		{
			campoColecao.options[campoColecao.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_colecao"), false, false);
		}
		setVisibility(document.getElementById('img_colecao'), true);
		if(tempColecao != null)
			campoColecao.value = tempColecao;
	}
	else
	{
		if(document.getElementById('ref_cod_biblioteca').value == "")
		{
			campoColecao.options[0].text = 'Selecione uma biblioteca';
			setVisibility(document.getElementById('img_colecao'), false);
		}
		else
		{
			campoColecao.options[0].text = 'A biblioteca não possui coleções';
			setVisibility(document.getElementById('img_colecao'), true);
		}
	}
}

function getEditora( xml_acervo_editora )
{
	var campoEditora = document.getElementById('ref_cod_acervo_editora');
	var DOM_array = xml_acervo_editora.getElementsByTagName( "acervo_editora" );

	if(DOM_array.length)
	{
		campoEditora.length = 1;
		campoEditora.options[0].text = 'Selecione uma editora';
		campoEditora.disabled = false;

		for( var i=0; i<DOM_array.length; i++)
		{
			campoEditora.options[campoEditora.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_editora"), false, false);
		}
		setVisibility(document.getElementById('img_editora'), true);
		if(tempEditora != null)
			campoEditora.value = tempEditora;
	}
	else
	{
		if(document.getElementById('ref_cod_biblioteca').value == "")
		{
			campoEditora.options[0].text = 'Selecione uma biblioteca';
			setVisibility(document.getElementById('img_editora'), false);
		}
		else
		{
			campoEditora.options[0].text = 'A biblioteca não possui editoras';
			setVisibility(document.getElementById('img_editora'), true);
		}
	}
}

function getIdioma( xml_acervo_idioma )
{
	var campoIdioma = document.getElementById('ref_cod_acervo_idioma');
	var DOM_array = xml_acervo_idioma.getElementsByTagName( "acervo_idioma" );

	if(DOM_array.length)
	{
		campoIdioma.length = 1;
		campoIdioma.options[0].text = 'Selecione uma idioma';
		campoIdioma.disabled = false;

		for( var i=0; i<DOM_array.length; i++)
		{
			campoIdioma.options[campoIdioma.options.length] = new Option(DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_idioma"), false, false);
		}
		setVisibility(document.getElementById('img_idioma'), true);
		if(tempIdioma != null)
			campoIdioma.value = tempIdioma;
	}
	else
	{
		if(document.getElementById('ref_cod_biblioteca').value == "")
		{
			campoIdioma.options[0].text = 'Selecione uma biblioteca';
			setVisibility(document.getElementById('img_idioma'), false);
		}
		else
		{
			campoIdioma.options[0].text = 'A biblioteca não possui idiomas';
			setVisibility(document.getElementById('img_idioma'), true);
		}
	}
}

document.getElementById('ref_cod_biblioteca').onchange = function()
{
	ajaxBiblioteca();
	if(document.getElementById('ref_cod_biblioteca').value != '')
		setVisibility(document.getElementById('img_autor'), true);
	else
		setVisibility(document.getElementById('img_autor'), false);
};

function ajaxBiblioteca(acao)
{
	var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

	var campoExemplarTipo = document.getElementById('ref_cod_exemplar_tipo');

	var campoColecao = document.getElementById('ref_cod_acervo_colecao');
	if(acao == 'novo')
	{
		tempColecao = campoColecao.value;
	}
	campoColecao.length = 1;
	campoColecao.disabled = true;
	campoColecao.options[0].text = 'Carregando coleções';

	var xml_colecao = new ajax( getColecao );
	xml_colecao.envia( "educar_colecao_xml.php?bib="+campoBiblioteca );

	var campoEditora = document.getElementById('ref_cod_acervo_editora');
	if(acao == 'novo')
	{
		tempEditora = campoEditora.value;
	}
	campoEditora.length = 1;
	campoEditora.disabled = true;
	campoEditora.options[0].text = 'Carregando editoras';

	var xml_editora = new ajax( getEditora );
	xml_editora.envia( "educar_editora_xml.php?bib="+campoBiblioteca );

	var campoIdioma = document.getElementById('ref_cod_acervo_idioma');
	if(acao == 'novo')
	{
		tempIdioma = campoIdioma.value;
	}
	campoIdioma.length = 1;
	campoIdioma.disabled = true;
	campoIdioma.options[0].text = 'Carregando idiomas';

	var xml_idioma = new ajax( getIdioma );
	xml_idioma.envia( "educar_idioma_xml.php?bib="+campoBiblioteca );

}

function pesquisa()
{
	var biblioteca = document.getElementById('ref_cod_biblioteca').value;
	if(!biblioteca)
	{
		alert('Por favor,\nselecione uma biblioteca!');
		return;
	}
	pesquisa_valores_popless('educar_pesquisa_acervo_lst.php?campo1=ref_cod_acervo&ref_cod_biblioteca=' + biblioteca , 'ref_cod_acervo')
}


function fixupPrincipalCheckboxes() {
  $j('#principal').hide();

  var $checkboxes = $j("input[type='checkbox']").filter("input[id^='principal_']");

  $checkboxes.change(function(){
    $checkboxes.not(this).removeAttr('checked');
  });
}

fixupPrincipalCheckboxes();

</script>
