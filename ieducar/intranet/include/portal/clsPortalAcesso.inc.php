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
* Criado em 29/09/2006 08:27 pelo gerador automatico de classes
*/

class clsPortalAcesso
{
	var $cod_acesso;
	var $data_hora;
	var $ip_externo;
	var $ip_interno;
	var $cod_pessoa;
	var $obs;
	var $sucesso;

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
	function clsPortalAcesso( $cod_acesso = null, $data_hora = null, $ip_externo = null, $ip_interno = null, $cod_pessoa = null, $obs = null, $sucesso = null )
	{
		$db = new clsBanco();
		$this->_schema = "portal.";
		$this->_tabela = "{$this->_schema}acesso";

		$this->_campos_lista = $this->_todos_campos = "cod_acesso, data_hora, ip_externo, ip_interno, cod_pessoa, obs, sucesso";



		if( is_numeric( $cod_acesso ) )
		{
			$this->cod_acesso = $cod_acesso;
		}
		if( is_string( $data_hora ) )
		{
			$this->data_hora = $data_hora;
		}
		if( is_string( $ip_externo ) )
		{
			$this->ip_externo = $ip_externo;
		}
		if( is_string( $ip_interno ) )
		{
			$this->ip_interno = $ip_interno;
		}
		if( is_numeric( $cod_pessoa ) )
		{
			$this->cod_pessoa = $cod_pessoa;
		}
		if( is_string( $obs ) )
		{
			$this->obs = $obs;
		}
		if( ! is_null( $sucesso ) )
		{
			$this->sucesso = $sucesso;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_string( $this->data_hora ) && is_string( $this->ip_externo ) && is_string( $this->ip_interno ) && is_numeric( $this->cod_pessoa ) && ! is_null( $this->sucesso ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_string( $this->data_hora ) )
			{
				$campos .= "{$gruda}data_hora";
				$valores .= "{$gruda}'{$this->data_hora}'";
				$gruda = ", ";
			}
			if( is_string( $this->ip_externo ) )
			{
				$campos .= "{$gruda}ip_externo";
				$valores .= "{$gruda}'{$this->ip_externo}'";
				$gruda = ", ";
			}
			if( is_string( $this->ip_interno ) )
			{
				$campos .= "{$gruda}ip_interno";
				$valores .= "{$gruda}'{$this->ip_interno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cod_pessoa ) )
			{
				$campos .= "{$gruda}cod_pessoa";
				$valores .= "{$gruda}'{$this->cod_pessoa}'";
				$gruda = ", ";
			}
			if( is_string( $this->obs ) )
			{
				$campos .= "{$gruda}obs";
				$valores .= "{$gruda}'{$this->obs}'";
				$gruda = ", ";
			}
			if( ! is_null( $this->sucesso ) )
			{
				$campos .= "{$gruda}sucesso";
				if( dbBool( $this->sucesso ) )
				{
					$valores .= "{$gruda}TRUE";
				}
				else
				{
					$valores .= "{$gruda}FALSE";
				}
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_acesso_seq");
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
		if( is_numeric( $this->cod_acesso ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_string( $this->data_hora ) )
			{
				$set .= "{$gruda}data_hora = '{$this->data_hora}'";
				$gruda = ", ";
			}
			if( is_string( $this->ip_externo ) )
			{
				$set .= "{$gruda}ip_externo = '{$this->ip_externo}'";
				$gruda = ", ";
			}
			if( is_string( $this->ip_interno ) )
			{
				$set .= "{$gruda}ip_interno = '{$this->ip_interno}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->cod_pessoa ) )
			{
				$set .= "{$gruda}cod_pessoa = '{$this->cod_pessoa}'";
				$gruda = ", ";
			}
			if( is_string( $this->obs ) )
			{
				$set .= "{$gruda}obs = '{$this->obs}'";
				$gruda = ", ";
			}
			if( ! is_null( $this->sucesso ) )
			{
				$val = dbBool( $this->sucesso ) ? "TRUE": "FALSE";
				$set .= "{$gruda}sucesso = {$val}";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_acesso = '{$this->cod_acesso}'" );
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
	function lista( $date_data_hora_ini = null, $date_data_hora_fim = null, $str_ip_externo = null, $str_ip_interno = null, $int_cod_pessoa = null, $str_obs = null, $bool_sucesso = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_acesso ) )
		{
			$filtros .= "{$whereAnd} cod_acesso = '{$int_cod_acesso}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_hora_ini ) )
		{
			$filtros .= "{$whereAnd} data_hora >= '{$date_data_hora_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_hora_fim ) )
		{
			$filtros .= "{$whereAnd} data_hora <= '{$date_data_hora_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ip_externo ) )
		{
			$filtros .= "{$whereAnd} ip_externo LIKE '%{$str_ip_externo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_ip_interno ) )
		{
			$filtros .= "{$whereAnd} ip_interno LIKE '%{$str_ip_interno}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_cod_pessoa ) )
		{
			$filtros .= "{$whereAnd} cod_pessoa = '{$int_cod_pessoa}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_obs ) )
		{
			$filtros .= "{$whereAnd} obs LIKE '%{$str_obs}%'";
			$whereAnd = " AND ";
		}
		if( ! is_null( $bool_sucesso ) )
		{
			if( dbBool( $bool_sucesso ) )
			{
				$filtros .= "{$whereAnd} sucesso = TRUE";
			}
			else
			{
				$filtros .= "{$whereAnd} sucesso = FALSE";
			}
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
	 * Retorna uma lista de falhas filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista_falhas( $int_cod_pessoa = null, $int_min_quantidade_falhas = null, $int_max_quantidade_falhas = null, $date_ultimo_sucesso_ini = null, $date_ultimo_sucesso_fim = null, $date_quinto_erro_ini = null, $date_quinto_erro_fim = null )
	{

$query_fonte = "
	SELECT COUNT(a1.cod_pessoa) AS falha, sub2.cod_pessoa, sub2.ultimo_sucesso
	, ( SELECT a3.data_hora FROM acesso AS a3 WHERE a3.cod_pessoa = sub2.cod_pessoa AND a3.sucesso = 'f' ORDER BY a3.data_hora DESC LIMIT 1 OFFSET 4 ) AS quinto_erro
	FROM acesso AS a1,
	(
		SELECT sub1.cod_pessoa,
		CASE WHEN sub1.ultimo_sucesso > ( NOW() - time '00:30' ) THEN
			sub1.ultimo_sucesso
		ELSE
			NOW() - time '00:30'
		END AS ultimo_sucesso
		FROM (
			SELECT
			a2.cod_pessoa,
			MAX(a2.data_hora) AS ultimo_sucesso
			FROM acesso AS a2
			WHERE
			sucesso = 't'
			GROUP BY cod_pessoa
		) AS sub1
	) AS sub2
	WHERE a1.cod_pessoa = sub2.cod_pessoa
	AND a1.data_hora > sub2.ultimo_sucesso
	AND a1.sucesso = 'f'
	GROUP BY sub2.cod_pessoa, sub2.ultimo_sucesso
";
		$sql = "
SELECT falha, cod_pessoa, ultimo_sucesso, quinto_erro FROM
(
{$query_fonte}
) AS sub3
";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_pessoa ) )
		{
			$filtros .= "{$whereAnd} cod_pessoa = '{$int_cod_pessoa}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_min_quantidade_falhas ) )
		{
			$filtros .= "{$whereAnd} falha >= '{$int_min_quantidade_falhas}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_max_quantidade_falhas ) )
		{
			$filtros .= "{$whereAnd} falha <= '{$int_max_quantidade_falhas}'";
			$whereAnd = " AND ";
		}

		if( is_string( $date_ultimo_sucesso_ini ) )
		{
			$filtros .= "{$whereAnd} ultimo_sucesso >= '{$date_ultimo_sucesso_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_ultimo_sucesso_fim ) )
		{
			$filtros .= "{$whereAnd} ultimo_sucesso <= '{$date_ultimo_sucesso_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_quinto_erro_ini ) )
		{
			$filtros .= "{$whereAnd} quinto_erro >= '{$date_quinto_erro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_quinto_erro_fim ) )
		{
			$filtros .= "{$whereAnd} quinto_erro <= '{$date_quinto_erro_fim}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "
SELECT COUNT(0) FROM (
{$query_fonte}
) AS sub3 {$filtros}" );

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
		if( is_numeric( $this->cod_acesso ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_acesso = '{$this->cod_acesso}'" );
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
		if( is_numeric( $this->cod_acesso ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_acesso = '{$this->cod_acesso}'" );
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
		if( is_numeric( $this->cod_acesso ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_acesso = '{$this->cod_acesso}'" );
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