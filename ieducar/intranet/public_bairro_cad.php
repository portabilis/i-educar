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
require_once( "include/public/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Bairro" );
		$this->processoAp = "756";
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

	var $idmun;
	var $geom;
	var $idbai;
	var $nome;
	var $idpes_rev;
	var $data_rev;
	var $origem_gravacao;
	var $idpes_cad;
	var $data_cad;
	var $operacao;
	var $idsis_rev;
	var $idsis_cad;

	var $idpais;
	var $sigla_uf;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->idbai=$_GET["idbai"];

		if( is_numeric( $this->idbai ) )
		{
			$obj_bairro = new clsPublicBairro();
			$lst_bairro = $obj_bairro->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idbai );
			if( $lst_bairro )
			{
				$registro = $lst_bairro[0];
			}
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

					
//				$this->fexcluir = true;

				$retorno = "Editar";
			}
		}
		$this->url_cancelar = ($retorno == "Editar") ? "public_bairro_det.php?idbai={$registro["idbai"]}" : "public_bairro_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
		// primary keys
		$this->campoOculto( "idbai", $this->idbai );

		// foreign keys
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsPais" ) )
		{
			$objTemp = new clsPais();
			$lista = $objTemp->lista( false, false, false, false, false, "nome ASC" );
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['idpais']}"] = "{$registro['nome']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsPais nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "idpais", "Pais", $opcoes, $this->idpais );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUf" ) )
		{
			if( $this->idpais ) 
			{
				$objTemp = new clsUf();
				$lista = $objTemp->lista( false, false, $this->idpais, false, false, "nome ASC" );
				if ( is_array( $lista ) && count( $lista ) ) 
				{
					foreach ( $lista as $registro ) 
					{
						$opcoes["{$registro['sigla_uf']}"] = "{$registro['nome']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUf nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "sigla_uf", "Estado", $opcoes, $this->sigla_uf );

		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsMunicipio" ) )
		{
			if( $this->sigla_uf ) 
			{
				$objTemp = new clsMunicipio();
				$lista = $objTemp->lista( false, $this->sigla_uf, false, false, false, false, false, false, false, false, false, "nome ASC" );
				if ( is_array( $lista ) && count( $lista ) ) 
				{
					foreach ( $lista as $registro ) 
					{
						$opcoes["{$registro['idmun']}"] = "{$registro['nome']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsMunicipio nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "idmun", "Munic&iacute;pio", $opcoes, $this->idmun );

		// text
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 255, true );
	}

	function Novo()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPublicBairro( $this->idmun, null, null, $this->nome, null, null, 'U', $this->pessoa_logada, null, 'I', null, 9 );
		$cadastrou = $obj->cadastra();
		if( $cadastrou )
		{
			$this->mensagem .= "Cadastro efetuado com sucesso.<br>";
			header( "Location: public_bairro_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
		echo "<!--\nErro ao cadastrar clsPublicBairro\nvalores obrigatorios\nis_numeric( $this->idmun ) && is_string( $this->nome ) && is_string( $this->origem_gravacao ) && is_string( $this->operacao ) && is_numeric( $this->idsis_cad )\n-->";
		return false;
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPublicBairro( $this->idmun, null, $this->idbai, $this->nome, $this->pessoa_logada, null, 'U', null, null, 'I', null, 9 );
		$editou = $obj->edita();
		if( $editou )
		{
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: public_bairro_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao editar clsPublicBairro\nvalores obrigatorios\nif( is_numeric( $this->idbai ) )\n-->";
		return false;
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsPublicBairro( null, null, $this->idbai, null, $this->pessoa_logada );
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: public_bairro_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsPublicBairro\nvalores obrigatorios\nif( is_numeric( $this->idbai ) )\n-->";
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

document.getElementById('idpais').onchange = function()
{
	var campoPais = document.getElementById('idpais').value;

	var campoUf= document.getElementById('sigla_uf');
	campoUf.length = 1;
	campoUf.disabled = true;
	campoUf.options[0].text = 'Carregando estado...';

	var xml_uf = new ajax( getUf );
	xml_uf.envia( "public_uf_xml.php?pais="+campoPais );
}

function getUf( xml_uf )
{
	var campoUf = document.getElementById('sigla_uf');
	var DOM_array = xml_uf.getElementsByTagName( "uf" );

	if(DOM_array.length)
	{
		campoUf.length = 1;
		campoUf.options[0].text = 'Selecione um estado';
		campoUf.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoUf.options[campoUf.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("sigla_uf"),false,false);
		}
	}
	else
	{
		campoUf.options[0].text = 'O pais não possui nenhum estado';
	}
}

document.getElementById('sigla_uf').onchange = function()
{
	var campoUf = document.getElementById('sigla_uf').value;

	var campoMunicipio= document.getElementById('idmun');
	campoMunicipio.length = 1;
	campoMunicipio.disabled = true;
	campoMunicipio.options[0].text = 'Carregando município...';

	var xml_municipio = new ajax( getMunicipio );
	xml_municipio.envia( "public_municipio_xml.php?uf="+campoUf );
}

function getMunicipio( xml_municipio )
{
	var campoMunicipio = document.getElementById('idmun');
	var DOM_array = xml_municipio.getElementsByTagName( "municipio" );

	if(DOM_array.length)
	{
		campoMunicipio.length = 1;
		campoMunicipio.options[0].text = 'Selecione um município';
		campoMunicipio.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoMunicipio.options[campoMunicipio.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("idmun"),false,false);
		}
	}
	else
	{
		campoMunicipio.options[0].text = 'O estado não possui nenhum município';
	}
}

</script>