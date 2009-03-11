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

class clsReuniao
{
	var $cod_reuniao;
	var $ref_moderador;
	var $ref_grupos_moderador;
	var $descricao;
	var $email_enviado;
	var $data_inicio_marcado;
	var $data_fim_marcado;
	var $data_inicio_real;
	var $data_fim_real;
	var $publica;
	var $camposLista;
	var $todosCampos;
	var $tabela;

	/**
	 * Construtor
	 *
	 * @return Object:clsnatureza
	 */
	function clsReuniao( $int_cod_reuniao = false, $int_ref_moderador = false, $int_ref_grupos_moderador = false, $str_descricao = false,  $bool_email_enviado = false,  $date_data_inicio_marcado = false, $date_data_fim_marcado = false, $date_data_inicio_real = false, $date_date_fim_real = false, $int_publica = false )
	{
		$this->cod_reuniao = is_numeric($int_cod_reuniao) ? $int_cod_reuniao : false;
		$this->ref_moderador = is_numeric($int_ref_moderador) ? $int_ref_moderador : false;
		$this->ref_grupos_moderador = is_numeric($int_ref_grupos_moderador) ? $int_ref_grupos_moderador : false;
		$this->descricao = is_string($str_descricao) ? $str_descricao : false;
		$this->email_enviado = is_numeric($bool_email_enviado) ? $bool_email_enviado : false;
		$this->data_inicio_marcado = is_string($date_data_inicio_marcado) ? $date_data_inicio_marcado : false;
		$this->data_fim_marcado = is_string($date_data_fim_marcado) ? $date_data_fim_marcado : false;
		$this->data_inicio_real = is_string($date_data_inicio_real) ? $date_data_inicio_real : false;
		$this->data_fim_real = is_string($date_date_fim_real) ? $date_date_fim_real : false;
		$this->publica = is_numeric($int_publica) ? $int_publica : false;
		
		$this->camposLista = $this->todosCampos = "cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, email_enviado, publica";
		$this->tabela = "pmiotopic.reuniao";
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
		if( $this->ref_moderador && $this->ref_grupos_moderador && $this->descricao && $this->data_inicio_marcado && $this->data_fim_marcado)
		{
			$campos = "";
			$valores = "";
			
			if($this->publica)
			{
				$campos .= ", publica";
				$valores .= ", $this->publica";
			}
			if($this->email_enviado)
			{
				$campos = ", email_enviado";
				$valores = ", NOW()";
			}
			//echo "INSERT INTO {$this->tabela} ( ref_moderador, ref_grupos_moderador, descricao, data_inicio_marcado, data_fim_marcado $campos ) VALUES ( '{$this->ref_moderador}', '{$this->ref_grupos_moderador}', '{$this->descricao}', '{$this->data_inicio_marcado}', '{$this->data_fim_marcado}' $valores )";
			//die();
			$db->Consulta( "INSERT INTO {$this->tabela} ( ref_moderador, ref_grupos_moderador, descricao, data_inicio_marcado, data_fim_marcado $campos ) VALUES ( '{$this->ref_moderador}', '{$this->ref_grupos_moderador}', '{$this->descricao}', '{$this->data_inicio_marcado}', '{$this->data_fim_marcado}' $valores )" );
			// Retorna Id cadastrado
			return $db->InsertId("pmiotopic.reuniao_cod_reuniao_seq");
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
		if( $this->cod_reuniao )
		{
			$setVir = "SET";
			if($this->descricao)
			{
				$set .= "$setVir descricao = '$this->descricao'";
				$setVir = ", ";

			}
			if($this->data_fim_marcado)
			{
				$set .= "$setVir data_fim_marcado = '$this->data_fim_marcado'";
				$setVir = ", ";				
			}				
			if($this->data_inicio_marcado)
			{
				$set .= "$setVir data_inicio_marcado = '$this->data_inicio_marcado'";
				$setVir = ", ";				
			}			
			if($this->data_fim_real)
			{
				$set .= "$setVir data_fim_real = '$this->data_fim_real'";
				$setVir = ", ";				
			}			
			if($this->data_inicio_real)
			{
				$set .= "$setVir data_inicio_real = '$this->data_inicio_real'";
				$setVir = ", ";				
			}		
			if($this->email_enviado)
			{
				$set .= "$setVir email_enviado = NOW()";
				$setVir = ", ";				
			}else 
			{
				$set .= "$setVir email_enviado = NULL";
				$setVir = ", ";						
			}
			if($this->publica)
			{
				$set .= "$setVir publica = '$this->publica'";
				$setVir = ", ";				
			}

			if($set)
			{
				$db = new clsBanco();
				//echo ( "UPDATE {$this->tabela} $set  WHERE cod_reuniao = '{$this->cod_reuniao}' " );
				//die();
				$db->Consulta( "UPDATE {$this->tabela} $set  WHERE cod_reuniao = '{$this->cod_reuniao}' " );
				return true;
			}
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
		if( $this->cod_reuniao )
		{
			$db = new clsBanco();
			$db->Consulta("DELETE FROM $this->tabela  WHERE cod_reuniao = '{$this->cod_reuniao}' ");
			return true;
		}
		return false;
	}
	
	/**
	 * Indica quais os campos da tabela serão selecionados
	 *
	 * @return Array
	 */
	function setCamposLista($str_campos)
	{
		$this->camposLista = $str_campos;
	}
	
	/**
	 * Indica todos os campos da tabela para busca
	 *
	 * @return void
	 */
	function resetCamposLista()
	{
		$this->camposLista = $this->todosCampos;
	}
	
	/**
	 * Exibe uma lista baseada nos parametros de filtragem passados
	 *
	 * @return Array
	 */
	function lista( $int_ref_moderador = false, $int_ref_grupos_moderador =false, $str_ordenacao = false, $date_data_inicio_marcado_ini=false, $date_data_inicio_marcado_fim = false, $int_limite_ini = false, $int_limite_qtd = false, $bool_nao_finalizadas = false, $bool_finalizadas = false, $int_publica = false, $date_data_inicio_real_ini=false, $date_data_inicio_real_fim = false )
	{
		// verificacoes de filtros a serem usados
		$whereAnd = "WHERE ";
		if( is_numeric( $int_ref_moderador ) )
		{
			$where .= "{$whereAnd}ref_moderador = '$int_ref_moderador'";
			$whereAnd = " AND ";
		}		
		
		if( is_numeric( $int_ref_grupos_moderador ) )
		{
			$where .= "{$whereAnd}ref_grupos_moderador = '$int_ref_grupos_moderador'";
			$whereAnd = " AND ";
		}
		elseif (is_array( $int_ref_grupos_moderador ))
		{
			$aux = implode(",", $int_ref_grupos_moderador);
			$where .= "{$whereAnd}ref_grupos_moderador IN ($aux)";
			$whereAnd = " AND ";
		}
		
		if( is_string( $date_data_inicio_marcado_ini) )
		{
			$where .= "{$whereAnd}data_inicio_marcado >= '$date_data_inicio_marcado_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_marcado_fim ) )
		{
			$where .= "{$whereAnd}data_inicio_marcado <= '$date_data_inicio_marcado_fim'";
			$whereAnd = " AND ";
		}
		
		if( is_string( $date_data_inicio_real_ini) )
		{
			$where .= "{$whereAnd}data_inicio_real >= '$date_data_inicio_real_ini'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_inicio_real_fim ) )
		{
			$where .= "{$whereAnd}data_inicio_real <= '$date_data_inicio_real_fim'";
			$whereAnd = " AND ";
		}
				
		if(  $bool_nao_finalizadas ) 
		{
			$where .= "{$whereAnd}data_fim_real is null";
			$whereAnd = " AND ";
		}
		if(  $bool_finalizadas ) 
		{
			$where .= "{$whereAnd}data_fim_real is NOT null";
			$whereAnd = " AND ";
		}
		if(  $int_publica ) 
		{
			$where .= "{$whereAnd}publica = '$int_publica'";
			$whereAnd = " AND ";
		}
				
		$orderBy = "";
		if( is_string( $str_ordenacao ) )
		{
			$orderBy = "ORDER BY $str_ordenacao";
		}
		
		$limit = "";
		if( is_numeric( $int_limite_ini ) && is_numeric( $int_limite_qtd ) )
		{
			$limit = " LIMIT $int_limite_ini,$int_limite_qtd";
		}
		
		// Seleciona o total de registro da tabela
		$db = new clsBanco();
		$total = $db->CampoUnico( "SELECT COUNT(0) AS total FROM {$this->tabela} $where" );
		
		$db->Consulta( "SELECT ".$this->camposLista." FROM {$this->tabela} $where $orderBy $limit" );
		
		$resultado = array();
		$countCampos = count( explode( ",", $this->camposLista ) );
		
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
				$resultado[] = $tupla["$this->camposLista"];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
	
	/**
	 * Exibe uma lista com os primeiros elementos correspondentes a data passada
	 *
	 * @return Array
	 */
	function listaReunioesData($ref_moderador, $inicio, $fim, $x, $y, $bool_nao_finalizada = false, $bool_finalizada = false)
	{
		if($bool_nao_finalizada)
		{
			$whereAnd = " AND ";
			$where .= "{$whereAnd}data_fim_real IS NULL";
		}
		
		if($bool_finalizada)
		{
			$whereAnd = " AND ";
			$where .= "{$whereAnd}data_fim_real IS NOT NULL";
		}
		
		
		$limit = " LIMIT $x,$y";
		$db = new clsBanco();
		//echo "<pre>";
		//echo ("SELECT cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, publica, email_enviado, 1 AS hoje FROM {$this->tabela} WHERE data_inicio_marcado < '$fim' AND data_inicio_marcado > '$inicio' AND ref_moderador = $ref_moderador $where UNION SELECT cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, publica, email_enviado, 0 AS hoje FROM {$this->tabela} WHERE data_inicio_marcado > '$fim' OR data_inicio_marcado < '$inicio' AND ref_moderador = $ref_moderador $where ORDER BY hoje DESC, data_inicio_marcado DESC $limit");
		//die();
		$db->Consulta("SELECT cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, publica, email_enviado, 1 AS hoje FROM {$this->tabela} WHERE data_inicio_marcado < '$fim' AND data_inicio_marcado > '$inicio' AND ref_moderador = $ref_moderador $where UNION SELECT cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, publica, email_enviado, 0 AS hoje FROM {$this->tabela} WHERE data_inicio_marcado > '$fim' OR data_inicio_marcado < '$inicio' AND ref_moderador = $ref_moderador $where ORDER BY hoje DESC, data_inicio_marcado DESC $limit");
	    $resultado = array();
		while ( $db->ProximoRegistro() ) 
		{
			$tupla = $db->Tupla();
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
		if($this->cod_reuniao)
		{
			$db = new clsBanco();
			$db->Consulta("SELECT cod_reuniao, ref_moderador, ref_grupos_moderador, data_inicio_marcado, data_fim_marcado, data_inicio_real, data_fim_real, descricao, email_enviado, publica FROM {$this->tabela} WHERE cod_reuniao = '$this->cod_reuniao' ");
			if( $db->ProximoRegistro() )
			{
				$tupla = $db->Tupla();
				return $tupla;
			}
		}
		return false;
	}
}
?>
