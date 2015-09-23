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
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Listagem Patch de Software" );
		$this->processoAp = "795";
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

	var $cod_software_patch;
	var $ref_funcionario_exc;
	var $ref_funcionario_cad;
	var $ref_cod_software;
	var $data_patch;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Software Patch - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			//"Software Patch",
			"Software",
			"Data Patch"
		) );

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisSoftware" ) )
		{
			$objTemp = new clsPmicontrolesisSoftware();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_software']}"] = "{$registro['nm_software']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisSoftware nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_software", "Software", $opcoes, $this->ref_cod_software );



		// outros Filtros
		$this->campoData( "data_patch", "Data Patch", $this->data_patch, false );


		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_software_patch = new clsPmicontrolesisSoftwarePatch();
		$obj_software_patch->setOrderby( "data_patch DESC" );
		$obj_software_patch->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_software_patch->lista(
			null,
			null,
			$this->ref_cod_software,
			$this->data_patch_ini,
			$this->data_patch_fim,
			null,
			null,
			1
		);

		$total = $obj_software_patch->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// muda os campos data
				$registro["data_patch_time"] = strtotime( substr( $registro["data_patch"], 0, 16 ) );
				$registro["data_patch_br"] = date( "d/m/Y", $registro["data_patch_time"] );

				$registro["data_cadastro_time"] = strtotime( substr( $registro["data_cadastro"], 0, 16 ) );
				$registro["data_cadastro_br"] = date( "d/m/Y H:i", $registro["data_cadastro_time"] );

				$registro["data_exclusao_time"] = strtotime( substr( $registro["data_exclusao"], 0, 16 ) );
				$registro["data_exclusao_br"] = date( "d/m/Y H:i", $registro["data_exclusao_time"] );


				// pega detalhes de foreign_keys
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

				if( class_exists( "clsPmicontrolesisSoftware" ) )
				{
					$obj_ref_cod_software = new clsPmicontrolesisSoftware( $registro["ref_cod_software"] );
					$det_ref_cod_software = $obj_ref_cod_software->detalhe();
					$registro["ref_cod_software"] = $det_ref_cod_software["nm_software"];
				}
				else
				{
					$registro["ref_cod_software"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisSoftware\n-->";
				}


				$this->addLinhas( array(
					//"<a href=\"controlesis_software_patch_det.php?cod_software_patch={$registro["cod_software_patch"]}\">{$registro["cod_software_patch"]}</a>",
					"<a href=\"controlesis_software_patch_det.php?cod_software_patch={$registro["cod_software_patch"]}\">{$registro["ref_cod_software"]}</a>",
					"<a href=\"controlesis_software_patch_det.php?cod_software_patch={$registro["cod_software_patch"]}\">{$registro["data_patch_br"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "controlesis_software_patch_lst.php", $total, $_GET, $this->nome, $this->__limite );

		$this->acao = "go(\"controlesis_software_patch_cad.php\")";
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
