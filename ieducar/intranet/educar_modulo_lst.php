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
require_once ("include/localizacaoSistema.php");

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - M&oacute;dulo" );
		$this->processoAp = "584";
                $this->addEstilo( "localizacaoSistema" );
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

	var $cod_modulo;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_tipo;
	var $descricao;
	var $num_meses;
	var $num_semanas;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "M&oacute;dulo - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"M&oacute;dulo",
			"N&uacute;mero Meses"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		$this->addCabecalhos($lista_busca);


		// Filtros de Foreign Keys
		include("include/pmieducar/educar_campo_lista.php");

		// Filtros de Foreign Keys
/*		if ($nivel_usuario == 1)
		{
			$opcoes = array( "" => "Selecione" );
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$obj_instituicao = new clsPmieducarInstituicao();
				$lista = $obj_instituicao->lista(null,null,null,null,null,null,null,null,null,null,null,null,null,1);
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_instituicao']}"] = "{$registro['nm_instituicao']}";
					}
				}
			}
			else
			{
				echo "<!--\nErro\nClasse clsPmieducarInstituicao n&atilde;o encontrada\n-->";
				$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
			}
			$this->campoLista( "ref_cod_instituicao", "Instituic&atilde;o", $opcoes, $this->ref_cod_instituicao, null,null,null,null,null,false );
		}
		else if ($nivel_usuario == 2)
		{
			$obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
			$obj_usuario_det = $obj_usuario->detalhe();
			$this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];
		}
*/
		// outros Filtros
		$this->campoTexto( "nm_tipo", "M&oacute;dulo", $this->nm_tipo, 30, 255, false );
		$this->campoNumero( "num_meses", "N&uacute;mero Meses", $this->num_meses, 2, 2, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_modulo = new clsPmieducarModulo();
		$obj_modulo->setOrderby( "nm_tipo ASC" );
		$obj_modulo->setLimite( $this->limite, $this->offset );

		$lista = $obj_modulo->lista(
			null,
			null,
			null,
			$this->nm_tipo,
			null,
			$this->num_meses,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
		);

		$total = $obj_modulo->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$obj_cod_instituicao_det = $obj_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $obj_cod_instituicao_det["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}
				$lista_busca = array(
					"<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["nm_tipo"]}</a>",
					"<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["num_meses"]}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_modulo_det.php?cod_modulo={$registro["cod_modulo"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);
			}
		}

		$this->addPaginador2( "educar_modulo_lst.php", $total, $_GET, $this->nome, $this->limite );

		if( $obj_permissoes->permissao_cadastra( 584, $this->pessoa_logada, 3 ) )
		{
			$this->acao = "go(\"educar_modulo_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Séries"
                ));
                $this->enviaLocalizacao($localizacao->montar());
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