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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Autor" );
		$this->processoAp = "594";
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

	var $cod_acervo_autor;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_autor;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_escola;
	var $ref_cod_instituicao;
	var $ref_cod_biblioteca;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Autor - Listagem";
		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		//$this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
	//	$this->ref_cod_escola = $obj_permissoes->getEscola($this->pessoa_logada);

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_biblioteca = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");


		switch ($nivel_usuario){
			case 1:
				$this->addCabecalhos( array(
					"Autor",
					"Biblioteca",
					"Escola",
					"Institui&ccedil;&atilde;o",
				) );
			break;
			case 2:
				$this->addCabecalhos( array(
					"Autor",
					"Escola"
				) );
			break;
			case 4:
				$this->addCabecalhos( array(
					"Autor"
				) );
			break;
			default:
				$this->addCabecalhos( array(
					"Autor"
				) );
				break;
		}


		// outros Filtros
		$this->campoTexto( "nm_autor", "Autor", $this->nm_autor, 30, 255, false );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_acervo_autor = new clsPmieducarAcervoAutor();
		$obj_acervo_autor->setOrderby( "nm_autor ASC" );
		$obj_acervo_autor->setLimite( $this->limite, $this->offset );


		$lista = $obj_acervo_autor->lista(
			null,
			null,
			null,
			$this->nm_autor,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca
			,$this->ref_cod_instituicao
			,$this->ref_cod_escola
		);


		$total = $obj_acervo_autor->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				$obj_biblioteca = new clsPmieducarBiblioteca($registro['ref_cod_biblioteca']);
				$det_biblioteca = $obj_biblioteca->detalhe();
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarEscola" ) )
				{
					$obj_ref_cod_escola = new clsPmieducarEscola( $det_biblioteca["ref_cod_escola"] );
					$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
					$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
				}
				else
				{
					$registro["ref_cod_escola"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarEscola\n-->";
				}

				switch ($nivel_usuario){
					case 1:

						if( class_exists( "clsPmieducarInstituicao" )  && class_exists( "clsPmieducarEscola" ) )
						{
							$obj_ref_cod_escola = new clsPmieducarEscola( $det_biblioteca["ref_cod_escola"] );
							$det_ref_cod_escola = $obj_ref_cod_escola->detalhe();
							$registro["ref_cod_instituicao"] = $det_ref_cod_escola["ref_cod_instituicao"];

							$obj_ref_cod_intituicao = new clsPmieducarInstituicao( $det_biblioteca["ref_cod_instituicao"] );
							$det_ref_cod_intituicao = $obj_ref_cod_intituicao->detalhe();
							$registro["ref_cod_instituicao"] = $det_ref_cod_intituicao["nm_instituicao"];
						}
						else
						{
							$registro["ref_cod_instituicao"] = "Erro na geracao";
							echo "<!--\nErro\nClasse nao existente: clsPmieducarIntituicao\n-->";
						}


					$this->addLinhas( array(
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["nm_autor"]}</a>",
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$det_biblioteca["nm_biblioteca"]}</a>",
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["ref_cod_escola"]}</a>",
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["ref_cod_instituicao"]}</a>"
					) );
					break;
					case 2:
					$this->addLinhas( array(
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["nm_autor"]}</a>",
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["ref_cod_escola"]}</a>"
					) );
					break;
					case 4:
					default:
					$this->addLinhas( array(
						"<a href=\"educar_acervo_autor_det.php?cod_acervo_autor={$registro["cod_acervo_autor"]}\">{$registro["nm_autor"]}</a>"
					) );
					break;

				}
			}
		}
		$this->addPaginador2( "educar_acervo_autor_lst.php", $total, $_GET, $this->nome, $this->limite );

		if( $obj_permissoes->permissao_cadastra( 594, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_acervo_autor_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Biblioteca",
                    ""                                  => "Lista de Autores"
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