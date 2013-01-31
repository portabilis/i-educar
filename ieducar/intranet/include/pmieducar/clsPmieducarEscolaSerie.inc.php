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
* Criado em 01/08/2006 11:40 pelo gerador automatico de classes
*/

require_once( "include/pmieducar/geral.inc.php" );

class clsPmieducarEscolaSerie
{
	var $ref_cod_escola;
	var $ref_cod_serie;
	var $ref_usuario_exc;
	var $ref_usuario_cad;
	var $hora_inicial;
	var $hora_final;
	var $data_cadastro;
	var $data_exclusao;
	var $ativo;
	var $hora_inicio_intervalo;
	var $hora_fim_intervalo;
  var $bloquear_enturmacao_sem_vagas;
  var $bloquear_cadastro_turma_para_serie_com_vagas;

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
	function clsPmieducarEscolaSerie( $ref_cod_escola = null, $ref_cod_serie = null, $ref_usuario_exc = null, $ref_usuario_cad = null, $hora_inicial = null, $hora_final = null, $data_cadastro = null, $data_exclusao = null, $ativo = null, $hora_inicio_intervalo = null, $hora_fim_intervalo = null, $bloquear_enturmacao_sem_vagas = null, $bloquear_cadastro_turma_para_serie_com_vagas = null )
	{
		$db = new clsBanco();
		$this->_schema = "pmieducar.";
		$this->_tabela = "{$this->_schema}escola_serie";

		$this->_campos_lista = $this->_todos_campos = "es.ref_cod_escola, es.ref_cod_serie, es.ref_usuario_exc, es.ref_usuario_cad, es.hora_inicial, es.hora_final, es.data_cadastro, es.data_exclusao, es.ativo, es.hora_inicio_intervalo, es.hora_fim_intervalo, es.bloquear_enturmacao_sem_vagas, es.bloquear_cadastro_turma_para_serie_com_vagas";

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
		if( is_numeric( $ref_cod_serie ) )
		{
			if( class_exists( "clsPmieducarSerie" ) )
			{
				$tmp_obj = new clsPmieducarSerie( $ref_cod_serie );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_serie = $ref_cod_serie;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.serie WHERE cod_serie = '{$ref_cod_serie}'" ) )
				{
					$this->ref_cod_serie = $ref_cod_serie;
				}
			}
		}
		if( is_numeric( $ref_cod_escola ) )
		{
			if( class_exists( "clsPmieducarEscola" ) )
			{
				$tmp_obj = new clsPmieducarEscola( $ref_cod_escola );
				if( method_exists( $tmp_obj, "existe") )
				{
					if( $tmp_obj->existe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
				else if( method_exists( $tmp_obj, "detalhe") )
				{
					if( $tmp_obj->detalhe() )
					{
						$this->ref_cod_escola = $ref_cod_escola;
					}
				}
			}
			else
			{
				if( $db->CampoUnico( "SELECT 1 FROM pmieducar.escola WHERE cod_escola = '{$ref_cod_escola}'" ) )
				{
					$this->ref_cod_escola = $ref_cod_escola;
				}
			}
		}


		if( ( $hora_inicial ) )
		{
			$this->hora_inicial = $hora_inicial;
		}
		if( ( $hora_final ) )
		{
			$this->hora_final = $hora_final;
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
		if( ( $hora_inicio_intervalo ) )
		{
			$this->hora_inicio_intervalo = $hora_inicio_intervalo;
		}
		if( ( $hora_fim_intervalo ) )
		{
			$this->hora_fim_intervalo = $hora_fim_intervalo;
		}

    $this->bloquear_enturmacao_sem_vagas = $bloquear_enturmacao_sem_vagas;
    $this->bloquear_cadastro_turma_para_serie_com_vagas = $bloquear_cadastro_turma_para_serie_com_vagas;
	}

	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_usuario_cad ) && ( $this->hora_inicial ) && ( $this->hora_final ) && ( $this->hora_inicio_intervalo ) && ( $this->hora_fim_intervalo ) )
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
			if( is_numeric( $this->ref_cod_serie ) )
			{
				$campos .= "{$gruda}ref_cod_serie";
				$valores .= "{$gruda}'{$this->ref_cod_serie}'";
				$gruda = ", ";
			}
			if( is_numeric( $this->ref_usuario_cad ) )
			{
				$campos .= "{$gruda}ref_usuario_cad";
				$valores .= "{$gruda}'{$this->ref_usuario_cad}'";
				$gruda = ", ";
			}
			if( ( $this->hora_inicial ) )
			{
				$campos .= "{$gruda}hora_inicial";
				$valores .= "{$gruda}'{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$campos .= "{$gruda}hora_final";
				$valores .= "{$gruda}'{$this->hora_final}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( ( $this->hora_inicio_intervalo ) )
			{
				$campos .= "{$gruda}hora_inicio_intervalo";
				$valores .= "{$gruda}'{$this->hora_inicio_intervalo}'";
				$gruda = ", ";
			}
			if( ( $this->hora_fim_intervalo ) )
			{
				$campos .= "{$gruda}hora_fim_intervalo";
				$valores .= "{$gruda}'{$this->hora_fim_intervalo}'";
				$gruda = ", ";
			}

			if(is_numeric($this->bloquear_enturmacao_sem_vagas)) {
				$campos .= "{$gruda}bloquear_enturmacao_sem_vagas";
				$valores .= "{$gruda}'{$this->bloquear_enturmacao_sem_vagas}'";
				$gruda = ", ";
			}

			if(is_numeric($this->bloquear_cadastro_turma_para_serie_com_vagas)) {
				$campos .= "{$gruda}bloquear_cadastro_turma_para_serie_com_vagas";
				$valores .= "{$gruda}'{$this->bloquear_cadastro_turma_para_serie_com_vagas}'";
				$gruda = ", ";
			}

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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_usuario_exc ) )
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
			if( ( $this->hora_inicial ) )
			{
				$set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
				$gruda = ", ";
			}
			if( ( $this->hora_final ) )
			{
				$set .= "{$gruda}hora_final = '{$this->hora_final}'";
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
			if( ( $this->hora_inicio_intervalo ) )
			{
				$set .= "{$gruda}hora_inicio_intervalo = '{$this->hora_inicio_intervalo}'";
				$gruda = ", ";
			}
			if( ( $this->hora_fim_intervalo ) )
			{
				$set .= "{$gruda}hora_fim_intervalo = '{$this->hora_fim_intervalo}'";
				$gruda = ", ";
			}

			if(is_numeric( $this->bloquear_enturmacao_sem_vagas)) {
				$set .= "{$gruda}bloquear_enturmacao_sem_vagas = '{$this->bloquear_enturmacao_sem_vagas}'";
				$gruda = ", ";
			}

			if(is_numeric( $this->bloquear_cadastro_turma_para_serie_com_vagas)) {
				$set .= "{$gruda}bloquear_cadastro_turma_para_serie_com_vagas = '{$this->bloquear_cadastro_turma_para_serie_com_vagas}'";
				$gruda = ", ";
			}

			if( $set )
			{
				$db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
	function lista( $int_ref_cod_escola = null, $int_ref_cod_serie = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $time_hora_inicial_ini = null, $time_hora_inicial_fim = null, $time_hora_final_ini = null, $time_hora_final_fim = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $time_hora_inicio_intervalo_ini = null, $time_hora_inicio_intervalo_fim = null, $time_hora_fim_intervalo_ini = null, $time_hora_fim_intervalo_fim = null, $int_ref_cod_instituicao = null, $int_ref_cod_curso = null, $bloquear_enturmacao_sem_vagas = null, $bloquear_cadastro_turma_para_serie_com_vagas = null )
	{
		$sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, s.ref_cod_curso, s.nm_serie FROM {$this->_tabela} es, {$this->_schema}serie s, {$this->_schema}curso c";

		$whereAnd = " AND ";
		$filtros = " WHERE es.ref_cod_serie = s.cod_serie AND s.ref_cod_curso = c.cod_curso AND s.ativo = 1 ";

		if( is_numeric( $int_ref_cod_escola ) )
		{
			$filtros .= "{$whereAnd} es.ref_cod_escola = '{$int_ref_cod_escola}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_serie ) )
		{
			$filtros .= "{$whereAnd} es.ref_cod_serie = '{$int_ref_cod_serie}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_exc ) )
		{
			$filtros .= "{$whereAnd} es.ref_usuario_exc = '{$int_ref_usuario_exc}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_usuario_cad ) )
		{
			$filtros .= "{$whereAnd} es.ref_usuario_cad = '{$int_ref_usuario_cad}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_ini ) )
		{
			$filtros .= "{$whereAnd} es.hora_inicial >= '{$time_hora_inicial_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicial_fim ) )
		{
			$filtros .= "{$whereAnd} es.hora_inicial <= '{$time_hora_inicial_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_ini ) )
		{
			$filtros .= "{$whereAnd} es.hora_final >= '{$time_hora_final_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_final_fim ) )
		{
			$filtros .= "{$whereAnd} es.hora_final <= '{$time_hora_final_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_ini ) )
		{
			$filtros .= "{$whereAnd} es.data_cadastro >= '{$date_data_cadastro_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_cadastro_fim ) )
		{
			$filtros .= "{$whereAnd} es.data_cadastro <= '{$date_data_cadastro_fim}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_ini ) )
		{
			$filtros .= "{$whereAnd} es.data_exclusao >= '{$date_data_exclusao_ini}'";
			$whereAnd = " AND ";
		}
		if( is_string( $date_data_exclusao_fim ) )
		{
			$filtros .= "{$whereAnd} es.data_exclusao <= '{$date_data_exclusao_fim}'";
			$whereAnd = " AND ";
		}
		if( is_null( $int_ativo ) || $int_ativo )
		{
			$filtros .= "{$whereAnd} es.ativo = '1'";
			$whereAnd = " AND ";
		}
		else
		{
			$filtros .= "{$whereAnd} es.ativo = '0'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} es.hora_inicio_intervalo >= '{$time_hora_inicio_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_inicio_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} es.hora_inicio_intervalo <= '{$time_hora_inicio_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_ini ) )
		{
			$filtros .= "{$whereAnd} es.hora_fim_intervalo >= '{$time_hora_fim_intervalo_ini}'";
			$whereAnd = " AND ";
		}
		if( ( $time_hora_fim_intervalo_fim ) )
		{
			$filtros .= "{$whereAnd} es.hora_fim_intervalo <= '{$time_hora_fim_intervalo_fim}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
			$whereAnd = " AND ";
		}
		if( is_numeric( $int_ref_cod_curso ) )
		{
			$filtros .= "{$whereAnd} s.ref_cod_curso = '{$int_ref_cod_curso}'";
			$whereAnd = " AND ";
		}

		if(is_numeric($bloquear_enturmacao_sem_vagas)) {
			$filtros .= "{$whereAnd} s.bloquear_enturmacao_sem_vagas = '{$bloquear_enturmacao_sem_vagas}'";
			$whereAnd = " AND ";
		}

		if(is_numeric($bloquear_cadastro_turma_para_serie_com_vagas)) {
			$filtros .= "{$whereAnd} s.bloquear_cadastro_turma_para_serie_com_vagas = '{$bloquear_cadastro_turma_para_serie_com_vagas}'";
			$whereAnd = " AND ";
		}

		$db = new clsBanco();
		$countCampos = count( explode( ",", $this->_campos_lista ) );
		$resultado = array();

		$sql .= $filtros . $this->getOrderby() . $this->getLimite();

		$this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} es, {$this->_schema}serie s, {$this->_schema}curso c {$filtros}" );

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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} es WHERE es.ref_cod_escola = '{$this->ref_cod_escola}' AND es.ref_cod_serie = '{$this->ref_cod_serie}'" );
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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
		if( is_numeric( $this->ref_cod_escola ) && is_numeric( $this->ref_cod_serie ) && is_numeric( $this->ref_usuario_exc ) )
		{

		/*
			delete
		$db = new clsBanco();
		$db->Consulta( "DELETE FROM {$this->_tabela} WHERE ref_cod_escola = '{$this->ref_cod_escola}' AND ref_cod_serie = '{$this->ref_cod_serie}'" );
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
