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
* Criado em 29/06/2006 14:32 pelo gerador automatico de classes
*/

require_once( "include/pmicontrolesis/geral.inc.php" );

class clsPmicontrolesisAcontecimento
{
	var $cod_acontecimento;
	var $ref_cod_tipo_acontecimento;
	var $ref_cod_funcionario_cad;
	var $ref_cod_funcionario_exc;
	var $titulo;
	var $descricao;
	var $dt_inicio;
	var $dt_fim;
	var $hr_inicio;
	var $hr_fim;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $local;
	var $contato;
	var $link;

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
	function clsPmicontrolesisAcontecimento( $cod_acontecimento = null, $ref_cod_tipo_acontecimento = null, $ref_cod_funcionario_cad = null, $ref_cod_funcionario_exc = null, $titulo = null, $descricao = null, $dt_inicio = null, $dt_fim = null, $hr_inicio = null, $hr_fim = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $local = null, $contato = null, $link = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmicontrolesis.";
		$this->_tabela = "{$this->_schema}acontecimento";

		$this->_campos_lista = $this->_todos_campos = "cod_acontecimento, ref_cod_tipo_acontecimento, ref_cod_funcionario_cad, ref_cod_funcionario_exc, titulo, descricao, dt_inicio, dt_fim, hr_inicio, hr_fim, data_cadastro, data_exclusao, ativo, local, contato, link";

		if( is_numeric( $ref_cod_tipo_acontecimento ) )
		{
			if( class_exists( "clsPmicontrolesisTipoAcontecimento" ) )
			{
				$tmp_obj = new clsPmicontrolesisTipoAcontecimento( $ref_cod_tipo_acontecimento );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_tipo_acontecimento = $ref_cod_tipo_acontecimento;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_tipo_acontecimento = $ref_cod_tipo_acontecimento;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmicontrolesis.tipo_acontecimento WHERE cod_tipo_acontecimento = '{$ref_cod_tipo_acontecimento}'" ) )
				{
					$this->ref_cod_tipo_acontecimento = $ref_cod_tipo_acontecimento;
				}
			}
		}
		if( is_numeric( $ref_cod_funcionario_exc ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_cod_funcionario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_funcionario_exc = $ref_cod_funcionario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_funcionario_exc = $ref_cod_funcionario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_cod_funcionario_exc}'" ) )
				{
					$this->ref_cod_funcionario_exc = $ref_cod_funcionario_exc;
				}
			}
		}
		if( is_numeric( $ref_cod_funcionario_cad ) )
		{
			if( class_exists( "clsFuncionario" ) )
			{
				$tmp_obj = new clsFuncionario( $ref_cod_funcionario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_funcionario_cad = $ref_cod_funcionario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_funcionario_cad = $ref_cod_funcionario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_cod_funcionario_cad}'" ) )
				{
					$this->ref_cod_funcionario_cad = $ref_cod_funcionario_cad;
				}
			}
		}


		if( is_numeric( $cod_acontecimento ) )
		{
			$this->cod_acontecimento = $cod_acontecimento;
		}
		if( is_string( $titulo ) )
		{
			$this->titulo = $titulo;
		}
		if( is_string( $descricao ) )
		{
			$this->descricao = $descricao;
		}
		if( is_string( $dt_inicio ) )
		{
			
			$this->dt_inicio = $dt_inicio;
		}
		if( is_string( $dt_fim ) )
		{
		
			$this->dt_fim = $dt_fim;
		}
		
		if( is_string( $hr_inicio ) )
		{
			$this->hr_inicio = $hr_inicio;
		}
		if( is_string( $hr_fim ) )
		{
			$this->hr_fim = $hr_fim;
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
		if(is_string($local))
		{
			$this->local = $local;
		}
		if(is_string($contato))
		{
			$this->contato = $contato;
		}
		if(is_string($link))
		{
			$this->link = $link;
		}
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_tipo_acontecimento ) && is_numeric( $this->ref_cod_funcionario_cad ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_tipo_acontecimento ) )
			{
				$campos .= "{$gruda}ref_cod_tipo_acontecimento";
				$valores .= "{$gruda}'{$this->ref_cod_tipo_acontecimento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_cad ) )
			{
				$campos .= "{$gruda}ref_cod_funcionario_cad";
				$valores .= "{$gruda}'{$this->ref_cod_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_exc ) )
			{
				$campos .= "{$gruda}ref_cod_funcionario_exc";
				$valores .= "{$gruda}'{$this->ref_cod_funcionario_exc}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$campos .= "{$gruda}titulo";
				$valores .= "{$gruda}'{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->local ) )
			{
				$campos .= "{$gruda}local";
				$valores .= "{$gruda}'{$this->local}'";
				$gruda = ", ";
			}
			if( is_string( $this->contato ) )
			{
				$campos .= "{$gruda}contato";
				$valores .= "{$gruda}'{$this->contato}'";
				$gruda = ", ";
			}
			if( is_string( $this->link ) )
			{
				$campos .= "{$gruda}link";
				$valores .= "{$gruda}'{$this->link}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->dt_inicio ) )
			{
				$campos .= "{$gruda}dt_inicio";
				$valores .= "{$gruda}'{$this->dt_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->dt_fim ) )
			{

				$campos .= "{$gruda}dt_fim";
				$valores .= "{$gruda}'{$this->dt_fim}'";
				$gruda = ", ";
			}
			if( ( $this->hr_inicio ) )
			{

				$campos .= "{$gruda}hr_inicio";
				$valores .= "{$gruda}'{$this->hr_inicio}'";
				$gruda = ", ";
			}
			if( ( $this->hr_fim ) )
			{
				$campos .= "{$gruda}hr_fim";
				$valores .= "{$gruda}'{$this->hr_fim}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acontecimento_seq");
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
		if( is_numeric( $this->cod_acontecimento ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_tipo_acontecimento ) )
			{
				$set .= "{$gruda}ref_cod_tipo_acontecimento = '{$this->ref_cod_tipo_acontecimento}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_cad ) )
			{
				$set .= "{$gruda}ref_cod_funcionario_cad = '{$this->ref_cod_funcionario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_funcionario_exc ) )
			{
				$set .= "{$gruda}ref_cod_funcionario_exc = '{$this->ref_cod_funcionario_exc}'";
				$gruda = ", ";
			}
			if( is_string( $this->titulo ) )
			{
				$set .= "{$gruda}titulo = '{$this->titulo}'";
				$gruda = ", ";
			}
			if( is_string( $this->local ) )
			{
				$set .= "{$gruda}local = '{$this->local}'";
				$gruda = ", ";
			}
			if( is_string( $this->contato ) )
			{
				$set .= "{$gruda}contato = '{$this->contato}'";
				$gruda = ", ";
			}
			if( is_string( $this->link ) )
			{
				$set .= "{$gruda}link = '{$this->link}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$set .= "{$gruda}descricao = '{$this->descricao}'";
				$gruda = ", ";
			}
			if( is_string( $this->dt_inicio ) )
			{
				$set .= "{$gruda}dt_inicio = '{$this->dt_inicio}'";
				$gruda = ", ";
			}
			if( is_string( $this->dt_fim ) )
			{
				$set .= "{$gruda}dt_fim = '{$this->dt_fim}'";
				$gruda = ", ";
			}
			if( ( $this->hr_inicio ) )
			{
				$set .= "{$gruda}hr_inicio = '{$this->hr_inicio}'";
				$gruda = ", ";
			}
			if( ( $this->hr_fim ) )
			{
				$set .= "{$gruda}hr_fim = '{$this->hr_fim}'";
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


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acontecimento = '{$this->cod_acontecimento}'" );
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
	function lista( $int_cod_acontecimento = null, $int_ref_cod_tipo_acontecimento = null, $int_ref_cod_funcionario_cad = null, $int_ref_cod_funcionario_exc = null, $str_titulo = null, $str_descricao = null, $date_dt_inicio_ini = null, $date_dt_inicio_fim = null, $date_dt_fim_ini = null, $date_dt_fim_fim = null, $time_hr_inicio_ini = null, $time_hr_inicio_fim = null, $time_hr_fim_ini = null, $time_hr_fim_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_local = null, $str_contato = null, $str_link = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_acontecimento ) )
		{
			$filtros .= "{$whereAnd} cod_acontecimento = '{$int_cod_acontecimento}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_tipo_acontecimento ) )
		{
			$filtros .= "{$whereAnd} ref_cod_tipo_acontecimento = '{$int_ref_cod_tipo_acontecimento}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_funcionario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_cod_funcionario_cad = '{$int_ref_cod_funcionario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_funcionario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_cod_funcionario_exc = '{$int_ref_cod_funcionario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_titulo ) )
		{
			$filtros .= "{$whereAnd} titulo LIKE '%{$str_titulo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_local ) )
		{
			$filtros .= "{$whereAnd} local LIKE '%{$str_local}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_contato ) )
		{
			$filtros .= "{$whereAnd} contato LIKE '%{$str_contato}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_link ) )
		{
			$filtros .= "{$whereAnd} link LIKE '%{$str_link}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_dt_inicio_ini ) )
		{
			$filtros .= "{$whereAnd} dt_inicio >= '{$date_dt_inicio_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_dt_inicio_fim ) )
		{
			$filtros .= "{$whereAnd} dt_inicio <= '{$date_dt_inicio_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_dt_fim_ini ) )
		{
			$filtros .= "{$whereAnd} dt_fim >= '{$date_dt_fim_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_dt_fim_fim ) )
		{
			$filtros .= "{$whereAnd} dt_fim <= '{$date_dt_fim_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hr_inicio_ini ) )
		{
			$filtros .= "{$whereAnd} hr_inicio >= '{$time_hr_inicio_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hr_inicio_fim ) )
		{
			$filtros .= "{$whereAnd} hr_inicio <= '{$time_hr_inicio_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hr_fim_ini ) )
		{
			$filtros .= "{$whereAnd} hr_fim >= '{$time_hr_fim_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hr_fim_fim ) )
		{
			$filtros .= "{$whereAnd} hr_fim <= '{$time_hr_fim_fim}'";
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
		if( is_numeric( $this->cod_acontecimento ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acontecimento = '{$this->cod_acontecimento}'" );
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
		if( is_numeric( $this->cod_acontecimento ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_acontecimento = '{$this->cod_acontecimento}'" );
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
		if( is_numeric( $this->cod_acontecimento ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_acontecimento = '{$this->cod_acontecimento}'" );
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