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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Ra&ccedil;a" );
		$this->processoAp = "678";
	}
}

class indice extends clsListagem
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $__pessoa_logada;

	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $__titulo;

	/**
	 * Quantidade de registros a ser apresentada em cada pagina
	 *
	 * @var int
	 */
	var $__limite;

	/**
	 * Inicio dos registros a serem exibidos (limit)
	 *
	 * @var int
	 */
	var $__offset;

	var $cod_raca;
	var $idpes_exc;
	var $idpes_cad;
	var $nm_raca;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Ra&ccedil;a - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Ra&ccedil;a" /*,
			"Idpes Exc",
			"Idpes Cad",
			"Nome Raca"*/
		) );

		// Filtros de Foreign Keys
		/*$opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
		if( $this->idpes_exc )
		{
			$objTemp = new clsPessoaFisica( $this->idpes_exc );
			$detalhe = $objTemp->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "idpes_exc", "idpes", "nome" );
		$parametros->setPessoa( "F" );
		$parametros->setPessoaNovo( 'S' );
		$parametros->setPessoaEditar( 'N' );
		$parametros->setPessoaTela( "frame" );
		$parametros->setPessoaCPF('N');
//		$parametros->setCodSistema(0);
		$this->campoListaPesq( "idpes_exc", "Idpes Exc", $opcoes, $this->idpes_exc, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
		$opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
		if( $this->idpes_cad )
		{
			$objTemp = new clsPessoaFisica( $this->idpes_cad );
			$detalhe = $objTemp->detalhe();
			$opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
		}
		$parametros = new clsParametrosPesquisas();
		$parametros->setSubmit( 0 );
		$parametros->adicionaCampoSelect( "idpes_cad", "idpes", "nome" );
		$parametros->setPessoa( "F" );
		$parametros->setPessoaNovo( 'S' );
		$parametros->setPessoaEditar( 'N' );
		$parametros->setPessoaTela( "frame" );
		$parametros->setPessoaCPF('N');
//		$parametros->setCodSistema(0);
		$this->campoListaPesq( "idpes_cad", "Idpes Cad", $opcoes, $this->idpes_cad, "pesquisa_pessoa_lst.php", "", false, "", "", null, null, "", false, $parametros->serializaCampos() );
	*/

		// outros Filtros
		$this->campoTexto( "nm_raca", "Ra&ccedil;a", $this->nm_raca, 30, 255, false );


		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_raca = new clsCadastroRaca();
		$obj_raca->setOrderby( "nm_raca ASC" );
		$obj_raca->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_raca->lista(
			null,
			null,
			$this->nm_raca,
			null,
			null,
			null,
			null,
			't'
		);

		$total = $obj_raca->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["data_cadastro_time"] = strtotime( substr( $registro["data_cadastro"], 0, 16 ) );
				$registro["data_cadastro_br"] = date( "d/m/Y H:i", $registro["data_cadastro_time"] );

				$registro["data_exclusao_time"] = strtotime( substr( $registro["data_exclusao"], 0, 16 ) );
				$registro["data_exclusao_br"] = date( "d/m/Y H:i", $registro["data_exclusao_time"] );


				// pega detalhes de foreign_keys
				/*if( class_exists( "clsCadastroFisica" ) )
				{
					$obj_idpes_exc = new clsCadastroFisica( $registro["idpes_exc"] );
					$det_idpes_exc = $obj_idpes_exc->detalhe();
					$registro["idpes_exc"] = $det_idpes_exc[""];
				}
				else
				{
					$registro["idpes_exc"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
				}*/

				/*if( class_exists( "clsCadastroFisica" ) )
				{
					$obj_idpes_cad = new clsCadastroFisica( $registro["idpes_cad"] );
					$det_idpes_cad = $obj_idpes_cad->detalhe();
					$registro["idpes_cad"] = $det_idpes_cad[""];
				}
				else
				{
					$registro["idpes_cad"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsCadastroFisica\n-->";
				}*/


				$this->addLinhas( array(
					//"<a href=\"cadastro_raca_det.php?cod_raca={$registro["cod_raca"]}\">{$registro["cod_raca"]}</a>",
					/*"<a href=\"cadastro_raca_det.php?cod_raca={$registro["cod_raca"]}\">{$registro["idpes_exc"]}</a>",
					"<a href=\"cadastro_raca_det.php?cod_raca={$registro["cod_raca"]}\">{$registro["idpes_cad"]}</a>",*/
					"<a href=\"educar_raca_det.php?cod_raca={$registro["cod_raca"]}\">{$registro["nm_raca"]}</a>"
				) );
				
			}
		}
		$this->addPaginador2( "educar_raca_lst.php", $total, $_GET, $this->nome, $this->__limite );
	
		$obj_permissao = new clsPermissoes();
		if( $obj_permissao->permissao_cadastra(678, $this->__pessoa_logada, 3) )
		{
			$this->acao = "go(\"educar_raca_cad.php\")";
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