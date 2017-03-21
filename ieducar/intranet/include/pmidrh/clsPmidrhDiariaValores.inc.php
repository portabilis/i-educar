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
* Criado em 20/06/2006 11:38 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhDiariaValores
{
	var $cod_diaria_valores;
	var $ref_funcionario_cadastro;
	var $ref_cod_diaria_grupo;
	var $estadual;
	var $p100;
	var $p75;
	var $p50;
	var $p25;
	var $data_vigencia;
	
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
	function clsPmidrhDiariaValores( $cod_diaria_valores = null, $ref_funcionario_cadastro = null, $ref_cod_diaria_grupo = null, $estadual = null, $p100 = null, $p75 = null, $p50 = null, $p25 = null, $data_vigencia = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}diaria_valores";

		$this->_campos_lista = $this->_todos_campos = "cod_diaria_valores, ref_funcionario_cadastro, ref_cod_diaria_grupo, estadual, p100, p75, p50, p25, data_vigencia";
		
		if( is_numeric( $ref_funcionario_cadastro ) )
		{
			if( class_exists( "clsPmidrhFuncionario" ) )
			{
				$tmp_obj = new clsPmidrhFuncionario( $ref_funcionario_cadastro );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario_cadastro}'" ) )
				{
					$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
				}
			}
		}
		if( is_numeric( $ref_cod_diaria_grupo ) )
		{
			if( class_exists( "clsPmidrhDiariaGrupo" ) )
			{
				$tmp_obj = new clsPmidrhDiariaGrupo( $ref_cod_diaria_grupo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" ) )
				{
					$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
				}
			}
		}

		
		if( is_numeric( $cod_diaria_valores ) )
		{
			$this->cod_diaria_valores = $cod_diaria_valores;
		}
		if( is_numeric( $estadual ) )
		{
			$this->estadual = $estadual;
		}
		if( is_numeric( $p100 ) )
		{
			$this->p100 = $p100;
		}
		if( is_numeric( $p75 ) )
		{
			$this->p75 = $p75;
		}
		if( is_numeric( $p50 ) )
		{
			$this->p50 = $p50;
		}
		if( is_numeric( $p25 ) )
		{
			$this->p25 = $p25;
		}
		if( is_string( $data_vigencia ) )
		{
			$this->data_vigencia = $data_vigencia;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $ref_funcionario_cadastro ) && is_numeric( $ref_cod_diaria_grupo ) && is_numeric( $estadual ) && is_string( $data_vigencia ) )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->ref_funcionario_cadastro ) )
			{
				$campos .= "{$gruda}ref_funcionario_cadastro";
				$valores .= "{$gruda}'{$this->ref_funcionario_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				$campos .= "{$gruda}ref_cod_diaria_grupo";
				$valores .= "{$gruda}'{$this->ref_cod_diaria_grupo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->estadual ) )
			{
				$campos .= "{$gruda}estadual";
				$valores .= "{$gruda}'{$this->estadual}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p100 ) )
			{
				$campos .= "{$gruda}p100";
				$valores .= "{$gruda}'{$this->p100}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p75 ) )
			{
				$campos .= "{$gruda}p75";
				$valores .= "{$gruda}'{$this->p75}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p50 ) )
			{
				$campos .= "{$gruda}p50";
				$valores .= "{$gruda}'{$this->p50}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p25 ) )
			{
				$campos .= "{$gruda}p25";
				$valores .= "{$gruda}'{$this->p25}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_vigencia ) )
			{
				$campos .= "{$gruda}data_vigencia";
				$valores .= "{$gruda}'{$this->data_vigencia}'";
				$gruda = ", ";
			}

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}__seq");
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
		if( is_numeric( $this->cod_diaria_valores ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_funcionario_cadastro ) )
			{
				$set .= "{$gruda}ref_funcionario_cadastro = '{$this->ref_funcionario_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				$set .= "{$gruda}ref_cod_diaria_grupo = '{$this->ref_cod_diaria_grupo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->estadual ) )
			{
				$set .= "{$gruda}estadual = '{$this->estadual}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p100 ) )
			{
				$set .= "{$gruda}p100 = '{$this->p100}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p75 ) )
			{
				$set .= "{$gruda}p75 = '{$this->p75}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p50 ) )
			{
				$set .= "{$gruda}p50 = '{$this->p50}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->p25 ) )
			{
				$set .= "{$gruda}p25 = '{$this->p25}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_vigencia ) )
			{
				$set .= "{$gruda}data_vigencia = '{$this->data_vigencia}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_diaria_valores = '{$this->cod_diaria_valores}'" );
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
	function lista( $int_cod_diaria_valores = null, $int_estadual = null, $int_p100 = null, $int_p75 = null, $int_p50 = null, $int_p25 = null, $date_data_vigencia = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_cod_diaria_valores ) )
		{
			$filtros .= "{$whereAnd} cod_diaria_valores = '{$int_cod_diaria_valores}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cadastro ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cadastro = '{$int_ref_funcionario_cadastro}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_diaria_grupo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_diaria_grupo = '{$int_ref_cod_diaria_grupo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_estadual ) )
		{
			$filtros .= "{$whereAnd} estadual = '{$int_estadual}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_p100 ) )
		{
			$filtros .= "{$whereAnd} p100 = '{$int_p100}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_p75 ) )
		{
			$filtros .= "{$whereAnd} p75 = '{$int_p75}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_p50 ) )
		{
			$filtros .= "{$whereAnd} p50 = '{$int_p50}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_p25 ) )
		{
			$filtros .= "{$whereAnd} p25 = '{$int_p25}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_vigencia_ini ) )
		{
			$filtros .= "{$whereAnd} data_vigencia >= '{$date_data_vigencia_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_vigencia_fim ) )
		{
			$filtros .= "{$whereAnd} data_vigencia <= '{$date_data_vigencia_fim}'";
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
		if( is_numeric( $this->cod_diaria_valores ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_diaria_valores = '{$this->cod_diaria_valores}'" );
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
		if( is_numeric( $this->cod_diaria_valores ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_diaria_valores = '{$this->cod_diaria_valores}'" );
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
		if( is_numeric( $this->cod_diaria_valores ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_diaria_valores = '{$this->cod_diaria_valores}'" );
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