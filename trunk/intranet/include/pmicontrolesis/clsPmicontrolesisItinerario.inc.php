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
* Criado em 05/07/2006 10:45 pelo gerador automatico de classes
*/

require_once( "include/pmicontrolesis/geral.inc.php" );

class clsPmicontrolesisItinerario
{
	var $cod_itinerario;
	var $ref_funcionario_cad;
	var $ref_funcionario_exc;
	var $numero;
	var $itinerario;
	var $retorno;
	var $horarios;
	var $descricao_horario;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nome;
	
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
	function clsPmicontrolesisItinerario( $cod_itinerario = null, $ref_funcionario_cad = null, $ref_funcionario_exc = null, $numero = null, $itinerario = null, $retorno = null, $horarios = null, $descricao_horario = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $nome = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmicontrolesis.";
		$this->_tabela = "{$this->_schema}itinerario";

		$this->_campos_lista = $this->_todos_campos = "cod_itinerario, ref_funcionario_cad, ref_funcionario_exc, numero, itinerario, retorno, horarios, descricao_horario, data_cadastro, data_exclusao, ativo, nome";
		
		if( is_numeric( $ref_funcionario_exc ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_funcionario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario_exc = $ref_funcionario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario_exc = $ref_funcionario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario_exc}'" ) )
				{
					$this->ref_funcionario_exc = $ref_funcionario_exc;
				}
			}
		}
		if( is_numeric( $ref_funcionario_cad ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_funcionario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario_cad = $ref_funcionario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario_cad = $ref_funcionario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario_cad}'" ) )
				{
					$this->ref_funcionario_cad = $ref_funcionario_cad;
				}
			}
		}

		
		if( is_numeric( $cod_itinerario ) )
		{
			$this->cod_itinerario = $cod_itinerario;
		}
		if( is_numeric( $numero ) )
		{
			$this->numero = $numero;
		}
		if( is_string( $itinerario ) )
		{
			$this->itinerario = $itinerario;
		}
		if( is_string( $retorno ) )
		{
			$this->retorno = $retorno;
		}
		if( is_string( $horarios ) )
		{
			$this->horarios = $horarios;
		}
		if( is_string( $descricao_horario ) )
		{
			$this->descricao_horario = $descricao_horario;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_exclusao ) )
		{
			$this->data_exclusao = $data_exclusao;
		}
		if( is_numeric( $ativo ) )
		{
			$this->ativo = $ativo;
		}
		if( is_string( $nome ) )
		{
			$this->nome = $nome;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_funcionario_cad ) && is_string( $this->nome ) )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->numero ) )
			{
				$campos .= "{$gruda}numero";
				$valores .= "{$gruda}'{$this->numero}'";
				$gruda = ", ";
			}
			if( is_string( $this->itinerario ) )
			{
				$campos .= "{$gruda}itinerario";
				$valores .= "{$gruda}'{$this->itinerario}'";
				$gruda = ", ";
			}
			if( is_string( $this->retorno ) )
			{
				$campos .= "{$gruda}retorno";
				$valores .= "{$gruda}'{$this->retorno}'";
				$gruda = ", ";
			}
			if( is_string( $this->horarios ) )
			{
				$campos .= "{$gruda}horarios";
				$valores .= "{$gruda}'{$this->horarios}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_horario ) )
			{
				$campos .= "{$gruda}descricao_horario";
				$valores .= "{$gruda}'{$this->descricao_horario}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_string( $this->nome ) )
			{
				$campos .= "{$gruda}nome";
				$valores .= "{$gruda}'{$this->nome}'";
				$gruda = ", ";
			}

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_itinerario_seq");
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
		if( is_numeric( $this->cod_itinerario ) && is_numeric( $this->ref_funcionario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_exc ) )
			{
				$set .= "{$gruda}ref_funcionario_exc = '{$this->ref_funcionario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->numero ) )
			{
				$set .= "{$gruda}numero = '{$this->numero}'";
				$gruda = ", ";
			}
			if( is_string( $this->itinerario ) )
			{
				$set .= "{$gruda}itinerario = '{$this->itinerario}'";
				$gruda = ", ";
			}
			if( is_string( $this->retorno ) )
			{
				$set .= "{$gruda}retorno = '{$this->retorno}'";
				$gruda = ", ";
			}
			if( is_string( $this->horarios ) )
			{
				$set .= "{$gruda}horarios = '{$this->horarios}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_horario ) )
			{
				$set .= "{$gruda}descricao_horario = '{$this->descricao_horario}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			$set .= "{$gruda}data_exclusao = NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome ) )
			{
				$set .= "{$gruda}nome = '{$this->nome}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_itinerario = '{$this->cod_itinerario}'" );
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
	function lista( $int_cod_itinerario = null, $int_ref_funcionario_cad = null, $int_ref_funcionario_exc = null, $int_numero = null, $str_itinerario = null, $str_retorno = null, $str_horarios = null, $str_descricao_horario = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nome = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_cod_itinerario ) )
		{
			$filtros .= "{$whereAnd} cod_itinerario = '{$int_cod_itinerario}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_exc = '{$int_ref_funcionario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_numero ) )
		{
			$filtros .= "{$whereAnd} numero = '{$int_numero}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_itinerario ) )
		{
			$filtros .= "{$whereAnd} itinerario LIKE '%{$str_itinerario}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_retorno ) )
		{
			$filtros .= "{$whereAnd} retorno LIKE '%{$str_retorno}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_horarios ) )
		{
			$filtros .= "{$whereAnd} horarios LIKE '%{$str_horarios}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao_horario ) )
		{
			$filtros .= "{$whereAnd} descricao_horario LIKE '%{$str_descricao_horario}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
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
		if( is_numeric( $this->cod_itinerario ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_itinerario = '{$this->cod_itinerario}'" );
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
		if( is_numeric( $this->cod_itinerario ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_itinerario = '{$this->cod_itinerario}'" );
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
		if( is_numeric( $this->cod_itinerario ) && is_numeric( $this->ref_funcionario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_itinerario = '{$this->cod_itinerario}'" );
		return true;
		*/

		$this->ativo = 0;
			return $this->edita();
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