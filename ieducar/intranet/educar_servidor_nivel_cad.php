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
		$this->SetTitulo( "{$this->_instituicao} i-Educar - Servidor N&iacute;vel" );
		$this->processoAp = "0";
		$this->renderBanner = false;
		$this->renderMenu   = false;
		$this->renderMenuSuspenso = false;

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

	var $cod_servidor;
	var $ref_cod_instituicao;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_nivel;
	var $ref_cod_subnivel;
	var $ref_cod_categoria;


	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada 	   			= $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_servidor		   			= $_GET["ref_cod_servidor"];
		$this->ref_cod_instituicao 			= $_GET["ref_cod_instituicao"];


		$obj_permissoes = new clsPermissoes();

		$obj_permissoes->permissao_cadastra( 635, $this->pessoa_logada, 3,  "educar_servidor_lst.php" );

		if( is_numeric( $this->cod_servidor ) && is_numeric( $this->ref_cod_instituicao ) )
		{

			$obj = new clsPmieducarServidor( $this->cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				$this->ref_cod_subnivel = $registro['ref_cod_subnivel'];
				$obj_subnivel = new clsPmieducarSubnivel($this->ref_cod_subnivel);
				$det_subnivel = $obj_subnivel->detalhe();
				if($det_subnivel)
					$this->ref_cod_nivel = $det_subnivel['ref_cod_nivel'];

				if($this->ref_cod_nivel)
				{
					$obj_nivel = new clsPmieducarNivel($this->ref_cod_nivel);
					$det_nivel = $obj_nivel->detalhe();
					$this->ref_cod_categoria = $det_nivel['ref_cod_categoria_nivel'];
				}

				$retorno = "Editar";
			}
		}
		else
		{
			echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
			die();
		}

		return $retorno;
	}

	function Gerar()
	{

		$this->campoOculto("cod_servidor",$this->cod_servidor);
		$this->campoOculto("ref_cod_instituicao",$this->ref_cod_instituicao);

		$obj_categoria = new clsPmieducarCategoriaNivel();
		$lst_categoria = $obj_categoria->lista(null,null,null,null,null,null,null,null,1);
		$opcoes = array('' => 'Selecione uma categoria');
		if($lst_categoria)
		{
			foreach ($lst_categoria as $categoria)
			{
				$opcoes[$categoria['cod_categoria_nivel']] = $categoria['nm_categoria_nivel'];
			}
		}

		$this->campoLista("ref_cod_categoria","Categoria",$opcoes,$this->ref_cod_categoria);

		$opcoes = array('' => 'Selecione uma categoria');
		if($this->ref_cod_categoria)
		{
			$obj_nivel = new clsPmieducarNivel();
			$lst_nivel = $obj_nivel->buscaSequenciaNivel($this->ref_cod_categoria);
			if($lst_nivel)
			{
				foreach ($lst_nivel as $nivel)
				{
					$opcoes[$nivel['cod_nivel']] = $nivel['nm_nivel'];
				}
			}
		}
		$this->campoLista("ref_cod_nivel","N&iacute;vel",$opcoes,$this->ref_cod_nivel,"",true);

		$opcoes = array('' => 'Selecione um nível');

		if($this->ref_cod_nivel)
		{
			$obj_nivel = new clsPmieducarSubnivel();
			$lst_nivel = $obj_nivel->buscaSequenciaSubniveis($this->ref_cod_nivel);
			if($lst_nivel)
			{
				foreach ($lst_nivel as $subnivel)
				{
					$opcoes[$subnivel['cod_subnivel']] = $subnivel['nm_subnivel'];
				}
			}
		}

		$this->campoLista("ref_cod_subnivel","Subn&iacute;vel",$opcoes,$this->ref_cod_subnivel,"",false,"","",false,true);

	}

	function Novo()
	{
			echo "<script>window.parent.fechaExpansivel( '{$_GET['div']}');</script>";
			die();
	}

	function Editar()
	{

		$obj_servidor = new clsPmieducarServidor($this->cod_servidor,null,null,null,null,null,null,$this->ref_cod_instituicao,$this->ref_cod_subnivel);
		$obj_servidor->edita();

		echo "<script>parent.fechaExpansivel( '{$_GET['div']}');window.parent.location.reload(true);</script>";
		die;


		return true;
	}

	function Excluir()
	{

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

	function trocaNiveis()
	{

		var campoCategoria = document.getElementById('ref_cod_categoria').value;
		var campoNivel	= document.getElementById('ref_cod_nivel');
		var campoSubNivel	= document.getElementById('ref_cod_subnivel');

		campoNivel.length = 1;
		campoSubNivel.length = 1;

		if( campoCategoria )
		{
			campoNivel.disabled = true;
			campoNivel.options[0].text = 'Carregando Níveis';
			var xml = new ajax(atualizaLstNiveis);
			xml.envia("educar_niveis_servidor_xml.php?cod_cat="+campoCategoria);
		}
		else
		{
			campoNivel.options[0].text = 'Selecione uma Categoria';
			campoNivel.disabled = false;
			campoSubNivel.options[0].text = 'Selecione um Nível';
			campoSubNivel.disabled = false;
		}
	}

	function atualizaLstNiveis(xml)
	{
		var campoNivel	= document.getElementById('ref_cod_nivel');

		campoNivel.length = 1;
		campoNivel.options[0].text = 'Selecione uma Categoria';
		campoNivel.disabled = false;

		var niveis = xml.getElementsByTagName('nivel');
		if(niveis.length)
		{
			for( var i = 0; i < niveis.length; i++ )
			{
				campoNivel.options[campoNivel.options.length] = new Option( niveis[i].firstChild.data, niveis[i].getAttribute('cod_nivel'),false,false);
			}
		}
		else
		{
			campoNivel.options[0].text = 'Categoria não possui níveis';
		}


	}

	function trocaSubniveis()
	{

		var campoNivel	= document.getElementById('ref_cod_nivel').value;
		var campoSubNivel	= document.getElementById('ref_cod_subnivel');

		campoSubNivel.length = 1;

		if( campoNivel )
		{
			campoSubNivel.disabled = true;
			campoSubNivel.options[0].text = 'Carregando Subníveis';
			var xml = new ajax(atualizaLstSubiveis);
			xml.envia("educar_subniveis_servidor_xml.php?cod_nivel="+campoNivel);
		}
		else
		{
			campoSubNivel.options[0].text = 'Selecione uma Nível';
			campoSubNivel.disabled = false;
		}
	}

	function atualizaLstSubiveis(xml)
	{
		var campoSubNivel	= document.getElementById('ref_cod_subnivel');

		campoSubNivel.length = 1;
		campoSubNivel.options[0].text = 'Selecione um Subnível';
		campoSubNivel.disabled = false;

		var subniveis = xml.getElementsByTagName('subnivel');
		if(subniveis.length)
		{
			for( var i = 0; i < subniveis.length; i++ )
			{
				campoSubNivel.options[campoSubNivel.options.length] = new Option( subniveis[i].firstChild.data, subniveis[i].getAttribute('cod_subnivel'),false,false);
			}
		}
		else
		{
			campoNivel.options[0].text = 'Nível não possui subníveis';
		}


	}

	document.getElementById('ref_cod_categoria').onchange = function(){
		trocaNiveis();
	}

	document.getElementById('ref_cod_nivel').onchange = function(){
		trocaSubniveis();
	}
</script>