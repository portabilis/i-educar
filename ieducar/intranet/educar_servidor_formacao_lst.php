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
/**
 * @author Adriano Erik Weiguert Nagasava
 */
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor Formacao" );
		$this->processoAp = "635";
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

	var $cod_formacao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_servidor;
	var $nm_formacao;
	var $tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->ref_cod_servidor	   = $_GET["ref_cod_servidor"];
		$this->ref_cod_instituicao = $_GET["ref_cod_instituicao"];

		$this->titulo = "Servidor Formacao - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Nome Forma&ccedil;&atilde;o",
			"Tipo"
		) );
		$this->campoOculto( "ref_cod_servidor", $this->ref_cod_servidor );
		$this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );

		// outros Filtros
		$this->campoTexto( "nm_formacao", "Nome da Forma&ccedil;&atilde;o", $this->nm_formacao, 30, 255, false );
		$opcoes = array( "" => "Selecione", "C" => "Cursos", "T" => "Títulos", "O" => "Concursos" );
		$this->campoLista( "tipo", "Tipo de Formação", $opcoes, $this->tipo );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_servidor_formacao = new clsPmieducarServidorFormacao();
		$obj_servidor_formacao->setOrderby( "nm_formacao ASC" );
		$obj_servidor_formacao->setLimite( $this->limite, $this->offset );

		if ( !isset( $this->tipo ) ) {
			$this->tipo = null;
		}
		$lista = $obj_servidor_formacao->lista(
			null,
			null,
			null,
			$this->ref_cod_servidor,
			$this->nm_formacao,
			$this->tipo,
			null,
			null,
			null,
			1
		);
		$total = $obj_servidor_formacao->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarUsuario" ) )
				{
					$obj_ref_usuario_exc 		 = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
					$det_ref_usuario_exc 		 = $obj_ref_usuario_exc->detalhe();
					$registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];
				}
				else
				{
					$registro["ref_usuario_exc"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
				}

				if( class_exists( "clsPmieducarServidor" ) )
				{
					$obj_ref_cod_servidor = new clsPmieducarServidor( $registro["ref_cod_servidor"] );
					$det_ref_cod_servidor = $obj_ref_cod_servidor->detalhe();
					$registro["ref_cod_servidor"] = $det_ref_cod_servidor["cod_servidor"];
				}
				else
				{
					$registro["ref_cod_servidor"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarServidor\n-->";
				}

				if ( $registro["tipo"] == "C" ) {
					$registro["tipo"] = "Curso";
				}
				elseif ( $registro["tipo"] == "T" ) {
					$registro["tipo"] = "T&iacute;tulo";
				}
				else {
					$registro["tipo"] = "Concurso";
				}

				$this->addLinhas( array(
					"<a href=\"educar_servidor_formacao_det.php?cod_formacao={$registro["cod_formacao"]}\">{$registro["nm_formacao"]}</a>",
					"<a href=\"educar_servidor_formacao_det.php?cod_formacao={$registro["cod_formacao"]}\">{$registro["tipo"]}</a>"
				) );
				$this->tipo = "";
			}
		}
		$this->addPaginador2( "educar_servidor_formacao_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3 ) )
		{
			$this->array_botao[] 	 = "Novo";
			$this->array_botao_url[] = "educar_servidor_formacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";
			//$this->acao = "go(\"educar_servidor_formacao_cad.php?ref_cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}\")";
			//$this->nome_acao = "Novo";
		}
		$this->array_botao[] 	 = "Voltar";
		$this->array_botao_url[] = "educar_servidor_det.php?cod_servidor={$this->ref_cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}";

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