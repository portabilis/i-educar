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


class clsPais
{
	var $idpais;
	var $nome;
	var $geom;

	var $tabela;
	var $schema = "public";

	/**
	 * Construtor
	 *
	 * @return Object:clsPais
	 */
	function clsPais( $int_idpais = false, $int_idpais__=false, $str_nome=false, $str_geom=false )
	{
		if($int_idpais)
			$this->idpais = $int_idpais;
		else 		
			$this->idpais = $int_idpais__;
		$this->nome = $str_nome;
		$this->geom = $str_geom;

		$this->tabela = "pais";
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
		if( is_numeric( $this->idpais )  && is_string( $this->nome ) )
		{
			$campos = "";
			$values = "";
			
			if( is_string( $this->geom ) )
			{
				$campos .= ", geom";
				$values .= ", '{$this->geom}'";
			}

			$db->Consulta( "INSERT INTO {$this->schema}.{$this->tabela} ( idpais, nome$campos ) VALUES ( '{$this->idpais}', '{$this->nome}'" );

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
		if( is_string( $this->nome ) )
		{
			$set = "SET nome = '{$this->nome}'";

			if( is_string( $this->geom ) )
			{
				$set .= ", geom = '{$this->geom}'";
			}
			else 
			{
				$set .= ", geom = NULL";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->schema}.{$this->tabela} $set WHERE idpais = '$this->ispais'" );
			
			return true;
		}
		return false;
	}

	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui( $int_cod_pessoa )
	{
		if(is_numeric($this->idpais))
		{
			$objUf = new clsUf();
			$listaUf = $objUf->lista(false, false, false, $this->idpais);
			
			if(!count($listaUf))
			{
				$db = new clsBanco();
				//$db->Consulta("DELETE FROM {$this->schema}.{$this->tabela} WHERE idpais = {$this->idpais}");
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
	function lista( $int_idpais=false, $str_nome=false, $str_geom=false, $int_limite_ini=0, $int_limite_qtd=20, $str_orderBy = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_string( $str_nome ) )
		{
			$where .= "{$whereAnd}nome LIKE '%$nome%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idpais ) )
		{
			$where .= "{$whereAnd}idpais = '$idpais'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_geom ) )
		{
			$where .= "{$whereAnd}geom LIKE '%$geom%'";
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
		$db->Consulta( "SELECT idpais, nome, geom FROM {$this->schema}.{$this->tabela} $where $orderBy $limit" );
		$resultado = array();
		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();
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
		if($this->idpais)
		{
			
			$db = new clsBanco();
			$db->Consulta("SELECT idpais, nome, geom FROM {$this->schema}.{$this->tabela} WHERE idpais='{$this->idpais}'");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				$this->idpais = $tupla["idpais"];
				$this->nome = $tupla["nome"];
				$this->geom = $tupla["geom"];

				return $tupla;
			}
		}
		return false;
	}
}
?>