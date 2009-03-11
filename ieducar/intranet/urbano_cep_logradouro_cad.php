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
require_once( "include/urbano/geral.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Cep Logradouro" );
		$this->processoAp = "758";
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

	var $idlog;
	var $nroini;
	var $nrofin;
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
	var $idmun;
	
	var $tab_cep = array();
	var $cep;
	var $idbai;
	
	function Inicializar()
	{
		$retorno = "Novo";
		@session_start();
		$this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$this->idlog=$_GET["idlog"];

		if( /*is_numeric( $this->cep ) &&*/ is_numeric( $this->idlog ) )
		{
			$obj_cep_logradouro = new clsUrbanoCepLogradouro();
			$lst_cep_logradouro = $obj_cep_logradouro->lista( null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $this->idlog );
			if( $lst_cep_logradouro )
			{
				$registro = $lst_cep_logradouro[0];
			}
			
			if( $registro )
			{
				foreach( $registro AS $campo => $val )	// passa todos os valores obtidos no registro para atributos do objeto
					$this->$campo = $val;

//				$this->fexcluir = true;

				$retorno = "Editar";
				
			//********************* CEP **********************//
			
				$obj_cep_logradouro_bairro = new clsCepLogradouroBairro();
				$lst_cep_logradouro_bairro = $obj_cep_logradouro_bairro->lista( $this->idlog, false, false, "cep ASC" );
				if ($lst_cep_logradouro_bairro)
				{
					foreach ($lst_cep_logradouro_bairro as $cep)
					{
						$this->tab_cep[] = array( int2CEP($cep['cep']->cep), $cep['idbai']->idbai );
					}
				}
							
			//********************* FIM CEP **********************//
			}
		}
		else
		{
			$this->tab_cep[] = array();
		}
		
		$this->url_cancelar = ($retorno == "Editar") ? "urbano_cep_logradouro_det.php?cep={$registro["cep"]}&idlog={$registro["idlog"]}" : "urbano_cep_logradouro_lst.php";
		$this->nome_url_cancelar = "Cancelar";
		return $retorno;
	}

	function Gerar()
	{
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
		
		
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsLogradouro" ) )
		{
			if( $this->idmun ) 
			{
				$objTemp = new clsLogradouro();
				$lista = $objTemp->lista( false, false, $this->idmun, false, false, false, false, "nome ASC" );
				if ( is_array( $lista ) && count( $lista ) ) 
				{
					foreach ( $lista as $registro ) 
					{
						$opcoes["{$registro['idlog']}"] = "{$registro['nome']}";
					}
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsLogradouro nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "idlog", "Logradouro", $opcoes, $this->idlog );
		
	//*********************** TABELA CEP *************************//
		$this->campoTabelaInicio( "tab_cep", "Tabela de CEP", array( 'CEP', 'Bairro' ), $this->tab_cep, 400 );

		$opcoes_bairro = array( "" => "Selecione" );
		
		if( $this->idmun ) 
		{
			$obj_bairro = new clsBairro();
			$lst_bairro = $obj_bairro->lista( $this->idmun, false, false, false, false, "nome ASC" );
			if ($lst_bairro)
			{
				foreach ($lst_bairro as $campo)
				{
					$opcoes_bairro[$campo['idbai']] = $campo['nome'];
				}
			}
		}
		$this->campoCep( "cep", "CEP", $this->cep, true );
		$this->campoLista( "idbai", "Bairro", $opcoes_bairro, $this->idbai );

		$this->campoTabelaFim();
	//*********************** FIM TABELA CEP *************************//		
		
		
	}

	function Novo()
	{
		$this->Editar();
		/*
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if( ($this->idbai[0] != "") && ($this->cep[0] != "") ) 
		{
			foreach( $this->cep AS $id => $cep )
			{
				$cep = idFederal2int($cep);
				
				$obj = new clsUrbanoCepLogradouro( $cep, $this->idlog, null, null, null, null, 'U', $this->pessoa_logada, null, 'I', null, 9 );
				if( $obj->cadastra() )
				{
					$obj_cep_log_bairro = new clsUrbanoCepLogradouroBairro( $this->idlog, $cep, $this->idbai[$id], null, null, 'U', $this->pessoa_logada, null, 'I', null, 9 );
					if( !$obj_cep_log_bairro->cadastra() ) 
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao editar clsUrbanoCepLogradouro\nvalores obrigatorios\nif( is_numeric( $cep ) && is_numeric( $this->idlog ) && is_numeric( {$this->idbai[$id]} ) && is_numeric( $this->pessoa_logada ) )\n-->";
						return false;
					}
				}
			}
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: urbano_cep_logradouro_lst.php" );
			die();
			return true;
		}
		else 
		{
			$this->mensagem = "É necessario adicionar pelo menos um CEP e bairro.<br>";
			return false;
		}
		*/
	}

	function Editar()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		if( ($this->idbai[0] != "") && ($this->cep[0] != "") ) 
		{
			foreach( $this->cep AS $id => $cep )
			{
				$cep = idFederal2int($cep);
				
				$obj = new clsUrbanoCepLogradouro( $cep, $this->idlog, null, null, null, null, 'U', $this->pessoa_logada, null, 'I', null, 9 );
				if( !$obj->existe() ) 
				{
					if( !$obj->cadastra() )
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao editar clsUrbanoCepLogradouro\nvalores obrigatorios\nif( is_numeric( $cep ) && is_numeric( $this->idlog ) && is_numeric( $this->pessoa_logada ) )\n-->";
						return false;
					}
				}
				
				$obj_cep_log_bairro = new clsUrbanoCepLogradouroBairro( $this->idlog, $cep, $this->idbai[$id], null, null, 'U', $this->pessoa_logada, null, 'I', null, 9 );
				if( !$obj_cep_log_bairro->existe() ) 
				{
					if( !$obj_cep_log_bairro->cadastra() ) 
					{
						$this->mensagem = "Cadastro n&atilde;o realizado.<br>";
						echo "<!--\nErro ao editar clsUrbanoCepLogradouroBairro\nvalores obrigatorios\nif( is_numeric( $cep ) && is_numeric( $this->idlog ) && is_numeric( {$this->idbai[$id]} ) && is_numeric( $this->pessoa_logada ) )\n-->";
						return false;
					}
				}
			}
			$this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
			header( "Location: urbano_cep_logradouro_lst.php" );
			die();
			return true;
		}
		else 
		{
			$this->mensagem = "É necessario adicionar pelo menos um CEP e bairro.<br>";
			return false;
		}
	}

	function Excluir()
	{
		@session_start();
		 $this->pessoa_logada = $_SESSION['id_pessoa'];
		@session_write_close();

		$obj = new clsUrbanoCepLogradouro($this->cep, $this->idlog, $this->nroini, $this->nrofin, $this->idpes_rev, $this->data_rev, $this->origem_gravacao, $this->idpes_cad, $this->data_cad, $this->operacao, $this->idsis_rev, $this->idsis_cad);
		$excluiu = $obj->excluir();
		if( $excluiu )
		{
			$this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
			header( "Location: urbano_cep_logradouro_lst.php" );
			die();
			return true;
		}

		$this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
		echo "<!--\nErro ao excluir clsUrbanoCepLogradouro\nvalores obrigatorios\nif( is_numeric( $this->cep ) && is_numeric( $this->idlog ) )\n-->";
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

document.getElementById('idmun').onchange = function()
{
	var campoMunicipio = document.getElementById('idmun').value;

	var campoLogradouro = document.getElementById('idlog');
	campoLogradouro.length = 1;
	campoLogradouro.disabled = true;
	campoLogradouro.options[0].text = 'Carregando logradouro...';

	var xml_logradouro = new ajax( getLogradouro );
	xml_logradouro.envia( "public_logradouro_xml.php?mun="+campoMunicipio );
	
	for( var i = 0; i < tab_add_1.id; i++ )
	{
		var campoBairro = document.getElementById('idbai['+i+']');
		campoBairro.length = 1;
		campoBairro.disabled = true;
		campoBairro.options[0].text = 'Carregando bairro...';
	}
	var xml_bairro = new ajax( getBairro );
	xml_bairro.envia( "public_bairro_xml.php?mun="+campoMunicipio );
}

function getLogradouro( xml_logradouro )
{
	var campoLogradouro = document.getElementById('idlog');
	var DOM_array = xml_logradouro.getElementsByTagName( "logradouro" );

	if(DOM_array.length)
	{
		campoLogradouro.length = 1;
		campoLogradouro.options[0].text = 'Selecione um logradouro';
		campoLogradouro.disabled = false;

		for( var i = 0; i < DOM_array.length; i++ )
		{
			campoLogradouro.options[campoLogradouro.options.length] = new Option( DOM_array[i].firstChild.data, DOM_array[i].getAttribute("idlog"),false,false);
		}
	}
	else
	{
		campoLogradouro.options[0].text = 'O município não possui nenhum logradouro';
	}
}

function getBairro( xml_bairro )
{
	var DOM_array = xml_bairro.getElementsByTagName( "bairro" );
	
	for( var i = 0; i < tab_add_1.id; i++ )
	{
		var campoBairro = document.getElementById('idbai['+i+']');

		if(DOM_array.length)
		{
			campoBairro.length = 1;
			campoBairro.options[0].text = 'Selecione um bairro';
			campoBairro.disabled = false;
	
			for( var j = 0; j < DOM_array.length; j++ )
			{
				campoBairro.options[campoBairro.options.length] = new Option( DOM_array[j].firstChild.data, DOM_array[j].getAttribute("idbai"),false,false);
			}
		}
		else
		{
			campoBairro.options[0].text = 'O município não possui nenhum bairro';
		}
	
	}
}

document.getElementById('btn_add_tab_add_1').onclick = function()
{
	tab_add_1.addRow();
	
	var campoMunicipio = document.getElementById('idmun').value;
	
	var pos = tab_add_1.id - 1;
	
	var campoBairro = document.getElementById('idbai['+pos+']');
	campoBairro.length = 1;
	campoBairro.disabled = true;
	campoBairro.options[0].text = 'Carregando bairro...';
	
	var xml_bairro = new ajax( getBairroUnico );
	xml_bairro.envia( "public_bairro_xml.php?mun="+campoMunicipio );
}

function getBairroUnico( xml_bairro )
{
	var pos = tab_add_1.id - 1;
	var campoBairro = document.getElementById('idbai['+pos+']');

	var DOM_array = xml_bairro.getElementsByTagName( "bairro" );
	
	if(DOM_array.length)
	{
		campoBairro.length = 1;
		campoBairro.options[0].text = 'Selecione um bairro';
		campoBairro.disabled = false;

		for( var j = 0; j < DOM_array.length; j++ )
		{
			campoBairro.options[campoBairro.options.length] = new Option( DOM_array[j].firstChild.data, DOM_array[j].getAttribute("idbai"),false,false);
		}
	}
	else
	{
		campoBairro.options[0].text = 'O município não possui nenhum bairro';
	}
}

/*
**
QND SELECIONADO UM LOGRADOURO, BUSCA POR REGISTROS DE CEP E BAIRRO
JA EXISTENTES NA BASE DE DADOS
**
*/
document.getElementById('idlog').onchange = function()
{
	var campoLogradouro = document.getElementById('idlog').value;

	var xml_cep = new ajax( getCepBairro );
	xml_cep.envia( "urbano_cep_logradouro_bairro_xml.php?log="+campoLogradouro );
}

function getCepBairro( xml_cep )
{
	var campoLogradouro = document.getElementById('idlog');
	var DOM_array = xml_cep.getElementsByTagName( "cep_bairro" );
	
	if(DOM_array.length)
	{
		for( var i = 0; i < DOM_array.length; i++ )
		{
			if( i != 0 )
			{
				tab_add_1.addRow();
			}
			
			var campoCep = document.getElementById('cep['+i+']');
			var cep = DOM_array[i].getAttribute("cep");
			campoCep.value = cep.substring(0,5) + "-" + cep.substring(5);
		}
		
		var campoMunicipio = document.getElementById('idmun').value;
		
		var xml_bairro = new ajax( getBairro_, DOM_array );
		xml_bairro.envia( "public_bairro_xml.php?mun="+campoMunicipio );
	}
}

function getBairro_( xml_bairro, DOM_array )
{
	var DOM_array_ = xml_bairro.getElementsByTagName( "bairro" );
	DOM_array = DOM_array[0];
	
	for( var i = 0; i < tab_add_1.id; i++ )
	{
		var campoBairro = document.getElementById('idbai['+i+']');

		if(DOM_array_.length)
		{
			campoBairro.length = 1;
			campoBairro.options[0].text = 'Selecione um bairro';
			campoBairro.disabled = false;
	
			for( var j = 0; j < DOM_array_.length; j++ )
			{
				campoBairro.options[campoBairro.options.length] = new Option( DOM_array_[j].firstChild.data, DOM_array_[j].getAttribute("idbai"),false,false);
			}
			campoBairro.value = DOM_array[i].firstChild.data;
		}
		else
		{
			campoBairro.options[0].text = 'O município não possui nenhum bairro';
		}
	
	}
}

</script>