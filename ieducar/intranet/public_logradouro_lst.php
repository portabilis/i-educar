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
require_once( "include/public/geral.inc.php" );
require_once( "include/urbano/clsUrbanoTipoLogradouro.inc.php" );

class clsIndexBase extends clsBase
{
	function Formular()
	{
		$this->SetTitulo( "{$this->_instituicao} Logradouro" );
		$this->processoAp = "757";
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

	var $idlog;
	var $idtlog;
	var $nome;
	var $idmun;
	var $geom;
	var $ident_oficial;
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

	function Gerar()
	{
		@session_start();
		$this->__pessoa_logada = $_SESSION['id_pessoa'];
		session_write_close();

		$this->__titulo = "Logradouro - Listagem";

		foreach( $_GET AS $var => $val ) // passa todos os valores obtidos no GET para atributos do objeto
			$this->$var = ( $val === "" ) ? null: $val;

		$this->addBanner( "imagens/nvp_top_intranet.jpg", "imagens/nvp_vert_intranet.jpg", "Intranet" );

		$this->addCabecalhos( array(
			"Tipo",
			"Nome",
			"Munic&iacute;pio",
			"Estado",
			"Pais"
		) );

		// Filtros de Foreign Keys
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
		$this->campoLista( "idpais", "Pais", $opcoes, $this->idpais, "", false, "", "", false, false );

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
		$this->campoLista( "sigla_uf", "Estado", $opcoes, $this->sigla_uf, "", false, "", "", false, false );

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
		$this->campoLista( "idmun", "Munic&iacute;pio", $opcoes, $this->idmun, "", false, "", "", false, false );
		
		$opcoes = array( "" => "Selecione" );
		if( class_exists( "clsUrbanoTipoLogradouro" ) )
		{
			$objTemp = new clsUrbanoTipoLogradouro();
			$objTemp->setOrderby( "descricao ASC" );
			$lista = $objTemp->lista();
			if ( is_array( $lista ) && count( $lista ) ) 
			{
				foreach ( $lista as $registro ) 
				{
					$opcoes["{$registro['idtlog']}"] = "{$registro['descricao']}";
				}
			}
		}
		else
		{
			echo "<!--\nErro\nClasse clsUrbanoTipoLogradouro nao encontrada\n-->";
			$opcoes = array( "" => "Erro na geracao" );
		}
		$this->campoLista( "idtlog", "Tipo de Logradouro", $opcoes, $this->idtlog, "", false, "", "", false, false );
		
		$this->campoTexto( "nome", "Nome", $this->nome, 30, 255, false );
		

		// Paginador
		$this->__limite = 20;
		$this->__offset = ( $_GET["pagina_{$this->nome}"] ) ? $_GET["pagina_{$this->nome}"]*$this->__limite-$this->__limite: 0;

		$obj_logradouro = new clsPublicLogradouro();
		$obj_logradouro->setOrderby( "nome ASC" );
		$obj_logradouro->setLimite( $this->__limite, $this->__offset );

		$lista = $obj_logradouro->lista(
			$this->idtlog,
			$this->nome,
			$this->idmun,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			$this->idpais,
			$this->sigla_uf
		);

		$total = $obj_logradouro->_total;
		
		// monta a lista
		if( is_array( $lista ) && count( $lista ) )
		{
			foreach ( $lista AS $registro )
			{
				// pega detalhes de foreign_keys
				if( class_exists( "clsUrbanoTipoLogradouro" ) )
				{
					$obj_idtlog = new clsUrbanoTipoLogradouro( $registro["idtlog"] );
					$det_idtlog = $obj_idtlog->detalhe();
					$registro["idtlog"] = $det_idtlog["descricao"];
				}
				else
				{
					$registro["idtlog"] = "Erro na geracao";
					echo "<!--\nErro\nClasse nao existente: clsUrbanoTipoLogradouro\n-->";
				}

				$this->addLinhas( array(
					"<a href=\"public_logradouro_det.php?idlog={$registro["idlog"]}\">{$registro["idtlog"]}</a>",
					"<a href=\"public_logradouro_det.php?idlog={$registro["idlog"]}\">{$registro["nome"]}</a>",
					"<a href=\"public_logradouro_det.php?idlog={$registro["idlog"]}\">{$registro["nm_municipio"]}</a>",
					"<a href=\"public_logradouro_det.php?idlog={$registro["idlog"]}\">{$registro["nm_estado"]}</a>",
					"<a href=\"public_logradouro_det.php?idlog={$registro["idlog"]}\">{$registro["nm_pais"]}</a>"
				) );
			}
		}
		$this->addPaginador2( "public_logradouro_lst.php", $total, $_GET, $this->nome, $this->__limite );

		$this->acao = "go(\"public_logradouro_cad.php\")";
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
	var DOM_array = xml_uf.getElementsByTagName( "estado" );

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