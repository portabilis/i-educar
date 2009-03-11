<?php
require_once ("include/clsBase.inc.php");
require_once ("include/clsListagem.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Listagem Categoria N&iacute;vel" );
		$this->processoAp = "829";
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

	var $cod_categoria_nivel;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $nm_categoria_nivel;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Categoria Nivel - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "http://ieducar.dccobra.com.br/intranet/imagens/nvp_top_intranet.jpg", "http://ieducar.dccobra.com.br/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			//"Categoria Nivel",
			"Nome Categoria Nivel"
		) );

		// Filtros de Foreign Keys


		// outros Filtros
		$this->campoTexto( "nm_categoria_nivel", "Nome Categoria Nivel", $this->nm_categoria_nivel, 30, 255, false );


		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_categoria_nivel = new clsPmieducarCategoriaNivel();
		$obj_categoria_nivel->setOrderby( "nm_categoria_nivel ASC" );
		$obj_categoria_nivel->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_categoria_nivel->lista(
			null,
			null,
			$this->nm_categoria_nivel,
			null,
			null,
			1
		);

		$total = $obj_categoria_nivel->_total;

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
				if( class_exists( "clsPmieducarUsuario" ) )
				{
					$obj_ref_usuario_cad = new clsPmieducarUsuario( $registro["ref_usuario_cad"] );
					$det_ref_usuario_cad = $obj_ref_usuario_cad->detalhe();
					$registro["ref_usuario_cad"] = $det_ref_usuario_cad["data_cadastro"];
				}
				else
				{
					$registro["ref_usuario_cad"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
				}

				if( class_exists( "clsPmieducarUsuario" ) )
				{
					$obj_ref_usuario_exc = new clsPmieducarUsuario( $registro["ref_usuario_exc"] );
					$det_ref_usuario_exc = $obj_ref_usuario_exc->detalhe();
					$registro["ref_usuario_exc"] = $det_ref_usuario_exc["data_cadastro"];
				}
				else
				{
					$registro["ref_usuario_exc"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmieducarUsuario\n-->";
				}


				$this->addLinhas( array(
					//"<a href=\"educar_categoria_nivel_det.php?cod_categoria_nivel={$registro["cod_categoria_nivel"]}\">{$registro["cod_categoria_nivel"]}</a>",
					"<a href=\"educar_categoria_nivel_det.php?cod_categoria_nivel={$registro["cod_categoria_nivel"]}\">{$registro["nm_categoria_nivel"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "educar_categoria_nivel_lst.php", $total, $_GET, $this->nome, $this->__limite );
		$obj_permissoes = new clsPermissoes();
		if( $obj_permissoes->permissao_cadastra( 829, $this->__pessoa_logada, 3, null, true ) )
		{
			$this->acao = "go(\"educar_categoria_nivel_cad.php\")";
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
