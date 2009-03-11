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

class clsPessoaEndereco
{
	var $idpes;
	var $idpes_cad;
	var $idpes_rev;
	var $tipo;
	var $cep;
	var $idlog;
	var $idbai;
	var $numero;
	var $complemento;
	var $reside_desde;
	var $letra;
	var $bloco;
	var $apartamento;
	var $andar;

	var $banco = 'gestao_homolog';
	var $schema_cadastro = "cadastro";
	var $tabela = "endereco_pessoa";


	function  clsPessoaEndereco($int_idpes = false, $numeric_cep = false, $int_idlog = false, $int_idbai =false, $numeric_numero=false, $str_complemento=false, $date_reside_desde=false, $str1_letra=false, $str_bloco = false, $int_apartamento = false, $int_andar = false, $idpes_cad = false, $idpes_rev = false)
	{
		$this->idpes = $int_idpes;
		$numeric_cep = idFederal2Int($numeric_cep);
		$obj = new clsCepLogradouroBairro($int_idlog, $numeric_cep, $int_idbai);
		if($obj->detalhe())
		{
			$this->idbai = $int_idbai;
			$this->idlog = $int_idlog;
			$this->cep = $numeric_cep;
		}

		$this->numero = $numeric_numero;
		$this->complemento = $str_complemento;
		$this->reside_desde = $date_reside_desde;
		$this->letra = $str1_letra;
		$this->bloco = $str_bloco;
		$this->apartamento = $int_apartamento;
		$this->andar = $int_andar;
		$this->idpes_cad = $idpes_cad? $idpes_cad : $_SESSION['id_pessoa'];
		$this->idpes_rev = $idpes_rev? $idpes_rev : $_SESSION['id_pessoa'];

	}

	function cadastra()
	{
		// Cadastro do endereço da pessoa na tabela endereco_pessoa
		if($this->idpes && $this->cep && $this->idlog && $this->idbai && $this->idpes_cad)
		{
			$campos = "";
			$valores = "";

			if($this->numero)
			{
				$campos .= ", numero";
				$valores .= ", '$this->numero' ";
			}
			if($this->letra)
			{
				$campos .= ", letra";
				$valores .= ", '$this->letra' ";
			}
			if($this->complemento)
			{
				$campos .= ", complemento";
				$valores .= ", '$this->complemento' ";
			}
			if($this->reside_desde)
			{
				$campos .= ", reside_desde";
				$valores .= ", '$this->reside_desde' ";
			}
			if($this->bloco)
			{
				$campos .= ", bloco";
				$valores .= ", '$this->bloco' ";
			}
			if($this->apartamento)
			{
				$campos .= ", apartamento";
				$valores .= ", '$this->apartamento' ";
			}
			if($this->andar)
			{
				$campos .= ", andar";
				$valores .= ", '$this->andar' ";
			}
			$db = new clsBanco();
			$db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela} (idpes, tipo, cep, idlog, idbai, origem_gravacao, idsis_cad, data_cad, operacao, idpes_cad $campos) VALUES ('$this->idpes', '1', '$this->cep', '$this->idlog', '$this->idbai', 'M', 17, NOW(), 'I', '$this->idpes_cad' $valores)");
			RETURN TRUE;
		}
		RETURN FALSE;
	}



	function edita()
	{
		// Cadastro do endereço da pessoa na tabela endereco_pessoa
		if($this->idpes && $this->idpes_rev)
		{
			$setVir = "SET ";
			$set = "";
			if($this->numero)
			{
				$set .= "$setVir numero = '$this->numero' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir numero = NULL ";
				$setVir = ", ";
			}
			if($this->letra)
			{
				$set .= "$setVir letra = '$this->letra' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir letra = NULL ";
				$setVir = ", ";
			}
			if($this->complemento)
			{
				$set .= "$setVir complemento = '$this->complemento' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir complemento = NULL ";
				$setVir = ", ";
			}
			if($this->reside_desde)
			{
				$set .= "$setVir reside_desde = '$this->reside_desde' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir reside_desde = NULL ";
				$setVir = ", ";
			}
			if($this->bloco)
			{
				$set .= "$setVir bloco = '$this->bloco' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir bloco = NULL ";
				$setVir = ", ";
			}
			if($this->apartamento)
			{
				$set .= "$setVir apartamento = '$this->apartamento' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir apartamento = NULL ";
				$setVir = ", ";
			}
			if($this->andar)
			{
				$set .= "$setVir andar = '$this->andar' ";
				$setVir = ", ";
			}else
			{
				$set .= "$setVir andar = NULL ";
				$setVir = ", ";
			}
			if($this->cep && $this->idbai && $this->idlog)
			{
				$set .= "$setVir cep = '$this->cep', idbai = '$this->idbai', idlog = '$this->idlog'";
				$setVir = ", ";
			}
			if($this->idpes_rev)
			{
				$set .= "$setVir idpes_rev ='$this->idpes_rev'";
			}

			if($set)
			{
				$db = new clsBanco();
				$db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela} $set WHERE idpes = $this->idpes");
				return true;
			}
		}
		return false;
	}

	function exclui()
	{
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela WHERE idpes = $this->idpes");
		}

	}

	function lista($int_idpes = false, $str_ordenacao = false ,$int_inicio_limite = false, $int_qtd_limite = false, $int_cep =false, $int_idlog = false, $int_idbai =false, $int_numero =false, $str_bloco = false, $int_apartamento = false, $int_andar = false, $str_letra = false, $str_complemento = false)
	{
		$whereAnd = "AND ";
		$where = "";

		if( is_numeric( $int_idpes) )
		{
			$where .= "{$whereAnd}idpes = '$int_idpes' ";
			$whereAnd = " AND ";
		}
		elseif (is_string($int_idpes))
		{
			$where .= "{$whereAnd}idpes IN ({$int_idpes}) ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_cep) )
		{
			$where .= "{$whereAnd}cep = '$int_cep' ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_idlog) )
		{
			$where .= "{$whereAnd}idlog = '$int_idlog' ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_idbai) )
		{
			$where .= "{$whereAnd}idbai = '$int_idbai' ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_numero) )
		{
			$where .= "{$whereAnd}numero = '$int_numero' ";
			$whereAnd = " AND ";
		}

		if( $str_bloco )
		{
			$where .= "{$whereAnd}bloco = '$str_bloco' ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_apartamento) )
		{
			$where .= "{$whereAnd}apartamento = '$int_apartamento' ";
			$whereAnd = " AND ";
		}

		if( is_numeric( $int_andar ) )
		{
			$where .= "{$whereAnd}andar = '$int_andar' ";
			$whereAnd = " AND ";
		}

		if(is_string($str_letra))
		{
			$where .= "{$whereAnd}letra = '$str_letra' ";
			$whereAnd = " AND ";
		}

		if(is_string($str_complemento))
		{
			$where .= "{$whereAnd}complemento ILIKE '%$str_complemento%' ";
			$whereAnd = " AND ";
		}

		if( $inicio_limite !== false && $qtd_registros )
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}

		if( $str_orderBy )
		{
			$orderBy .= " ORDER BY $str_orderBy ";
		}

		$db = new clsBanco();
		$db->Consulta( "SELECT COUNT(0) AS total FROM $this->schema_cadastro.$this->tabela WHERE tipo=1 $where" );
		$db->ProximoRegistro();
		$total = $db->Campo( "total" );

		$db = new clsBanco($this->banco);
		//echo "SELECT idpes, tipo, cep, idlog, numero, letra, complemento, reside_desde, idbai, bloco, apartamento, andar FROM $this->schema_cadastro.$this->tabela WHERE tipo=1 $where $orderBy $limite"."<br>"; die();
		$db->Consulta("SELECT idpes, tipo, cep, idlog, numero, letra, complemento, reside_desde, idbai, bloco, apartamento, andar FROM $this->schema_cadastro.$this->tabela WHERE tipo=1 $where $orderBy $limite");
		$resultado = array();
		while ($db->ProximoRegistro())
		{
			$tupla = $db->Tupla();
			$tupla['cep'] = new clsCepLogradouro($tupla['cep'],$tupla['idlog']);
			$tupla['idlog'] = new clsCepLogradouro($tupla['cep'],$tupla['idlog']);
			$tupla['idbai'] = new clsBairro($tupla['idbai']);

			$tupla["total"] = $total;

			$resultado[] = $tupla;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function detalhe()
	{
		if($this->idpes)
		{
			$db = new clsBanco($this->banco);
			$db->Consulta("SELECT idpes, tipo, cep, idlog, numero, letra, complemento, reside_desde, idbai, bloco, apartamento, andar FROM $this->schema_cadastro.$this->tabela WHERE idpes = $this->idpes");

			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$cep = $tupla['cep'];
				$tupla['cep'] = new clsCepLogradouro($cep,$tupla['idlog']);
				$tupla['idlog'] = new clsCepLogradouro($cep,$tupla['idlog']);
				$tupla['idbai'] = new clsBairro($tupla['idbai']);
				return $tupla;
			}
		}
		return false;
	}

	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function existe()
	{
		if( is_numeric( $this->idpes ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->schema_cadastro}.{$this->tabela} WHERE IDPES = '{$this->idpes}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}


}
?>
