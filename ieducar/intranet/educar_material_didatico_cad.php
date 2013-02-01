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
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Material Did&aacute;tico" );
		$this->processoAp = "569";
	}
}

class indice extends clsCadastro
{
	/**
	 * Referencia pega da session para o idpes do usuario atual
	 *
	 * @var int
	 */
	var $pessoa_logada;

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

	//var $ref_cod_instituicao;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_material_didatico=$_GET["cod_material_didatico"];

		$obj_permissoes = new clsPermissoes();
		$obj_permissoes->permissao_cadastra( 569, $this->pessoa_logada,3, "educar_material_didatico_lst.php" );

		if( is_numeric( $this->cod_material_didatico ) )
		{

			$obj = new clsPmieducarMaterialDidatico( $this->cod_material_didatico );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

				$this->fexcluir = $obj_permissoes->permissao_excluir( 569, $this->pessoa_logada,3 );
				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "educar_material_didatico_det.php?cod_material_didatico={$registro["cod_material_didatico"]}" : "educar_material_didatico_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_material_didatico", $this->cod_material_didatico );

		$obrigatorio = true;
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
		
		/*************************COLOCADO*********************************/
		$script = "javascript:showExpansivelIframe(520, 250, 'educar_material_tipo_cad_pop.php');";
		if ($this->ref_cod_instituicao)// && $this->ref_cod_escola	 && $this->ref_cod_curso)
		{
			$script = "<img id='img_tipo_material' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
//			$this->campoLista( "ref_ref_cod_serie", "Série", $opcoes_serie, $this->ref_cod_serie, "", false, "", $script, true);
		}
		else
		{
			$script = "<img id='img_tipo_material' style='display: none;' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
			
		}
		/*************************COLOCADO*********************************/
		$this->campoLista( "ref_cod_material_tipo", "Tipo de Material", $opcoes, $this->ref_cod_material_tipo, "", false, "", $script );

		// text
		$this->campoTexto( "nm_material", "Material", $this->nm_material, 30, 255, true );
		$this->campoMemo( "desc_material", "Descri&ccedil;&atilde;o", $this->desc_material, 60, 5, false );
		$this->campoMonetario( "custo_unitario", "Custo Unit&aacute;rio", $this->custo_unitario, 10, 10, true );

	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->custo_unitario = str_replace(".","",$this->custo_unitario);
		$this->custo_unitario = str_replace(",",".",$this->custo_unitario);

		$obj = new clsPmieducarMaterialDidatico( null, $this->ref_cod_instituicao, null, $this->pessoa_logada, $this->ref_cod_material_tipo, $this->nm_material, $this->desc_material, $this->custo_unitario, null, null, 1 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: educar_material_didatico_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmieducarMaterialDidatico\nvalores obrigat&oacute;rios\nis_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->pessoa_logada ) && is_numeric( $this->ref_cod_material_tipo ) && is_string( $this->nm_material ) && is_numeric( $this->custo_unitario )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->custo_unitario = str_replace(".","",$this->custo_unitario);
		$this->custo_unitario = str_replace(",",".",$this->custo_unitario);

		$obj = new clsPmieducarMaterialDidatico( $this->cod_material_didatico, $this->ref_cod_instituicao, $this->pessoa_logada, null, $this->ref_cod_material_tipo, $this->nm_material, $this->desc_material, $this->custo_unitario,null,null,1 );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_material_didatico_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmieducarMaterialDidatico\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_material_didatico ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPmieducarMaterialDidatico($this->cod_material_didatico, null, $this->pessoa_logada, null, null, null, null, null, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: educar_material_didatico_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmieducarMaterialDidatico\nvalores obrigat&oacute;rios\nif( is_numeric( $this->cod_material_didatico ) && is_numeric( $this->pessoa_logada ) )\n-->";
		return false;
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
	if (this.value == '') 
	{
		$('img_tipo_material').style.display = 'none;';
	}
	else
	{
		$('img_tipo_material').style.display = '';
	}
}

</script>