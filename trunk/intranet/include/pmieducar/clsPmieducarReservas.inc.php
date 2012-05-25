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
* Criado em 19/07/2006 15:02 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarReservas
{
	var $cod_reserva;
	var $ref_usuario_libera;
	var $ref_usuario_cad;
	var $ref_cod_cliente;
	var $data_reserva;
	var $data_prevista_disponivel;
	var $data_retirada;
	var $ref_cod_exemplar;
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
	function clsPmieducarReservas( $cod_reserva = null, $ref_usuario_libera = null, $ref_usuario_cad = null, $ref_cod_cliente = null, $data_reserva = null, $data_prevista_disponivel = null, $data_retirada = null, $ref_cod_exemplar = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}reservas";

		$this->_campos_lista = $this->_todos_campos = "r.cod_reserva, r.ref_usuario_libera, r.ref_usuario_cad, r.ref_cod_cliente, r.data_reserva, r.data_prevista_disponivel, r.data_retirada, r.ref_cod_exemplar, r.ativo";

		if( is_numeric( $ref_cod_exemplar ) )
		{
			if( class_exists( "clsPmieducarExemplar" ) )
			{
				$tmp_obj = new clsPmieducarExemplar( $ref_cod_exemplar );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_exemplar = $ref_cod_exemplar;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_exemplar = $ref_cod_exemplar;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.exemplar WHERE cod_exemplar = '{$ref_cod_exemplar}'" ) )
				{
					$this->ref_cod_exemplar = $ref_cod_exemplar;
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
		if( is_numeric( $ref_usuario_libera ) )
		{
			if( class_exists( "clsPmieducarUsuario" ) )
			{
				$tmp_obj = new clsPmieducarUsuario( $ref_usuario_libera );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_usuario_libera = $ref_usuario_libera;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_usuario_libera = $ref_usuario_libera;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_libera}'" ) )
				{
					$this->ref_usuario_libera = $ref_usuario_libera;
				}
			}
		}
		if( is_numeric( $ref_cod_cliente ) )
		{
			if( class_exists( "clsPmieducarCliente" ) )
			{
				$tmp_obj = new clsPmieducarCliente( $ref_cod_cliente );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_cliente = $ref_cod_cliente;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_cliente = $ref_cod_cliente;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.cliente WHERE cod_cliente = '{$ref_cod_cliente}'" ) )
				{
					$this->ref_cod_cliente = $ref_cod_cliente;
				}
			}
		}


		if( is_numeric( $cod_reserva ) )
		{
			$this->cod_reserva = $cod_reserva;
		}
		if( is_string( $data_reserva ) )
		{
			$this->data_reserva = $data_reserva;
		}
		if( is_string( $data_prevista_disponivel ) )
		{
			$this->data_prevista_disponivel = $data_prevista_disponivel;
		}
		if( is_string( $data_retirada ) )
		{
			$this->data_retirada = $data_retirada;
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
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_cod_cliente ) && is_numeric( $this->ref_cod_exemplar ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_usuario_libera ) )
			{
				$campos .= "{$gruda}ref_usuario_libera";
				$valores .= "{$gruda}'{$this->ref_usuario_libera}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_cliente ) )
			{
				$campos .= "{$gruda}ref_cod_cliente";
				$valores .= "{$gruda}'{$this->ref_cod_cliente}'";
				$gruda = ", ";
			}
//			if( is_string( $this->data_reserva ) )
//			{
				$campos .= "{$gruda}data_reserva";
				$valores .= "{$gruda}NOW()";
				$gruda = ", ";
//			}
			if( is_string( $this->data_prevista_disponivel ) )
			{
				$campos .= "{$gruda}data_prevista_disponivel";
				$valores .= "{$gruda}'{$this->data_prevista_disponivel}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_retirada ) )
			{
				$campos .= "{$gruda}data_retirada";
				$valores .= "{$gruda}'{$this->data_retirada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_exemplar ) )
			{
				$campos .= "{$gruda}ref_cod_exemplar";
				$valores .= "{$gruda}'{$this->ref_cod_exemplar}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_reserva_seq");
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
		if( is_numeric( $this->cod_reserva ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_usuario_libera ) )
			{
				$set .= "{$gruda}ref_usuario_libera = '{$this->ref_usuario_libera}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_cliente ) )
			{
				$set .= "{$gruda}ref_cod_cliente = '{$this->ref_cod_cliente}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_reserva ) )
			{
				$set .= "{$gruda}data_reserva = '{$this->data_reserva}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_prevista_disponivel ) )
			{
				$set .= "{$gruda}data_prevista_disponivel = '{$this->data_prevista_disponivel}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_retirada ) )
			{
				$set .= "{$gruda}data_retirada = '{$this->data_retirada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_exemplar ) )
			{
				$set .= "{$gruda}ref_cod_exemplar = '{$this->ref_cod_exemplar}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ativo ) )
			{
				$set .= "{$gruda}ativo = '{$this->ativo}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_reserva = '{$this->cod_reserva}'" );
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
	function lista( $int_cod_reserva = null, $int_ref_usuario_libera = null, $int_ref_usuario_cad = null, $int_ref_cod_cliente = null, $date_data_reserva_ini = null, $date_data_reserva_fim = null, $date_data_prevista_disponivel_ini = null, $date_data_prevista_disponivel_fim = null, $date_data_retirada_ini = null, $date_data_retirada_fim = null, $int_ref_cod_exemplar = null, $int_ativo = null, $int_ref_cod_biblioteca = null, $int_ref_cod_instituicao = null, $int_ref_cod_escola = null, $data_retirada_null = false )
	{
		$sql = "SELECT {$this->_campos_lista}, a.ref_cod_biblioteca, b.ref_cod_instituicao, b.ref_cod_escola FROM {$this->_tabela} r, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b";

		$whereAnd = " AND ";
		$filtros = " WHERE r.ref_cod_exemplar = e.cod_exemplar AND e.ref_cod_acervo = a.cod_acervo AND a.ref_cod_biblioteca = b.cod_biblioteca ";

		if( is_numeric( $int_cod_reserva ) )
		{
			$filtros .= "{$whereAnd} r.cod_reserva = '{$int_cod_reserva}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_libera ) )
		{
			$filtros .= "{$whereAnd} r.ref_usuario_libera = '{$int_ref_usuario_libera}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} r.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_cliente ) )
		{
			$filtros .= "{$whereAnd} r.ref_cod_cliente = '{$int_ref_cod_cliente}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_reserva_ini ) )
		{
			$filtros .= "{$whereAnd} r.data_reserva >= '{$date_data_reserva_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_reserva_fim ) )
		{
			$filtros .= "{$whereAnd} r.data_reserva <= '{$date_data_reserva_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_prevista_disponivel_ini ) )
		{
			$filtros .= "{$whereAnd} r.data_prevista_disponivel >= '{$date_data_prevista_disponivel_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_prevista_disponivel_fim ) )
		{
			$filtros .= "{$whereAnd} r.data_prevista_disponivel <= '{$date_data_prevista_disponivel_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_retirada_ini ) )
		{
			$filtros .= "{$whereAnd} r.data_retirada >= '{$date_data_retirada_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_retirada_fim ) )
		{
			$filtros .= "{$whereAnd} r.data_retirada <= '{$date_data_retirada_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_exemplar ) )
		{
			$filtros .= "{$whereAnd} r.ref_cod_exemplar = '{$int_ref_cod_exemplar}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} r.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} r.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} a.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} b.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} b.ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}

		if($data_retirada_null)
		{
			$filtros .= "{$whereAnd} r.data_retirada is null";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} r, {$this->_schema}exemplar e, {$this->_schema}acervo a, {$this->_schema}biblioteca b {$filtros}" );

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
		if( is_numeric( $this->cod_reserva ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} r WHERE r.cod_reserva = '{$this->cod_reserva}'" );
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
		if( is_numeric( $this->cod_reserva ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_reserva = '{$this->cod_reserva}'" );
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
		if( is_numeric( $this->cod_reserva ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_reserva = '{$this->cod_reserva}'" );
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

	/**
	 * Retorna uma lista com as ultimas reservas de cada exemplar
	 *
	 * @return string
	 */
	function getUltimasReservas( $int_ref_cod_acervo, $int_limite = null )
	{
		if( is_numeric( $int_ref_cod_acervo ) )
		{
			$db = new clsBanco();
			$sql = "SELECT r.ref_cod_exemplar, max(data_prevista_disponivel) AS data_prevista_disponivel
					FROM {$this->_tabela} r,
					     {$this->_schema}exemplar e
					WHERE r.ref_cod_exemplar = e.cod_exemplar AND
					  	  e.ref_cod_acervo = '{$int_ref_cod_acervo}'
					GROUP BY r.ref_cod_exemplar
					ORDER BY max(data_prevista_disponivel) ASC";
			if ($int_limite)
			{
				$sql .= " limit '{$int_limite}'";
			}

			$db->Consulta( $sql );
			$resultado = array();
			while ($db->ProximoRegistro()) {
				$resultado[] = $db->Tupla();
			}
			if( count( $resultado ) )
			{
				return $resultado;
			}
			return false;
		}
		return false;
	}

	/**
	 * Retorna a ultima reserva do exemplar
	 *
	 * @return string
	 */
	function getUltimaReserva( $int_ref_cod_exemplar )
	{
		if( is_numeric( $int_ref_cod_exemplar ) )
		{
			$db = new clsBanco();
			$sql = "SELECT r.ref_cod_exemplar, max(data_prevista_disponivel) AS data_prevista_disponivel
					FROM {$this->_tabela} r,
					     {$this->_schema}exemplar e
					WHERE r.ref_cod_exemplar = e.cod_exemplar AND
					  	  e.cod_exemplar = '{$int_ref_cod_exemplar}'
					GROUP BY r.ref_cod_exemplar
					ORDER BY max(data_prevista_disponivel) ASC";
			$db->Consulta( $sql );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}
}
?>
