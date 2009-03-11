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

class clsPmiacoesAcaoGoverno
{
	var $cod_acao_governo;
	var $ref_funcionario_exc;
	var $ref_funcionario_cad;
	var $nm_acao;
	var $descricao;
	var $data_inauguracao;
	var $valor;
	var $destaque;
	var $status_acao;
	var $idbai;
	var $ativo;
	var $categoria;
	var $numero_acao;

	
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
	function __construct( $cod_acao_governo = null, $ref_funcionario_exc = null, $ref_funcionario_cad = null, $nm_acao = null, $descricao = null, $data_inauguracao = null, $valor = null, $destaque = null, $status_acao = null, $ativo = null,$numero_acao = null, $categoria = null, $idbai = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo";

		$this->_campos_lista = $this->_todos_campos = "cod_acao_governo, ref_funcionario_exc, ref_funcionario_cad, nm_acao, descricao, data_inauguracao, valor, destaque, status_acao, ativo,numero_acao, categoria, idbai";
		
		if( is_numeric( $cod_acao_governo ) )
		{
			$this->cod_acao_governo = $cod_acao_governo;
		}
		if( is_numeric( $ref_funcionario_exc ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_exc );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_exc = $ref_funcionario_exc;
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
		if( is_numeric( $valor ) )
		{
			$this->valor = $valor;
		}
		if( is_numeric( $destaque ) )
		{
			$this->destaque = $destaque;
		}
		if( is_numeric( $status_acao ) )
		{
			$this->status_acao = $status_acao;
		}
		if( is_numeric( $idbai ) )
		{
			$this->idbai = $idbai;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_string( $nm_acao ) )
		{
			$this->nm_acao = $nm_acao;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_numeric( $categoria ) )
		{
			$this->categoria = $categoria;
		}
		if( is_string( $data_inauguracao ) )
		{
			$this->data_inauguracao = $data_inauguracao;
		}		
		
		if( is_numeric( $numero_acao ) )
		{
			$this->numero_acao = $numero_acao;
		}

	}
	/**
	 * Construtor (PHP 4)
	 *
	 * @return object
	 */
	function clsPmiacoesAcaoGoverno( $cod_acao_governo = null, $ref_funcionario_exc = null, $ref_funcionario_cad = null, $nm_acao = null, $descricao = null, $data_inauguracao = null, $valor = null, $destaque = null, $status_acao = null, $ativo = null, $numero_acao = null, $categoria = null , $idbai = null)
	{
		$db = new clsBanco();
		$this->_schema = "pmiacoes.";
		$this->_tabela = "{$this->_schema}acao_governo";

		$this->_campos_lista = $this->_todos_campos = "cod_acao_governo, ref_funcionario_exc, ref_funcionario_cad, nm_acao, descricao, data_inauguracao, valor, destaque, status_acao, ativo,numero_acao, categoria, idbai";
		
		if( is_numeric( $cod_acao_governo ) )
		{
			$this->cod_acao_governo = $cod_acao_governo;
		}
		if( is_numeric( $ref_funcionario_exc ) )
		{
			$tmp_obj = new clsFuncionario( $ref_funcionario_exc );
			if( $tmp_obj->detalhe() )
			{
				$this->ref_funcionario_exc = $ref_funcionario_exc;
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
		if( is_numeric( $valor ) )
		{
			$this->valor = $valor;
		}
		if( is_numeric( $idbai ) )
		{
			$this->idbai = $idbai;
		}
		if( is_numeric( $destaque ) )
		{
			$this->destaque = $destaque;
		}
		if( is_numeric( $status_acao ) )
		{
			$this->status_acao = $status_acao;
		}
		if( is_numeric( $categoria ) )
		{
			$this->categoria = $categoria;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_string( $nm_acao ) )
		{
			$this->nm_acao = $nm_acao;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_string( $data_inauguracao ) )
		{
			$this->data_inauguracao = $data_inauguracao;
		}		
		
		if( is_numeric( $numero_acao ) )
		{
			$this->numero_acao = $numero_acao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->destaque ) && is_numeric( $this->status_acao ) && is_numeric( $this->ativo ) && is_string( $this->nm_acao )  )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->cod_acao_governo ) )
			{
				$campos .= "{$gruda}cod_acao_governo";
				$valores .= "{$gruda}'{$this->cod_acao_governo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_exc ) )
			{
				$campos .= "{$gruda}ref_funcionario_exc";
				$valores .= "{$gruda}'{$this->ref_funcionario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->categoria ) )
			{
				$campos .= "{$gruda}categoria";
				$valores .= "{$gruda}'{$this->categoria}'";
				$gruda = ", ";
			}
		if( is_numeric( $this->idbai ) )
			{
				$campos .= "{$gruda}idbai";
				$valores .= "{$gruda}'{$this->idbai}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->destaque ) )
			{
				$campos .= "{$gruda}destaque";
				$valores .= "{$gruda}'{$this->destaque}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->status_acao ) )
			{
				$campos .= "{$gruda}status_acao";
				$valores .= "{$gruda}'{$this->status_acao}'";
				$gruda = ", ";
				
				if($this->status_acao == 1)
				{
					$this->numero_acao = $this->calculaNumeroMaximoAcao();
				}				
			}
			if( is_string( $this->nm_acao ) )
			{
				$campos .= "{$gruda}nm_acao";
				$valores .= "{$gruda}'{$this->nm_acao}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) &&  !empty($this->descricao))
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inauguracao ) &&  !empty($this->data_inauguracao) )
			{
				$campos .= "{$gruda}data_inauguracao";
				$valores .= "{$gruda}'{$this->data_inauguracao}'";
				$gruda = ", ";
			}
			
			if(is_numeric($this->numero_acao))
			{
				$campos .= "{$gruda}numero_acao";
				$valores .= "{$gruda}'{$this->numero_acao}'";
				$gruda = ", ";				
				
			}

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acao_governo_seq" );
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
		if( is_numeric( $this->cod_acao_governo ) )
		{
			$db = new clsBanco();
			$set = "";
			
			if( is_numeric( $this->ref_funcionario_exc ) )
			{
				$set .= "{$gruda}ref_funcionario_exc = '{$this->ref_funcionario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->categoria ) )
			{
				$set .= "{$gruda}categoria = '{$this->categoria}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->destaque ) )
			{
				$set .= "{$gruda}destaque = '{$this->destaque}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->idbai ) )
			{
				$set .= "{$gruda}idbai = '{$this->idbai}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->status_acao ) )
			{
				$set .= "{$gruda}status_acao = '{$this->status_acao}'";
				$gruda = ", ";
				$det = $this->detalhe();
				if($this->status_acao == 1 && $det["numero_acao"] <= 0 )
				{
					$this->numero_acao = $this->calculaNumeroMaximoAcao();
				}				
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
				if($ativo == 0)
				{
					$set .= "{$gruda}numero_acao = NULL";
					$gruda = ", ";				
				}				
			}
			if( is_string( $this->nm_acao ) )
			{
				$set .= "{$gruda}nm_acao = '{$this->nm_acao}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_inauguracao ) )
			{
				$set .= "{$gruda}data_inauguracao = '{$this->data_inauguracao}'";
				$gruda = ", ";
			}

			if(is_string($this->numero_acao))
			{
				$set .= "{$gruda}numero_acao = {$this->numero_acao}";
				$gruda = ", ";			
				
			}			
			
			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acao_governo = '{$this->cod_acao_governo}'" );
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
	function lista( $int_cod_acao_governo = null, $int_ref_funcionario_exc = null, $int_ref_funcionario_cad = null, $int_valor = null, $int_destaque = null, $int_status_acao = null, $int_ativo = null, $str_nm_acao = null, $str_descricao = null, $str_data_inauguracao_inicio = null, $str_data_inauguracao_fim = null, $int_categoria = null, $int_idbai = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_cod_acao_governo ) )
		{
			$filtros .= "{$whereAnd} cod_acao_governo = '{$int_cod_acao_governo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_exc = '{$int_ref_funcionario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_categoria ) )
		{
			$filtros .= "{$whereAnd} categoria = '{$int_categoria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor ) )
		{
			$filtros .= "{$whereAnd} valor = '{$int_valor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_destaque ) )
		{
			$filtros .= "{$whereAnd} destaque = '{$int_destaque}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_idbai ) )
		{
			$filtros .= "{$whereAnd} idbai = '{$int_idbai}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_status_acao ) )
		{
			$filtros .= "{$whereAnd} status_acao = '{$int_status_acao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ativo ) )
		{
			$filtros .= "{$whereAnd} ativo = '{$int_ativo}'";
			$whereAnd = " AND ";

		}
		if( is_string( $str_nm_acao ) )
		{
			$filtros .= "{$whereAnd} nm_acao ILIKE '%{$str_nm_acao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao ILIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_inauguracao_inicio ) )
		{
			$filtros .= "{$whereAnd} data_inauguracao >= '{$str_data_inauguracao_inicio}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_data_inauguracao_fim ) )
		{
			$filtros .= "{$whereAnd} data_inauguracao <= '{$str_data_inauguracao_fim}'";
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
		if( is_numeric( $this->cod_acao_governo ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acao_governo = '{$this->cod_acao_governo}'" );
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
		if( is_numeric( $this->cod_acao_governo ) )
		{
			
			//	delete
			$db = new clsBanco();
//			return true;
			$numero_acao = $db->CampoUnico( "SELECT numero_acao FROM {$this->_tabela} WHERE cod_acao_governo = '{$this->cod_acao_governo}'" );
			
			$this->ativo = 0;
			//$this->numero_acao = null;
			$return =  $this->edita();
			
			
			
			
			if($return)
			{
				$this->calculaNumeroAcoes($numero_acao);	
				return true;
			}
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
	
	/**
	 * Retorna o numero do registro
	 *
	 * @return string
	 */
	function calculaNumeroAcoes($numero_acao)
	{
		if( is_numeric( $numero_acao ) )
		{
			$db = new clsBanco();
			$db->Consulta( "UPDATE {$this->_tabela} set numero_acao = numero_acao - 1 WHERE numero_acao > '{$numero_acao}' AND status_acao = 1" );
			return true;
		}
		return false;
	}	
	
	/**
	 * Retorna o numero maximo do registro
	 *
	 * @return string
	 */
	function calculaNumeroMaximoAcao()
	{
			$db = new clsBanco();
			$numero = $db->CampoUnico( "SELECT coalesce(Max(numero_acao),0)+1 from {$this->_tabela}" );
			return $numero;
	}		
	
}
?>