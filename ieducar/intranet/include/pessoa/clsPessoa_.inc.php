<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
require_once ("include/clsBanco.inc.php");

class clsPessoa_
{
	var $idpes;
	var $nome;
	var $idpes_cad;
	var $data_cad;
	var $url;
	var $tipo;
	var $idpes_rev;
	var $data_rev;
	var $situacao;
	var $origem_gravacao;
	var $email;


	var $banco = 'gestao_homolog';
	var $schema_cadastro = "cadastro";
	var $tabela_pessoa = "pessoa";
	var $tabela_endereco = "endereco_pessoa";
	var $tabela_telefone = "fone_pessoa";


	function  clsPessoa_($int_idpes = false, $str_nome = false, $int_idpes_cad =false, $str_url = false, $int_tipo = false, $int_idpes_rev =false, $str_data_rev = false, $str_email = false )
	{
		$this->idpes = $int_idpes;
		$this->nome = $str_nome;
		$this->idpes_cad = $int_idpes_cad ? $int_idpes_cad  : $_SESSION['id_pessoa'];
		$this->url = $str_url;
		$this->tipo = $int_tipo;
		$this->idpes_rev = is_numeric($int_idpes_rev) ? $int_idpes_rev :  $_SESSION['id_pessoa'];
		$this->data_rev = $str_data_rev;
		$this->email = $str_email;
	}


	function cadastra()
	{

		if($this->nome && $this->tipo)
		{
			$campos = "";
			$valores = "";
			if($this->url)
			{
				$campos .= ", url";
				$valores .= ", '$this->url' ";
			}
			if($this->email)
			{
				$campos .= ", email";
				$valores .= ", '$this->email' ";
			}
			if($this->idpes_cad)
			{
				$campos .= ", idpes_cad";
				$valores .= ", '$this->idpes_cad' ";
			}

			$db = new clsBanco();

			$db->Consulta("INSERT INTO {$this->schema_cadastro}.{$this->tabela_pessoa} (nome, data_cad,tipo,situacao,origem_gravacao,  idsis_cad, operacao $campos) VALUES ('$this->nome', NOW(), '$this->tipo', 'P', 'U', 17, 'I' $valores)");
			$this->idpes = $db->InsertId("{$this->schema_cadastro}.seq_pessoa");

			return $this->idpes;
		}
	}

	function edita()
	{
		//Cadastro de dados na tabela de pessoa
		if($this->idpes)
		{
			$set = "";
			$gruda = "";

			if($this->url || $this->url==="")
			{
				$set .= " url =  '$this->url' ";
				$gruda = ", ";
			}
			if($this->email || $this->email==="")
			{
				$set .= "$gruda email = '$this->email' ";
				$gruda = ", ";
			}
			if($this->nome || $this->nome==="")
			{
				$set .= "$gruda nome = '$this->nome' ";
				$gruda = ", ";
			}

			if($this->idpes_rev)
			{
				$set .= "$gruda idpes_rev = '$this->idpes_rev'";
			}
			if($set)
			{
				$db = new clsBanco();
				$db->Consulta("UPDATE {$this->schema_cadastro}.{$this->tabela_pessoa} SET $set, data_rev = 'NOW()' WHERE idpes = $this->idpes");
				return true;
			}
		}
	}

	function exclui()
	{
		if($this->idpes)
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->schema_cadastro.$this->tabela_pessoa WHERE idpes = $this->idpes");
			return true;
		}
		return false;
	}

	function lista($str_nome = false, $inicio_limite = false, $qtd_registros = false, $str_orderBy = false, $arrayint_idisin=false, $arrayint_idnotin = false, $str_tipo_pessoa = false, $str_email = false, $str_data_cad_ini = false, $str_data_cad_fim = false )
	{
		$whereAnd = "WHERE ";
		$where = "";
		if( is_string( $str_nome ) )
		{
			$str_nome = str_replace(" ", "%", $str_nome);
			$where .= "{$whereAnd} nome ILIKE '%{$str_nome}%' ";
			$whereAnd = " AND ";
		}
		if( is_string( $str_tipo_pessoa ) )
		{
			$where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
			$whereAnd = " AND ";
		}

		if( is_array( $arrayint_idisin ) )
		{
			$ok = true;
			foreach ( $arrayint_idisin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes IN ( " . implode( ",", $arrayint_idisin ) . " )";
				$whereAnd = " AND ";
			}
		}

		if( is_array( $arrayint_idnotin ) )
		{
			$ok = true;
			foreach ( $arrayint_idnotin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes NOT IN ( " . implode( ",", $arrayint_idnotin ) . " )";
				$whereAnd = " AND ";
			}
		}
		if( $inicio_limite !== false && $qtd_registros )
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}

		if(is_string($str_data_cad_ini))
		{
			if(!$str_data_edicao_fim)
			{
				$where .= "{$whereAnd}data_cad >= '$str_data_cad_ini 00:00:00' AND data_cad <= '$str_data_cad_ini 23:59:59'";
				$whereAnd = " AND ";
			}
			else
			{
				$where .= "{$whereAnd}data_cad >= '$str_data_cad_ini'";
				$whereAnd = " AND ";
			}
		}

		if(is_string($str_data_cad_fim))
		{
			$where .= "{$whereAnd}data_cad <= '$str_data_cad_fim'";
			$whereAnd = " AND ";
		}

		$orderBy = " ORDER BY ";
		if( $str_orderBy )
		{
			$orderBy .= "$str_orderBy ";
		}
		else
		{
			$orderBy .= "nome ";
		}

		if( is_string( $str_email) )
		{
			$where .= "{$whereAnd}email ILIKE '%$str_email%' ";
			$whereAnd = " AND ";
		}

		$db = new clsBanco($this->banco);
		$total = $db->UnicoCampo("SELECT count(0) FROM cadastro.pessoa $where");

		//echo "SELECT idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, situacao, origem_gravacao, email FROM cadastro.pessoa $where $orderBy $limite";
		$db->Consulta("SELECT idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, situacao, origem_gravacao, email FROM cadastro.pessoa $where $orderBy $limite");
		/*
			Ordem de retorno da lista:
			1	2	3		4		5	6	7		8		9		10
			idpes,	nome,	idpes_cad,	data_cad,	url,	tipo,	data_rev,	situacao,	origem_grav,	email
		*/
		$resultado = array();
		while ($db->ProximoRegistro())
		{
			$tupla = $db->Tupla();
			$nome = strtolower($tupla['nome']);
			$arrayNome = explode(" ", $nome);
			$nome ="";
			foreach ($arrayNome as $parte) {
				if( $parte != "de" && $parte != "da" && $parte != "dos" && $parte != "do" && $parte != "das" && $parte != "e")
				{
					$nome .= strtoupper(substr($parte,0,1)).substr($parte,1)." ";
				}
				else
				{
					$nome .= $parte." ";
				}
			}
	 		$nome = str_replace( array( "�","�","�","�", "�", "�", "�", "�", "�", "�"),
 							 	 array( "�","�","�","�", "�", "�", "�", "�", "�", "�"), $nome );
			$tupla['nome'] = $nome;
			$tupla['total']= $total;
			$resultado[] = $tupla;

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function listaCod($str_nome = false, $inicio_limite = false, $qtd_registros = false, $str_orderBy = false, $arrayint_idisin=false, $arrayint_idnotin = false, $str_tipo_pessoa = false )
	{
		$whereAnd = "WHERE ";
		$where = "";
		if( is_string( $str_nome ) )
		{
			$where .= "{$whereAnd} fcn_upper_nrm(nome) ILIKE fcn_upper_nrm('%{$str_nome}%') ";
			$whereAnd = " AND ";
		}
		if( is_string( $str_tipo_pessoa ) )
		{
			$where .= "{$whereAnd}tipo = '$str_tipo_pessoa' ";
			$whereAnd = " AND ";
		}

		if( is_array( $arrayint_idisin ) )
		{
			$ok = true;
			foreach ( $arrayint_idisin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes IN ( " . implode( ",", $arrayint_idisin ) . " )";
				$whereAnd = " AND ";
			}
		}

		if( is_array( $arrayint_idnotin ) )
		{
			$ok = true;
			foreach ( $arrayint_idnotin AS $val )
			{
				if( ! is_numeric( $val ) )
				{
					$ok = false;
				}
			}
			if( $ok )
			{
				$where .= "{$whereAnd}idpes NOT IN ( " . implode( ",", $arrayint_idnotin ) . " )";
				$whereAnd = " AND ";
			}
		}
		if( $inicio_limite !== false && $qtd_registros )
		{
			$limite = "LIMIT $qtd_registros OFFSET $inicio_limite ";
		}

		$orderBy = " ORDER BY ";
		if( $str_orderBy )
		{
			$orderBy .= "$str_orderBy ";
		}
		else
		{
			$orderBy .= "nome ";
		}

		$db = new clsBanco($this->banco);
		$db->Consulta("SELECT idpes FROM cadastro.pessoa $where $orderBy $limite");
		$resultado = array();
		while ($db->ProximoRegistro())
		{
			$tupla = $db->Tupla();
			$resultado[] = $tupla['idpes'];

		}
		if(count($resultado) > 0)
		{
			return $resultado;
		}
		return false;
	}

	function detalhe()
	{
		if( is_numeric( $this->idpes ) )
		{
			$db = new clsBanco($this->banco);
			$db->Consulta("SELECT idpes, nome, idpes_cad, data_cad, url, tipo, idpes_rev, data_rev, situacao, origem_gravacao, email FROM cadastro.pessoa WHERE idpes = $this->idpes ");
			if($db->ProximoRegistro())
			{
				$tupla = $db->Tupla();
				$nome = strtolower($tupla['nome']);
				$arrayNome = explode(" ", $nome);
				$arrNovoNome = array();
				foreach ($arrayNome as $parte) {
					if( $parte != "de" && $parte != "da" && $parte != "dos" && $parte != "do" && $parte != "das" && $parte != "e")
					{
						if( $parte != "s.a" && $parte != "ltda" )
						{
							$arrNovoNome[] = strtoupper(substr($parte,0,1)).substr($parte,1);
						}
						else
						{
							$arrNovoNome[] = strtoupper( $parte );
						}
					}
					else
					{
						$arrNovoNome[] = $parte;
					}
				}
				$nome = implode( " ", $arrNovoNome );
		 		//$nome = str_replace( array( "�", "�", "�", "�", "�", 	"�", "�", "�", "�", 	"�", "�", "�", "?", "�", 	"�", "�", "�", "�", "�", 	"�", "�", "�", "?", "�", 	"�", "?", "?", "?", 	"�", 	"�" ),
 				//			array( "�", "�", "�", "�", "�", 	"�", "�", "�", "�", 	"�", "�", "�", "?", "�", 	"�", "�", "�", "�", "�", 	"�", "�", "�", "?", "�",  	"�", "?", "?", "y", 	"�", 	"�"), $nome );
				$tupla['nome'] = $nome;
				list($this->idpes, $this->nome, $this->idpes_cad, $this->data_cad, $this->url, $this->tipo, $this->idpes_rev, $this->data_rev, $this->situacao, $this->origem_gravacao, $this->email ) = $tupla;
				return $tupla;
			}
		}
		return false;
	}
}
?>
