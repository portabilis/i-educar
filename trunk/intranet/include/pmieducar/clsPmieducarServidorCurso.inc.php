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
* Criado em 26/06/2006 16:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarServidorCurso
{
	var $cod_servidor_curso;
	var $ref_cod_formacao;
	var $data_conclusao;
	var $data_registro;
	var $diplomas_registros;

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
	function clsPmieducarServidorCurso( $cod_servidor_curso = null, $ref_cod_formacao = null, $data_conclusao = null, $data_registro = null, $diplomas_registros = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}servidor_curso";

		$this->_campos_lista = $this->_todos_campos = "cod_servidor_curso, ref_cod_formacao, data_conclusao, data_registro, diplomas_registros";

		if( is_numeric( $ref_cod_formacao ) )
		{
			if( class_exists( "clsPmieducarServidorFormacao" ) )
			{
				$tmp_obj = new clsPmieducarServidorFormacao( $ref_cod_formacao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_formacao = $ref_cod_formacao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_formacao = $ref_cod_formacao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.servidor_formacao WHERE cod_formacao = '{$ref_cod_formacao}'" ) )
				{
					$this->ref_cod_formacao = $ref_cod_formacao;
				}
			}
		}


		if( is_numeric( $cod_servidor_curso ) )
		{
			$this->cod_servidor_curso = $cod_servidor_curso;
		}
		if( is_string( $data_conclusao ) )
		{
			$this->data_conclusao = $data_conclusao;
		}
		if( is_string( $data_registro ) )
		{
			$this->data_registro = $data_registro;
		}
		if( is_string( $diplomas_registros ) )
		{
			$this->diplomas_registros = $diplomas_registros;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_formacao ) && is_string( $this->data_conclusao ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_formacao ) )
			{
				$campos .= "{$gruda}ref_cod_formacao";
				$valores .= "{$gruda}'{$this->ref_cod_formacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_conclusao ) )
			{
				$campos .= "{$gruda}data_conclusao";
				$valores .= "{$gruda}'{$this->data_conclusao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_registro ) )
			{
				$campos .= "{$gruda}data_registro";
				$valores .= "{$gruda}'{$this->data_registro}'";
				$gruda = ", ";
			}
			if( is_string( $this->diplomas_registros ) )
			{
				$campos .= "{$gruda}diplomas_registros";
				$valores .= "{$gruda}'{$this->diplomas_registros}'";
				$gruda = ", ";
			}
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_servidor_curso_seq");
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
		if( is_numeric( $this->cod_servidor_curso ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_formacao ) )
			{
				$set .= "{$gruda}ref_cod_formacao = '{$this->ref_cod_formacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_conclusao ) )
			{
				$set .= "{$gruda}data_conclusao = '{$this->data_conclusao}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_registro ) )
			{
				$set .= "{$gruda}data_registro = '{$this->data_registro}'";
				$gruda = ", ";
			}
			if( is_string( $this->diplomas_registros ) )
			{
				$set .= "{$gruda}diplomas_registros = '{$this->diplomas_registros}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'" );
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
	function lista( $int_cod_servidor_curso = null, $int_ref_cod_formacao = null, $date_data_conclusao_ini = null, $date_data_conclusao_fim = null, $date_data_registro_ini = null, $date_data_registro_fim = null, $str_diplomas_registros = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_servidor_curso ) )
		{
			$filtros .= "{$whereAnd} cod_servidor_curso = '{$int_cod_servidor_curso}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_formacao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_formacao = '{$int_ref_cod_formacao}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_conclusao_ini ) )
		{
			$filtros .= "{$whereAnd} data_conclusao >= '{$date_data_conclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_conclusao_fim ) )
		{
			$filtros .= "{$whereAnd} data_conclusao <= '{$date_data_conclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_registro_ini ) )
		{
			$filtros .= "{$whereAnd} data_registro >= '{$date_data_registro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_registro_fim ) )
		{
			$filtros .= "{$whereAnd} data_registro <= '{$date_data_registro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_diplomas_registros ) )
		{
			$filtros .= "{$whereAnd} diplomas_registros LIKE '%{$str_diplomas_registros}%'";
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
		if( is_numeric( $this->cod_servidor_curso ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		elseif ( $this->ref_cod_formacao ) {
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_formacao = '{$this->ref_cod_formacao}'" );
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
		if( is_numeric( $this->cod_servidor_curso ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'" );
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
		if( is_numeric( $this->cod_servidor_curso ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_servidor_curso = '{$this->cod_servidor_curso}'" );
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