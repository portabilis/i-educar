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
* Criado em 26/06/2006 10:24 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarTipoAvaliacaoValores
{
	var $ref_cod_tipo_avaliacao;
	var $sequencial;
	var $nome;
	var $valor;
	var $valor_min;
	var $valor_max;
	var $ativo;

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
	function clsPmieducarTipoAvaliacaoValores( $ref_cod_tipo_avaliacao = null, $sequencial = null, $nome = null, $valor = null, $valor_min = null, $valor_max = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}tipo_avaliacao_valores";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_tipo_avaliacao, sequencial, nome, valor, valor_min, valor_max,ativo";

		if( is_numeric( $ref_cod_tipo_avaliacao ) )
		{
			if( class_exists( "clsPmieducarTipoAvaliacao" ) )
			{
				$tmp_obj = new clsPmieducarTipoAvaliacao( $ref_cod_tipo_avaliacao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_tipo_avaliacao = $ref_cod_tipo_avaliacao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_tipo_avaliacao = $ref_cod_tipo_avaliacao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.tipo_avaliacao WHERE cod_tipo_avaliacao = '{$ref_cod_tipo_avaliacao}'" ) )
				{
					$this->ref_cod_tipo_avaliacao = $ref_cod_tipo_avaliacao;
				}
			}
		}


		if( is_numeric( $sequencial ) )
		{
			$this->sequencial = $sequencial;
		}
		if( is_string( $nome ) )
		{
			$this->nome = $nome;
		}
		if( is_numeric( $valor ) )
		{
			$this->valor = $valor;
		}
		if( is_numeric( $valor_min ) )
		{
			$this->valor_min = $valor_min;
		}
		if( is_numeric( $valor_max ) )
		{
			$this->valor_max = $valor_max;
		}
		if( !is_null( $ativo ) )
		{
			$this->ativo = dbBool($ativo) ? "true" : "false";
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_tipo_avaliacao ) && is_numeric( $this->sequencial ) && is_string( $this->nome ) && is_numeric( $this->valor ) && is_numeric( $this->valor_min ) && is_numeric( $this->valor_max ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_tipo_avaliacao ) )
			{
				$campos .= "{$gruda}ref_cod_tipo_avaliacao";
				$valores .= "{$gruda}'{$this->ref_cod_tipo_avaliacao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->sequencial ) )
			{
				$campos .= "{$gruda}sequencial";
				$valores .= "{$gruda}'{$this->sequencial}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome ) )
			{
				$campos .= "{$gruda}nome";
				$valores .= "{$gruda}'{$this->nome}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor ) )
			{
				$campos .= "{$gruda}valor";
				$valores .= "{$gruda}'{$this->valor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_min ) )
			{
				$campos .= "{$gruda}valor_min";
				$valores .= "{$gruda}'{$this->valor_min}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_max ) )
			{
				$campos .= "{$gruda}valor_max";
				$valores .= "{$gruda}'{$this->valor_max}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}TRUE";
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
		if( is_numeric( $this->ref_cod_tipo_avaliacao ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->nome ) )
			{
				$set .= "{$gruda}nome = '{$this->nome}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor ) )
			{
				$set .= "{$gruda}valor = '{$this->valor}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_min ) )
			{
				$set .= "{$gruda}valor_min = '{$this->valor_min}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->valor_max ) )
			{
				$set .= "{$gruda}valor_max = '{$this->valor_max}'";
				$gruda = ", ";
			}
			if( dbBool($this->ativo) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'" );
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
	function lista( $int_ref_cod_tipo_avaliacao = null, $int_sequencial = null, $str_nome = null, $int_valor = null, $int_valor_min = null, $int_valor_max = null, $bool_ativo = null)
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_tipo_avaliacao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_tipo_avaliacao = '{$int_ref_cod_tipo_avaliacao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_sequencial ) )
		{
			$filtros .= "{$whereAnd} sequencial = '{$int_sequencial}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome ) )
		{
			$filtros .= "{$whereAnd} nome LIKE '%{$str_nome}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor ) )
		{
			$filtros .= "{$whereAnd} valor = '{$int_valor}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor_min ) )
		{
			$filtros .= "{$whereAnd} valor_min <= '{$int_valor_min}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_valor_max ) )
		{
			$filtros .= "{$whereAnd} valor_max >= '{$int_valor_max}'";
			$whereAnd = " AND ";
		}

		if( dbBool( $bool_ativo ) )
		{
			$bool_ativo = dbBool($bool_ativo) ? "true" : "false";
			$filtros .= "{$whereAnd} ativo = $bool_ativo";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} ativo = true";
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
		if( is_numeric( $this->ref_cod_tipo_avaliacao ) && is_numeric( $this->sequencial ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'" );
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
		if( is_numeric( $this->ref_cod_tipo_avaliacao ) && is_numeric( $this->sequencial ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna uma string com o nome da nota em que $nota se encontra, pertencendo essa ao conjunto de notas
	 * de $cod_tipo_avaliacao e $sequencial
	 *
	 * @param int $nota
	 *
	 * @return string
	 */
	function nomeNota($nota,$cod_tipo_avaliacao)
	{
		if( is_numeric($nota) && is_numeric($cod_tipo_avaliacao) )
		{
			$db = new clsBanco();
			return $db->CampoUnico( "SELECT nome FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$cod_tipo_avaliacao}' AND valor_min <= '{$nota}' AND valor_max >= '{$nota}' AND ativo = true LIMIT 1" );
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
		if( is_numeric( $this->ref_cod_tipo_avaliacao ) && is_numeric( $this->sequencial ) )
		{

			$this->ativo = "false";
			if($this->edita())
				return true;
		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND sequencial = '{$this->sequencial}'" );
		return true;
		*/


		}
		return false;
	}

	/**
	 * Exclui todos os registros referentes a um tipo de avaliacao
	 */
	function  excluirTodos()
	{
		if ( is_numeric( $this->ref_cod_tipo_avaliacao ) ) {
			$db = new clsBanco();
			//$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' AND NOT EXISTS(SELECT 1 FROM pmieducar.nota_aluno n where n.ref_ref_cod_tipo_avaliacao = ref_cod_tipo_avaliacao)" );
			$db->Consulta( "UPDATE {$this->_tabela} set ativo = false WHERE ref_cod_tipo_avaliacao = '{$this->ref_cod_tipo_avaliacao}' " );
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