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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Editora" );
		$this->processoAp = "595";
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

	var $cod_acervo_editora;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $nm_editora;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $telefone;
	var $ddd_telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Editora - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Editora",
			"Estado",
			"Cidade",
			"Biblioteca"
		) );
	
		$get_escola = true;
		$get_biblioteca = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->campoTexto( "nm_editora", "Editora", $this->nm_editora, 30, 255, false );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			$objTemp = new clsUf();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}

		$this->campoLista( "ref_sigla_uf", "Estado", $opcoes, $this->ref_sigla_uf, null,null,null,null,null,false );

		// outros Filtros
		$this->campoTexto( "cidade", "Cidade", $this->cidade, 30, 60, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		if(!is_numeric($this->ref_cod_biblioteca))
		{
			$obj_bib_user = new clsPmieducarBibliotecaUsuario();
			$this->ref_cod_biblioteca = $obj_bib_user->listaBibliotecas($this->pessoa_logada);
		}
		
		$obj_acervo_editora = new clsPmieducarAcervoEditora();
		$obj_acervo_editora->setOrderby( "nm_editora ASC" );
		$obj_acervo_editora->setLimite( $this->limite, $this->offset );

		$lista = $obj_acervo_editora->lista(
			null,
			null,
			null,
			null,
			$this->ref_sigla_uf,
			$this->nm_editora,
			null,
			$this->cidade,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca
		);

		$total = $obj_acervo_editora->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsUf" ) )
				{
					$obj_ref_sigla_uf = new clsUf( $registro["ref_sigla_uf"] );
					$det_ref_sigla_uf = $obj_ref_sigla_uf->detalhe();
					$registro["ref_sigla_uf"] = $det_ref_sigla_uf["nome"];
				}
				else
				{
					$registro["ref_sigla_uf"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsUf\n-->";
				}
				$obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
				$det_biblioteca = $obj_biblioteca->detalhe();
				$registro['ref_cod_biblioteca'] = $det_biblioteca['nm_biblioteca'];
				$this->addLinhas( array(
					"<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro["cod_acervo_editora"]}\">{$registro["nm_editora"]}</a>",
					"<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro["cod_acervo_editora"]}\">{$registro["ref_sigla_uf"]}</a>",
					"<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro["cod_acervo_editora"]}\">{$registro["cidade"]}</a>",
					"<a href=\"educar_acervo_editora_det.php?cod_acervo_editora={$registro["cod_acervo_editora"]}\">{$registro["ref_cod_biblioteca"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_acervo_editora_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 595, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_acervo_editora_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_biblioteca_index.php"                  => "Biblioteca",
                    ""                                  => "Lista de Editoras"
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