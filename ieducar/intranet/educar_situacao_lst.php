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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Situa&ccedil;&atilde;o" );
		$this->processoAp = "602";
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

	var $cod_situacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_situacao;
	var $permite_emprestimo;
	var $descricao;
	var $situacao_padrao;
	var $situacao_emprestada;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

	var $ref_cod_instituicao;
	var $ref_cod_escola;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Situa&ccedil;&atilde;o - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Situa&ccedil;&atilde;o",
			"Permite Empr&eacute;stimo"
		);

		// Filtros de Foreign Keys
		$get_escola = true;
		$get_biblioteca = true;
		$get_cabecalho = "lista_busca";
		include("include/pmieducar/educar_campo_lista.php");

		$this->addCabecalhos($lista_busca);

		// outros Filtros
		$this->campoTexto( "nm_situacao", "Situa&ccedil;&atilde;o", $this->nm_situacao, 30, 255, false );
		$opcoes = array("" => "Selecione", 1 => "n&atilde;o", 2 => "sim" );
		$this->campoLista( "permite_emprestimo", "Permite Empr&eacute;stimo", $opcoes, $this->permite_emprestimo, null,null,null,null,null,false);


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_situacao = new clsPmieducarSituacao();
		$obj_situacao->setOrderby( "nm_situacao ASC" );
		$obj_situacao->setLimite( $this->limite, $this->offset );

		$lista = $obj_situacao->lista(
			null,
			null,
			null,
			$this->nm_situacao,
			$this->permite_emprestimo,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_biblioteca,
			$this->ref_cod_instituicao,
			$this->ref_cod_escola
		);

		$total = $obj_situacao->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarBiblioteca" ) )
				{
					$obj_ref_cod_biblioteca = new clsPmieducarBiblioteca( $registro["ref_cod_biblioteca"] );
					$det_ref_cod_biblioteca = $obj_ref_cod_biblioteca->detalhe();
					$registro["ref_cod_biblioteca"] = $det_ref_cod_biblioteca["nm_biblioteca"];
					$registro["ref_cod_instituicao"] = $det_ref_cod_biblioteca["ref_cod_instituicao"];
					$registro["ref_cod_escola"] = $det_ref_cod_biblioteca["ref_cod_escola"];
					if( $registro["ref_cod_instituicao"] )
					{
						$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
						$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
						$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
					}
					if( $registro["ref_cod_escola"] )
					{
						$obj_ref_cod_escola = new clsPmieducarEscola();
						$det_ref_cod_escola = array_shift($obj_ref_cod_escola->lista($registro["ref_cod_escola"]));
						$registro["ref_cod_escola"] = $det_ref_cod_escola["nome"];
					}
				}
				else
				{
					$registro["ref_cod_biblioteca"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarBiblioteca\n-->";
				}

				if ($registro["permite_emprestimo"] == 1)
					$registro["permite_emprestimo"] = "n&atilde;o";
				else if ($registro["permite_emprestimo"] == 2)
					$registro["permite_emprestimo"] = "sim";

				$lista_busca = array(
					"<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["nm_situacao"]}</a>",
					"<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["permite_emprestimo"]}</a>"
				);

				if ($qtd_bibliotecas > 1 && ($nivel_usuario == 4 || $nivel_usuario == 8))
					$lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["ref_cod_biblioteca"]}</a>";
				else if ($nivel_usuario == 1 || $nivel_usuario == 2 || $nivel_usuario == 4)
					$lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["ref_cod_biblioteca"]}</a>";
				if ($nivel_usuario == 1 || $nivel_usuario == 2)
					$lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["ref_cod_escola"]}</a>";
				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_situacao_det.php?cod_situacao={$registro["cod_situacao"]}\">{$registro["ref_cod_instituicao"]}</a>";

				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_situacao_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 602, $this->pessoa_logada, 11 ) )
		{
			$this->acao = "go(\"educar_situacao_cad.php\")";
			$this->nome_acao = "Novo";
		}

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_biblioteca_index.php"                  => "Bilioteca",
                    ""                                  => "Lista de Situações do Exemplares"
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