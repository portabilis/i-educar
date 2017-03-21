<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version SVN: $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once 'include/pmieducar/geral.inc.php';

class clsPmieducarMotivoAfastamento {

  public
    $cod_motivo_afastamento = NULL,
    $ref_usuario_exc        = NULL,
    $ref_usuario_cad        = NULL,
    $nm_motivo              = NULL,
    $descricao              = NULL,
    $data_cadastro          = NULL,
    $data_exclusao          = NULL,
    $ativo                  = NULL,
    $ref_cod_instituicao    = NULL;

  /**
   * Armazena o total de resultados obtidos na ultima chamada ao metodo lista
   *
   * @var int
   */
  public $_total;

  /**
   * Nome do schema
   *
   * @var string
   */
  public $_schema;

  /**
   * Nome da tabela
   *
   * @var string
   */
  public $_tabela;

  /**
   * Lista separada por virgula, com os campos que devem ser selecionados na proxima chamado ao metodo lista
   *
   * @var string
   */
  public $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por virgula, padrao para selecao no metodo lista
   *
   * @var string
   */
  public $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo metodo lista
   *
   * @var int
   */
  public $_limite_quantidade;

	/**
	 * Define o valor de offset no retorno dos registros no metodo lista
	 *
	 * @var int
	 */
	public $_limite_offset;

  /**
   * Define o campo padrao para ser usado como padrao de ordenacao no metodo lista
   *
   * @var string
   */
  public $_campo_order_by;



  /**
   * Construtor (PHP 4).
   *
   * @return clsPmieducarMotivoAfastamento
   */
  public function clsPmieducarMotivoAfastamento($cod_motivo_afastamento = NULL,
    $ref_usuario_exc = NULL, $ref_usuario_cad = NULL, $nm_motivo = NULL,
    $descricao = NULL, $data_cadastro = NULL, $data_exclusao = NULL,
    $ativo = NULL, $ref_cod_instituicao = NULL) {

    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = "{$this->_schema}motivo_afastamento";

    $this->_campos_lista = $this->_todos_campos = 'cod_motivo_afastamento, ref_usuario_exc, ref_usuario_cad, nm_motivo, descricao, data_cadastro, data_exclusao, ativo, ref_cod_instituicao';

    if (is_numeric($ref_usuario_cad)) {

      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_cad}'")) {
          $this->ref_usuario_cad = $ref_usuario_cad;
        }
      }

    }

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.usuario WHERE cod_usuario = '{$ref_usuario_exc}'")) {
          $this->ref_usuario_exc = $ref_usuario_exc;
        }
      }
    }

    if (is_numeric($cod_motivo_afastamento)) {
      $this->cod_motivo_afastamento = $cod_motivo_afastamento;
    }

    if (is_string($nm_motivo)) {
      $this->nm_motivo = $nm_motivo;
    }

    if (is_string( $descricao)) {
      $this->descricao = $descricao;
    }

    if (is_string($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (is_string($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

    if (is_numeric($ref_cod_instituicao)) {
      if (class_exists('clsPmieducarInstituicao')) {
        $tmp_obj = new clsPmieducarInstituicao( $ref_cod_instituicao );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.instituicao WHERE cod_instituicao = '{$ref_cod_instituicao}'")) {
          $this->ref_cod_instituicao = $ref_cod_instituicao;
        }
      }
    }

  }



	/**
	 * Cria um novo registro
	 *
	 * @return bool
	 */
	function cadastra()
	{
		if( is_numeric( $this->ref_usuario_cad ) && is_string( $this->nm_motivo ) && is_numeric( $this->ref_cod_instituicao ) )
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
			if( is_string( $this->nm_motivo ) )
			{
				$campos .= "{$gruda}nm_motivo";
				$valores .= "{$gruda}'{$this->nm_motivo}'";
				$gruda = ", ";
			}
			if( is_string( $this->descricao ) )
			{
				$campos .= "{$gruda}descricao";
				$valores .= "{$gruda}'{$this->descricao}'";
				$gruda = ", ";
			}
			$campos .= "{$gruda}data_cadastro";
			$valores .= "{$gruda}NOW()";
			$gruda = ", ";
			$campos .= "{$gruda}ativo";
			$valores .= "{$gruda}'1'";
			$gruda = ", ";
			if( is_numeric( $this->ref_cod_instituicao ) )
			{
				$campos .= "{$gruda}ref_cod_instituicao";
				$valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
				$gruda = ", ";
			}

			$db->Consulta( "INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )" );
			return $db->InsertId( "{$this->_tabela}_cod_motivo_afastamento_seq");
		}
		return false;
	}



  /**
   * Atualiza um registro.
   *
   * @param  bool TRUE para que seja gravado a data atual no campo
   *                 data_exclusao
   * @return bool
   */
  public function edita($setDataExclusao = FALSE) {

    if (is_numeric($this->cod_motivo_afastamento) && is_numeric($this->ref_usuario_exc)
        && is_numeric($this->ref_cod_instituicao)) {

      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_motivo)) {
        $set .= "{$gruda}nm_motivo = '{$this->nm_motivo}'";
        $gruda = ", ";
      }

      if (is_string($this->descricao)) {
        $set .= "{$gruda}descricao = '{$this->descricao}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao)) {
        $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
        $gruda = ", ";
      }

      // Configura a data de exclusão para a data da execução do comando SQL
      if ($setDataExclusao == TRUE) {
        $set .= $gruda . ' data_exclusao = NOW()';
      }

      if ($set) {
        $db->Consulta( "UPDATE {$this->_tabela} SET $set WHERE
          cod_motivo_afastamento = '{$this->cod_motivo_afastamento}'");

        return TRUE;
      }
    }

    return FALSE;
  }

	/**
	 * Retorna uma lista filtrados de acordo com os parametros
	 *
	 * @return array
	 */
	function lista( $int_cod_motivo_afastamento = null, $int_ref_usuario_exc = null, $int_ref_usuario_cad = null, $str_nm_motivo = null, $str_descricao = null, $date_data_cadastro_ini = null, $date_data_cadastro_fim = null, $date_data_exclusao_ini = null, $date_data_exclusao_fim = null, $int_ativo = null, $int_ref_cod_instituicao = null )
	{
		$sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
		$filtros = "";

		$whereAnd = " WHERE ";

		if( is_numeric( $int_cod_motivo_afastamento ) )
		{
			$filtros .= "{$whereAnd} cod_motivo_afastamento = '{$int_cod_motivo_afastamento}'";
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
		if( is_string( $str_nm_motivo ) )
		{
			$filtros .= "{$whereAnd} nm_motivo LIKE '%{$str_nm_motivo}%'";
			$whereAnd = " AND ";
		}
		if( is_string( $str_descricao ) )
		{
			$filtros .= "{$whereAnd} descricao LIKE '%{$str_descricao}%'";
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
		if( is_numeric( $int_ref_cod_instituicao ) )
		{
			$filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
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
		if( is_numeric( $this->cod_motivo_afastamento ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_motivo_afastamento = '{$this->cod_motivo_afastamento}'" );
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
		if( is_numeric( $this->cod_motivo_afastamento ) )
		{

		$db = new clsBanco();
		$db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE cod_motivo_afastamento = '{$this->cod_motivo_afastamento}'" );
		$db->ProximoRegistro();
		return $db->Tupla();
		}
		return false;
	}




  /**
   * Marca um registro como inativo.
   *
   * O registro é marcado como inativo para que outros registros que usam
   * esta tabela em um relacionamento possam recuperar a informação
   * complementar, nesse caso, um motivo de afastamento.
   *
   * @return bool
   */
  public function excluir() {
    if (is_numeric($this->cod_motivo_afastamento) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;

      return $this->edita(TRUE);
    }

    return FALSE;
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