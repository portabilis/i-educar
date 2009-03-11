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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor" );
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

	var $cod_servidor;
	var $ref_cod_deficiencia;
	var $ref_idesco;
	var $ref_cod_funcao;
	var $carga_horaria;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	var $ref_cod_instituicao;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Servidor - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Nome do Servidor",
			"Matr&iacute;cula",
			"Institui&ccedil;&atilde;o"
		) );

//		$get_escola = true;
		include("include/pmieducar/educar_campo_lista.php");
		$opcoes = array( "" => "Pesquise o funcionario clicando na lupa ao lado" );
		if( $this->cod_servidor )
		{
			$objTemp = new clsFuncionario( $this->cod_servidor );
			$detalhe = $objTemp->detalhe();
			$detalhe = $detalhe["idpes"]->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "cod_servidor", "ref_cod_pessoa_fj", "nome" );
		$this->campoListaPesq( "cod_servidor", "Servidor", $opcoes, $this->cod_servidor, "pesquisa_funcionario_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos()."&com_matricula=false", true );


		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_servidor = new clsPmieducarServidor();
		$obj_servidor->setOrderby( "carga_horaria ASC" );
		$obj_servidor->setLimite( $this->limite, $this->offset );

		$lista = $obj_servidor->lista(
			$this->cod_servidor,
			$this->ref_cod_deficiencia,
			$this->ref_idesco,
			$this->carga_horaria,
			null,
			null,
			null,
			null,
			1,
			$this->ref_cod_instituicao
			,null
			,null
			,null
			,null
			,null
			,null
			,true
		);

		$total = $obj_servidor->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{

				// pega detalhes de foreign_keys
				if( class_exists( "clsPmieducarInstituicao" ) )
				{
					$obj_ref_cod_instituicao = new clsPmieducarInstituicao( $registro["ref_cod_instituicao"] );
					$det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
					$registro["ref_cod_instituicao"] = $det_ref_cod_instituicao["nm_instituicao"];
				}
				else
				{
					$registro["ref_cod_instituicao"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarInstituicao\n-->";
				}

				if( class_exists( "clsFuncionario" ) )
				{
					$obj_cod_servidor = new clsFuncionario( $registro["cod_servidor"] );
					$det_cod_servidor = $obj_cod_servidor->detalhe();
					$registro["matricula"] = $det_cod_servidor['matricula'];
					$det_cod_servidor = $det_cod_servidor["idpes"]->detalhe();
					$registro["nome"] = $det_cod_servidor["nome"];

				}
				else
				{
					$registro["cod_servidor"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
				}

				$this->addLinhas( array(
					"<a href=\"educar_servidor_det.php?cod_servidor={$registro["cod_servidor"]}&ref_cod_instituicao={$det_ref_cod_instituicao["cod_instituicao"]}\">{$registro["nome"]}</a>",
					"<a href=\"educar_servidor_det.php?cod_servidor={$registro["cod_servidor"]}&ref_cod_instituicao={$det_ref_cod_instituicao["cod_instituicao"]}\">{$registro["matricula"]}</a>",
					"<a href=\"educar_servidor_det.php?cod_servidor={$registro["cod_servidor"]}&ref_cod_instituicao={$det_ref_cod_instituicao["cod_instituicao"]}\">{$registro["ref_cod_instituicao"]}</a>"

				) );
			}
		}
		$this->addPaginador2( "educar_servidor_lst.php", $total, $_GET, $this->nome, $this->limite );
		$obj_permissoes = new clsPermissoes();


		if( $obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3 ) )
		{
		$this->acao = "go(\"educar_servidor_cad.php\")";
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