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

class clsLogradouro
{
	var $idlog;
	var $idtlog;
	var $nome;
	var $idnum;
	var $geom;
	var $ident_oficial;
	
	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsLogradouro
	 */
	function clsLogradouro( $int_idlog = false, $str_idtlog=false, $str_nome=false, $int_idnum=false, $str_geom=false, $str_ident_oficial=false )
	{
		$this->idlog = $int_idlog;
		
		$objLog = new clsTipoLogradouro($str_idtlog);
		if($objLog->detalhe())
		{
			$this->idtlog = $str_idtlog;
		}
		
		$this->nome = $str_nome;
		$this->idnum = $int_idnum;
		$this->geom = $str_geom;
		$this->ident_oficial = $str_ident_oficial;
		
		$this->tabela = "logradouro";
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
		if( is_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idnum ) && is_string($this->ident_oficial) )
		{
			$campos = "";
			$values = "";
			
			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idtlog, nome, idnum, ident_oficial$campos ) VALUES ( '{$this->idtlog}', '{$this->nome}', '{$this->idnum}'$values )" );
			
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
		if( is_numeric( $this->idlog )  && is_string( $this->idtlog ) && is_string( $this->nome ) && is_numeric( $this->idnum ) && is_string($this->ident_oficial) )
		{
			$set = "SET idtlog = '{$this->idtlog}', nome = '{$this->nome}', idnum = '{$this->idnum}', ident_oficial = '{$this->ident_oficial}'";
			
			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else
			{
				$set .= ", geom = NULL";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idlog = '$this->idlog'" );
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
		if(is_numeric($this->idlog))
		{
			$objEndPessoa = new clsEnderecoPessoa();
			$listaEndPessoa = $objEndPessoa->lista(false, false, false, false, false, $this->idlog);
			
			$objCepLog = new clsCepLogradouro();
			$listaCepLog = $objCepLog->lista(false, $this->idlog);
			
			$objCepLogBai = new clsCepLogradouroBairro();
			$listaCepLogBai = $objCepLogBai->lista($this->idlog);
			
			if(!count($listaEndPessoa) && !count($listaCepLog) && !count($listaCepLogBai))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idlog={$this->idlog}");
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
	function lista( $str_idtlog=false, $str_nome=false, $int_idnum=false, $str_geom=false, $str_ident_oficial=false, $int_limite_ini=0, $int_limite_qtd=20, $str_orderBy = false, $int_idlog=false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_idtlog ) )
		{
//			$str_idtlog = limpa_acentos( $str_idtlog );
			$where .= "{$whereAnd}fcn_upper_nrm( idtlog ) ILIKE fcn_upper_nrm('%$str_idtlog%')";
			$whereAnd = " AND ";
		}
		if( is_string( $int_idlog ) )
		{
			$where .= "{$whereAnd}idlog  = '$int_idlog'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$str_nome = limpa_acentos( $str_nome );
			$where .= "{$whereAnd}fcn_upper_nrm( nome ) ILIKE '%$str_nome%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idnum ) )
		{
			$where .= "{$whereAnd}idmun = '$int_idnum'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		
		if( is_string( $str_ident_oficial ) )
		{
			$where .= "{$whereAnd}ident_oficial LIKE '%$str_ident_oficial%'";
			$whereAnd = " AND ";
		}
		
		if($str_orderBy)
		{
			$orderBy = "ORDER BY $str_orderBy";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_qtd OFFSET $int_limite_ini";
		}
		
		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM {$this->schema}.{$this->tabela} $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );
		
		$db->Consulta( "SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			$tupla["idtlog"] = new clsTipoLogradouro( $tupla["idtlog"] );

			$tupla["total"] = $total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	} 
/**
	 * Exibe uma lista baseada nos parametros de filtragem passados incluindo id municipio
	 *
	 * @return Array
	 */
	function listamun( $str_idtlog=false, $str_nome=false, $int_idnum=false, $int_idmun=false, $str_geom=false, $str_ident_oficial=false, $int_limite_ini=0, $int_limite_qtd=20, $str_orderBy = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_idtlog ) )
		{
			$where .= "{$whereAnd}idtlog LIKE '%$str_idtlog%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$where .= "{$whereAnd}nome LIKE '%$str_nome%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idnum ) )
		{
			$where .= "{$whereAnd}idnum = '$int_idnum'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idmun ) )
		{
			$where .= "{$whereAnd}idmun = '$int_idmun'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$str_geom%'";
			$whereAnd = " AND ";
		}
		
		if( is_string( $str_ident_oficial ) )
		{
			$where .= "{$whereAnd}ident_oficial LIKE '%$str_ident_oficial%'";
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
	
		$db->Consulta( "SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			$tupla["idtlog"] = new clsTipoLogradouro( $tupla["idtlog"] );

			$tupla["total"] = $total;
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
		if($this->idlog)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT idlog, idtlog, nome, idmun, geom, ident_oficial FROM {$this->schema}.{$this->tabela} WHERE idlog='{$this->idlog}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idlog = $tupla["idlog"];
				$this->idtlog = $tupla["idtlog"];
				$this->nome = $tupla["nome"];
				$this->idnum = $tupla["idnum"];
				$this->geom = $tupla["geom"];
				$this->ident_oficial = $tupla["ident_oficial"];
				
				$tupla["idtlog"] = new clsTipoLogradouro( $tupla["idtlog"] );
				return $tupla;
			}
		}
		return false;
	}
}
?>
