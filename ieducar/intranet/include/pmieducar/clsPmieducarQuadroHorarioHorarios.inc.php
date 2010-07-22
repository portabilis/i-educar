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
 * clsPmieducarQuadroHorarioHorarios class
 *
 * @author      Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since       Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarQuadroHorarioHorarios
{
  var $ref_cod_quadro_horario;
  var $ref_ref_cod_serie;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_disciplina;
  var $sequencial;
  var $ref_cod_instituicao_substituto;
  var $ref_cod_instituicao_servidor;
  var $ref_servidor_substituto;
  var $ref_servidor;
  var $hora_inicial;
  var $hora_final;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $dia_semana;

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
   * Construtor.
   */
  function clsPmieducarQuadroHorarioHorarios($ref_cod_quadro_horario = NULL,
    $ref_ref_cod_serie = NULL, $ref_ref_cod_escola = NULL,
    $ref_ref_cod_disciplina = NULL, $sequencial = NULL,
    $ref_cod_instituicao_substituto = NULL, $ref_cod_instituicao_servidor = NULL,
    $ref_servidor_substituto = NULL, $ref_servidor = NULL, $hora_inicial = NULL,
    $hora_final = NULL, $data_cadastro = NULL, $data_exclusao = NULL,
    $ativo = NULL, $dia_semana = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'quadro_horario_horarios';

    $this->_campos_lista = $this->_todos_campos = 'ref_cod_quadro_horario, ref_cod_serie, ref_cod_escola, ref_cod_disciplina, sequencial, ref_cod_instituicao_substituto, ref_cod_instituicao_servidor, ref_servidor_substituto, ref_servidor, hora_inicial, hora_final, data_cadastro, data_exclusao, ativo, dia_semana';

    if (is_numeric($ref_servidor_substituto) && is_numeric($ref_cod_instituicao_substituto)) {
      if (class_exists('clsPmieducarServidor')) {
        $tmp_obj = new clsPmieducarServidor($ref_servidor_substituto, NULL,
          NULL, NULL, NULL, NULL, NULL, $ref_cod_instituicao_substituto);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_servidor_substituto = $ref_servidor_substituto;
            $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_servidor_substituto = $ref_servidor_substituto;
            $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
          }
        }
      }
      elseif ($db->CampoUnico("SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor_substituto}' AND ref_cod_instituicao = '{$ref_cod_instituicao_substituto}'")) {
          $this->ref_servidor_substituto = $ref_servidor_substituto;
          $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
      }
    }

    if (is_numeric($ref_servidor) && is_numeric($ref_cod_instituicao_servidor)) {
      if (class_exists('clsPmieducarServidor')) {
        $tmp_obj = new clsPmieducarServidor($ref_servidor, NULL, NULL, NULL,
          NULL, NULL, NULL, $ref_cod_instituicao_servidor);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_servidor = $ref_servidor;
            $this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_servidor = $ref_servidor;
            $this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
          }
        }
      }
      elseif ($db->CampoUnico("SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor}' AND ref_cod_instituicao = '{$ref_cod_instituicao_servidor}'")) {
          $this->ref_servidor = $ref_servidor;
          $this->ref_cod_instituicao_servidor = $ref_cod_instituicao_servidor;
      }
    }

    if (is_numeric($ref_servidor_substituto) && is_numeric($ref_cod_instituicao_substituto)) {
      if (class_exists('clsPmieducarServidor')) {
        $tmp_obj = new clsPmieducarServidor($ref_servidor_substituto, NULL, NULL,
          NULL, NULL, NULL, NULL, NULL, $ref_cod_instituicao_substituto);

          if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_servidor_substituto = $ref_servidor_substituto;
            $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_servidor_substituto = $ref_servidor_substituto;
            $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
          }
        }
      }
      elseif ($db->CampoUnico("SELECT 1 FROM pmieducar.servidor WHERE cod_servidor = '{$ref_servidor_substituto}' AND ref_cod_instituicao = '{$ref_cod_instituicao_substituto}'")) {
          $this->ref_servidor_substituto = $ref_servidor_substituto;
          $this->ref_cod_instituicao_substituto = $ref_cod_instituicao_substituto;
      }
    }

    if (is_numeric($ref_ref_cod_disciplina) && is_numeric($ref_ref_cod_serie)) {
      require_once 'ComponenteCurricular/Model/AnoEscolarDataMapper.php';
      $anoEscolarMapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();
      $componenteAnos = $anoEscolarMapper->findAll(array(), array(
        'componenteCurricular' => $ref_ref_cod_disciplina,
        'anoEscolar'           => $ref_ref_cod_serie
      ));

      if (1 == count($componenteAnos)) {
        $this->ref_ref_cod_disciplina = $ref_ref_cod_disciplina;
        $this->ref_ref_cod_serie      = $ref_ref_cod_serie;
        $this->ref_ref_cod_escola     = $ref_ref_cod_escola;
      }
    }

    if (is_numeric($ref_cod_quadro_horario)) {
      if (class_exists('clsPmieducarQuadroHorario')) {
        $tmp_obj = new clsPmieducarQuadroHorario($ref_cod_quadro_horario);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
          }
        }
      }
      elseif ($db->CampoUnico("SELECT 1 FROM pmieducar.quadro_horario WHERE cod_quadro_horario = '{$ref_cod_quadro_horario}'")) {
          $this->ref_cod_quadro_horario = $ref_cod_quadro_horario;
      }
    }

    if (is_numeric($sequencial)) {
      $this->sequencial = $sequencial;
    }

    if (($hora_inicial)) {
      $this->hora_inicial = $hora_inicial;
    }

    if (($hora_final)) {
      $this->hora_final = $hora_final;
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

    if (is_numeric($dia_semana)) {
      $this->dia_semana = $dia_semana;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if(is_numeric($this->ref_cod_quadro_horario) &&
      is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_disciplina) &&
      is_numeric($this->ref_cod_instituicao_servidor) &&
      is_numeric($this->ref_servidor) && ($this->hora_inicial) && ($this->hora_final) &&
      is_numeric($this->dia_semana)
    ) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_cod_quadro_horario)) {
        $campos .= "{$gruda}ref_cod_quadro_horario";
        $valores .= "{$gruda}'{$this->ref_cod_quadro_horario}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_serie)) {
        $campos .= "{$gruda}ref_cod_serie";
        $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_escola)) {
        $campos .= "{$gruda}ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_disciplina)) {
        $campos .= "{$gruda}ref_cod_disciplina";
        $valores .= "{$gruda}'{$this->ref_ref_cod_disciplina}'";
        $gruda = ", ";
      }

      $this->sequencial = $db->CampoUnico("
        SELECT
          (COALESCE(MAX(sequencial), 0) + 1) AS sequencial
        FROM
          pmieducar.quadro_horario_horarios
        WHERE
          ref_cod_quadro_horario = {$this->ref_cod_quadro_horario}
                             AND ref_cod_serie      = {$this->ref_ref_cod_serie}
          AND ref_cod_escola = {$this->ref_ref_cod_escola}");

      $campos .= "{$gruda}sequencial";
      $valores .= "{$gruda}'{$this->sequencial}'";
      $gruda = ", ";

      if (is_numeric($this->ref_cod_instituicao_substituto)) {
        $campos .= "{$gruda}ref_cod_instituicao_substituto";
        $valores .= "{$gruda}'{$this->ref_cod_instituicao_substituto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao_servidor)) {
        $campos .= "{$gruda}ref_cod_instituicao_servidor";
        $valores .= "{$gruda}'{$this->ref_cod_instituicao_servidor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_servidor_substituto)) {
        $campos .= "{$gruda}ref_servidor_substituto";
        $valores .= "{$gruda}'{$this->ref_servidor_substituto}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_servidor)) {
        $campos .= "{$gruda}ref_servidor";
        $valores .= "{$gruda}'{$this->ref_servidor}'";
        $gruda = ", ";
      }

      if (($this->hora_inicial)) {
        $campos .= "{$gruda}hora_inicial";
        $valores .= "{$gruda}'{$this->hora_inicial}'";
        $gruda = ", ";
      }

      if (($this->hora_final)) {
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

      if (is_numeric($this->dia_semana)) {
        $campos .= "{$gruda}dia_semana";
        $valores .= "{$gruda}'{$this->dia_semana}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->ref_cod_quadro_horario) &&
      is_numeric($this->ref_ref_cod_serie) &&
      is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_disciplina) &&
      is_numeric($this->sequencial)
    ) {
      $db = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_cod_instituicao_substituto)) {
        $set .= "{$gruda}ref_cod_instituicao_substituto = '{$this->ref_cod_instituicao_substituto}'";
        $gruda = ", ";
      }
      else {
        $set .= "{$gruda}ref_cod_instituicao_substituto = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao_servidor)) {
        $set .= "{$gruda}ref_cod_instituicao_servidor = '{$this->ref_cod_instituicao_servidor}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_servidor_substituto)) {
        $set .= "{$gruda}ref_servidor_substituto = '{$this->ref_servidor_substituto}'";
        $gruda = ", ";
      }
      else {
        $set .= "{$gruda}ref_servidor_substituto = NULL";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_servidor)) {
        $set .= "{$gruda}ref_servidor = '{$this->ref_servidor}'";
        $gruda = ", ";
      }

      if (($this->hora_inicial)) {
        $set .= "{$gruda}hora_inicial = '{$this->hora_inicial}'";
        $gruda = ", ";
      }

      if (($this->hora_final)) {
        $set .= "{$gruda}hora_final = '{$this->hora_final}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->dia_semana)) {
        $set .= "{$gruda}dia_semana = '{$this->dia_semana}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_ref_cod_quadro_horario = NULL,
    $int_ref_ref_cod_serie = NULL, $int_ref_ref_cod_escola = NULL,
    $int_ref_ref_cod_disciplina = NULL, $int_ref_ref_cod_turma = NULL,
    $int_sequencial = NULL, $int_ref_cod_instituicao_substituto = NULL,
    $int_ref_cod_instituicao_servidor = NULL, $int_ref_servidor_substituto = NULL,
    $int_ref_servidor = NULL, $time_hora_inicial_ini = NULL,
    $time_hora_inicial_fim = NULL, $time_hora_final_ini = NULL,
    $time_hora_final_fim = NULL, $date_data_cadastro_ini = NULL,
    $date_data_cadastro_fim = NULL, $date_data_exclusao_ini = NULL,
    $date_data_exclusao_fim = NULL, $int_ativo = NULL, $int_dia_semana = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} qhh";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_ref_cod_quadro_horario)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_quadro_horario = '{$int_ref_cod_quadro_horario}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_serie)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_serie = '{$int_ref_ref_cod_serie}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_escola)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_escola = '{$int_ref_ref_cod_escola}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_ref_cod_disciplina)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_disciplina = '{$int_ref_ref_cod_disciplina}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_sequencial)) {
      $filtros .= "{$whereAnd} qhh.sequencial = '{$int_sequencial}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_instituicao_substituto)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_instituicao_substituto = '{$int_ref_cod_instituicao_substituto}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_instituicao_servidor)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_servidor_substituto)) {
      $filtros .= "{$whereAnd} qhh.ref_servidor_substituto = '{$int_ref_servidor_substituto}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_servidor)) {
      $filtros .= "{$whereAnd} qhh.ref_servidor = '{$int_ref_servidor}'";
      $whereAnd = ' AND ';
    }

    if (($time_hora_inicial_ini)) {
      $filtros .= "{$whereAnd} qhh.hora_inicial >= '{$time_hora_inicial_ini}'";
      $whereAnd = ' AND ';
    }

    if (($time_hora_inicial_fim)) {
      $filtros .= "{$whereAnd} qhh.hora_inicial <= '{$time_hora_inicial_fim}'";
      $whereAnd = ' AND ';
    }

    if (($time_hora_final_ini)) {
      $filtros .= "{$whereAnd} qhh.hora_final >= '{$time_hora_final_ini}'";
      $whereAnd = ' AND ';
    }

    if (($time_hora_final_fim)) {
      $filtros .= "{$whereAnd} qhh.hora_final <= '{$time_hora_final_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} qhh.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} qhh.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} qhh.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} qhh.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} qhh.ativo = '1'";
      $whereAnd = ' AND ';
    }
    else {
      $filtros .= "{$whereAnd} qhh.ativo = '0'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_dia_semana)) {
      $filtros .= "{$whereAnd} qhh.dia_semana = '{$int_dia_semana}'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} qhh {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe($ref_cod_escola = NULL)
    {
    if (is_numeric($this->ref_cod_quadro_horario) &&
      is_numeric($this->ref_ref_cod_serie) &&
      is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_disciplina) &&
      is_numeric($this->sequencial)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($ref_cod_escola) && is_numeric($this->ref_cod_instituicao_servidor) &&
      is_numeric($this->ref_servidor) && is_string($this->hora_inicial) &&
      is_string($this->hora_final) && is_numeric($this->dia_semana)
    ) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_cod_escola = {$ref_cod_escola} AND ref_cod_instituicao_servidor = {$this->ref_cod_instituicao_servidor} AND ref_servidor = {$this->ref_servidor} AND hora_inicial = '{$this->hora_inicial}' AND hora_final = '{$this->hora_final}' AND ativo = 1 AND dia_semana = {$this->dia_semana}");
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
    if (is_numeric($this->ref_cod_quadro_horario) &&
      is_numeric($this->ref_ref_cod_serie) && is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_disciplina) && is_numeric($this->sequencial)
    ) {
    $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}' AND ref_cod_serie = '{$this->ref_ref_cod_serie}' AND ref_cod_escola = '{$this->ref_ref_cod_escola}' AND ref_cod_disciplina = '{$this->ref_ref_cod_disciplina}' AND sequencial = '{$this->sequencial}'");
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
    if (is_numeric($this->ref_cod_quadro_horario) &&
      is_numeric($this->ref_ref_cod_serie) &&
      is_numeric($this->ref_ref_cod_escola) &&
      is_numeric($this->ref_ref_cod_disciplina) &&
      is_numeric($this->sequencial)
    ) {
    $this->ativo = 0;
      return $this->edita();
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
   * Substitui a alocação entre servidores no quadro de horários
   *
   * Substitui a alocação entre servidores, atualizando a tabela
   * pmieducar.servidor_quadro_horario_horarios. O servidor nesse caso é
   * de alguma função do tipo professor. Esse método não valida esse dado,
   * sendo de responsabilidade do código cliente
   *
   * @param   int   $int_ref_cod_servidor_substituto  Código do servidor que substituirá o atual
   * @return  bool  TRUE em caso de sucesso, FALSE caso contrário
   */
  public function substituir_servidor($int_ref_cod_servidor_substituto) {
    if (is_numeric($int_ref_cod_servidor_substituto) &&
        is_numeric( $this->ref_cod_instituicao_servidor)) {

      $servidor = new clsPmieducarServidor($int_ref_cod_servidor_substituto,
        NULL, NULL, NULL, NULL, NULL, NULL, $this->ref_cod_instituicao_servidor);

      if (!$servidor->existe()) {
        return FALSE;
      }
    }

    if (is_numeric($this->ref_servidor) &&
        is_numeric($this->ref_cod_instituicao_servidor)) {

      $sql  = "UPDATE %s SET ref_servidor='%d', data_exclusao = NOW() ";
      $sql .= "WHERE ref_servidor = '%d' AND ref_cod_instituicao_servidor = '%d'";

      $sql = sprintf($sql, $this->_tabela, $int_ref_cod_servidor_substituto,
        $this->ref_servidor, $this->ref_cod_instituicao_servidor);

      $db = new clsBanco();
      $db->Consulta($sql);

      return TRUE;
    }

    return FALSE;
  }

  function retornaHorario($int_ref_cod_instituicao_servidor, $int_ref_ref_cod_escola,
    $int_ref_ref_cod_serie, $int_ref_ref_cod_turma, $int_dia_semana)
    {
    if (is_numeric($int_ref_cod_instituicao_servidor) &&
      is_numeric($int_ref_ref_cod_escola) && is_numeric($int_ref_ref_cod_serie) &&
      is_numeric($int_ref_ref_cod_turma) && is_numeric($int_dia_semana)
    ) {
      $db = new clsBanco();
      $db->Consulta("
        SELECT
          qhh.*
        FROM
          {$this->_schema}quadro_horario_horarios qhh,
          {$this->_schema}quadro_horario qh,
          {$this->_schema}turma t
        WHERE
          qhh.ref_cod_serie = t.ref_ref_cod_serie AND
          qhh.ref_cod_escola = t.ref_ref_cod_escola AND
          t.cod_turma = qh.ref_cod_turma AND
          qhh.ref_cod_quadro_horario = qh.cod_quadro_horario AND
          t.cod_turma = {$int_ref_ref_cod_turma} AND
          qhh.ref_cod_instituicao_servidor = {$int_ref_cod_instituicao_servidor} AND
          qhh.ref_cod_escola = {$int_ref_ref_cod_escola} AND
          qhh.ref_cod_serie = {$int_ref_ref_cod_serie} AND
          qhh.dia_semana = {$int_dia_semana} AND
          qhh.ativo = 1
        ORDER BY
          hora_inicial");

      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }

      if (count($resultado)) {
        return $resultado;
      }
    }

    return FALSE;
  }

  function excluirTodos()
  {
    $db = new clsBanco();
    if (is_numeric($this->ref_cod_quadro_horario)) {
      $db->Consulta("UPDATE {$this->_tabela} SET ativo = 0 WHERE ref_cod_quadro_horario = '{$this->ref_cod_quadro_horario}'");
      return TRUE;
    }

    return FALSE;
  }

  function listaHoras($int_ref_cod_instituicao_servidor = null, $int_ativo = null,
    $int_dia_semana = null)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela} qhh";
    $filtros = '';

    $whereAnd = " WHERE ";

    if (is_numeric($int_ref_cod_instituicao_servidor)) {
      $filtros .= "{$whereAnd} qhh.ref_cod_instituicao_servidor = '{$int_ref_cod_instituicao_servidor}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ativo)) {
      $filtros .= "{$whereAnd} qhh.ativo = '{$int_ativo}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_dia_semana)) {
      $filtros .= "{$whereAnd} qhh.dia_semana <> '{$int_dia_semana}'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} qhh {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();

        $tupla['_total'] = $this->_total;
        $resultado[] = $tupla;
      }
    }
    else {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla[$this->_campos_lista];
      }
    }

    if (count($resultado)) {
      return $resultado;
    }

    return FALSE;
  }
}
