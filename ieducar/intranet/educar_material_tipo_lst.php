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
		if($_GET["ativo"]== "excluido")
			$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Material Did&aacute;tico Exclu&iacute;dos" );
		else
			$this->SetTitulo( "{$this->_instituicao} i-Educar - Tipo Material Did&aacute;tico" );

		$this->processoAp = "563";
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

	var $cod_material_tipo;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $nm_tipo;
	var $desc_tipo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

	//var $tipo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();



		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		if($this->ativo == "excluido")
			$this->titulo = "Tipo Material - Listagem excluídos";
		else
			$this->titulo = "Tipo Material - Listagem";

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Material Did&aacute;tico"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
		include("include/pmieducar/educar_campo_lista.php");

		$this->ativo = $this->ativo == "excluido" ? 0 : 1 ;

		// outros Filtros
		$this->campoTexto( "nm_tipo", "Material Did&aacute;tico", $this->nm_tipo, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_material_tipo = new clsPmieducarMaterialTipo();
		$obj_material_tipo->setOrderby( "nm_tipo ASC" );
		$obj_material_tipo->setLimite( $this->limite, $this->offset );

		$lista = $obj_material_tipo->lista(
			null,
			null,
			null,
			$this->nm_tipo,
			null,
			null,
			null,
			$this->ativo,
			$this->ref_cod_instituicao
		);

		$total = $obj_material_tipo->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
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
					"<a href=\"educar_material_tipo_det.php?cod_material_tipo={$registro["cod_material_tipo"]}\">{$registro["nm_tipo"]}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_material_tipo_det.php?cod_material_tipo={$registro["cod_material_tipo"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_material_tipo_lst.php", $total, $_GET, $this->nome, $this->limite );

		//** Verificacao de permissao para exclusao
		$obj_permissao = new clsPermissoes();

		//materiais inativos
		$inativos = $obj_material_tipo->lista(null,null,null,null,null,null,null,0);

		if($obj_permissao->permissao_cadastra(563, $this->pessoa_logada,3))
		{
			$this->acao = "go(\"educar_material_tipo_cad.php\")";
			$this->nome_acao = "Novo";
		/*	if($this->ativo === 1){
				$this->array_botao[] = "Novo";
				$this->array_botao_url[] = "educar_material_tipo_cad.php";
				if($inativos)
				{
					$this->array_botao[] = "Ativar tipo material inativo";
					$this->array_botao_url[] = "educar_material_tipo_lst.php?ativo=excluido";
				}
			}
			else
			{

				$this->array_botao[] = "Voltar";
				$this->array_botao_url[] = "educar_material_tipo_lst.php";
			}*/
		}
		//**

		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Tipos de materiais"
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