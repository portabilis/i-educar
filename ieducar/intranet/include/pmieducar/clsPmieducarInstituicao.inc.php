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
* Criado em 22/06/2006 11:52 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarInstituicao
{
	var $cod_instituicao;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $complemento;
	var $nm_responsavel;
	var $ddd_telefone;
	var $telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $nm_instituicao;

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
	function clsPmieducarInstituicao( $cod_instituicao = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_idtlog = null, $ref_sigla_uf = null, $cep = null, $cidade = null, $bairro = null, $logradouro = null, $numero = null, $complemento = null, $nm_responsavel = null, $ddd_telefone = null, $telefone = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $nm_instituicao = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}instituicao";

		$this->_campos_lista = $this->_todos_campos = "cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_idtlog, ref_sigla_uf, cep, cidade, bairro, logradouro, numero, complemento, nm_responsavel, ddd_telefone, telefone, data_cadastro, data_exclusao, ativo, nm_instituicao";
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
		if( is_string( $ref_idtlog ) )
		{
			if( class_exists( "clsTipoLogradouro" ) )
			{
				$tmp_obj = new clsTipoLogradouro( $ref_idtlog );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idtlog = $ref_idtlog;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idtlog = $ref_idtlog;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM urbano.tipo_logradouro WHERE idtlog = '{$ref_idtlog}'" ) )
				{
					$this->ref_idtlog = $ref_idtlog;
				}
			}
		}


		if( is_numeric( $cod_instituicao ) )
		{
			$this->cod_instituicao = $cod_instituicao;
		}
		if( is_string( $ref_sigla_uf ) )
		{
			$this->ref_sigla_uf = $ref_sigla_uf;
		}
		if( is_numeric( $cep ) )
		{
			$this->cep = $cep;
		}
		if( is_string( $cidade ) )
		{
			$this->cidade = $cidade;
		}
		if( is_string( $bairro ) )
		{
			$this->bairro = $bairro;
		}
		if( is_string( $logradouro ) )
		{
			$this->logradouro = $logradouro;
		}
		if( is_numeric( $numero ) )
		{
			$this->numero = $numero;
		}
		if( is_string( $complemento ) )
		{
			$this->complemento = $complemento;
		}
		if( is_string( $nm_responsavel ) )
		{
			$this->nm_responsavel = $nm_responsavel;
		}
		if( is_numeric( $ddd_telefone ) )
		{
			$this->ddd_telefone = $ddd_telefone;
		}
		if( is_numeric( $telefone ) )
		{
			$this->telefone = $telefone;
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
		if( is_string( $nm_instituicao ) )
		{
			$this->nm_instituicao = $nm_instituicao;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_string( $this->ref_idtlog ) && is_string( $this->ref_sigla_uf ) && is_numeric( $this->cep ) && is_string( $this->cidade ) && is_string( $this->bairro ) && is_string( $this->logradouro ) && is_string( $this->nm_responsavel ) && is_numeric( $this->ativo ) && is_string( $this->nm_instituicao ))
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$campos .= "{$gruda}ref_usuario_exc";
				$valores .= "{$gruda}'{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_idtlog ) )
			{
				$campos .= "{$gruda}ref_idtlog";
				$valores .= "{$gruda}'{$this->ref_idtlog}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_sigla_uf ) )
			{
				$campos .= "{$gruda}ref_sigla_uf";
				$valores .= "{$gruda}'{$this->ref_sigla_uf}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cep ) )
			{
				$campos .= "{$gruda}cep";
				$valores .= "{$gruda}'{$this->cep}'";
				$gruda = ", ";
			}
			if( is_string( $this->cidade ) )
			{
				$campos .= "{$gruda}cidade";
				$valores .= "{$gruda}'{$this->cidade}'";
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
			if( is_string( $this->nm_responsavel ) )
			{
				$campos .= "{$gruda}nm_responsavel";
				$valores .= "{$gruda}'{$this->nm_responsavel}'";
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
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			if( is_numeric( $this->ativo ) )
			{
				$campos .= "{$gruda}ativo";
				$valores .= "{$gruda}'{$this->ativo}'";
				$gruda = ", ";
			}
			if( is_string( $this->nm_instituicao ) )
			{
				$campos .= "{$gruda}nm_instituicao";
				$valores .= "{$gruda}'{$this->nm_instituicao}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_instituicao_seq");
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
		if( is_numeric( $this->cod_instituicao ) )
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
			if( is_string( $this->ref_idtlog ) )
			{
				$set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_sigla_uf ) )
			{
				$set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cep ) )
			{
				$set .= "{$gruda}cep = '{$this->cep}'";
				$gruda = ", ";
			}
			if( is_string( $this->cidade ) )
			{
				$set .= "{$gruda}cidade = '{$this->cidade}'";
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
			if( is_string( $this->nm_responsavel ) )
			{
				$set .= "{$gruda}nm_responsavel = '{$this->nm_responsavel}'";
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
			if( is_string( $this->nm_instituicao ) )
			{
				$set .= "{$gruda}nm_instituicao = '{$this->nm_instituicao}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_instituicao = '{$this->cod_instituicao}'" );
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
	function lista( $int_cod_instituicao = null, $str_ref_sigla_uf = null, $int_cep = null, $str_cidade = null, $str_bairro = null, $str_logradouro = null, $int_numero = null, $str_complemento = null, $str_nm_responsavel = null, $int_ddd_telefone = null, $int_telefone = null, $date_data_cadastro = null, $date_data_exclusao = null, $int_ativo = null, $str_nm_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} cod_instituicao = '{$int_cod_instituicao}'";
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
		if( is_string( $str_ref_idtlog ) )
		{
			$filtros .= "{$whereAnd} ref_idtlog LIKE '%{$str_ref_idtlog}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ref_sigla_uf ) )
		{
			$filtros .= "{$whereAnd} ref_sigla_uf LIKE '%{$str_ref_sigla_uf}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cep ) )
		{
			$filtros .= "{$whereAnd} cep = '{$int_cep}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_cidade ) )
		{
			$filtros .= "{$whereAnd} cidade LIKE '%{$str_cidade}%'";
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
		if( is_string( $str_nm_responsavel ) )
		{
			$filtros .= "{$whereAnd} nm_responsavel LIKE '%{$str_nm_responsavel}%'";
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
		if( is_string( $str_nm_instituicao ) )
		{
			$filtros .= "{$whereAnd} nm_instituicao LIKE '%{$str_nm_instituicao}%'";
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
		if( is_numeric( $this->cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos},fcn_upper_nrm(nm_instituicao) as nm_instituicao_upper FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'" );
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
		if( is_numeric( $this->cod_instituicao ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'" );
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
		if( is_numeric( $this->cod_instituicao ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_instituicao = '{$this->cod_instituicao}'" );
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