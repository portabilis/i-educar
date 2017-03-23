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
* Criado em 20/06/2006 11:38 pelo gerador automatico de classes
*/

require_once( "include/pmidrh/geral.inc.php" );

class clsPmidrhDiaria
{
	var $cod_diaria;
	var $ref_funcionario_cadastro;
	var $ref_cod_diaria_grupo;
	var $ref_funcionario;
	var $conta_corrente;
	var $agencia;
	var $banco;
	var $dotacao_orcamentaria;
	var $objetivo;
	var $data_partida;
	var $data_chegada;
	var $estadual;
	var $destino;
	var $data_pedido;
	var $vl100;
	var $vl75;
	var $vl50;
	var $vl25;
	var $roteiro;
	var $ref_cod_administracao_secretaria;

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
	function clsPmidrhDiaria( $cod_diaria = null, $ref_funcionario_cadastro = null, $ref_cod_diaria_grupo = null, $ref_funcionario = null, $conta_corrente = null, $agencia = null, $banco = null, $dotacao_orcamentaria = null, $objetivo = null, $data_partida = null, $data_chegada = null, $estadual = null, $destino = null, $data_pedido = null, $vl100 = null, $vl75 = null, $vl50 = null, $vl25 = null, $roteiro = null, $ref_cod_administracao_secretaria = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmidrh.";
		$this->_tabela = "{$this->_schema}diaria";

		$this->_campos_lista = $this->_todos_campos = "cod_diaria, ref_funcionario_cadastro, ref_cod_diaria_grupo, ref_funcionario, conta_corrente, agencia, banco, dotacao_orcamentaria, objetivo, data_partida, data_chegada, estadual, destino, data_pedido, vl100, vl75, vl50, vl25, roteiro, ref_cod_administracao_secretaria";

		if( is_numeric( $ref_cod_administracao_secretaria ) )
		{
			if( class_exists( "clsPmidrhAdministracaoSecretaria" ) )
			{
				$tmp_obj = new clsPmidrhAdministracaoSecretaria( $ref_cod_administracao_secretaria );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM administracao_secretaria WHERE cod_administracao_secretaria = '{$ref_cod_administracao_secretaria}'" ) )
				{
					$this->ref_cod_administracao_secretaria = $ref_cod_administracao_secretaria;
				}
			}
		}
		if( is_numeric( $ref_funcionario_cadastro ) )
		{
			if( class_exists( "clsPmidrhFuncionario" ) )
			{
				$tmp_obj = new clsPmidrhFuncionario( $ref_funcionario_cadastro );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario_cadastro}'" ) )
				{
					$this->ref_funcionario_cadastro = $ref_funcionario_cadastro;
				}
			}
		}
		if( is_numeric( $ref_cod_diaria_grupo ) )
		{
			if( class_exists( "clsPmidrhDiariaGrupo" ) )
			{
				$tmp_obj = new clsPmidrhDiariaGrupo( $ref_cod_diaria_grupo );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmidrh.diaria_grupo WHERE cod_diaria_grupo = '{$ref_cod_diaria_grupo}'" ) )
				{
					$this->ref_cod_diaria_grupo = $ref_cod_diaria_grupo;
				}
			}
		}
		if( is_numeric( $ref_funcionario ) )
		{
			if( class_exists( "clsPmidrhFuncionario" ) )
			{
				$tmp_obj = new clsPmidrhFuncionario( $ref_funcionario );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_funcionario = $ref_funcionario;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_funcionario = $ref_funcionario;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM funcionario WHERE ref_cod_pessoa_fj = '{$ref_funcionario}'" ) )
				{
					$this->ref_funcionario = $ref_funcionario;
				}
			}
		}


		if( is_numeric( $cod_diaria ) )
		{
			$this->cod_diaria = $cod_diaria;
		}
		if( is_numeric( $conta_corrente ) )
		{
			$this->conta_corrente = $conta_corrente;
		}
		if( is_numeric( $agencia ) )
		{
			$this->agencia = $agencia;
		}
		if( is_numeric( $banco ) )
		{
			$this->banco = $banco;
		}
		if( is_string( $dotacao_orcamentaria ) )
		{
			$this->dotacao_orcamentaria = $dotacao_orcamentaria;
		}
		if( is_string( $objetivo ) )
		{
			$this->objetivo = $objetivo;
		}
		if( is_string( $data_partida ) )
		{
			$this->data_partida = $data_partida;
		}
		if( is_string( $data_chegada ) )
		{
			$this->data_chegada = $data_chegada;
		}
		if( is_numeric( $estadual ) )
		{
			$this->estadual = $estadual;
		}
		if( is_string( $destino ) )
		{
			$this->destino = $destino;
		}
		if( is_string( $data_pedido ) )
		{
			$this->data_pedido = $data_pedido;
		}
		if( is_numeric( $vl100 ) )
		{
			$this->vl100 = $vl100;
		}
		if( is_numeric( $vl75 ) )
		{
			$this->vl75 = $vl75;
		}
		if( is_numeric( $vl50 ) )
		{
			$this->vl50 = $vl50;
		}
		if( is_numeric( $vl25 ) )
		{
			$this->vl25 = $vl25;
		}
		if( is_numeric( $roteiro ) )
		{
			$this->roteiro = $roteiro;
		}

	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $ref_funcionario_cadastro ) && is_numeric( $ref_cod_diaria_grupo ) && is_numeric( $ref_funcionario ) )
		{
			$db = new clsBanco();

			$campos = "";
			$valores = "";
			$gruda = "";

			if( is_numeric( $this->ref_funcionario_cadastro ) )
			{
				$campos .= "{$gruda}ref_funcionario_cadastro";
				$valores .= "{$gruda}'{$this->ref_funcionario_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				$campos .= "{$gruda}ref_cod_diaria_grupo";
				$valores .= "{$gruda}'{$this->ref_cod_diaria_grupo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario ) )
			{
				$campos .= "{$gruda}ref_funcionario";
				$valores .= "{$gruda}'{$this->ref_funcionario}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->conta_corrente ) )
			{
				$campos .= "{$gruda}conta_corrente";
				$valores .= "{$gruda}'{$this->conta_corrente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->agencia ) )
			{
				$campos .= "{$gruda}agencia";
				$valores .= "{$gruda}'{$this->agencia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->banco ) )
			{
				$campos .= "{$gruda}banco";
				$valores .= "{$gruda}'{$this->banco}'";
				$gruda = ", ";
			}
			if( is_string( $this->dotacao_orcamentaria ) )
			{
				$campos .= "{$gruda}dotacao_orcamentaria";
				$valores .= "{$gruda}'{$this->dotacao_orcamentaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->objetivo ) )
			{
				$campos .= "{$gruda}objetivo";
				$valores .= "{$gruda}'{$this->objetivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_partida ) )
			{
				$campos .= "{$gruda}data_partida";
				$valores .= "{$gruda}'{$this->data_partida}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_chegada ) )
			{
				$campos .= "{$gruda}data_chegada";
				$valores .= "{$gruda}'{$this->data_chegada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->estadual ) )
			{
				$campos .= "{$gruda}estadual";
				$valores .= "{$gruda}'{$this->estadual}'";
				$gruda = ", ";
			}
			if( is_string( $this->destino ) )
			{
				$campos .= "{$gruda}destino";
				$valores .= "{$gruda}'{$this->destino}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_pedido ) )
			{
				$campos .= "{$gruda}data_pedido";
				$valores .= "{$gruda}'{$this->data_pedido}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl100 ) )
			{
				$campos .= "{$gruda}vl100";
				$valores .= "{$gruda}'{$this->vl100}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl75 ) )
			{
				$campos .= "{$gruda}vl75";
				$valores .= "{$gruda}'{$this->vl75}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl50 ) )
			{
				$campos .= "{$gruda}vl50";
				$valores .= "{$gruda}'{$this->vl50}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl25 ) )
			{
				$campos .= "{$gruda}vl25";
				$valores .= "{$gruda}'{$this->vl25}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->roteiro ) )
			{
				$campos .= "{$gruda}roteiro";
				$valores .= "{$gruda}'{$this->roteiro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_administracao_secretaria ) )
			{
				$campos .= "{$gruda}ref_cod_administracao_secretaria";
				$valores .= "{$gruda}'{$this->ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}__seq");
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
		if( is_numeric( $this->cod_diaria ) )
		{

			$db = new clsBanco();
			$set = "";

			if( is_numeric( $this->ref_funcionario_cadastro ) )
			{
				$set .= "{$gruda}ref_funcionario_cadastro = '{$this->ref_funcionario_cadastro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_diaria_grupo ) )
			{
				$set .= "{$gruda}ref_cod_diaria_grupo = '{$this->ref_cod_diaria_grupo}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_funcionario ) )
			{
				$set .= "{$gruda}ref_funcionario = '{$this->ref_funcionario}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->conta_corrente ) )
			{
				$set .= "{$gruda}conta_corrente = '{$this->conta_corrente}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->agencia ) )
			{
				$set .= "{$gruda}agencia = '{$this->agencia}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->banco ) )
			{
				$set .= "{$gruda}banco = '{$this->banco}'";
				$gruda = ", ";
			}
			if( is_string( $this->dotacao_orcamentaria ) )
			{
				$set .= "{$gruda}dotacao_orcamentaria = '{$this->dotacao_orcamentaria}'";
				$gruda = ", ";
			}
			if( is_string( $this->objetivo ) )
			{
				$set .= "{$gruda}objetivo = '{$this->objetivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_partida ) )
			{
				$set .= "{$gruda}data_partida = '{$this->data_partida}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_chegada ) )
			{
				$set .= "{$gruda}data_chegada = '{$this->data_chegada}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->estadual ) )
			{
				$set .= "{$gruda}estadual = '{$this->estadual}'";
				$gruda = ", ";
			}
			if( is_string( $this->destino ) )
			{
				$set .= "{$gruda}destino = '{$this->destino}'";
				$gruda = ", ";
			}
			if( is_string( $this->data_pedido ) )
			{
				$set .= "{$gruda}data_pedido = '{$this->data_pedido}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl100 ) )
			{
				$set .= "{$gruda}vl100 = '{$this->vl100}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl75 ) )
			{
				$set .= "{$gruda}vl75 = '{$this->vl75}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl50 ) )
			{
				$set .= "{$gruda}vl50 = '{$this->vl50}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->vl25 ) )
			{
				$set .= "{$gruda}vl25 = '{$this->vl25}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->roteiro ) )
			{
				$set .= "{$gruda}roteiro = '{$this->roteiro}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_cod_administracao_secretaria ) )
			{
				$set .= "{$gruda}ref_cod_administracao_secretaria = '{$this->ref_cod_administracao_secretaria}'";
				$gruda = ", ";
			}


			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_diaria = '{$this->cod_diaria}'" );
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
	function lista( $int_cod_diaria = null, $int_conta_corrente = null, $int_agencia = null, $int_banco = null, $str_dotacao_orcamentaria = null, $str_objetivo = null, $date_data_partida = null, $date_data_chegada = null, $int_estadual = null, $str_destino = null, $date_data_pedido = null, $int_vl100 = null, $int_vl75 = null, $int_vl50 = null, $int_vl25 = null, $int_roteiro = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_diaria ) )
		{
			$filtros .= "{$whereAnd} cod_diaria = '{$int_cod_diaria}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario_cadastro ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario_cadastro = '{$int_ref_funcionario_cadastro}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_diaria_grupo ) )
		{
			$filtros .= "{$whereAnd} ref_cod_diaria_grupo = '{$int_ref_cod_diaria_grupo}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_funcionario ) )
		{
			$filtros .= "{$whereAnd} ref_funcionario = '{$int_ref_funcionario}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_conta_corrente ) )
		{
			$filtros .= "{$whereAnd} conta_corrente = '{$int_conta_corrente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_agencia ) )
		{
			$filtros .= "{$whereAnd} agencia = '{$int_agencia}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_banco ) )
		{
			$filtros .= "{$whereAnd} banco = '{$int_banco}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_partida_ini ) )
		{
			$filtros .= "{$whereAnd} data_partida >= '{$date_data_partida_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_partida_fim ) )
		{
			$filtros .= "{$whereAnd} data_partida <= '{$date_data_partida_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_chegada_ini ) )
		{
			$filtros .= "{$whereAnd} data_chegada >= '{$date_data_chegada_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_chegada_fim ) )
		{
			$filtros .= "{$whereAnd} data_chegada <= '{$date_data_chegada_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_estadual ) )
		{
			$filtros .= "{$whereAnd} estadual = '{$int_estadual}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_pedido_ini ) )
		{
			$filtros .= "{$whereAnd} data_pedido >= '{$date_data_pedido_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_pedido_fim ) )
		{
			$filtros .= "{$whereAnd} data_pedido <= '{$date_data_pedido_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_vl100 ) )
		{
			$filtros .= "{$whereAnd} vl100 = '{$int_vl100}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_vl75 ) )
		{
			$filtros .= "{$whereAnd} vl75 = '{$int_vl75}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_vl50 ) )
		{
			$filtros .= "{$whereAnd} vl50 = '{$int_vl50}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_vl25 ) )
		{
			$filtros .= "{$whereAnd} vl25 = '{$int_vl25}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_roteiro ) )
		{
			$filtros .= "{$whereAnd} roteiro = '{$int_roteiro}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_administracao_secretaria ) )
		{
			$filtros .= "{$whereAnd} ref_cod_administracao_secretaria = '{$int_ref_cod_administracao_secretaria}'";
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
		if( is_numeric( $this->cod_diaria ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_diaria = '{$this->cod_diaria}'" );
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
		if( is_numeric( $this->cod_diaria ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_diaria = '{$this->cod_diaria}'" );
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
		if( is_numeric( $this->cod_diaria ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_diaria = '{$this->cod_diaria}'" );
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