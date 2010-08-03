<?php

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Arquivo disponível desde a versão 1.0.0
 * @version   $Id$
 */

require_once 'include/pmieducar/geral.inc.php';

/**
 * clsPmieducarServidorAlocacao class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarServidorAlocacao
{
  var $cod_servidor_alocacao;
  var $ref_ref_cod_instituicao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_escola;
  var $ref_cod_servidor;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $carga_horaria;
  var $periodo;

  /**
   * Carga horária máxima para um período de alocação (em horas).
   * @var float
   */
  static $cargaHorariaMax = 36.0;

  /**
   * Armazena o total de resultados obtidos na última chamada ao método lista().
   * @var int
   */
  var $_total;

  /**
   * Nome do schema.
   * @var string
   */
  var $_schema;

  /**
   * Nome da tabela.
   * @var string
   */
  var $_tabela;

  /**
   * Lista separada por vírgula, com os campos que devem ser selecionados na
   * próxima chamado ao método lista().
   * @var string
   */
  var $_campos_lista;

  /**
   * Lista com todos os campos da tabela separados por vírgula, padrão para
   * seleção no método lista.
   * @var string
   */
  var $_todos_campos;

  /**
   * Valor que define a quantidade de registros a ser retornada pelo método lista().
   * @var int
   */
  var $_limite_quantidade;

  /**
   * Define o valor de offset no retorno dos registros no método lista().
   * @var int
   */
  var $_limite_offset;

  /**
   * Define o campo para ser usado como padrão de ordenação no método lista().
   * @var string
   */
  var $_campo_order_by;

  /**
   * Define o campo para ser usado como padrão de agrupamento no método lista().
   * @var string
   */
  var $_campo_group_by;

  /**
   * Construtor.
   */
  function clsPmieducarServidorAlocacao($cod_servidor_alocacao = NULL,
    $ref_ref_cod_instituicao = NULL, $ref_usuario_exc = NULL, $ref_usuario_cad = NULL,
    $ref_cod_escola = NULL, $ref_cod_servidor = NULL, $data_cadastro = NULL,
    $data_exclusao = NULL, $ativo = NULL, $carga_horaria = NULL, $periodo = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'servidor_alocacao';

    $this->_campos_lista = $this->_todos_campos = 'cod_servidor_alocacao, ref_ref_cod_instituicao, ref_usuario_exc, ref_usuario_cad, ref_cod_escola, ref_cod_servidor, data_cadastro, data_exclusao, ativo, carga_horaria, periodo';

    if (is_numeric($ref_usuario_cad)) {
      $usuario = new clsPmieducarUsuario($ref_usuario_cad);
      if ($usuario->existe()) {
        $this->ref_usuario_cad = $ref_usuario_cad;
      }
    }

    if (is_numeric($ref_usuario_exc)) {
      $usuario = new clsPmieducarUsuario($ref_usuario_exc);
      if ($usuario->existe()) {
        $this->ref_usuario_exc = $ref_usuario_exc;
      }
    }

    if (is_numeric($ref_cod_escola)) {
      $escola = new clsPmieducarEscola($ref_cod_escola);
      if ($escola->existe()) {
        $this->ref_cod_escola = $ref_cod_escola;
      }
    }

    if (is_numeric($ref_cod_servidor) && is_numeric($ref_ref_cod_instituicao)) {
      $servidor = new clsPmieducarServidor($ref_cod_servidor, NULL, NULL, NULL,
        NULL, NULL, NULL, $ref_ref_cod_instituicao);

      if ($servidor->existe()) {
        $this->ref_cod_servidor = $ref_cod_servidor;
        $this->ref_ref_cod_instituicao = $ref_ref_cod_instituicao;
      }
    }

    if (is_numeric($cod_servidor_alocacao)) {
      $this->cod_servidor_alocacao = $cod_servidor_alocacao;
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

    // Valida a carga horária
    if (is_string($carga_horaria)) {
      $datetime = explode(':', $carga_horaria);
      $minutos  = (((int) $datetime[0]) * 60) + (int) $datetime[1];

      if ((self::$cargaHorariaMax * 60) >= $minutos) {
        $this->carga_horaria = $carga_horaria;
      }
    }

    if (is_numeric($periodo)) {
      $this->periodo = $periodo;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_ref_cod_instituicao) && is_numeric($this->ref_usuario_cad) &&
      is_numeric($this->ref_cod_escola) && is_numeric($this->ref_cod_servidor) &&
      is_string($this->carga_horaria) && ($this->periodo)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $campos  .= "{$gruda}ref_ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_ref_cod_instituicao}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos  .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_escola)) {
        $campos  .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_cod_escola}'";
        $gruda    = ', ';
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $campos  .= "{$gruda}ref_cod_servidor";
        $valores .= "{$gruda}'{$this->ref_cod_servidor}'";
        $gruda    = ', ';
      }

      if (is_string($this->carga_horaria)) {
        $campos  .= "{$gruda}carga_horaria";
        $valores .= "{$gruda}'{$this->carga_horaria}'";
        $gruda    = ', ';
      }

      if (($this->periodo)) {
        $campos  .= "{$gruda}periodo";
        $valores .= "{$gruda}'{$this->periodo}'";
        $gruda    = ', ';
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda    = ", ";

      $campos  .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda    = ", ";

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $db->InsertId("{$this->_tabela}_cod_servidor_alocacao_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_servidor_alocacao) && is_numeric($this->ref_usuario_exc)) {
      $db = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_ref_cod_instituicao)) {
        $set .= "{$gruda}ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set  .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set  .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_cod_escola)) {
        $set  .= "{$gruda}ref_cod_escola = '{$this->ref_cod_escola}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_cod_servidor)) {
        $set  .= "{$gruda}ref_cod_servidor = '{$this->ref_cod_servidor}'";
        $gruda = ', ';
      }

      if (is_numeric($this->carga_horaria)) {
        $set  .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
        $gruda = ', ';
      }

      if (($this->periodo)) {
        $set  .= "{$gruda}periodo = '{$this->periodo}'";
        $gruda = ', ';
      }

      if (is_string($this->data_cadastro)) {
        $set  .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ', ';
      }

      $set  .= "{$gruda}data_exclusao = NOW()";
      $gruda = ', ';

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_servidor_alocacao = NULL, $int_ref_ref_cod_instituicao = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL, $int_ref_cod_escola = NULL,
    $int_ref_cod_servidor = NULL, $date_data_cadastro_ini = NULL,
    $date_data_cadastro_fim = NULL, $date_data_exclusao_ini = NULL,
    $date_data_exclusao_fim = NULL, $int_ativo = NULL, $int_carga_horaria = NULL,
    $int_periodo = NULL, $bool_busca_nome = FALSE, $boo_professor = NULL)
  {
    $filtros  = '';
    $whereAnd = ' WHERE ';

    if (is_bool($bool_busca_nome) && $bool_busca_nome == TRUE) {
      $join     = ', cadastro.pessoa p ';
      $filtros .= $whereAnd.' sa.ref_cod_servidor = p.idpes';
      $whereAnd = ' AND ';
      $this->_campos_lista .= ',p.nome';
    }

    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} sa{$join}";

    if (is_numeric($int_cod_servidor_alocacao)) {
      $filtros .= "{$whereAnd} sa.cod_servidor_alocacao = '{$int_cod_servidor_alocacao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} sa.ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} sa.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }
    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} sa.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} sa.ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_servidor)) {
      $filtros .= "{$whereAnd} sa.ref_cod_servidor = '{$int_ref_cod_servidor}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_carga_horaria)) {
      $filtros .= "{$whereAnd} sa.carga_horaria = '{$int_carga_horaria}'";
      $whereAnd = ' AND ';
    }

    if (($int_periodo)) {
      $filtros .= "{$whereAnd} sa.periodo = '{$int_periodo}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} sa.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} sa.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} sa.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} sa.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} sa.ativo = '1'";
      $whereAnd = ' AND ';
    }
    else {
      $filtros .= "{$whereAnd} sa.ativo = '0'";
      $whereAnd = ' AND ';
    }

    if (is_bool($boo_professor)) {
      $not = $boo_professor? "=" : "!=";
      $filtros .= "{$whereAnd} EXISTS(SELECT 1 FROM pmieducar.servidor_funcao,pmieducar.funcao WHERE ref_cod_funcao = cod_funcao AND ref_cod_servidor = sa.ref_cod_servidor AND sa.ref_ref_cod_instituicao = ref_ref_cod_instituicao AND professor $not 1)";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado   = array();

    $sql .= $filtros . $this->getGroupBy() . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} sa {$join} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[]     = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla       = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }


  function listaEscolas($int_ref_ref_cod_instituicao = NULL)
  {
    if (is_numeric($int_ref_ref_cod_instituicao)) {
      $sql = "SELECT DISTINCT ref_cod_escola FROM {$this->_tabela} WHERE ref_ref_cod_instituicao = '{$int_ref_ref_cod_instituicao}' AND ativo = '1'";

      $db = new clsBanco();
      $resultado = array();

      $db->Consulta($sql);

      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla;
      }

      if (count($resultado)) {
        return $resultado;
      }

      return FALSE;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_servidor_alocacao)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existe()
  {
    if (is_numeric($this->cod_servidor_alocacao)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_servidor_alocacao = '{$this->cod_servidor_alocacao}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_servidor_alocacao) && is_numeric($this->ref_usuario_exc)) {
      $this->ativo = 0;
      return $this->edita();
    }

    return FALSE;
  }

  /**
   * Exclui um registro baseado no período da alocação.
   * @return bool
   */
  function excluir_horario()
  {
    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao) &&
      is_numeric($this->ref_cod_escola) && is_numeric($this->periodo)
    ) {
      $db = new clsBanco();
      $db->Consulta("DELETE FROM {$this->_tabela} WHERE ref_cod_servidor = '{$this->ref_cod_servidor}' AND ref_ref_cod_instituicao = '{$this->ref_ref_cod_instituicao}' AND ref_cod_escola = '{$this->ref_cod_escola}' AND periodo = '$this->periodo'");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Substitui a alocação entre servidores
   *
   * Substitui a alocação entre servidores, atualizando a tabela
   * pmieducar.servidor_alocacao. A única atualização na tabela ocorre no
   * identificador do servidor, o campo ref_cod_servidor. Para usar este
   * método, um objeto desta classe precisa estar instanciado com os atributos
   * do servidor a ser substituido.
   *
   * @param  int  $int_ref_cod_servidor_substituto  Código do servidor que substituirá o atual
   * @return bool TRUE em caso de sucesso, FALSE caso contrário
   */
  function substituir_servidor($int_ref_cod_servidor_substituto)
  {
    if (is_numeric($int_ref_cod_servidor_substituto) &&
        is_numeric($this->ref_ref_cod_instituicao)
    ) {
      $servidor = new clsPmieducarServidor($int_ref_cod_servidor_substituto,
        NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_ref_cod_instituicao);

      if (!$servidor->existe()) {
        return FALSE;
      }
    }

    if (is_numeric($this->ref_cod_servidor) && is_numeric($this->ref_ref_cod_instituicao) &&
        is_numeric($this->ref_cod_escola) && is_numeric($this->periodo) &&
        is_string($this->carga_horaria)
    ) {
      $sql  = "UPDATE %s SET ref_cod_servidor='%d' WHERE ref_cod_servidor = '%d' ";
      $sql .= "AND ref_ref_cod_instituicao = '%d' AND ref_cod_escola = '%d' AND ";
      $sql .= "carga_horaria = '%s' AND periodo = '%d'";

      $sql = sprintf($sql, $this->_tabela, $int_ref_cod_servidor_substituto,
        $this->ref_cod_servidor, $this->ref_ref_cod_instituicao, $this->ref_cod_escola,
        $this->carga_horaria, $this->periodo);

      $db = new clsBanco();
      $db->Consulta($sql);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Define quais campos da tabela serão selecionados no método Lista().
   */
  function setCamposLista($str_campos)
  {
    $this->_campos_lista = $str_campos;
  }

  /**
   * Define que o método Lista() deverpa retornar todos os campos da tabela.
   */
  function resetCamposLista()
  {
    $this->_campos_lista = $this->_todos_campos;
  }

  /**
   * Define limites de retorno para o método Lista().
   */
  function setLimite($intLimiteQtd, $intLimiteOffset = NULL)
  {
    $this->_limite_quantidade = $intLimiteQtd;
    $this->_limite_offset = $intLimiteOffset;
  }

  /**
   * Retorna a string com o trecho da query responsável pelo limite de
   * registros retornados/afetados.
   *
   * @return string
   */
  function getLimite()
  {
    if (is_numeric($this->_limite_quantidade)) {
      $retorno = " LIMIT {$this->_limite_quantidade}";
      if (is_numeric($this->_limite_offset)) {
        $retorno .= " OFFSET {$this->_limite_offset} ";
      }
      return $retorno;
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado como ordenação no método Lista().
   */
  function setOrderby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_order_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pela Ordenação dos
   * registros.
   *
   * @return string
   */
  function getOrderby()
  {
    if (is_string($this->_campo_order_by)) {
      return " ORDER BY {$this->_campo_order_by} ";
    }
    return '';
  }

  /**
   * Define o campo para ser utilizado na agrupação no método Lista().
   */
  function setGroupby($strNomeCampo)
  {
    if (is_string($strNomeCampo) && $strNomeCampo ) {
      $this->_campo_group_by = $strNomeCampo;
    }
  }

  /**
   * Retorna a string com o trecho da query responsável pelo Agrupamento dos
   * registros.
   *
   * @return string
   */
  function getGroupBy()
  {
    if (is_string($this->_campo_group_by)) {
      return " GROUP BY {$this->_campo_group_by} ";
    }
    return '';
  }
}