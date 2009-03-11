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
require_once ("include/clsBanco.inc.php");
require_once ("include/Geral.inc.php");

class clsMunicipio
{
	var $idmun;
	var	$nome;
	var	$sigla_uf;
	var	$area_km2;
	var	$idmreg;
	var	$idasmun;
	var	$cod_ibge;
	var	$geom;
	var	$tipo;
	var	$idmun_pai;

	var $idpes_cad;
	var $idpes_rev;
	var $origem_gravacao;
	var $operacao;
	var $idsis_cad;
	var $idsis_rev;

	var $tabela;
	var $schema = "public";

	var $_total;

	/**
	 * Construtor
	 *
	 * @return Object:clsMunicipio
	 */
	function clsMunicipio( $int_idmun = false, $str_nome = false, $str_sigla_uf = false, $int_area_km2 = false, $int_idmreg = false, $int_idasmun = false, $int_cod_ibge = false, $str_geom =false, $str_tipo = false, $int_idmun_pai = false, $int_idpes_cad = false, $int_idpes_rev = false, $str_origem_gravacao = false, $str_operacao=false, $int_idsis_cad=false, $int_idsis_rev=false )
	{
		if($int_idmun)
		{
			$this->idmun = $int_idmun;
		}

		$this->nome = $str_nome;

		$objUf = new clsUf($str_sigla_uf);
		if($objUf->detalhe())
		{
			$this->sigla_uf = $str_sigla_uf;
		}

		$this->area_km2 = $int_area_km2;
		$this->idmreg = $int_idmreg;

		$objPais = new clsPais($int_idasmun);
		if($objPais->detalhe())
		{
			$this->idasmun = $int_idasmun;
		}

		$this->cod_ibge = $int_cod_ibge;
		$this->geom = $str_geom;
		$this->tipo = $str_tipo;
		$this->idpes_cad = $int_idpes_cad;
		$this->idpes_rev = $int_idpes_rev;
		$this->idsis_cad = $int_idsis_cad;
		$this->idsis_rev = $int_idsis_rev;
		$this->operacao = $str_operacao;
		$this->origem_gravacao = $str_origem_gravacao;


		$objPais = new clsPais($int_idmun_pai);
		if($objPais->detalhe())
		{
			$this->idmun_pai = $int_idmun_pai;
		}

		$this->tabela = "municipio";
	}

	/**
	 * Funcao que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$db = new clsBanco();
		// verificacoes de campos obrigatorios para insercao
		if( is_numeric( $this->idmun )  && is_string( $this->nome ) && is_string( $this->sigla_uf ) && is_string( $this->tipo ) && is_numeric( $this->idpes_cad ) && is_numeric( $this->idsis_cad ) && is_string( $this->operacao ) && is_string($this->origem_gravacao ) )
		{
			$campos = "";
			$values = "";

			if( is_numeric( $this->area_km2 ) )
			{
				$campos .= ", area_km2";
				$values .= ", '{$this->area_km2}'";
			}
			if( is_numeric( $this->idpes_cad ) )
			{
				$campos .= ", idpes_cad";
				$values .= ", '{$this->idpes_cad}'";
			}
			if( is_numeric( $this->idsis_cad ) )
			{
				$campos .= ", idsis_cad";
				$values .= ", '{$this->idsis_cad}'";
			}
			if( is_string( $this->operacao) )
			{
				$campos .= ", operacao";
				$values .= ", '{$this->operacao}'";
			}
			if( is_string( $this->origem_gravacao) )
			{
				$campos .= ", origem_gravacao";
				$values .= ", '{$this->origem_gravacao}'";
			}
			if( is_numeric( $this->idmreg ) )
			{
				$campos .= ", idmreg";
				$values .= ", '{$this->idmreg}'";
			}
			if( is_numeric( $this->idasmun ) )
			{
				$campos .= ", idasmun";
				$values .= ", '{$this->idasmun}'";
			}
			if( is_numeric( $this->cod_ibge ) )
			{
				$campos .= ", cod_ibge";
				$values .= ", '{$this->cod_ibge}'";
			}
			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}
			if( is_numeric( $this->idmun_pai ) )
			{
				$campos .= ", idmun_pai";
				$values .= ", '{$this->idmun_pai}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idmun, nome, sigla_uf, tipo, data_cad$campos ) VALUES ( '{$this->idmun}', '{$this->nome}', '{$this->sigla_uf}', '{$this->tipo}', NOW()$values )" );

			return true;
		}
		return false;
	}

	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{
		// verifica campos obrigatorios para edicao
		if( is_string( $this->nome ) && is_string( $this->sigla_uf ) && is_string( $this->tipo ) )
		{
			$set = "SET nome = '{$this->nome}', sigla_uf = '{$this->sigla_uf}', tipo = '{$this->tipo}'";

			if( is_numeric( $this->area_km2 ) )
			{
				$set .= ", area_km2 = '{$this->area_km2}'";
			}
			else
			{
				$set .= ", area_km2 = NULL";
			}

			if( is_numeric( $this->idmreg ) )
			{
				$set .= ", idmreg = '{$this->idmreg}'";
			}
			else
			{
				$set .= ", idmreg = NULL";
			}

			if( is_numeric( $this->idasmun ) )
			{
				$set .= ", idasmun = '{$this->idasmun}'";
			}
			else
			{
				$set .= ", idasmun = NULL";
			}

			if( is_numeric( $this->cod_ibge ) )
			{
				$set .= ", cod_ibge = '{$this->cod_ibge}'";
			}
			else
			{
				$set .= ", cod_ibge = NULL";
			}

			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else
			{
				$set .= ", geom = NULL";
			}

			if( is_numeric( $this->idmun_pai ) )
			{
				$set .= ", idmun_pai = '{$this->idmun_pai}'";
			}
			else
			{
				$set .= ", idmun_pai = NULL";
			}

			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idmun = '$this->idmun'" );
			return true;
		}
		return false;
	}

	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui()
	{
		if(is_numeric($this->idmun))
		{
			$objBairro = new clsBairro();
			$listaBairro = $objBairro->lista($this->idmun);

			$objVila = new clsVila();
			$listaVila = $objVila->lista($this->idmun);

			$objLog = new clsLogradouro();
			$listaLog = $objLog->lista(false, false, $this->idmun);

			if(!count($listaBairro) && !count($listaVila) && !count($listaLog))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idmun={$this->idmun}");
				return true;
			}
			return false;
		}
		return false;
	}

	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_nome = false, $str_sigla_uf = false, $int_area_km2 = false, $int_idmreg = false, $int_idasmun = false, $int_cod_ibge = false, $str_geom =false, $str_tipo = false, $int_idmun_pai = false, $int_limite_ini=false, $int_limite_qtd=false, $str_orderBy = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_nome ) )
		{
			$where .= "{$whereAnd}nome LIKE '%$str_nome%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_sigla_uf ) )
		{
			$where .= "{$whereAnd}sigla_uf LIKE '%$str_sigla_uf%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_area_km2 ) )
		{
			$where .= "{$whereAnd}area_km2 = '$int_area_km2'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmreg ) )
		{
			$where .= "{$whereAnd}idmreg = '$int_area_km2'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idasmun ) )
		{
			$where .= "{$whereAnd}idasmun = '$int_idasmun'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cod_ibge ) )
		{
			$where .= "{$whereAnd}cod_ibge = '$int_cod_ibge'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_tipo ) )
		{
			$where .= "{$whereAnd}tipo LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmun_pai ) )
		{
			$where .= "{$whereAnd}idmun_pai = '$int_idmun_pai'";
			$whereAnd = " AND ";
		}

		if($str_orderBy)
		{
			$orderBy = "ORDER BY $str_orderBy";
		}

		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		$db->Consulta( "SELECT idmun, nome, sigla_uf, area_km2, idmreg, idasmun, cod_ibge, geom , tipo, idmun_pai FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
			$tupla["sigla_uf"] = new clsUf( $tupla["sigla_uf"] );
			$tupla["idasmun"] = new clsUf( $tupla["idasmun"] );
			$tupla["idmun_pai"] = new clsUf( $tupla["idamun_pai"] );
			$tupla["total"] = $total;
			$this->_total = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna um array com os detalhes do objeto
	 *
	 * @return Array
	 */
	function detalhe()
	{
		if($this->idmun)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idmun, nome, sigla_uf, area_km2, idmreg, idasmun, cod_ibge, geom , tipo, idmun_pai FROM {$this->schema}.{$this->tabela} WHERE idmun={$this->idmun}");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idmun = $tupla["idmun"];
				$this->nome = $tupla["nome"];
				$this->sigla_uf = $tupla["sigla_uf"];
				$this->area_km2 = $tupla["area_km2"];
				$this->idmreg = $tupla["idmreg"];
				$this->idasmun = $tupla["idasmun"];
				$this->cod_ibge = $tupla["cod_ibge"];
				$this->geom = $tupla["geom"];
				$this->tipo = $tupla["tipo"];
				$this->idmun_pai = $tupla["idmun_pai"];

				$tupla["sigla_uf"] = new clsUf( $tupla["sigla_uf"] );
				$tupla["idasmun"] = new clsUf( $tupla["idasmun"] );
				$tupla["idmun_pai"] = new clsUf( $tupla["idamun_pai"] );

				return $tupla;
			}
		}
		return false;
	}

	/**
	 * Retorna a proxima chave do a inserir no Banco
	 *
	 * @return integer
	 */

	function proximaChave()
	{
		$db = new clsBanco();
		$db->Consulta("SELECT MAX(idmun) FROM {$this->schema}.{$this->tabela}");
		if( $db->ProximoRegistro() )
		{
			list($chave) = $db->Tupla();
			$chave++;
			$this->idmun = $chave;
			return $this->idmun;
		}
		return false;
	}
}
?>
