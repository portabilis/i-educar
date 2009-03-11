<?php

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*																	     *
*	@author Prefeitura Municipal de Itaja�								 *
*	@updated 29/03/2007													 *
*   Pacote: i-PLB Software P�blico Livre e Brasileiro					 *
*																		 *
*	Copyright (C) 2006	PMI - Prefeitura Municipal de Itaja�			 *
*						ctima@itajai.sc.gov.br					    	 *
*																		 *
*	Este  programa  �  software livre, voc� pode redistribu�-lo e/ou	 *
*	modific�-lo sob os termos da Licen�a P�blica Geral GNU, conforme	 *
*	publicada pela Free  Software  Foundation,  tanto  a vers�o 2 da	 *
*	Licen�a   como  (a  seu  crit�rio)  qualquer  vers�o  mais  nova.	 *
*																		 *
*	Este programa  � distribu�do na expectativa de ser �til, mas SEM	 *
*	QUALQUER GARANTIA. Sem mesmo a garantia impl�cita de COMERCIALI-	 *
*	ZA��O  ou  de ADEQUA��O A QUALQUER PROP�SITO EM PARTICULAR. Con-	 *
*	sulte  a  Licen�a  P�blica  Geral  GNU para obter mais detalhes.	 *
*																		 *
*	Voc�  deve  ter  recebido uma c�pia da Licen�a P�blica Geral GNU	 *
*	junto  com  este  programa. Se n�o, escreva para a Free Software	 *
*	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA	 *
*	02111-1307, USA.													 *
*																		 *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/**
* @author Prefeitura Municipal de Itaja�
*
* Criado em 26/06/2006 16:19 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarEscolaComplemento
{
	var $ref_cod_escola;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $cep;
	var $numero;
	var $complemento;
	var $email;
	var $nm_escola;
	var $municipio;
	var $bairro;
	var $logradouro;
	var $ddd_telefone;
	var $telefone;
	var $ddd_fax;
	var $fax;
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
	function clsPmieducarEscolaComplemento( $ref_cod_escola = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $cep = null, $numero = null, $complemento = null, $email = null, $nm_escola = null, $municipio = null, $bairro = null, $logradouro = null, $ddd_telefone = null, $telefone = null, $ddd_fax = null, $fax = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}escola_complemento";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_escola, ref_usuario_exc, ref_usuario_cad, cep, numero, complemento, email, nm_escola, municipio, bairro, logradouro, ddd_telefone, telefone, ddd_fax, fax, data_cadastro, data_exclusao, ativo";

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


		if( is_numeric( $ref_cod_escola ) )
		{
			$this->ref_cod_escola = $ref_cod_escola;
		}
		if( is_numeric( $cep ) )
		{
			$this->cep = $cep;
		}
		if( is_numeric( $numero ) )
		{
			$this->numero = $numero;
		}
		if( is_string( $complemento ) )
		{
			$this->complemento = $complemento;
		}
		if( is_string( $email ) )
		{
			$this->email = $email;
		}
		if( is_string( $nm_escola ) )
		{
			$this->nm_escola = $nm_escola;
		}
		if( is_string( $municipio ) )
		{
			$this->municipio = $municipio;
		}
		if( is_string( $bairro ) )
		{
			$this->bairro = $bairro;
		}
		if( is_string( $logradouro ) )
		{
			$this->logradouro = $logradouro;
		}
		if( is_numeric( $ddd_telefone ) )
		{
			$this->ddd_telefone = $ddd_telefone;
		}
		if( is_numeric( $telefone ) )
		{
			$this->telefone = $telefone;
		}
		if( is_numeric( $ddd_fax ) )
		{
			$this->ddd_fax = $ddd_fax;
		}
		if( is_numeric( $fax ) )
		{
			$this->fax = $fax;
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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->cep ) && is_numeric( $this->numero ) && is_string( $this->nm_escola ) && is_string( $this->municipio ) && is_string( $this->bairro ) && is_string( $this->logradouro ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_cod_escola ) )
			{
				$campos .= "{$gruda}ref_cod_escola";
				$valores .= "{$gruda}'{$this->ref_cod_escola}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cep ) )
			{
				$campos .= "{$gruda}cep";
				$valores .= "{$gruda}'{$this->cep}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->numero ) )
			{
				$campos .= "{$gruda}numero";
				$valores .= "{$gruda}'{$this->numero}'";
				$gruda = ", ";
			}
			if( is_string( $this->complemento ) )
			{
				$campos .= "{$gruda}complemento";
				$valores .= "{$gruda}'{$this->complemento}'";
				$gruda = ", ";
			}
			if( is_string( $this->email ) )
			{
				$campos .= "{$gruda}email";
				$valores .= "{$gruda}'{$this->email}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_escola ) )
			{
				$campos .= "{$gruda}nm_escola";
				$valores .= "{$gruda}'{$this->nm_escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->municipio ) )
			{
				$campos .= "{$gruda}municipio";
				$valores .= "{$gruda}'{$this->municipio}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$campos .= "{$gruda}bairro";
				$valores .= "{$gruda}'{$this->bairro}'";
				$gruda = ", ";
			}
			if( is_string( $this->logradouro ) )
			{
				$campos .= "{$gruda}logradouro";
				$valores .= "{$gruda}'{$this->logradouro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_telefone ) )
			{
				$campos .= "{$gruda}ddd_telefone";
				$valores .= "{$gruda}'{$this->ddd_telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->telefone ) )
			{
				$campos .= "{$gruda}telefone";
				$valores .= "{$gruda}'{$this->telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_fax ) )
			{
				$campos .= "{$gruda}ddd_fax";
				$valores .= "{$gruda}'{$this->ddd_fax}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->fax ) )
			{
				$campos .= "{$gruda}fax";
				$valores .= "{$gruda}'{$this->fax}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

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
			if( is_numeric( $this->cep ) )
			{
				$set .= "{$gruda}cep = '{$this->cep}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->numero ) )
			{
				$set .= "{$gruda}numero = '{$this->numero}'";
				$gruda = ", ";
			}
			if( is_string( $this->complemento ) )
			{
				$set .= "{$gruda}complemento = '{$this->complemento}'";
				$gruda = ", ";
			}
			if( is_string( $this->email ) )
			{
				$set .= "{$gruda}email = '{$this->email}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_escola ) )
			{
				$set .= "{$gruda}nm_escola = '{$this->nm_escola}'";
				$gruda = ", ";
			}
			if( is_string( $this->municipio ) )
			{
				$set .= "{$gruda}municipio = '{$this->municipio}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$set .= "{$gruda}bairro = '{$this->bairro}'";
				$gruda = ", ";
			}
			if( is_string( $this->logradouro ) )
			{
				$set .= "{$gruda}logradouro = '{$this->logradouro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_telefone ) )
			{
				$set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->telefone ) )
			{
				$set .= "{$gruda}telefone = '{$this->telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_fax ) )
			{
				$set .= "{$gruda}ddd_fax = '{$this->ddd_fax}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->fax ) )
			{
				$set .= "{$gruda}fax = '{$this->fax}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_escola = '{$this->ref_cod_escola}'" );
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
	function lista( $int_ref_cod_escola = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_cep = null, $int_numero = null, $str_complemento = null, $str_email = null, $str_nm_escola = null, $str_municipio = null, $str_bairro = null, $str_logradouro = null, $int_ddd_telefone = null, $int_telefone = null, $int_ddd_fax = null, $int_fax = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} ref_cod_escola = '{$int_ref_cod_escola}'";
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
		if( is_numeric( $int_cep ) )
		{
			$filtros .= "{$whereAnd} cep = '{$int_cep}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_numero ) )
		{
			$filtros .= "{$whereAnd} numero = '{$int_numero}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_complemento ) )
		{
			$filtros .= "{$whereAnd} complemento LIKE '%{$str_complemento}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_email ) )
		{
			$filtros .= "{$whereAnd} email LIKE '%{$str_email}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_escola ) )
		{
			$filtros .= "{$whereAnd} nm_escola LIKE '%{$str_nm_escola}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_municipio ) )
		{
			$filtros .= "{$whereAnd} municipio LIKE '%{$str_municipio}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_bairro ) )
		{
			$filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_logradouro ) )
		{
			$filtros .= "{$whereAnd} logradouro LIKE '%{$str_logradouro}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ddd_telefone ) )
		{
			$filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_telefone ) )
		{
			$filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ddd_fax ) )
		{
			$filtros .= "{$whereAnd} ddd_fax = '{$int_ddd_fax}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_fax ) )
		{
			$filtros .= "{$whereAnd} fax = '{$int_fax}'";
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
		if( is_numeric( $this->ref_cod_escola ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'" );
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
		if( is_numeric( $this->ref_cod_escola ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'" );
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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}'" );
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