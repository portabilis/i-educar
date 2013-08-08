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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Material Did&aacute;tico" );
		$this->processoAp = "569";
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

	var $cod_material_didatico;
	var $ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_material_tipo;
	var $nm_material;
	var $desc_material;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Material Did&aacute;tico - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$lista_busca = array(
			"Material",
			"Tipo",
			"Custo Unit&aacute;rio"
		);

		$obj_permissoes = new clsPermissoes();
		$nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
		if ($nivel_usuario == 1)
			$lista_busca[] = "Institui&ccedil;&atilde;o";

		$this->addCabecalhos($lista_busca);

		// Filtros de Foreign Keys
		include("include/pmieducar/educar_campo_lista.php");

		// Filtros de Foreign Keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmieducarMaterialTipo" ) )
		{
			/*$todos_tipos_materiais = "tipo_material = new Array();\n";
			$objTemp = new clsPmieducarMaterialTipo();
			$objTemp->setOrderby('nm_tipo ASC');
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$todos_tipos_materiais .= "tipo_material[tipo_material.length] = new Array( {$registro["cod_material_tipo"]}, '{$registro['nm_tipo']}', {$registro["ref_cod_instituicao"]} );\n";
				}
			}
			echo "<script>{$todos_tipos_materiais}</script>";*/

			// EDITAR
			if ($this->ref_cod_instituicao)
			{
				$objTemp = new clsPmieducarMaterialTipo();
				$objTemp->setOrderby('nm_tipo ASC');
				$lista = $objTemp->lista( null,null,null,null,null,null,null,1,$this->ref_cod_instituicao );
				if ( is_array( $lista ) && count( $lista ) )
				{
					foreach ( $lista as $registro )
					{
						$opcoes["{$registro['cod_material_tipo']}"] = "{$registro['nm_tipo']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmieducarMaterialTipo n&atilde;o encontrada\n-->";
			$opcoes = array( "" => "Erro na gera&ccedil;&atilde;o" );
		}
		$this->campoLista( "ref_cod_material_tipo", "Tipo de Material", $opcoes, $this->ref_cod_material_tipo,null,null,null,null,null,false );

		// outros Filtros
		$this->campoTexto( "nm_material", "Material", $this->nm_material, 30, 255, false );

		// Paginador
		$this->limite = 20;
		$this->offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

		$obj_material_didatico = new clsPmieducarMaterialDidatico();
		$obj_material_didatico->setOrderby( "nm_material ASC" );
		$obj_material_didatico->setLimite( $this->limite, $this->offset );
		$lista = $obj_material_didatico->lista(
			null,
			$this->ref_cod_instituicao,
			null,
			null,
			$this->ref_cod_material_tipo,
			$this->nm_material,
			null,
			null,
			null,
			null,
			null,
			null,
			1
		);

		$total = $obj_material_didatico->_total;

		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				if( class_exists( "clsPmieducarMaterialTipo" ) )
				{
					$obj_ref_cod_material_tipo = new clsPmieducarMaterialTipo( $registro["ref_cod_material_tipo"] );
					$det_ref_cod_material_tipo = $obj_ref_cod_material_tipo->detalhe();
					$registro["ref_cod_material_tipo"] = $det_ref_cod_material_tipo["nm_tipo"];
				}
				else
				{
					$registro["ref_cod_material_tipo"] = "Erro na gera&ccedil;&atilde;o";
					echo "<!--\nErro\nClasse n&atilde;o existente: clsPmieducarMaterialTipo\n-->";
				}
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

				$valor = number_format($registro["custo_unitario"],2,",",".");

				$lista_busca = array(
					"<a href=\"educar_material_didatico_det.php?cod_material_didatico={$registro["cod_material_didatico"]}\">{$registro["nm_material"]}</a>",
					"<a href=\"educar_material_didatico_det.php?cod_material_didatico={$registro["cod_material_didatico"]}\">{$registro["ref_cod_material_tipo"]}</a>",
					"<a href=\"educar_material_didatico_det.php?cod_material_didatico={$registro["cod_material_didatico"]}\">{$valor}</a>"
				);

				if ($nivel_usuario == 1)
					$lista_busca[] = "<a href=\"educar_material_didatico_det.php?cod_material_didatico={$registro["cod_material_didatico"]}\">{$registro["ref_cod_instituicao"]}</a>";
				$this->addLinhas($lista_busca);
			}
		}
		$this->addPaginador2( "educar_material_didatico_lst.php", $total, $_GET, $this->nome, $this->limite );

		if( $obj_permissoes->permissao_cadastra( 569, $this->pessoa_logada,3 ) ) {
			$this->acao = "go(\"educar_material_didatico_cad.php\")";
			$this->nome_acao = "Novo";
		}
		$this->largura = "100%";
                
                $localizacao = new LocalizacaoSistema();
                $localizacao->entradaCaminhos( array(
                    $_SERVER['SERVER_NAME']."/intranet" => "i-Educar",
                    "educar_index.php"                  => "Escola",
                    ""                                  => "Lista de Material didático"
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

<script>

function getMaterialTipo(xml_material_tipo)
{
	/*
	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;
	var campoTipoMaterial = document.getElementById('ref_cod_material_tipo');

	campoTipoMaterial.length = 1;
	for (var j = 0; j < tipo_material.length; j++)
	{
		if (tipo_material[j][2] == campoInstituicao)
		{
			campoTipoMaterial.options[campoTipoMaterial.options.length] = new Option( tipo_material[j][1], tipo_material[j][0],false,false);
		}
	}
	*/
	var campoTipoMaterial = document.getElementById('ref_cod_material_tipo');
	var DOM_array = xml_material_tipo.getElementsByTagName( "material_tipo" );

	if(DOM_array.length)
	{
		campoTipoMaterial.length = 1;
		campoTipoMaterial.options[0].text = 'Selecione um tipo de material';
		campoTipoMaterial.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoTipoMaterial.options[campoTipoMaterial.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("cod_material_tipo"),false,false);
		}
	}
	else
		campoTipoMaterial.options[0].text = 'A instituição não possui nenhum tipo de material';
}

document.getElementById('ref_cod_instituicao').onchange = function()
{
//	getMaterialTipo();

	var campoInstituicao = document.getElementById('ref_cod_instituicao').value;

	var campoTipoMaterial = document.getElementById('ref_cod_material_tipo');
	campoTipoMaterial.length = 1;
	campoTipoMaterial.disabled = true;
	campoTipoMaterial.options[0].text = 'Carregando tipo de material';

	var xml_material_tipo = new ajax( getMaterialTipo );
	xml_material_tipo.envia( "educar_material_tipo_xml.php?ins="+campoInstituicao );
}

</script>