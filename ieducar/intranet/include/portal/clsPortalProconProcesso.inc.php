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
* Criado em 08/02/2007 08:39 pelo gerador automatico de classes
*/

require_once( "include/portal/geral.inc.php" );

	require_once( "include/portal/clsPortalProconProcesso.inc.php" );
	require_once( "include/portal/clsPortalProconConfiguracao.inc.php" );
	require_once( "include/portal/clsPortalProconProcessoReclamada.inc.php" );

class clsPortalProconProcesso
{
	var $cod_processo;
	var $ref_funcionario_finaliza;
	var $ref_funcionario_cad;
	var $ref_idpes;
	var $num_processo;
	var $nome_representante;
	var $descricao_fatos;
	var $legislacao;
	var $parecer;
	var $data_cadastro;
	var $data_finalizacao;

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
	 * @param integer cod_processo
	 * @param integer ref_funcionario_finaliza
	 * @param integer ref_funcionario_cad
	 * @param integer ref_idpes
	 * @param integer num_processo
	 * @param string nome_representante
	 * @param string descricao_fatos
	 * @param string legislacao
	 * @param string parecer
	 * @param string data_cadastro
	 * @param string data_finalizacao
	 *
	 * @return object
	 */
	function clsPortalProconProcesso( $cod_processo = null, $ref_funcionario_finaliza = null, $ref_funcionario_cad = null, $ref_idpes = null, $num_processo = null, $nome_representante = null, $descricao_fatos = null, $legislacao = null, $parecer = null, $data_cadastro = null, $data_finalizacao = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}procon_processo";

		$this->_campos_lista = $this->_todos_campos = "cod_processo, ref_funcionario_finaliza, ref_funcionario_cad, ref_idpes, num_processo, nome_representante, descricao_fatos, legislacao, parecer, data_cadastro, data_finalizacao";

		if( is_numeric( $ref_funcionario_finaliza ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_funcionario_finaliza );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario_finaliza = $ref_funcionario_finaliza;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario_finaliza = $ref_funcionario_finaliza;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario_finaliza}'" ) )
				{
					$this->ref_funcionario_finaliza = $ref_funcionario_finaliza;
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
		if( is_numeric( $ref_idpes ) )
		{
			if( class_exists( "clsCadastroPessoa" ) )
			{
				$tmp_obj = new clsCadastroPessoa( $ref_idpes );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.pessoa WHERE idpes = '{$ref_idpes}'" ) )
				{
					$this->ref_idpes = $ref_idpes;
				}
			}
		}


		if( is_numeric( $cod_processo ) )
		{
			$this->cod_processo = $cod_processo;
		}
		if( is_numeric( $num_processo ) )
		{
			$this->num_processo = $num_processo;
		}
		if( is_string( $nome_representante ) )
		{
			$this->nome_representante = $nome_representante;
		}
		if( is_string( $descricao_fatos ) )
		{
			$this->descricao_fatos = $descricao_fatos;
		}
		if( is_string( $legislacao ) )
		{
			$this->legislacao = $legislacao;
		}
		if( is_string( $parecer ) )
		{
			$this->parecer = $parecer;
		}
		if( is_string( $data_cadastro ) )
		{
			$this->data_cadastro = $data_cadastro;
		}
		if( is_string( $data_finalizacao ) )
		{
			$this->data_finalizacao = $data_finalizacao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_funcionario_cad ) && is_numeric( $this->ref_idpes ) && is_string( $this->descricao_fatos ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_funcionario_finaliza ) )
			{
				$campos .= "{$gruda}ref_funcionario_finaliza";
				$valores .= "{$gruda}'{$this->ref_funcionario_finaliza}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idpes ) )
			{
				$campos .= "{$gruda}ref_idpes";
				$valores .= "{$gruda}'{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_processo ) )
			{
				$campos .= "{$gruda}num_processo";
				$valores .= "{$gruda}'{$this->num_processo}'";
				$gruda = ", ";
			}
			else 
			{
				$campos .= "{$gruda}num_processo";
				$select = "(select (count (cod_processo)+1) as num_processo from procon_processo where data_cadastro >= (extract(year from now() ) || '-01-01 00:00:00') AND data_cadastro <= (extract(year from now() ) ||'-12-31 23:59:59'))";
				$valores .= "{$gruda}{$select}";
			}
			if( is_string( $this->nome_representante ) )
			{
				$campos .= "{$gruda}nome_representante";
				$valores .= "{$gruda}'{$this->nome_representante}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_fatos ) )
			{
				$campos .= "{$gruda}descricao_fatos";
				$valores .= "{$gruda}'{$this->descricao_fatos}'";
				$gruda = ", ";
			}
			if( is_string( $this->legislacao ) )
			{
				$campos .= "{$gruda}legislacao";
				$valores .= "{$gruda}'{$this->legislacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->parecer ) )
			{
				$campos .= "{$gruda}parecer";
				$valores .= "{$gruda}'{$this->parecer}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_string( $this->data_finalizacao ) )
			{
				$campos .= "{$gruda}data_finalizacao";
				$valores .= "{$gruda}'{$this->data_finalizacao}'";
				$gruda = ", ";
			}
			
			$sql = "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )";
			
			// echo $sql;


			$db->Consulta( $sql );
			return $db->InsertId( "{$this->_tabela}_cod_processo_seq");
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
		if( is_numeric( $this->cod_processo ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_funcionario_finaliza ) )
			{
				$set .= "{$gruda}ref_funcionario_finaliza = '{$this->ref_funcionario_finaliza}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_funcionario_cad = '{$this->ref_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_idpes ) )
			{
				$set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->num_processo ) )
			{
				$set .= "{$gruda}num_processo = '{$this->num_processo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nome_representante ) )
			{
				$set .= "{$gruda}nome_representante = '{$this->nome_representante}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao_fatos ) )
			{
				$set .= "{$gruda}descricao_fatos = '{$this->descricao_fatos}'";
				$gruda = ", ";
			}
			if( is_string( $this->legislacao ) )
			{
				$set .= "{$gruda}legislacao = '{$this->legislacao}'";
				$gruda = ", ";
			}
			if( is_string( $this->parecer ) )
			{
				$set .= "{$gruda}parecer = '{$this->parecer}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_finalizacao ) )
			{
				$set .= "{$gruda}data_finalizacao = '{$this->data_finalizacao}'";
				$gruda = ", ";
			}
			
			$sql = "UPDATE {$this->_tabela} SET $set WHERE cod_processo = '{$this->cod_processo}'";


			if( $set )
			{
				
				// echo $sql;
				
				$db->Consulta( $sql );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param integer int_ref_funcionario_finaliza
	 * @param integer int_ref_funcionario_cad
	 * @param integer int_ref_idpes
	 * @param integer int_num_processo
	 * @param string str_nome_representante
	 * @param string str_descricao_fatos
	 * @param string str_legislacao
	 * @param string str_parecer
	 * @param string date_data_cadastro_ini
	 * @param string date_data_cadastro_fim
	 * @param string date_data_finalizacao_ini
	 * @param string date_data_finalizacao_fim
	 *
	 * @return array
	 */
	function lista( $int_ref_funcionario_finaliza = null, $int_ref_funcionario_cad = null, $int_ref_idpes = null, $int_num_processo = null, $str_nome_representante = null, $str_descricao_fatos = null, $str_legislacao = null, $str_parecer = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_finalizacao_ini = null, $date_data_finalizacao_fim = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_processo ) )
		{
			$filtros .= "{$whereAnd} cod_processo = '{$int_cod_processo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_finaliza ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_finaliza = '{$int_ref_funcionario_finaliza}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cad = '{$int_ref_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_num_processo ) )
		{
			$filtros .= "{$whereAnd} num_processo = '{$int_num_processo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nome_representante ) )
		{
			$filtros .= "{$whereAnd} nome_representante LIKE '%{$str_nome_representante}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao_fatos ) )
		{
			$filtros .= "{$whereAnd} descricao_fatos LIKE '%{$str_descricao_fatos}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_legislacao ) )
		{
			$filtros .= "{$whereAnd} legislacao LIKE '%{$str_legislacao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_parecer ) )
		{
			$filtros .= "{$whereAnd} parecer LIKE '%{$str_parecer}%'";
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
		if( is_string( $date_data_finalizacao_ini ) )
		{
			$filtros .= "{$whereAnd} data_finalizacao >= '{$date_data_finalizacao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_finalizacao_fim ) )
		{
			$filtros .= "{$whereAnd} data_finalizacao <= '{$date_data_finalizacao_fim}'";
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
		if( is_numeric( $this->cod_processo ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_processo = '{$this->cod_processo}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

	/**
	 * Retorna true se o registro existir. Caso contrário retorna false.
	 *
	 * @return bool
	 */
	function existe()
	{
		if( is_numeric( $this->cod_processo ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_processo = '{$this->cod_processo}'" );
			if( $db->ProximoRegistro() )
			{
				return true;
			}
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
		if( is_numeric( $this->cod_processo ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_processo = '{$this->cod_processo}'" );
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