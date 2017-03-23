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
require_once ("include/otopic/otopicGeral.inc.php");


class clsPessoaAuxiliar
{
	var $cod_pessoa_auxiliar;
	var $nm_pessoa;
	var $sexo;
	var $cep;
	var $logradouro;
	var $cidade;
	var $bairro;
	var $complemento;
	var $numero;
	var $letra;
	var $numero_ap;
	var $bloco;
	var $andar;
	var $estado;
	var $email;
	var $rg;
	var $data_edicao;
	var $data_nasc;
	
	var $campos_lista;
	var $todos_campos;
	
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object
	 */
	function clsPessoaAuxiliar( $int_cod_pessoa_auxiliar = null, $str_nm_pessoa = null, $str_sexo = null, $int_cep = null, $str_logradouro = null, $str_cidade = null, $str_bairro = null, $str_complemento = null, $int_numero = null, $str_letra = null, $int_numero_ap = null, $int_bloco = null, $int_andar = null, $str_estado = null, $str_email = null, $int_rg = null, $str_data_edicao = null, $str_data_nasc = null )
	{
		if(is_numeric($int_cod_pessoa_auxiliar))
		{
			$this->cod_pessoa_auxiliar = $int_cod_pessoa_auxiliar;
		}
		
		if(is_string($str_nm_pessoa))
		{
			$this->nm_pessoa = $str_nm_pessoa;
		}
		
		if(is_string($str_sexo))
		{
			$this->sexo = $str_sexo;
		}
		
		if(is_numeric($int_cep))
		{
			$this->cep = $int_cep;
		}
		
		if(is_string($str_logradouro))
		{
			$this->logradouro = $str_logradouro;
		}
		
		if(is_string($str_cidade))
		{
			$this->cidade = $str_cidade;
		}
		
		if(is_string($str_bairro))
		{
			$this->bairro = $str_bairro;
		}
		
		if(is_string($str_complemento))
		{
			$this->complemento = $str_complemento;
		}
		
		if(is_numeric($int_numero))
		{
			$this->numero = $int_numero;
		}
		
		if(is_string($str_letra))
		{
			$this->letra = $str_letra;
		}
		
		if(is_numeric($int_numero_ap))
		{
			$this->numero_ap = $int_numero_ap;
		}
		
		if(is_numeric($int_bloco))
		{
			$this->bloco = $int_bloco;
		}
		
		if(is_numeric($int_andar))
		{
			$this->andar = $int_andar;
		}
		
		if(is_string($str_estado))
		{
			$this->estado = $str_estado;
		}
		
		if(is_string($str_email))
		{
			$this->email = $str_email;
		}
		
		if(is_numeric($int_rg))
		{
			$this->rg = $int_rg;
		}
		
		if(is_string($str_data_edicao))
		{
			$this->data_edicao = $str_data_edicao;
		}
		
		if(is_string($str_data_nasc))
		{
			$this->data_nasc = $str_data_nasc;
		}
		
		$this->campos_lista = $this->todos_campos = "cod_pessoa_auxiliar, nm_pessoa, sexo, cep, logradouro, cidade, bairro, complemento, numero, letra, numero_ap, bloco, andar, estado, email, rg, data_edicao, data_nasc";
		
		$this->tabela = "pmiotopic.pessoa_auxiliar";
		
	}
	
	/**
	 * Função que cadastra um novo registro com os valores atuais
	 *
	 * @return bool
	 */
	function cadastra()
	{
		$campos = "data_edicao";
		$valoes = "'NOW()'";
		$virgula = ", ";
		
		if($this->nm_pessoa)
		{
			$campos .= ", nm_pessoa";
			$valoes .= ", '{$this->nm_pessoa}'";
		}
		
		if($this->sexo)
		{
			$campos .= ", sexo";
		 	$valoes .= ", '{$this->sexo}'";
		}
		
		if($this->cep)
		{
			$campos .= ", cep";
		 	$valoes .= ", '{$this->cep}'";
		}
		
		if($this->logradouro)
		{
			$campos .= ", logradouro";
		 	$valoes .= ", '{$this->logradouro}'";
		}
		
		if($this->cidade)
		{
			$campos .= ", cidade";
		 	$valoes .= ", '{$this->cidade}'";
		}
		
		if($this->bairro)
		{
			$campos .= ", bairro";
		 	$valoes .= ", '{$this->bairro}'";
		}
		
		if($this->complemento)
		{
			$campos .= ", complemento";
		 	$valoes .= ", '{$this->complemento}'";
		}
				
		if($this->numero)
		{
			$campos .= ", numero";
		 	$valoes .= ", '{$this->numero}'";
		}
		
		if($this->letra)
		{
			$campos .= ", letra";
		 	$valoes .= ", '{$this->letra}'";
		}
		
		if($this->numero_ap)
		{
			$campos .= ", numero_ap";
		 	$valoes .= ", '{$this->numero_ap}'";
		}
		
		if($this->bloco)
		{
			$campos .= ", bloco";
		 	$valoes .= ", '{$this->bloco}'";
		}
		
		if($this->andar)
		{
			$campos .= ", andar";
		 	$valoes .= ", '{$this->andar}'";
		}
		
		if($this->estado)
		{
			$campos .= ", estado";
		 	$valoes .= ", '{$this->estado}'";
		}
		
		if($this->email)
		{
			$campos .= ", email";
		 	$valoes .= ", '{$this->email}'";
		}
		
		if($this->rg)
		{
			$campos .= ", rg";
		 	$valoes .= ", '{$this->rg}'";
		}
		
		if($this->data_nasc)
		{
			$campos .= ", data_nasc";
		 	$valoes .= ", '{$this->data_nasc}'";
		}
		
		$db = new clsBanco();
		$db->Consulta("INSERT INTO {$this->tabela} ( $campos ) VALUES ( $valoes )");
		return $db->InsertId("pmiotopic.pessoa_auxiliar_cod_pessoa_auxiliar_seq");
		return false;
	}
	
	/**
	 * Edita o registro atual
	 *
	 * @return bool
	 */
	function edita()
	{
		if($this->cod_pessoa_auxiliar)
		{
			$set = "data_edicao = 'NOW()'";
			$virgula = ", ";
			
			if($this->nm_pessoa)
			{
				$set .= ", nm_pessoa = '{$this->nm_pessoa}'";
			}
			
			if($this->sexo)
			{
				$set .= ", sexo = '{$this->sexo}'";
			}
			
			if($this->cep)
			{
				$set .= ", cep = '{$this->cep}'";
			}
			
			if($this->logradouro)
			{
				$set .= ", logradouro = '{$this->logradouro}'";
			}
			
			if($this->cidade)
			{
				$set .= ", cidade = '{$this->cidade}'";
			}
			
			if($this->bairro)
			{
				$set .= ", bairro = '{$this->bairro}'";
			}
			
			if($this->complemento)
			{
				$set .= ", complemento = '{$this->complemento}'";
			}
					
			if($this->numero)
			{
				$set .= ", numero = '{$this->numero}'";
			}
			
			if($this->letra)
			{
				$set .= ", letra = '{$this->letra}'";
			}
			
			if($this->numero_ap)
			{
				$set .= ", numero_ap = '{$this->numero_ap}'";
			}
			
			if($this->bloco)
			{
				$set .= ", bloco = '{$this->bloco}'";
			}
			
			if($this->andar)
			{
				$set .= ", andar = '{$this->andar}'";
			}
			
			if($this->estado)
			{
				$set .= ", estado = '{$this->estado}'";
			}
			
			if($this->email)
			{
				$set .= ", email = '{$this->email}'";
			}
			
			if($this->rg)
			{
				$set .= ", rg = '{$this->rg}'";
			}
			
			if($this->data_nasc)
			{
				$set .= ", data_nasc = '{$this->data_nasc}'";
			}
			
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->tabela} SET $set WHERE cod_pessoa_auxiliar = '{$this->cod_pessoa_auxiliar}'");
			return true;
		}
		return false;
	}
	
	/**
	 * Remove o registro atual
	 *
	 * @return bool
	 */
	function exclui( )
	{
		// verifica se existe um ID definido para delecao
		if( $this->cod_pessoa_auxiliar )
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM {$this->tabela} WHERE cod_pessoa_auxiliar='{$this->cod_pessoa_auxiliar}'");
			return true;
		}
		return false;
	}
	
	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setcampos_lista($str_campos)
	{
		$this->campos_lista = $str_campos;
	}
	
	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetcampos_lista()
	{
		$this->campos_lista = $this->todos_campos;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $str_nm_pessoa = null, $str_sexo = null, $int_cep = null, $str_logradouro = null, $str_cidade = null, $str_bairro = null, $str_complemento = null, $int_numero = null, $str_letra = null, $int_numero_ap = null, $int_bloco = null, $int_andar = null, $str_estado = null, $str_email = null, $int_rg = null, $str_data_edicao_ini = null, $str_data_edicao_fim = null, $int_limite_ini = false, $int_limite_qtd = false, $str_order_by = false, $int_cod_pessoa_auxiliar = false, $str_data_nasc_ini = false, $str_data_nasc_fim = false, $int_mes_aniversario = false )
	{
		// verificacoes de filtros a serem usados
		$where = "";
		$and = "";
		
		if(is_string($int_cod_pessoa_auxiliar))
		{
			$where .= " $and cod_pessoa_auxiliar IN ({$int_cod_pessoa_auxiliar})";
			$and = " AND ";
		}
		
		if(is_string($str_nm_pessoa))
		{
			$where .= " $and fcn_upper_nrm(nm_pessoa) ILIKE fcn_upper_nrm('%{$str_nm_pessoa}%')";
			$and = " AND ";
		}
		
		if(is_string($str_sexo))
		{
			$where .= " $and sexo ILIKE '%$str_sexo%'";
			$and = " AND ";
		}
		
		if(is_numeric($int_cep))
		{
			$where .= " $and cep = '$int_cep'";
			$and = " AND ";
		}
		
		if(is_string($str_logradouro))
		{
			$where .= " $and logradouro ILIKE '%$str_logradouro%'";
			$and = " AND ";
		}
		
		if(is_string($str_cidade))
		{
			$where .= " $and cidade ILIKE '%$str_cidade%'";
			$and = " AND ";
		}
		
		if(is_string($str_bairro))
		{
			$where .= " $and bairro ILIKE '%$str_bairro%'";
			$and = " AND ";
		}
		
		if(is_string($str_complemento))
		{
			$where .= " $and complemento ILIKE '%$str_complemento%'";
			$and = " AND ";
		}
		
		if(is_numeric($int_numero))
		{
			$where .= " $and numero = '$int_numero'";
			$and = " AND ";
		}
		
		if(is_string($str_letra))
		{
			$where .= " $and letra ILIKE '%$str_letra%'";
			$and = " AND ";
		}
		
		if(is_numeric($int_numero_ap))
		{
			$where .= " $and numero_ap = '$int_numero_ap'";
			$and = " AND ";
		}
		
		if(is_numeric($int_bloco))
		{
			$where .= " $and bloco = '$int_bloco'";
			$and = " AND ";
		}
		
		if(is_numeric($int_andar))
		{
			$where .= " $and andar = '$int_andar'";
			$and = " AND ";
		}
		
		if(is_string($str_estado))
		{
			$where .= " $and estado ILIKE '%$str_estado%'";
			$and = " AND ";
		}
		
		if(is_string($str_email))
		{
			$where .= " $and email ILIKE '%$str_email%'";
			$and = " AND ";
		}
		
		if(is_numeric($int_rg))
		{
			$where .= " $and rg = '$int_rg'";
			$and = " AND ";
		}
		
		if(is_string($str_data_edicao_ini))
		{
			if(!$str_data_edicao_fim)
			{
				$where .= "{$whereAnd}data_edicao >= '$str_data_edicao_ini 00:00:00' AND data_edicao <= '$str_data_edicao_ini 23:59:59'";
				$whereAnd = " AND ";
			}
			else
			{ 
				$where .= "{$whereAnd}data_edicao >= '$str_data_edicao_ini'";
				$whereAnd = " AND ";
			}
		}
		
		if(is_string($str_data_edicao_fim))
		{
			$where .= "{$whereAnd}data_edicao <= '$str_data_edicao_fim'";
			$whereAnd = " AND ";
		}
		
		if(is_string($str_data_nasc_ini))
		{
			$dia = substr($str_data_nasc_ini, 8, 2);
			$mes = substr($str_data_nasc_ini, 5, 2);
			$ano = substr($str_data_nasc_ini, 0, 4);
			
			$operador = $str_data_nasc_fim ? ">=" : "=";
			
			if($dia != "" && $dia != "00")
			{
				$where .= "{$whereAnd}EXTRACT(DAY FROM data_nasc) {$operador} '{$dia}'";
				$whereAnd = " AND ";
			}
			
			if($mes != "" && $mes != "00")
			{
				$where .= "{$whereAnd}EXTRACT(MONTH FROM data_nasc) {$operador} '{$mes}'";
				$whereAnd = " AND ";
			}
			
			if($ano != "" && $ano != "0000")
			{
				$where .= "{$whereAnd}EXTRACT(YEAR FROM data_nasc) {$operador} '{$ano}'";
				$whereAnd = " AND ";
			}
		}
		
		if(is_string($str_data_nasc_fim))
		{
			$dia = substr($str_data_nasc_fim, 8, 2);
			$mes = substr($str_data_nasc_fim, 5, 2);
			$ano = substr($str_data_nasc_fim, 0, 4);
			
			if($dia != "" && $dia != "00")
			{
				$where .= "{$whereAnd}EXTRACT(DAY FROM data_nasc) <= '{$dia}'";
				$whereAnd = " AND ";
			}
			
			if($mes != "" && $mes != "00")
			{
				$where .= "{$whereAnd}EXTRACT(MONTH FROM data_nasc) <= '{$mes}'";
				$whereAnd = " AND ";
			}
			
			if($ano != "" && $ano != "0000")
			{
				$where .= "{$whereAnd}EXTRACT(YEAR FROM data_nasc) <= '{$ano}'";
				$whereAnd = " AND ";
			}
		}
		
		if(is_numeric($int_mes_aniversario))
		{
			$where .= "{$whereAnd} EXTRACT (MONTH FROM data_nasc) = '$int_mes_aniversario'";
			$whereAnd = " AND ";
		}
		
		$orderBy = "";
		if( is_string( $str_order_by))
		{
			$orderBy = "ORDER BY $str_order_by";
		}
		
		if($where)
		{
			$where = " WHERE $where";
		}
		
		if($int_limite_ini !== false && $int_limite_qtd)
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		$db = new clsBanco();
		$total = $db->UnicoCampo( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		
		//echo "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit"; die();
		$db->Consulta( "SELECT ".$this->campos_lista." FROM {$this->tabela} $where $orderBy $limit" );
		
		$resultado = array();
		$countCampos = count( explode( ",", $this->campos_lista ) );
		
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
			
			if($countCampos > 1 )
			{
				$tupla["total"] = $total;
				$resultado[] = $tupla;
			}
			else 
			{
				$resultado[] = $tupla["$this->campos_lista"];
			}
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
		if( $this->cod_pessoa_auxiliar )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->todos_campos} FROM {$this->tabela} WHERE  cod_pessoa_auxiliar = '$this->cod_pessoa_auxiliar'" );
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				
				$this->ativo = $tupla['ativo'];
				
				return $tupla;
			}
		}
		return false;
	}
}
?>
