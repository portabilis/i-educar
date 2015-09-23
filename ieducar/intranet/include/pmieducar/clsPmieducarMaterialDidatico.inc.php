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
* Criado em 28/06/2006 08:40 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarMaterialDidatico
{
	var $cod_material_didatico;
	var $ref_cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_cod_material_tipo;
	var $nm_material;
	var $desc_material;
	var $custo_unitario;
	var $data_cadastro;
	var $data_exclusao;
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
	function clsPmieducarMaterialDidatico( $cod_material_didatico = null, $ref_cod_instituicao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_cod_material_tipo = null, $nm_material = null, $desc_material = null, $custo_unitario = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}material_didatico";

		$this->_campos_lista = $this->_todos_campos = "cod_material_didatico, ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_cod_material_tipo, nm_material, desc_material, custo_unitario, data_cadastro, data_exclusao, ativo";

		if( is_numeric( $ref_cod_instituicao ) )
		{
			if( class_exists( "clsPmieducarInstituicao" ) )
			{
				$tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
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
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'" ) )
				{
					$this->ref_cod_instituicao = $ref_cod_instituicao;
				}
			}
		}
		if( is_numeric( $ref_cod_material_tipo ) )
		{
			if( class_exists( "clsPmieducarMaterialTipo" ) )
			{
				$tmp_obj = new clsPmieducarMaterialTipo( $ref_cod_material_tipo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_material_tipo = $ref_cod_material_tipo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_material_tipo = $ref_cod_material_tipo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.material_tipo WHERE cod_material_tipo = '{$ref_cod_material_tipo}'" ) )
				{
					$this->ref_cod_material_tipo = $ref_cod_material_tipo;
				}
			}
		}
		if( is_numeric( $ref_usuario_cad ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_cad = $ref_usuario_cad;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'" ) )
				{
					$this->ref_usuario_cad = $ref_usuario_cad;
				}
			}
		}
		if( is_numeric( $ref_usuario_exc ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_exc = $ref_usuario_exc;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'" ) )
				{
					$this->ref_usuario_exc = $ref_usuario_exc;
				}
			}
		}


		if( is_numeric( $cod_material_didatico ) )
		{
			$this->cod_material_didatico = $cod_material_didatico;
		}
		if( is_string( $nm_material ) )
		{
			$this->nm_material = $nm_material;
		}
		if( is_string( $desc_material ) )
		{
			$this->desc_material = $desc_material;
		}
		if( is_numeric( $custo_unitario ) )
		{
			$this->custo_unitario = $custo_unitario;
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
		if( is_numeric( $this->ref_cod_instituicao ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_material_tipo ) && is_string( $this->nm_material ) && is_numeric( $this->custo_unitario ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_material_tipo ) )
			{
				$campos .= "{$gruda}ref_cod_material_tipo";
				$valores .= "{$gruda}'{$this->ref_cod_material_tipo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_material ) )
			{
				$campos .= "{$gruda}nm_material";
				$valores .= "{$gruda}'{$this->nm_material}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_material ) )
			{
				$campos .= "{$gruda}desc_material";
				$valores .= "{$gruda}'{$this->desc_material}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->custo_unitario ) )
			{
				$campos .= "{$gruda}custo_unitario";
				$valores .= "{$gruda}'{$this->custo_unitario}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_material_didatico_seq");
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
		if( is_numeric( $this->cod_material_didatico ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_material_tipo ) )
			{
				$set .= "{$gruda}ref_cod_material_tipo = '{$this->ref_cod_material_tipo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_material ) )
			{
				$set .= "{$gruda}nm_material = '{$this->nm_material}'";
				$gruda = ", ";
			}
			if( is_string( $this->desc_material ) )
			{
				$set .= "{$gruda}desc_material = '{$this->desc_material}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->custo_unitario ) )
			{
				$set .= "{$gruda}custo_unitario = '{$this->custo_unitario}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_material_didatico = '{$this->cod_material_didatico}'" );
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
	function lista( $int_cod_material_didatico = null, $int_ref_cod_instituicao = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_cod_material_tipo = null, $str_nm_material = null, $str_desc_material = null, $int_custo_unitario = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_material_didatico ) )
		{
			$filtros .= "{$whereAnd} cod_material_didatico = '{$int_cod_material_didatico}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{

			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_material_tipo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_material_tipo = '{$int_ref_cod_material_tipo}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_material ) )
		{
			$filtros .= "{$whereAnd} nm_material LIKE '%{$str_nm_material}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_desc_material ) )
		{
			$filtros .= "{$whereAnd} desc_material LIKE '%{$str_desc_material}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_custo_unitario ) )
		{
			$filtros .= "{$whereAnd} custo_unitario = '{$int_custo_unitario}'";
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
		//die($sql);
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
		if( is_numeric( $this->cod_material_didatico ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_material_didatico = '{$this->cod_material_didatico}'" );
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
		if( is_numeric( $this->cod_material_didatico ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_material_didatico = '{$this->cod_material_didatico}'" );
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
		if( is_numeric( $this->cod_material_didatico ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_material_didatico = '{$this->cod_material_didatico}'" );
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