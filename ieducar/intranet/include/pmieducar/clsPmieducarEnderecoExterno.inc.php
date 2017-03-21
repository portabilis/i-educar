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
* Criado em 10/07/2006 10:39 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarEnderecoExterno
{
	var $ref_cod_pessoa_educ;
	var $ref_sigla_uf;
	var $ref_idtlog;
	var $logradouro;
	var $numero;
	var $complemento;
	var $letra;
	var $bairro;
	var $cep;
	var $cidade;
	var $andar;
	var $bloco;
	var $apartamento;
	
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
	function clsPmieducarEnderecoExterno( $ref_cod_pessoa_educ = null, $ref_sigla_uf = null, $ref_idtlog = null, $logradouro = null, $numero = null, $complemento = null, $letra = null, $bairro = null, $cep = null, $cidade = null, $andar = null, $bloco = null, $apartamento = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}endereco_externo";

		$this->_campos_lista = $this->_todos_campos = "ref_cod_pessoa_educ, ref_sigla_uf, ref_idtlog, logradouro, numero, complemento, letra, bairro, cep, cidade, andar, bloco, apartamento";
		
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
		if( is_numeric( $ref_cod_pessoa_educ ) )
		{
			if( class_exists( "clsPmieducarPessoaEduc" ) )
			{
				$tmp_obj = new clsPmieducarPessoaEduc( $ref_cod_pessoa_educ );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.pessoa_educ WHERE cod_pessoa_educ = '{$ref_cod_pessoa_educ}'" ) )
				{
					$this->ref_cod_pessoa_educ = $ref_cod_pessoa_educ;
				}
			}
		}

		
		if( is_string( $ref_sigla_uf ) )
		{
			$this->ref_sigla_uf = $ref_sigla_uf;
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
		if( is_string( $letra ) )
		{
			$this->letra = $letra;
		}
		if( is_string( $bairro ) )
		{
			$this->bairro = $bairro;
		}
		if( is_numeric( $cep ) )
		{
			$this->cep = $cep;
		}
		if( is_string( $cidade ) )
		{
			$this->cidade = $cidade;
		}
		if( is_numeric( $andar ) )
		{
			$this->andar = $andar;
		}
		if( is_string( $bloco ) )
		{
			$this->bloco = $bloco;
		}
		if( is_numeric( $apartamento ) )
		{
			$this->apartamento = $apartamento;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_pessoa_educ ) && is_string( $this->ref_sigla_uf ) && is_string( $this->ref_idtlog ) )
		{
			$db = new clsBanco();
			
			$campos = "";
			$valores = "";
			$gruda = "";
			
			if( is_numeric( $this->ref_cod_pessoa_educ ) )
			{
				$campos .= "{$gruda}ref_cod_pessoa_educ";
				$valores .= "{$gruda}'{$this->ref_cod_pessoa_educ}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_sigla_uf ) )
			{
				$campos .= "{$gruda}ref_sigla_uf";
				$valores .= "{$gruda}'{$this->ref_sigla_uf}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_idtlog ) )
			{
				$campos .= "{$gruda}ref_idtlog";
				$valores .= "{$gruda}'{$this->ref_idtlog}'";
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
			if( is_string( $this->letra ) )
			{
				$campos .= "{$gruda}letra";
				$valores .= "{$gruda}'{$this->letra}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$campos .= "{$gruda}bairro";
				$valores .= "{$gruda}'{$this->bairro}'";
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
			if( is_numeric( $this->andar ) )
			{
				$campos .= "{$gruda}andar";
				$valores .= "{$gruda}'{$this->andar}'";
				$gruda = ", ";
			}
			if( is_string( $this->bloco ) )
			{
				$campos .= "{$gruda}bloco";
				$valores .= "{$gruda}'{$this->bloco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->apartamento ) )
			{
				$campos .= "{$gruda}apartamento";
				$valores .= "{$gruda}'{$this->apartamento}'";
				$gruda = ", ";
			}

			
			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_ref_cod_pessoa_educ_seq");
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
		if( is_numeric( $this->ref_cod_pessoa_educ ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->ref_sigla_uf ) )
			{
				$set .= "{$gruda}ref_sigla_uf = '{$this->ref_sigla_uf}'";
				$gruda = ", ";
			}
			if( is_string( $this->ref_idtlog ) )
			{
				$set .= "{$gruda}ref_idtlog = '{$this->ref_idtlog}'";
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
			if( is_string( $this->letra ) )
			{
				$set .= "{$gruda}letra = '{$this->letra}'";
				$gruda = ", ";
			}
			if( is_string( $this->bairro ) )
			{
				$set .= "{$gruda}bairro = '{$this->bairro}'";
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
			if( is_numeric( $this->andar ) )
			{
				$set .= "{$gruda}andar = '{$this->andar}'";
				$gruda = ", ";
			}
			if( is_string( $this->bloco ) )
			{
				$set .= "{$gruda}bloco = '{$this->bloco}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->apartamento ) )
			{
				$set .= "{$gruda}apartamento = '{$this->apartamento}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'" );
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
	function lista( $int_ref_cod_pessoa_educ = null, $str_ref_sigla_uf = null, $str_ref_idtlog = null, $str_logradouro = null, $int_numero = null, $str_complemento = null, $str_letra = null, $str_bairro = null, $int_cep = null, $str_cidade = null, $int_andar = null, $str_bloco = null, $int_apartamento = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";
		
		$whereAnd = " WHERE ";
		
		if( is_numeric( $int_ref_cod_pessoa_educ ) )
		{
			$filtros .= "{$whereAnd} ref_cod_pessoa_educ = '{$int_ref_cod_pessoa_educ}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ref_sigla_uf ) )
		{
			$filtros .= "{$whereAnd} ref_sigla_uf LIKE '%{$str_ref_sigla_uf}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ref_idtlog ) )
		{
			$filtros .= "{$whereAnd} ref_idtlog LIKE '%{$str_ref_idtlog}%'";
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
		if( is_string( $str_letra ) )
		{
			$filtros .= "{$whereAnd} letra LIKE '%{$str_letra}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_bairro ) )
		{
			$filtros .= "{$whereAnd} bairro LIKE '%{$str_bairro}%'";
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
		if( is_numeric( $int_andar ) )
		{
			$filtros .= "{$whereAnd} andar = '{$int_andar}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_bloco ) )
		{
			$filtros .= "{$whereAnd} bloco LIKE '%{$str_bloco}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_apartamento ) )
		{
			$filtros .= "{$whereAnd} apartamento = '{$int_apartamento}'";
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
		if( is_numeric( $this->ref_cod_pessoa_educ ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'" );
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
		if( is_numeric( $this->ref_cod_pessoa_educ ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'" );
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
		if( is_numeric( $this->ref_cod_pessoa_educ ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_pessoa_educ = '{$this->ref_cod_pessoa_educ}'" );
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