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

class clsPmiacoesAcaoGovernoFoto
{
	var $cod_acao_governo_foto;
	var $ref_funcionario_cad;
	var $ref_cod_acao_governo;
	var $nm_foto;
	var $caminho;
	var $data_foto;
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
	function __construct( $cod_acao_governo_foto = null, $ref_funcionario_cad = null, $ref_cod_acao_governo = null, $nm_foto = null, $caminho = null, $data_foto = null, $data_cadastro = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo_foto";

		$this->_campos_lista = $this->_todos_campos = "cod_acao_governo_foto, ref_funcionario_cad, ref_cod_acao_governo, nm_foto, caminho, data_foto, data_cadastro";
		
		if( is_numeric( $cod_acao_governo_foto ) )
		{
			$this->cod_acao_governo_foto = $cod_acao_governo_foto;
		}
		if( is_numeric( $ref_funcionario_cad ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_cad );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_cad = $ref_funcionario_cad;
			}
		}
		if( is_numeric( $ref_cod_acao_governo ) )
		{
			$tmp_obj = new clsPmiacoesAcaoGoverno( $ref_cod_acao_governo );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_cod_acao_governo = $ref_cod_acao_governo;
			}
		}
		if( is_string( $nm_foto ) )
		{
			$this->nm_foto = $nm_foto;
		}
		if( is_string( $caminho ) )
		{
			$this->caminho = $caminho;
		}
		if( is_string( $data_foto ) )
		{
			$this->data_foto = $data_foto;
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
	function clsPmiacoesAcaoGovernoFoto( $cod_acao_governo_foto = null, $ref_funcionario_cad = null, $ref_cod_acao_governo = null, $nm_foto = null, $caminho = null, $data_foto = null, $data_cadastro = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo_foto";

		$this->_campos_lista = $this->_todos_campos = "cod_acao_governo_foto, ref_funcionario_cad, ref_cod_acao_governo, nm_foto, caminho, data_foto, data_cadastro";
		
		if( is_numeric( $cod_acao_governo_foto ) )
		{
			$this->cod_acao_governo_foto = $cod_acao_governo_foto;
		}
		if( is_numeric( $ref_funcionario_cad ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_cad );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_cad = $ref_funcionario_cad;
			}
		}
		if( is_numeric( $ref_cod_acao_governo ) )
		{
			$tmp_obj = new clsPmiacoesAcaoGoverno( $ref_cod_acao_governo );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_cod_acao_governo = $ref_cod_acao_governo;
			}
		}
		if( is_string( $nm_foto ) )
		{
			$this->nm_foto = $nm_foto;
		}
		if( is_string( $caminho ) )
		{
			$this->caminho = $caminho;
		}
		if( is_string( $data_foto ) )
		{
			$this->data_foto = $data_foto;
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
		if(  is_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->ref_cod_acao_governo ) && is_string( $this->nm_foto ) && is_string( $this->caminho )   )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->cod_acao_governo_foto ) )
			{
				$campos .= "{$gruda}cod_acao_governo_foto";
				$valores .= "{$gruda}'{$this->cod_acao_governo_foto}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acao_governo ) )
			{
				$campos .= "{$gruda}ref_cod_acao_governo";
				$valores .= "{$gruda}'{$this->ref_cod_acao_governo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_foto ) )
			{
				$campos .= "{$gruda}nm_foto";
				$valores .= "{$gruda}'{$this->nm_foto}'";
				$gruda = ", ";
			}
			if( is_string( $this->caminho ) )
			{
				$campos .= "{$gruda}caminho";
				$valores .= "{$gruda}'{$this->caminho}'";
				$gruda = ", ";
			}
			
			if( is_string( $this->data_foto ) )
			{
				$campos .= "{$gruda}data_foto";
				$valores .= "{$gruda}'{$this->data_foto}'";
				$gruda = ", ";
			}
			
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acao_governo_foto_seq" );
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
		if( is_numeric( $this->cod_acao_governo_foto ) )
		{
			$db = new clsBanco();
			$set = "";
			
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_acao_governo ) )
			{
				$set .= "{$gruda}ref_cod_acao_governo = '{$this->ref_cod_acao_governo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_foto ) )
			{
				$set .= "{$gruda}nm_foto = '{$this->nm_foto}'";
				$gruda = ", ";
			}
			if( is_string( $this->caminho ) )
			{
				$set .= "{$gruda}caminho = '{$this->caminho}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_foto ) )
			{
				$set .= "{$gruda}data_foto = '{$this->data_foto}'";
				$gruda = ", ";
			}

			
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acao_governo_foto = '{$this->cod_acao_governo_foto}'" );
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
	function lista( $int_cod_acao_governo_foto = null, $int_ref_funcionario_cad = null, $int_ref_cod_acao_governo = null, $str_nm_foto = null, $str_caminho = null, $str_data_foto_inicio = null, $str_data_foto_fim = null, $str_data_cadastro_inicio = null, $str_data_cadastro_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_cod_acao_governo_foto ) )
		{
			$filtros .= "{$whereAnd} cod_acao_governo_foto = '{$int_cod_acao_governo_foto}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_acao_governo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_acao_governo = '{$int_ref_cod_acao_governo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_foto ) )
		{
			$filtros .= "{$whereAnd} nm_foto ILIKE '%{$str_nm_foto}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_caminho ) )
		{
			$filtros .= "{$whereAnd} caminho ILIKE '%{$str_caminho}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_foto_inicio ) )
		{
			$filtros .= "{$whereAnd} data_foto >= '{$str_data_foto_inicio}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_foto_fim ) )
		{
			$filtros .= "{$whereAnd} data_foto <= '{$str_data_foto_fim}'";
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
		if( is_numeric( $this->cod_acao_governo_foto ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acao_governo_foto = '{$this->cod_acao_governo_foto}'" );
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
		if( is_numeric( $this->cod_acao_governo_foto ) )
		{
		
			//	delete
			$db = new clsBanco();
			$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_acao_governo_foto = '{$this->cod_acao_governo_foto}'" );
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