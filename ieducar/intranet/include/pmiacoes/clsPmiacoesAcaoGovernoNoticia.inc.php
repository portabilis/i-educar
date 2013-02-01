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

require_once( "include/pmiacoes/geral.inc.php" );

class clsPmiacoesAcaoGovernoNoticia
{
	var $ref_cod_acao_governo;
	var $ref_cod_not_portal;
	var $ref_funcionario_cad;
	var $data_cadastro;

	
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
	 * Construtor (PHP 5)
	 *
	 * @return object
	 */
	function __construct( $ref_cod_acao_governo = null, $ref_cod_not_portal = null, $ref_funcionario_cad = null, $data_cadastro = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo_noticia";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_acao_governo, ref_cod_not_portal, ref_funcionario_cad, data_cadastro";
		
		if( is_numeric( $ref_cod_acao_governo ) )
		{
			$tmp_obj = new clsPmiacoesAcaoGoverno( $ref_cod_acao_governo );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_cod_acao_governo = $ref_cod_acao_governo;
			}
		}
		if( is_numeric( $ref_cod_not_portal ) )
		{
				$this->ref_cod_not_portal = $ref_cod_not_portal;
		}
		if( is_numeric( $ref_funcionario_cad ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_cad );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_cad = $ref_funcionario_cad;
			}
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}

	}
	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmiacoesAcaoGovernoNoticia( $ref_cod_acao_governo = null, $ref_cod_not_portal = null, $ref_funcionario_cad = null, $data_cadastro = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo_noticia";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_acao_governo, ref_cod_not_portal, ref_funcionario_cad, data_cadastro";
		
		if( is_numeric( $ref_cod_acao_governo ) )
		{
			$tmp_obj = new clsPmiacoesAcaoGoverno( $ref_cod_acao_governo );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_cod_acao_governo = $ref_cod_acao_governo;
			}
		}
		if( is_numeric( $ref_cod_not_portal ) )
		{
			$db = new clsBanco();
			$consulta = "select 'X'
						   from not_portal as a
						  where a.cod_not_portal = '{$ref_cod_not_portal}'";
			
			$existe = $db->CampoUnico($consulta);
			
			//$tmp_obj = new ( $ref_cod_not_portal );
			if( $existe != false )
			{
				$this->ref_cod_not_portal = $ref_cod_not_portal;
			}
		}
		if( is_numeric( $ref_funcionario_cad ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_cad );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_cad = $ref_funcionario_cad;
			}
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if(  is_numeric( $this->ref_cod_acao_governo ) && is_numeric( $this->ref_cod_not_portal ) && is_numeric( $this->ref_funcionario_cad )   )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->ref_cod_acao_governo ) )
			{
				$campos .= "{$gruda}ref_cod_acao_governo";
				$valores .= "{$gruda}'{$this->ref_cod_acao_governo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_not_portal ) )
			{
				$campos .= "{$gruda}ref_cod_not_portal";
				$valores .= "{$gruda}'{$this->ref_cod_not_portal}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";

			
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
		if( is_numeric( $this->ref_cod_acao_governo ) && is_numeric( $this->ref_cod_not_portal ) )
		{
			$db = new clsBanco();
			$set = "";
			
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}

			
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_acao_governo = '{$this->ref_cod_acao_governo}' AND ref_cod_not_portal = '{$this->ref_cod_not_portal}'" );
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
	function lista( $int_ref_cod_acao_governo = null, $int_ref_cod_not_portal = null, $int_ref_funcionario_cad = null, $str_data_cadastro_inicio = null, $str_data_cadastro_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_ref_cod_acao_governo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_acao_governo = '{$int_ref_cod_acao_governo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_not_portal ) )
		{
			$filtros .= "{$whereAnd} ref_cod_not_portal = '{$int_ref_cod_not_portal}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_cadastro_inicio ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$str_data_cadastro_inicio}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$str_data_cadastro_fim}'";
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
		if( is_numeric( $this->ref_cod_acao_governo ) && is_numeric( $this->ref_cod_not_portal ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_acao_governo = '{$this->ref_cod_acao_governo}' AND ref_cod_not_portal = '{$this->ref_cod_not_portal}'" );
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
		if( is_numeric( $this->ref_cod_acao_governo ) && is_numeric( $this->ref_cod_not_portal ) )
		{
			
			//	delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_acao_governo = '{$this->ref_cod_acao_governo}' AND ref_cod_not_portal = '{$this->ref_cod_not_portal}'" );
			return true;
			
			
			
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