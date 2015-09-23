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
require_once ("include/clsDetalhe.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "Prefeitura de Itaja&iacute; - Detalhe Altera&ccedil;&atilde;o de Software" );
		$this->processoAp = "794";
	}
}

class indice extends clsDetalhe
{
	/**
	 * Titulo no topo da pagina
	 *
	 * @var int
	 */
	var $titulo;

	var $cod_software_alteracao;
	var $ref_funcionario_exc;
	var $ref_funcionario_cad;
	var $ref_cod_software;
	var $motivo;
	var $tipo;
	var $descricao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $script_banco;

	function Gerar()
	{
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->titulo = "Software Alteracao - Detalhe";
		$this->addBanner( "/intranet/imagens/nvp_top_intranet.jpg", "/intranet/imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->cod_software_alteracao=$_GET["cod_software_alteracao"];

		$tmp_obj = new clsPmicontrolesisSoftwareAlteracao( $this->cod_software_alteracao );
		$registro = $tmp_obj->detalhe();

		if( ! $registro )
		{
			header( "location: controlesis_software_alteracao_lst.php" );
			die();
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


		if( $registro["cod_software_alteracao"] )
		{
			$this->addDetalhe( array( "Software Alterac&atilde;o", "{$registro["cod_software_alteracao"]}") );
		}
		if( $registro["ref_cod_software"] )
		{
			$this->addDetalhe( array( "Software", "{$registro["ref_cod_software"]}") );
		}
		if( $registro["motivo"] )
		{
			$opcoes = array('' => 'Selecione','i' => 'Inserção','a' => 'Alteração','e' => 'Exclusão');
			$this->addDetalhe( array( "Motivo", $opcoes["{$registro["motivo"]}"]) );
		}
		if( $registro["tipo"] )
		{
			$opcoes = array('' => 'Selecione','s' => 'Script','b' => 'Banco');
			$this->addDetalhe( array( "Tipo", $opcoes["{$registro["tipo"]}"]) );
		}
		if( $registro["script_banco"] )
		{

			$this->addDetalhe( array( "Script/Banco", "{$registro["script_banco"]}") );
		}
		if( $registro["descricao"] )
		{
			$this->addDetalhe( array( "Descric&atilde;o", "{$registro["descricao"]}") );
		}


		$this->url_novo = "controlesis_software_alteracao_cad.php";
		$this->url_editar = "controlesis_software_alteracao_cad.php?cod_software_alteracao={$registro["cod_software_alteracao"]}";

		$this->url_cancelar = "controlesis_software_alteracao_lst.php";
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
