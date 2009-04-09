<?php
/**
 *
 *	@author Prefeitura Municipal de Itajaí
 *	@updated 29/03/2007
 *   Pacote: i-PLB Software Público Livre e Brasileiro
 *
 *	Copyright (C) 2006	PMI - Prefeitura Municipal de Itajaí
 *						ctima@itajai.sc.gov.br
 *
 *	Este  programa  é  software livre, você pode redistribuí-lo e/ou
 *	modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 *	publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 *	Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 *	Este programa  é distribuído na expectativa de ser útil, mas SEM
 *	QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 *	ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 *	sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 *	Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 *	junto  com  este  programa. Se não, escreva para a Free Software
 *	Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 *	02111-1307, USA.
 *
 */

/*
 * Criado em 17/07/2006 09:18 pelo gerador automatico de classes
 */

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarCliente
{
	var $cod_cliente;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $ref_idpes;
	var $login;
	var $senha;
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
	function clsPmieducarCliente( $cod_cliente = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $ref_idpes = null, $login = null, $senha = null, $data_cadastro = null, $data_exclusao = null, $ativo = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}cliente";

		$this->_campos_lista = $this->_todos_campos = "c.cod_cliente, c.ref_usuario_exc, c.ref_usuario_cad, c.ref_idpes, c.login, c.senha, c.data_cadastro, c.data_exclusao, c.ativo";

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
		if( is_numeric( $ref_idpes ) )
		{
			if( class_exists( "clsCadastroFisica" ) )
			{
				$tmp_obj = new clsCadastroFisica( $ref_idpes );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_idpes = $ref_idpes;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_idpes}'" ) )
				{
					$this->ref_idpes = $ref_idpes;
				}
			}
		}


		if( is_numeric( $cod_cliente ) )
		{
			$this->cod_cliente = $cod_cliente;
		}
		if( is_numeric( $login ) )
		{
			$this->login = $login;
		}
		if( is_string( $senha ) )
		{
			$this->senha = $senha;
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
		if( is_numeric( $this->ref_usuario_cad ) && is_numeric( $this->ref_idpes ) )
		{
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
			if( is_numeric( $this->ref_idpes ) )
			{
				$campos .= "{$gruda}ref_idpes";
				$valores .= "{$gruda}'{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->login ) )
			{
				$campos .= "{$gruda}login";
				$valores .= "{$gruda}'{$this->login}'";
				$gruda = ", ";
			}
			if( is_string( $this->senha ) )
			{
				$campos .= "{$gruda}senha";
				$valores .= "{$gruda}'{$this->senha}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";


			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_cliente_seq");
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
		if( is_numeric( $this->cod_cliente ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( is_numeric( $this->ref_idpes ) )
			{
				$set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->login ) )
			{
				$set .= "{$gruda}login = '{$this->login}'";
				$gruda = ", ";
			}
			if( is_string( $this->senha ) )
			{
				$set .= "{$gruda}senha = '{$this->senha}'";
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
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE cod_cliente = '{$this->cod_cliente}'" );
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
	function lista( $int_cod_cliente = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $int_login = null, $str_senha = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_cliente = null, $str_suspenso = null, $int_ref_cod_biblioteca = null )
	{
		$tab_adicional  = '';
		$condicao	    = '';
		$camp_adicional = '';
		if ( is_string( $str_suspenso ) )
		{
			$tab_adicional  .= ", {$this->_schema}cliente_suspensao cs ";
			$condicao 	    .= " AND c.cod_cliente = cs.ref_cod_cliente ";
		}
		if( is_numeric($int_ref_cod_biblioteca) || is_array($int_ref_cod_biblioteca))
		{
			$tab_adicional  .= ", {$this->_schema}cliente_tipo ct ";
			$tab_adicional  .= ", {$this->_schema}cliente_tipo_cliente ctc ";
		}
		$sql 	  = "SELECT {$this->_campos_lista}, p.nome{$camp_adicional} FROM {$this->_tabela} c, cadastro.pessoa p {$tab_adicional}";
		$whereAnd = " AND ";

		$filtros = "WHERE c.ref_idpes = p.idpes {$condicao}";

		if( is_numeric( $int_cod_cliente ) )
		{
			$filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_login ) )
		{
			$filtros .= "{$whereAnd} c.login = '{$int_login}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_senha ) )
		{
			$filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} c.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} c.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_cliente ) )
		{
			$filtros .= "{$whereAnd} p.nome LIKE '%{$str_nm_cliente}%'";
			$whereAnd = " AND ";
		}
		if(is_array($int_ref_cod_biblioteca))
		{
			$bibs = implode(", ", $int_ref_cod_biblioteca);
			$filtros .= "{$whereAnd} c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.ref_cod_biblioteca IN ($bibs) ";
			$whereAnd = " AND ";
		}
		elseif (is_numeric($int_ref_cod_biblioteca))
		{
			$filtros .= "{$whereAnd} c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND ct.ref_cod_biblioteca = '$int_ref_cod_biblioteca' ";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} c, cadastro.pessoa p{$tab_adicional} {$filtros}" );

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
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function listaCompleta( $int_cod_cliente = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $int_login = null, $str_senha = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = 1, $str_nm_cliente = null, $str_suspenso = null, $int_cod_cliente_tipo = null, $int_cod_escola = null, $int_cod_biblioteca = null, $int_cod_instituicao = null )
	{
		$tab_adicional  = '';
		$condicao	      = '';
		$camp_adicional = '';
		
		// Se suspenso não for nulo, seleciona clientes suspensos
		if (!is_null($str_suspenso)) 
		{
			$camp_adicional .= ", pmieducar.cliente_suspensao cs ";
			$condicao       .= " AND c.cod_cliente = cs.ref_cod_cliente";
		}
		else
		{
      $camp_adicional .= ", pmieducar.cliente_suspensao cs ";
			$condicao       .= " AND c.cod_cliente <> cs.ref_cod_cliente";		
		}

		$sql1      = "SELECT c.cod_cliente,
						     c.ref_idpes,
						     c.ref_usuario_cad,
						     c.login,
						     p.nome,
						     ct.nm_tipo,
						     ct.cod_cliente_tipo,
						     b.nm_biblioteca,
						     b.cod_biblioteca,
						     e.cod_escola as cod_escola,
						     i.cod_instituicao,
						     (SELECT 'S'::text
						        FROM pmieducar.cliente_suspensao cs 
						       WHERE cs.ref_cod_cliente = c.cod_cliente
						         AND cs.data_liberacao IS NULL) AS id_suspensao
				        FROM pmieducar.cliente                c,
					 	     pmieducar.cliente_tipo_cliente ctc,
				 		     pmieducar.cliente_tipo          ct,
				 		     pmieducar.biblioteca             b,
						     pmieducar.escola                 e,
						     pmieducar.instituicao            i,
						     cadastro.pessoa                  p{$camp_adicional}
					   WHERE c.cod_cliente         = ctc.ref_cod_cliente
				 	 	 AND ct.cod_cliente_tipo   = ctc.ref_cod_cliente_tipo
				 		 AND b.cod_biblioteca      = ct.ref_cod_biblioteca
				 		 AND e.cod_escola          = b.ref_cod_escola
				 		 AND i.cod_instituicao     = b.ref_cod_instituicao
				 		 AND e.ref_cod_instituicao = i.cod_instituicao{$condicao}
						 AND p.idpes               = c.ref_idpes
						 AND c.ativo               = '{$int_ativo}'
						 AND ctc.ativo			   = '{$int_ativo}'";

		$sql2      = "SELECT c.cod_cliente,
					         c.ref_idpes,
						     c.ref_usuario_cad,
						     c.login,
						     p.nome,
						     ct.nm_tipo,
						     ct.cod_cliente_tipo,
						     b.nm_biblioteca,
						     b.cod_biblioteca,
						     null as cod_escola,
						     i.cod_instituicao,
						     (SELECT 'S'::text
						        FROM pmieducar.cliente_suspensao cs
						       WHERE cs.ref_cod_cliente = c.cod_cliente
						         AND cs.data_liberacao IS NULL) AS id_suspensao
					    FROM pmieducar.cliente                c,
						     pmieducar.cliente_tipo_cliente ctc,
						     pmieducar.cliente_tipo          ct,
						     pmieducar.biblioteca             b,
						     pmieducar.instituicao            i,
						     cadastro.pessoa                  p{$camp_adicional}
					   WHERE c.cod_cliente         = ctc.ref_cod_cliente
						 AND ct.cod_cliente_tipo   = ctc.ref_cod_cliente_tipo
						 AND b.cod_biblioteca      = ct.ref_cod_biblioteca
						 AND i.cod_instituicao     = b.ref_cod_instituicao
						 AND b.ref_cod_escola      IS NULL
						 AND ct.ref_cod_biblioteca = b.cod_biblioteca{$condicao}
						 AND p.idpes               = c.ref_idpes
						 AND c.ativo               = '{$int_ativo}'
						 AND ctc.ativo			   = '{$int_ativo}'";

		$filtros = "";
		$whereAnd = " AND ";

		if( is_numeric( $int_cod_cliente ) )
		{
			$filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_login ) )
		{
			$filtros .= "{$whereAnd} c.login = '{$int_login}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_senha ) )
		{
			$filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} c.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} c.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_cliente ) )
		{
			$filtros .= "{$whereAnd} p.nome LIKE '%{$str_nm_cliente}%'";
			$whereAnd = " AND ";
		}
		if ( is_numeric( $int_cod_cliente_tipo ) )
		{
			$filtros .= "{$whereAnd} ct.cod_cliente_tipo = '{$int_cod_cliente_tipo}'";
			$whereAnd = " AND ";
		}
		if ( is_array( $int_cod_biblioteca ) )
		{
			$array_biblioteca = implode(", ", $int_cod_biblioteca);				
			$filtros .= "{$whereAnd} b.cod_biblioteca IN ({$array_biblioteca})";
			$whereAnd = " AND ";
		}
		if ( is_numeric( $int_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} b.cod_biblioteca = '{$int_cod_biblioteca}'";
			$whereAnd = " AND ";
		}
		if ( is_numeric( $int_cod_escola ) )
		{
			$filtros .= "{$whereAnd} e.cod_escola = '{$int_cod_escola}'";
			$whereAnd = " AND ";
		}
		if ( is_numeric( $int_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} i.cod_instituicao = '{$int_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_suspenso ) ) {
			$filtros .= "{$whereAnd} cs.data_liberacao IS NULL";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$resultado = array();

		$sql1 .= $filtros; /*. $this->getOrderby() . $this->getLimite();*/
		$sql2 .= $filtros;

		if(is_numeric($int_cod_escola))
		{
			$this->_total = $db->CampoUnico( "SELECT count(0) FROM ( {$sql1} ) AS SUBQUERY" );
		}
		else 
		{
			$this->_total = $db->CampoUnico( "SELECT count(0) FROM ( ".$sql1." UNION ".$sql2." ) AS SUBQUERY" );
		}
		
		$sql2 .= $this->getOrderby() . $this->getLimite();

		if ( is_numeric( $int_cod_escola ) )
			$sql = $sql1;
		else
			$sql = $sql1." UNION ".$sql2;

		$db->Consulta( $sql );

		while ( $db->ProximoRegistro() )
		{
			$tupla = $db->Tupla();

			$tupla["_total"] = $this->_total;
			$resultado[] = $tupla;
		}
		if( count( $resultado ) )
		{
			return $resultado;
		}
		return false;
	}

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function listaPesquisaCliente( $int_cod_cliente = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $int_ref_idpes = null, $int_login = null, $str_senha = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $str_nm_cliente = null, $int_ref_cod_biblioteca = null )
	{
		$sql = "SELECT {$this->_campos_lista}, ct.ref_cod_biblioteca, p.nome FROM {$this->_tabela} c, {$this->_schema}cliente_tipo_cliente ctc, {$this->_schema}cliente_tipo ct, cadastro.pessoa p";
		$filtros = "";

		$whereAnd = " WHERE c.cod_cliente = ctc.ref_cod_cliente AND ctc.ref_cod_cliente_tipo = ct.cod_cliente_tipo AND c.ref_idpes = p.idpes AND";

		if( is_numeric( $int_cod_cliente ) )
		{
			$filtros .= "{$whereAnd} c.cod_cliente = '{$int_cod_cliente}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} c.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_idpes ) )
		{
			$filtros .= "{$whereAnd} c.ref_idpes = '{$int_ref_idpes}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_login ) )
		{
			$filtros .= "{$whereAnd} c.login = '{$int_login}'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_senha ) )
		{
			$filtros .= "{$whereAnd} c.senha = '{$str_senha}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} c.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} c.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} c.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_nm_cliente ) )
		{
			$filtros .= "{$whereAnd} p.nome LIKE '%{$str_nm_cliente}%'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_biblioteca ) )
		{
			$filtros .= "{$whereAnd} ct.ref_cod_biblioteca = '{$int_ref_cod_biblioteca}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} c, {$this->_schema}cliente_tipo_cliente ctc, {$this->_schema}cliente_tipo ct, cadastro.pessoa p {$filtros}" );

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
		if( is_numeric( $this->cod_cliente ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} c WHERE c.cod_cliente = '{$this->cod_cliente}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		elseif ( is_numeric( $this->ref_idpes ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} c WHERE c.ref_idpes = '{$this->ref_idpes}'" );
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
		if( is_numeric( $this->cod_cliente ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_cliente = '{$this->cod_cliente}'" );
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
		if( is_numeric( $this->cod_cliente ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE cod_cliente = '{$this->cod_cliente}'" );
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
	 * Retorna um array com o codigo do tipo de cliente e o nome do tipo de cliente
	 *
	 * @return array
	 */
	function retornaTipoCliente( $int_cod_cliente, $int_cod_biblioteca )
	{
		if( is_numeric( $int_cod_cliente ) && is_numeric( $int_cod_biblioteca ) )
		{
			$db = new clsBanco();
			$db->Consulta( "SELECT ct.cod_cliente_tipo,
							       ct.nm_tipo
							  FROM pmieducar.cliente	            c,
							       pmieducar.cliente_tipo_cliente ctc,
							       pmieducar.cliente_tipo 	       ct
							 WHERE c.cod_cliente 	     = ctc.ref_cod_cliente
							   AND ct.cod_cliente_tipo   = ctc.ref_cod_cliente_tipo
							   AND c.cod_cliente         = '{$int_cod_cliente}'
							   AND ct.ref_cod_biblioteca = '{$int_cod_biblioteca}'" );
			$db->ProximoRegistro();
			return $db->Tupla();
		}
		return false;
	}

}
?>
