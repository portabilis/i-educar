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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Topo Portal" );
		$this->processoAp = "694";
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
	
	var $cod_topo_portal;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $ref_cod_menu_portal;
	var $caminho1;
	var $caminho2;
	var $caminho3;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	
	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();
		
		$this->titulo = "Topo Portal - Listagem";
		
		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;
		
		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );
	
		$this->addCabecalhos( array( 
			"Cod. Topo Portal","Menu Portal",
			"Topo"
		) );
		
		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisMenuPortal" ) )
		{
			$objTemp = new clsPmicontrolesisMenuPortal();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['cod_menu_portal']}"] = "{$registro['nm_menu']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisMenuPortal nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_menu_portal", "Menu Portal", $opcoes, $this->ref_cod_menu_portal );

		// outros Filtros
		$this->campoTexto( "caminho1", "Caminho1", $this->caminho1, 30, 255, false );
		$this->campoTexto( "caminho2", "Caminho2", $this->caminho2, 30, 255, false );
		$this->campoTexto( "caminho3", "Caminho3", $this->caminho3, 30, 255, false );

		
		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;
		
		$obj_topo_portal = new clsPmicontrolesisTopoPortal();
		$obj_topo_portal->setOrderby( "caminho1 ASC" );
		$obj_topo_portal->setLimite( $this->limite, $this->offset );
		
		$lista = $obj_topo_portal->lista(
			$this->cod_topo_portal,
			null,
			null,
			$this->ref_cod_menu_portal,
			$this->caminho1,
			$this->caminho2,
			$this->caminho3,
			null,
			null,
			1
		);
		
		$total = $obj_topo_portal->_total;
		
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
				if( class_exists( "clsFuncionario" ) )
				{
					$obj_ref_funcionario_exc = new clsFuncionario( $registro["ref_funcionario_exc"] );
					$det_ref_funcionario_exc = $obj_ref_funcionario_exc->detalhe();
					if( is_object( $det_ref_funcionario_exc["idpes"] ) )
					{
						$det_ref_funcionario_exc = $det_ref_funcionario_exc["idpes"]->detalhe();
						$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
					}
					else
					{
						$pessoa = new clsPessoa_( $det_ref_funcionario_exc["idpes"] );
						$det_ref_funcionario_exc = $pessoa->detalhe();
						$registro["ref_funcionario_exc"] = $det_ref_funcionario_exc["nome"];
					}
				}
				else
				{
					$registro["ref_funcionario_exc"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
				}

				if( class_exists( "clsFuncionario" ) )
				{
					$obj_ref_funcionario_cad = new clsFuncionario( $registro["ref_funcionario_cad"] );
					$det_ref_funcionario_cad = $obj_ref_funcionario_cad->detalhe();
					if( is_object( $det_ref_funcionario_cad["idpes"] ) )
					{
						$det_ref_funcionario_cad = $det_ref_funcionario_cad["idpes"]->detalhe();
						$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
					}
					else
					{
						$pessoa = new clsPessoa_( $det_ref_funcionario_cad["idpes"] );
						$det_ref_funcionario_cad = $pessoa->detalhe();
						$registro["ref_funcionario_cad"] = $det_ref_funcionario_cad["nome"];
					}
				}
				else
				{
					$registro["ref_funcionario_cad"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsFuncionario\n-->";
				}

				if( class_exists( "clsPmicontrolesisMenuPortal" ) )
				{
					if($registro["ref_cod_menu_portal"] == "")
					{
						$registro["ref_cod_menu_portal"] = "Geral";
					}
					else
					{
						$obj_ref_cod_menu_portal = new clsPmicontrolesisMenuPortal( $registro["ref_cod_menu_portal"] );
						$det_ref_cod_menu_portal = $obj_ref_cod_menu_portal->detalhe();
						$registro["ref_cod_menu_portal"] = $det_ref_cod_menu_portal["nm_menu"];
					}
				}
				else
				{
					$registro["ref_cod_menu_portal"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisMenuPortal\n-->";
				}
			

				$this->addLinhas( array( 
					"<a href=\"controlesis_topo_portal_det.php?cod_topo_portal={$registro["cod_topo_portal"]}\">{$registro["cod_topo_portal"]}</a>",
					"<a href=\"controlesis_topo_portal_det.php?cod_topo_portal={$registro["cod_topo_portal"]}\">{$registro["ref_cod_menu_portal"]}</a>",
					"<a href=\"controlesis_topo_portal_det.php?cod_topo_portal={$registro["cod_topo_portal"]}\"><img border='0' src='imagens/topos/{$registro["caminho1"]}' height='40'><img border='0' height='40' src='imagens/topos/{$registro["caminho2"]}'><img src='imagens/topos/{$registro["caminho3"]}' border='0' height='40'></a>" 
				) );
			}
		}
		$this->addPaginador2( "controlesis_topo_portal_lst.php", $total, $_GET, $this->nome, $this->limite );

		$this->acao = "go(\"controlesis_topo_portal_cad.php\")";
		$this->nome_acao = "Novo";

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