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
require_once ("include/clsListagem.inc.php");
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

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $offset;

	var $cod_usuario;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_tipo_usuario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	var $ref_cod_nivel_usuario;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Usu&aacute;rio - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Usu&aacute;rio",
			"Tipo Usu&aacute;rio",
			"N&iacute;vel de Acesso"
		) );

		$objPermissao = new clsPermissoes();

		// Filtros de Foreign Keys
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
		$parametros->adicionaCampoSelect( "cod_usuario", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "cod_usuario", "Usu&aacute;rio", $opcoes, $this->cod_usuario, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarTipoUsuario" ) )
		{
			$objTemp = new clsPmieducarTipoUsuario();
			$objTemp->setOrderby('nm_tipo ASC');
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_usuario']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarTipoUsuario n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_cod_tipo_usuario", "Tipo Usu&aacute;rio", $opcoes, $this->ref_cod_tipo_usuario,null,null,null,null,null,false);



		
		// filtro de nivel de acesso
		$obj_tipo_usuario = new clsPmieducarTipoUsuario($this->pessoa_logada);
		$tipo_usuario = $obj_tipo_usuario->detalhe();

		$obj_super_usuario = new clsMenuFuncionario($this->pessoa_logada,false,false,0);
		$super_usuario_det = $obj_super_usuario->detalhe();


		if( $super_usuario_det )
		{
			$opcoes = array( "" => "Selecione", "1" => "Poli-Institucional", "2" => "Institucional", "4" => "Escolar", "8" => "Biblioteca");
		}
		elseif ($tipo_usuario["nivel"] == 1)
		{
			$opcoes = array( "" => "Selecione", "2" => "Institucional", "4" => "Escolar", "8" => "Biblioteca");
		}
		elseif ($tipo_usuario["nivel"] == 2)
		{
			$opcoes = array( "" => "Selecione", "4" => "Escolar", "8" => "Biblioteca");
		}
		elseif ($tipo_usuario["nivel"] == 4)
		{
			$opcoes = array( "" => "Selecione", "8" => "Biblioteca");
		}
		$this->campoLista( "ref_cod_nivel_usuario", "N&iacute;vel de Acesso", $opcoes, $this->ref_cod_nivel_usuario,null,null,null,null,null,false );
		
		if ($super_usuario_det)
		{
			$get_escola = true;
			include("include/pmieducar/educar_campo_lista.php");
		}
		
		
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_usuario = new clsPmieducarUsuario();
		$obj_usuario->setOrderby( "nivel ASC" );
		$obj_usuario->setLimite( $this->limite, $this->offset );

		$lista = $obj_usuario->lista(
			$this->cod_usuario,
			$this->ref_cod_escola,
			$this->ref_cod_instituicao,
			null,
			null,
			$this->ref_cod_tipo_usuario,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_nivel_usuario
		);

		$total = $obj_usuario->_total;

		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPessoa_" ) )
				{
					$obj_cod_usuario = new clsPessoa_($registro["cod_usuario"] );
					$obj_usuario_det = $obj_cod_usuario->detalhe();
					$nome_usuario = $obj_usuario_det['nome'];
				}
				else
				{
					$nome_usuario = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPessoa_\n-->";
				}
				if( class_exists( "clsPmieducarTipoUsuario" ) )
				{
					$obj_tipo_usuario = new clsPmieducarTipoUsuario( $registro["ref_cod_tipo_usuario"] );
					$obj_tipo_usuario_det = $obj_tipo_usuario->detalhe();
					$nm_tipo_usuario = $obj_tipo_usuario_det["nm_tipo"];
					$registro["ref_cod_nivel_usuario"] = $obj_tipo_usuario_det["nivel"];

					if ($registro["ref_cod_nivel_usuario"] == 1)
					{
						$nivel = "Poli-Institucional";
					}
					elseif ($registro["ref_cod_nivel_usuario"] == 2)
					{
						$nivel = "Institucional";
					}
					elseif ($registro["ref_cod_nivel_usuario"] == 4)
					{
						$nivel = "Escolar";
					}
					elseif ($registro["ref_cod_nivel_usuario"] == 8)
					{
						$nivel = "Biblioteca";
					}
				}
				else
				{
					$registro["ref_cod_tipo_usuario"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarTipoUsuario\n-->";
				}


				$this->addLinhas( array(
					"<a href=\"educar_usuario_det.php?cod_usuario={$registro["cod_usuario"]}\">{$nome_usuario}</a>",
					"<a href=\"educar_usuario_det.php?cod_usuario={$registro["cod_usuario"]}\">{$nm_tipo_usuario}</a>",
					"<a href=\"educar_usuario_det.php?cod_usuario={$registro["cod_usuario"]}\">{$nivel}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_usuario_lst.php", $total, $_GET, $this->nome, $this->limite );

		$objPermissao = new clsPermissoes();
		if( $objPermissao->permissao_cadastra( 555, $this->pessoa_logada,7,null,true ) )
		{
			$this->acao = "go(\"educar_usuario_cad.php\")";
			$this->nome_acao = "Novo";
		}
		$this->largura = "100%";
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