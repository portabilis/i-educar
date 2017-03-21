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
/**
* @author Prefeitura Municipal de Itajaí
*
* Criado em 01/08/2006 11:40 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCalendarioDiaAnotacao
{
	var $ref_dia;
	var $ref_mes;
	var $ref_ref_cod_calendario_ano_letivo;
	var $ref_cod_calendario_anotacao;
	
	// propriedades padrao
	
	/**
	 * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
	 *
	 * @var int
	 */
	var $_total;
	
	/**
	 * Nome do schema
	 *
	 * @var string
	 */
	var $_schema;
	
	/**
	 * Nome da tabela
	 *
	 * @var string
	 */
	var $_tabela;
	
	/**
	 * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
	 *
	 * @var string
	 */
	var $_campos_lista;
	
	/**
	 * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
	 *
	 * @var string
	 */
	var $_todos_campos;
	
	/**
	 * Valor que define a quantidade de registros a ser retornada pelo metodo lista
	 *
	 * @var int
	 */
	var $_limite_quantidade;
	
	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	var $_limite_offset;
	
	/**
	 * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
	 *
	 * @var string
	 */
	var $_campo_order_by;
	
	
	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmieducarCalendarioDiaAnotacao( $ref_dia = null, $ref_mes = null, $ref_ref_cod_calendario_ano_letivo = null, $ref_cod_calendario_anotacao = null )
	{

		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}calendario_dia_anotacao";

		$this->_campos_lista = $this->_todos_campos = "ref_dia, ref_mes, ref_ref_cod_calendario_ano_letivo, ref_cod_calendario_anotacao";
		
		if( is_numeric( $ref_cod_calendario_anotacao ) )
		{
			if( class_exists( "clsPmieducarCalendarioAnotacao" ) )
			{
				$tmp_obj = new clsPmieducarCalendarioAnotacao( $ref_cod_calendario_anotacao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_calendario_anotacao = $ref_cod_calendario_anotacao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_calendario_anotacao = $ref_cod_calendario_anotacao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.calendario_anotacao WHERE cod_calendario_anotacao = '{$ref_cod_calendario_anotacao}'" ) )
				{
					$this->ref_cod_calendario_anotacao = $ref_cod_calendario_anotacao;
				}
			}
		}
		if( is_numeric( $ref_ref_cod_calendario_ano_letivo ) && is_numeric( $ref_mes ) && is_numeric( $ref_dia ) )
		{
			if( class_exists( "clsPmieducarCalendarioDia" ) )
			{
				$tmp_obj = new clsPmieducarCalendarioDia( $ref_ref_cod_calendario_ano_letivo, $ref_mes, $ref_dia );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_ref_cod_calendario_ano_letivo = $ref_ref_cod_calendario_ano_letivo;
						$this->ref_mes = $ref_mes;
						$this->ref_dia = $ref_dia;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_ref_cod_calendario_ano_letivo = $ref_ref_cod_calendario_ano_letivo;
						$this->ref_mes = $ref_mes;
						$this->ref_dia = $ref_dia;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.calendario_dia WHERE ref_cod_calendario_ano_letivo = '{$ref_ref_cod_calendario_ano_letivo}' AND mes = '{$ref_mes}' AND dia = '{$ref_dia}'" ) )
				{
					$this->ref_ref_cod_calendario_ano_letivo = $ref_ref_cod_calendario_ano_letivo;
					$this->ref_mes = $ref_mes;
					$this->ref_dia = $ref_dia;
				}
			}
		}

		

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
//		/echo " is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao )";die;
		if( is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao ) )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->ref_dia ) )
			{
				$campos .= "{$gruda}ref_dia";
				$valores .= "{$gruda}'{$this->ref_dia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_mes ) )
			{
				$campos .= "{$gruda}ref_mes";
				$valores .= "{$gruda}'{$this->ref_mes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) )
			{
				$campos .= "{$gruda}ref_ref_cod_calendario_ano_letivo";
				$valores .= "{$gruda}'{$this->ref_ref_cod_calendario_ano_letivo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_calendario_anotacao ) )
			{
				$campos .= "{$gruda}ref_cod_calendario_anotacao";
				$valores .= "{$gruda}'{$this->ref_cod_calendario_anotacao}'";
				$gruda = ", ";
			}

		
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return true;
		}
		return false;
	}
	
	/**
	 * Edita os dados de um registro
	 *
	 * @return bool
	 */
	function edita()
	{
		if( is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao ) )
		{

			$db = new clsBanco();
			$set = "";



			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'" );
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_ref_dia = null, $int_ref_mes = null, $int_ref_ref_cod_calendario_ano_letivo = null, $int_ref_cod_calendario_anotacao = null,$is_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_ref_dia ) )
		{
			$filtros .= "{$whereAnd} ref_dia = '{$int_ref_dia}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_mes ) )
		{
			$filtros .= "{$whereAnd} ref_mes = '{$int_ref_mes}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_ref_cod_calendario_ano_letivo ) )
		{
			$filtros .= "{$whereAnd} ref_ref_cod_calendario_ano_letivo = '{$int_ref_ref_cod_calendario_ano_letivo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_calendario_anotacao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_calendario_anotacao = '{$int_ref_cod_calendario_anotacao}'";
			$whereAnd = " AND ";
		}
		if($is_ativo){
			$filtros .= "{$whereAnd} exists (SELECT 1 FROM pmieducar.calendario_anotacao WHERE calendario_anotacao.cod_calendario_anotacao = ref_cod_calendario_anotacao and ativo = 1)";
			$whereAnd = " AND ";			
		}

		
		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();
		
		$sql .= $filtros . $this->getOrderby() . $this->getLimite();
		
		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} {$filtros}" );
		
		$db->Consulta( $sql );
		
		if( $countCampos > 1 )
		{
			while ( $db->ProximoRegistro() ) 
			{
				$tupla = $db->Tupla();
			
				$tupla["_total"] = $this->_total;
				$resultado[] = $tupla;
			}
		}
		else 
		{
			while ( $db->ProximoRegistro() ) 
			{
				$tupla = $db->Tupla();
				$resultado[] = $tupla[$this->_campos_lista];
			}
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}
	
	/**
	 * Retorna um array com os dados de um registro
	 *
	 * @return array
	 */
	function detalhe()
	{
		if( is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
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
		if( is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}
	
	/**
	 * Exclui um registro
	 *
	 * @return bool
	 */
	function excluir()
	{
		if( is_numeric( $this->ref_dia ) && is_numeric( $this->ref_mes ) && is_numeric( $this->ref_ref_cod_calendario_ano_letivo ) && is_numeric( $this->ref_cod_calendario_anotacao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_dia = '{$this->ref_dia}' AND ref_mes = '{$this->ref_mes}' AND ref_ref_cod_calendario_ano_letivo = '{$this->ref_ref_cod_calendario_ano_letivo}' AND ref_cod_calendario_anotacao = '{$this->ref_cod_calendario_anotacao}'" );
		return true;
		*/

		
		}
		return false;
	}
	
	/**
	 * Define quais campos da tabela serao selecionados na invocacao do metodo lista
	 *
	 * @return null
	 */
	function setCamposLista( $str_campos )
	{
		$this->_campos_lista = $str_campos;
	}
	
	/**
	 * Define que o metodo Lista devera retornoar todos os campos da tabela
	 *
	 * @return null
	 */
	function resetCamposLista()
	{
		$this->_campos_lista = $this->_todos_campos;
	}
	
	/**
	 * Define limites de retorno para o metodo lista
	 *
	 * @return null
	 */
	function setLimite( $intLimiteQtd, $intLimiteOffset = null )
	{
		$this->_limite_quantidade = $intLimiteQtd;
		$this->_limite_offset = $intLimiteOffset;
	}
	
	/**
	 * Retorna a string com o trecho da query resposavel pelo Limite de registros
	 *
	 * @return string
	 */
	function getLimite()
	{
		if( is_numeric( $this->_limite_quantidade ) )
		{
			$retorno = " LIMIT {$this->_limite_quantidade}";
			if( is_numeric( $this->_limite_offset ) )
			{
				$retorno .= " OFFSET {$this->_limite_offset} ";
			}
			return $retorno;
		}
		return "";
	}
	
	/**
	 * Define campo para ser utilizado como ordenacao no metolo lista
	 *
	 * @return null
	 */
	function setOrderby( $strNomeCampo )
	{
		// limpa a string de possiveis erros (delete, insert, etc)
		//$strNomeCampo = eregi_replace();
		
		if( is_string( $strNomeCampo ) && $strNomeCampo )
		{
			$this->_campo_order_by = $strNomeCampo;
		}
	}
	
	/**
	 * Retorna a string com o trecho da query resposavel pela Ordenacao dos registros
	 *
	 * @return string
	 */
	function getOrderby()
	{
		if( is_string( $this->_campo_order_by ) )
		{
			return " ORDER BY {$this->_campo_order_by} ";
		}
		return "";
	}
	
}
?>