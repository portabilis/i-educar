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
* Criado em 14/07/2006 09:28 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarAcervoEditora
{
	var $cod_acervo_editora;
	var $ref_usuario_cad;
	var $ref_usuario_exc;
	var $ref_idtlog;
	var $ref_sigla_uf;
	var $nm_editora;
	var $cep;
	var $cidade;
	var $bairro;
	var $logradouro;
	var $numero;
	var $telefone;
	var $ddd_telefone;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $ref_cod_biblioteca;

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
	function clsPmieducarAcervoEditora( $cod_acervo_editora = null, $ref_usuario_cad = null, $ref_usuario_exc = null, $ref_idtlog = null, $ref_sigla_uf = null, $nm_editora = null, $cep = null, $cidade = null, $bairro = null, $logradouro = null, $numero = null, $telefone = null, $ddd_telefone = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $ref_cod_biblioteca = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}acervo_editora";

		$this->_campos_lista = $this->_todos_campos = "cod_acervo_editora, ref_usuario_cad, ref_usuario_exc, ref_idtlog, ref_sigla_uf, nm_editora, cep, cidade, bairro, logradouro, numero, telefone, ddd_telefone, data_cadastro, data_exclusao, ativo, ref_cod_biblioteca";

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
		if( is_string( $ref_idtlog ) )
		{
			if( class_exists( "clsUrbanoTipoLogradouro" ) )
			{
				$tmp_obj = new clsUrbanoTipoLogradouro( $ref_idtlog );
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

		if( is_string( $ref_sigla_uf ) )
		{
			if( class_exists( "clsUf" ) )
			{
				$tmp_obj = new clsUf( $ref_sigla_uf );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_sigla_uf = $ref_sigla_uf;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_sigla_uf = $ref_sigla_uf;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM uf WHERE sigla_uf = '{$ref_sigla_uf}'" ) )
				{
					$this->ref_sigla_uf = $ref_sigla_uf;
				}
			}
		}


		if( is_numeric( $cod_acervo_editora ) )
		{
			$this->cod_acervo_editora = $cod_acervo_editora;
		}
		if( is_string( $nm_editora ) )
		{
			$this->nm_editora = $nm_editora;
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
		if( is_numeric( $telefone ) )
		{
			$this->telefone = $telefone;
		}
		if( is_numeric( $ddd_telefone ) )
		{
			$this->ddd_telefone = $ddd_telefone;
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
		if( is_numeric($ref_cod_biblioteca))
		{
			$this->ref_cod_biblioteca = $ref_cod_biblioteca;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra() {
		if(is_numeric($this->ref_usuario_cad ) && is_string( $this->nm_editora ) && is_numeric($this->ref_cod_biblioteca)) {
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

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
			if( is_string( $this->nm_editora ) )
			{
				$campos .= "{$gruda}nm_editora";
				$valores .= "{$gruda}'{$this->nm_editora}'";
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
			if( is_numeric( $this->telefone ) )
			{
				$campos .= "{$gruda}telefone";
				$valores .= "{$gruda}'{$this->telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_telefone ) )
			{
				$campos .= "{$gruda}ddd_telefone";
				$valores .= "{$gruda}'{$this->ddd_telefone}'";
				$gruda = ", ";
			}
			if(is_numeric($this->ref_cod_biblioteca))
			{
				$campos .= "{$gruda}ref_cod_biblioteca";
				$valores .= "{$gruda}'{$this->ref_cod_biblioteca}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acervo_editora_seq");
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
		if( is_numeric( $this->cod_acervo_editora ) && is_numeric( $this->ref_usuario_exc ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_exc ) )
			{
				$set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
				$gruda = ", ";
			}

			if(is_string($this->ref_idtlog )){
			  $set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
  			$gruda = ", ";
      }
      else {
			  $set .= "{$gruda}ref_idtlog = null";
  			$gruda = ", ";
      }

			if(is_string($this->ref_sigla_uf)){
			  $set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
  			$gruda = ", ";
      }
      else {
			  $set .= "{$gruda}ref_sigla_uf = null";
  			$gruda = ", ";
      }

			if( is_string( $this->nm_editora ) )
			{
				$set .= "{$gruda}nm_editora = '{$this->nm_editora}'";
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
			if( is_numeric( $this->telefone ) )
			{
				$set .= "{$gruda}telefone = '{$this->telefone}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ddd_telefone ) )
			{
				$set .= "{$gruda}ddd_telefone = '{$this->ddd_telefone}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_cadastro ) )
			{
				$set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric($this->ref_cod_biblioteca ))
			{
				$set .= "{$gruda}ref_cod_biblioteca = '{$this->ref_cod_biblioteca}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'" );
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
	function lista( $int_cod_acervo_editora = null, $int_ref_usuario_cad = null, $int_ref_usuario_exc = null, $str_ref_idtlog = null, $str_ref_sigla_uf = null, $str_nm_editora = null, $int_cep = null, $str_cidade = null, $str_bairro = null, $str_logradouro = null, $int_numero = null, $int_telefone = null, $int_ddd_telefone = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_biblioteca = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_acervo_editora ) )
		{
			$filtros .= "{$whereAnd} cod_acervo_editora = '{$int_cod_acervo_editora}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
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
		if( is_string( $str_nm_editora ) )
		{
			$filtros .= "{$whereAnd} nm_editora LIKE '%{$str_nm_editora}%'";
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
		if( is_numeric( $int_telefone ) )
		{
			$filtros .= "{$whereAnd} telefone = '{$int_telefone}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ddd_telefone ) )
		{
			$filtros .= "{$whereAnd} ddd_telefone = '{$int_ddd_telefone}'";
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
		if(is_array($int_ref_cod_biblioteca))
		{
			$bibs = implode(", ", $int_ref_cod_biblioteca);
			$filtros .= "{$whereAnd} (ref_cod_biblioteca IN ($bibs) OR ref_cod_biblioteca IS NULL)";
			$whereAnd = " AND ";
		}
		elseif (is_numeric($int_ref_cod_biblioteca))
		{
			$filtros .= "{$whereAnd} ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
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
		if( is_numeric( $this->cod_acervo_editora ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'" );
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
		if( is_numeric( $this->cod_acervo_editora ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'" );
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
		if( is_numeric( $this->cod_acervo_editora ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_acervo_editora = '{$this->cod_acervo_editora}'" );
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
