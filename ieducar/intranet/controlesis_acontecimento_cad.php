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
require_once( "include/pmicontrolesis/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Acontecimento" );
		$this->processoAp = "605";
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

	var $cod_acontecimento;
	var $ref_cod_tipo_acontecimento;
	var $ref_cod_funcionario_cad;
	var $ref_cod_funcionario_exc;
	var $titulo;
	var $descricao;
	var $dt_inicio;
	var $dt_fim;
	var $hr_inicio;
	var $hr_fim;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $local;
	var $contato;
	var $link;
	var $todas_fotos;

	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->cod_acontecimento=$_GET["cod_acontecimento"];
		
		if($_POST)
		{
			foreach ($_POST as $campo => $valor)
			{
				$this->$campo = $valor;
			}
		}

		if( is_numeric( $this->cod_acontecimento ) )
		{

			$obj = new clsPmicontrolesisAcontecimento( $this->cod_acontecimento );
			$registro  = $obj->detalhe();
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;
				if($this->dt_inicio)
					$this->dt_inicio = dataFromPgToBr( $this->dt_inicio );
				if($this->dt_fim)
					$this->dt_fim = dataFromPgToBr( $this->dt_fim );
				$this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
				$this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

				$this->fexcluir = true;
				$retorno = "Editar";
				
				$db = new clsBanco();
				$db->Consulta("SELECT ref_cod_foto_evento FROM pmicontrolesis.foto_vinc WHERE ref_cod_acontecimento = $this->cod_acontecimento");
				while ($db->ProximoRegistro())
				{
					list($cod) = $db->Tupla();
					$this->qtd_fotos++;
					$this->todas_fotos[] = $cod;
				}
			}
		}
		
		if(!empty($_POST["todas_fotos"]))
			$this->todas_fotos = unserialize(urldecode($_POST["todas_fotos"]));
		if(!empty($_POST["qtd_fotos"]))
			$this->qtd_fotos = $_POST["qtd_fotos"];
		else
			$this->qtd_fotos = 0;
		if( $_POST["id_foto"] != "")
		{
			$conitnua = "true";
			if(is_array($this->todas_fotos))
				foreach($this->todas_fotos as $foto)
				{
					if($_POST["id_foto"] == $foto)
						$conitnua = "false";
				}
			if($conitnua =="true")
				{
					$this->qtd_fotos +=1;
					$this->todas_fotos[] =  $_POST["id_foto"];
				}
		}
		if(!empty($_POST["id_foto_deletar"]))
		{
			foreach($this->todas_fotos as $i=>$id_foto)
			{
				if($id_foto == $_POST["id_foto_deletar"])
				{
					unset($this->todas_fotos[$i] );
					$this->qtd_fotos -= 1;
				}
			}
			$this->id_foto_deletar="";
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "controlesis_acontecimento_det.php?cod_acontecimento={$registro["cod_acontecimento"]}" : "controlesis_acontecimento_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "cod_acontecimento", $this->cod_acontecimento );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPmicontrolesisTipoAcontecimento" ) )
		{
			$objTemp = new clsPmicontrolesisTipoAcontecimento();
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) )
			{
				foreach ( $lista as $registro )
				{
					$opcoes["{$registro['cod_tipo_acontecimento']}"] = "{$registro['nm_tipo']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPmicontrolesisTipoAcontecimento nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "ref_cod_tipo_acontecimento", "Tipo Acontecimento", $opcoes, $this->ref_cod_tipo_acontecimento );

		// text
		$this->campoTexto( "titulo", "Titulo", $this->titulo, 30, 255, true );
		$this->campoMemo( "descricao", "Descric&atilde;o", $this->descricao, 60, 10, false );

		$this->campoTexto( "local", "Local", $this->local, 30, 255 );
		$this->campoTexto( "contato", "Contato", $this->contato, 30, 255 );
		$this->campoTexto( "link", "Link", $this->link, 30, 255 );

		// data
		$this->campoData( "dt_inicio", "Data Inicio", $this->dt_inicio, false );
		$this->campoData( "dt_fim", "Data Final", $this->dt_fim, false );

		// hora
		$this->campoHora( "hr_inicio", "Hora Inicio", $this->hr_inicio, false );
		$this->campoHora( "hr_fim", "Hora Final", $this->hr_fim, false );
		
		//fotos
		$this->campoOculto( "id_foto_deletar", $this->id_foto_deletar );
		$this->campoOculto( "qtd_fotos", $this->qtd_fotos);
		if(is_array($this->todas_fotos))
		{
			foreach($this->todas_fotos as $id=>$foto)
			{
				$this->campoTextoInv( "id_foto_$id", "Fotos", $foto,  "15", "15", true,false,false, "","<a href='#' onclick=\"javascript:excluirSumit({$foto},'id_foto_deletar') \">Clique aqui para Excluir</a>");
			}
		}
		$this->campoOculto( "todas_fotos", serialize($this->todas_fotos));
		$this->campoOculto( "id_foto", $this->id_foto);
		$this->campoProcurarAdicionar("id_foto_", "Vincular com foto", $this->id_foto, 10, 5, "showExpansivel( 500,500, '<iframe name=\'miolo\' id=\'miolo\' frameborder=\'0\' height=\'100%\' width=\'500\' marginheight=\'0\' marginwidth=\'0\' src=\'add_fotos_evento.php?campo=id_foto\'></iframe>');", "Procurar","insereSubmit()","");

		
		
	}

		
	function Novo()
	{
		if(!$this->dt_fim)
		{
			$this->dt_fim = null;
		}
		if(!$this->dt_inicio)
		{
			$this->dt_inicio = null;
		}
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		$obj = new clsPmicontrolesisAcontecimento( $this->cod_acontecimento, $this->ref_cod_tipo_acontecimento, $this->pessoa_logada, null, $this->titulo, $this->descricao, $this->dt_inicio,$this->dt_fim, $this->hr_inicio, $this->hr_fim, null, null, 1, $this->local, $this->contato, $this->link );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$db = new clsBanco();
			$this->todas_fotos =  unserialize(urldecode($this->todas_fotos));
			if(!empty($this->todas_fotos))
			{
				foreach ($this->todas_fotos as $id=>$foto)
				{
					$db->Consulta( "INSERT INTO pmicontrolesis.foto_vinc (ref_cod_acontecimento, ref_cod_foto_evento) VALUES ({$cadastrou}, {$foto})" );
				}
			}
				
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: controlesis_acontecimento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPmicontrolesisAcontecimento\nvalores obrigatorios\nis_numeric( $this->ref_cod_tipo_acontecimento ) && is_numeric( $this->ref_cod_funcionario_cad )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		if(empty($this->dt_fim))
		{
			$this->dt_fim = null;
		}
		if(empty($this->dt_inicio))
		{
			$this->dt_inicio = null;
		}
		$obj = new clsPmicontrolesisAcontecimento($this->cod_acontecimento, $this->ref_cod_tipo_acontecimento, null, $this->pessoa_logada, $this->titulo, $this->descricao, $this->dt_inicio, $this->dt_fim, $this->hr_inicio, $this->hr_fim, null, null, 1, $this->local, $this->contato, $this->link);
		$editou = $obj->edita();
		if( $editou )
		{
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM pmicontrolesis.foto_vinc WHERE ref_cod_acontecimento={$this->cod_acontecimento}");


			$this->todas_fotos =  unserialize(urldecode($this->todas_fotos));
			if(!empty($this->todas_fotos))
			{
				foreach ($this->todas_fotos as $id=>$foto)
				{
					$db->Consulta( "INSERT INTO pmicontrolesis.foto_vinc (ref_cod_acontecimento, ref_cod_foto_evento) VALUES ({$cadastrou}, {$foto})" );
				}
		    }
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_acontecimento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPmicontrolesisAcontecimento\nvalores obrigatorios\nif( is_numeric( $this->cod_acontecimento ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();
		if(empty($this->dt_fim))
		{
			$this->dt_fim = null;
		}
		if(empty($this->dt_inicio))
		{
			$this->dt_inicio = null;
		}
		$obj = new clsPmicontrolesisAcontecimento($this->cod_acontecimento, $this->ref_cod_tipo_acontecimento, null, $this->pessoa_logada, $this->titulo, $this->descricao, $this->dt_inicio, $this->dt_fim, $this->hr_inicio, $this->hr_fim, null, null, 0);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: controlesis_acontecimento_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPmicontrolesisAcontecimento\nvalores obrigatorios\nif( is_numeric( $this->cod_acontecimento ) )\n-->";
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