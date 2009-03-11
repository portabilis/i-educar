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
* Criado em 18/12/2006 16:10 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhTipoPortaria
{
	var $cod_tipo_portaria;
	var $ref_pessoa_exc;
	var $ref_pessoa_cad;
	var $nm_tipo;
	var $texto_simples;
	var $texto_completo;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_instituicao;

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
	 * @param integer cod_tipo_portaria
	 * @param integer ref_pessoa_exc
	 * @param integer ref_pessoa_cad
	 * @param string nm_tipo
	 * @param string texto_simples
	 * @param string texto_completo
	 * @param string data_cadastro
	 * @param string data_exclusao
	 * @param integer ativo
	 * @param integer ref_cod_instituicao
	 *
	 * @return object
	 */
	function clsPmidrhTipoPortaria( $cod_tipo_portaria = null, $ref_pessoa_exc = null, $ref_pessoa_cad = null, $nm_tipo = null, $texto_simples = null, $texto_completo = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_instituicao = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}tipo_portaria";

		$this->_campos_lista = $this->_todos_campos = "cod_tipo_portaria, ref_pessoa_exc, ref_pessoa_cad, nm_tipo, texto_simples, texto_completo, data_cadastro, data_exclusao, ativo, ref_cod_instituicao";

		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmidrhInstituicao" ) )
			{
				$tmp_obj = new clsPmidrhInstituicao( $ref_cod_instituicao );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_instituicao = $ref_cod_instituicao;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}
		if( is_numeric( $ref_pessoa_exc ) )
		{
			if( class_exists( "clsPmidrhUsuario" ) )
			{
				$tmp_obj = new clsPmidrhUsuario( $ref_pessoa_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_pessoa_exc = $ref_pessoa_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_pessoa_exc = $ref_pessoa_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.usuario WHERE ref_cod_pessoa = '{$ref_pessoa_exc}'" ) )
				{
					$this->ref_pessoa_exc = $ref_pessoa_exc;
				}
			}
		}
		if( is_numeric( $ref_pessoa_cad ) )
		{
			if( class_exists( "clsPmidrhUsuario" ) )
			{
				$tmp_obj = new clsPmidrhUsuario( $ref_pessoa_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_pessoa_cad = $ref_pessoa_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_pessoa_cad = $ref_pessoa_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.usuario WHERE ref_cod_pessoa = '{$ref_pessoa_cad}'" ) )
				{
					$this->ref_pessoa_cad = $ref_pessoa_cad;
				}
			}
		}


		if( is_numeric( $cod_tipo_portaria ) )
		{
			$this->cod_tipo_portaria = $cod_tipo_portaria;
		}
		if( is_string( $nm_tipo ) )
		{
			$this->nm_tipo = $nm_tipo;
		}
		if( is_string( $texto_simples ) )
		{
			$this->texto_simples = $texto_simples;
		}
		if( is_string( $texto_completo ) )
		{
			$this->texto_completo = $texto_completo;
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

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_pessoa_cad ) && is_string( $this->nm_tipo ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_pessoa_exc ) )
			{
				$campos .= "{$gruda}ref_pessoa_exc";
				$valores .= "{$gruda}'{$this->ref_pessoa_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_pessoa_cad ) )
			{
				$campos .= "{$gruda}ref_pessoa_cad";
				$valores .= "{$gruda}'{$this->ref_pessoa_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_tipo ) )
			{
				$campos .= "{$gruda}nm_tipo";
				$valores .= "{$gruda}'{$this->nm_tipo}'";
				$gruda = ", ";
			}
			if( is_string( $this->texto_simples ) )
			{
				$campos .= "{$gruda}texto_simples";
				$valores .= "{$gruda}'{$this->texto_simples}'";
				$gruda = ", ";
			}
			if( is_string( $this->texto_completo ) )
			{
				$campos .= "{$gruda}texto_completo";
				$valores .= "{$gruda}'{$this->texto_completo}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_tipo_portaria_seq");
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
		if( is_numeric( $this->cod_tipo_portaria ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_pessoa_exc ) )
			{
				$set .= "{$gruda}ref_pessoa_exc = '{$this->ref_pessoa_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_pessoa_cad ) )
			{
				$set .= "{$gruda}ref_pessoa_cad = '{$this->ref_pessoa_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_tipo ) )
			{
				$set .= "{$gruda}nm_tipo = '{$this->nm_tipo}'";
				$gruda = ", ";
			}
			if( is_string( $this->texto_simples ) )
			{
				$set .= "{$gruda}texto_simples = '{$this->texto_simples}'";
				$gruda = ", ";
			}
			if( is_string( $this->texto_completo ) )
			{
				$set .= "{$gruda}texto_completo = '{$this->texto_completo}'";
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
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_tipo_portaria = '{$this->cod_tipo_portaria}'" );
				return true;
			}
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 * 
	 * @param integer int_ref_pessoa_exc
	 * @param integer int_ref_pessoa_cad
	 * @param string str_nm_tipo
	 * @param string str_texto_simples
	 * @param string str_texto_completo
	 * @param string date_data_cadastro_ini
	 * @param string date_data_cadastro_fim
	 * @param string date_data_exclusao_ini
	 * @param string date_data_exclusao_fim
	 * @param integer int_ativo
	 * @param integer int_ref_cod_instituicao
	 *
	 * @return array
	 */
	function lista( $int_ref_pessoa_exc = null, $int_ref_pessoa_cad = null, $str_nm_tipo = null, $str_texto_simples = null, $str_texto_completo = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_tipo_portaria ) )
		{
			$filtros .= "{$whereAnd} cod_tipo_portaria = '{$int_cod_tipo_portaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_pessoa_exc ) )
		{
			$filtros .= "{$whereAnd} ref_pessoa_exc = '{$int_ref_pessoa_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_pessoa_cad ) )
		{
			$filtros .= "{$whereAnd} ref_pessoa_cad = '{$int_ref_pessoa_cad}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_tipo ) )
		{
			$filtros .= "{$whereAnd} nm_tipo LIKE '%{$str_nm_tipo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_texto_simples ) )
		{
			$filtros .= "{$whereAnd} texto_simples LIKE '%{$str_texto_simples}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_texto_completo ) )
		{
			$filtros .= "{$whereAnd} texto_completo LIKE '%{$str_texto_completo}%'";
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
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
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
		if( is_numeric( $this->cod_tipo_portaria ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_tipo_portaria = '{$this->cod_tipo_portaria}'" );
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
		if( is_numeric( $this->cod_tipo_portaria ) )
		{

			$db = new clsBanco();
			$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_tipo_portaria = '{$this->cod_tipo_portaria}'" );
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
		if( is_numeric( $this->cod_tipo_portaria ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_tipo_portaria = '{$this->cod_tipo_portaria}'" );
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