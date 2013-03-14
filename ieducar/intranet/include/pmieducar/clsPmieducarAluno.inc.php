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
 * clsPmieducarEscola class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarAluno
{
  var $cod_aluno;
  var $ref_cod_aluno_beneficio;
  var $ref_cod_religiao;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_idpes;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $caminho_foto;
  var $analfabeto;
  var $nm_pai;
  var $nm_mae;
  var $tipo_responsavel;

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
  function clsPmieducarAluno($cod_aluno = NULL, $ref_cod_aluno_beneficio = NULL,
    $ref_cod_religiao = NULL, $ref_usuario_exc = NULL, $ref_usuario_cad = NULL,
    $ref_idpes = NULL, $data_cadastro = NULL, $data_exclusao = NULL, $ativo = NULL,
    $caminho_foto = NULL,$analfabeto = NULL, $nm_pai = NULL, $nm_mae = NULL,
    $tipo_responsavel = NULL, $aluno_estado_id = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'aluno';

    $this->_campos_lista = $this->_todos_campos = 'cod_aluno, ref_cod_aluno_beneficio, ref_cod_religiao, ref_usuario_exc, ref_usuario_cad, ref_idpes, data_cadastro, data_exclusao, ativo, caminho_foto, analfabeto, nm_pai, nm_mae,tipo_responsavel, aluno_estado_id';

    if (is_numeric($ref_cod_aluno_beneficio)) {
      if (class_exists('clsPmieducarAlunoBeneficio')) {
        $tmp_obj = new clsPmieducarAlunoBeneficio($ref_cod_aluno_beneficio);

        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
          }
        }
        elseif (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.aluno_beneficio WHERE cod_aluno_beneficio = '{$ref_cod_aluno_beneficio}'")) {
          $this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
        }
      }
    }
    elseif ($ref_cod_aluno_beneficio == 'NULL') {
      $this->ref_cod_aluno_beneficio = $ref_cod_aluno_beneficio;
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

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);

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

    if (is_numeric($ref_idpes)) {
      if ($db->CampoUnico("SELECT 1 FROM cadastro.fisica WHERE idpes = '{$ref_idpes}'")) {
        $this->ref_idpes = $ref_idpes;
      }
    }

    if (is_numeric($cod_aluno)) {
      $this->cod_aluno = $cod_aluno;
    }

    if (is_numeric($ref_cod_religiao)  || $ref_cod_aluno_beneficio == "NULL") {
      $this->ref_cod_religiao = $ref_cod_religiao;
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

    if (is_string($caminho_foto)) {
      $this->caminho_foto = $caminho_foto;
    }

    if (is_numeric($analfabeto)) {
      $this->analfabeto = $analfabeto;
    }

    if (is_string($caminho_foto)) {
      $this->caminho_foto = $caminho_foto;
    }

    if (is_string($nm_pai)) {
      $this->nm_pai = $nm_pai;
    }

    if (is_string($nm_mae)) {
      $this->nm_mae = $nm_mae;
    }

    if (is_string($tipo_responsavel)) {
      $this->tipo_responsavel = $tipo_responsavel;
    }

    $this->aluno_estado_id = $aluno_estado_id;
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_cod_aluno_beneficio)) {
        $campos  .= "{$gruda}ref_cod_aluno_beneficio";
        $valores .= "{$gruda}'{$this->ref_cod_aluno_beneficio}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_cod_religiao)) {
        $campos  .= "{$gruda}ref_cod_religiao";
        $valores .= "{$gruda}'{$this->ref_cod_religiao}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos  .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_idpes)) {
        $campos  .= "{$gruda}ref_idpes";
        $valores .= "{$gruda}'{$this->ref_idpes}'";
        $gruda = ', ';
      }

      if (is_numeric($this->analfabeto)) {
        $campos  .= "{$gruda}analfabeto";
        $valores .= "{$gruda}'{$this->analfabeto}'";
        $gruda = ', ';
      }

      $campos  .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ', ';

      $campos  .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ', ';

      if (is_string($this->caminho_foto)) {
        $campos  .= "{$gruda}caminho_foto";
        $valores .= "{$gruda}'{$this->caminho_foto}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_pai) && $this->nm_pai != "NULL") {
        $campos  .= "{$gruda}nm_pai";
        $valores .= "{$gruda}'{$this->nm_pai}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_mae) && $this->nm_mae != "NULL") {
        $campos  .= "{$gruda}nm_mae";
        $valores .= "{$gruda}'{$this->nm_mae}'";
        $gruda = ', ';
      }

      if (is_string($this->tipo_responsavel ) && sizeof($this->tipo_responsavel) <= 1) {
        $campos  .= "{$gruda}tipo_responsavel";
        $valores .= "{$gruda}'{$this->tipo_responsavel}'";
        $gruda = ', ';
      }

      if ($this->aluno_estado_id) {
        $campos  .= "{$gruda}aluno_estado_id";
        $valores .= "{$gruda}'{$this->aluno_estado_id}'";
        $gruda = ', ';
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $db->InsertId("{$this->_tabela}_cod_aluno_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro.
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_aluno) && is_numeric($this->ref_usuario_exc)) {
      $db  = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_cod_aluno_beneficio) || $this->ref_cod_aluno_beneficio == "NULL") {
        $set .= "{$gruda}ref_cod_aluno_beneficio = {$this->ref_cod_aluno_beneficio}";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_cod_religiao) || $this->ref_cod_religiao == "NULL") {
        $set .= "{$gruda}ref_cod_religiao = {$this->ref_cod_religiao}";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ', ';
      }

      if (is_numeric($this->ref_idpes)) {
        $set .= "{$gruda}ref_idpes = '{$this->ref_idpes}'";
        $gruda = ', ';
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ', ';
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ', ';

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ', ';
      }

      if (is_string($this->caminho_foto) &&  $this->caminho_foto != "NULL") {
        $set .= "{$gruda}caminho_foto = '{$this->caminho_foto}'";
        $gruda = ', ';
      }
      elseif ($this->caminho_foto == "NULL"){
        $set .= "{$gruda}caminho_foto = {$this->caminho_foto}";
        $gruda = ', ';
      }

      if (is_numeric($this->analfabeto)) {
        $set .= "{$gruda}analfabeto = '{$this->analfabeto}'";
        $gruda = ', ';
      }

      if (is_string($this->nm_pai) && $this->nm_pai != "NULL") {
        $set .= "{$gruda}nm_pai = '{$this->nm_pai}'";
        $gruda = ', ';
      }
      elseif ($this->nm_pai == "NULL") {
        $set .= "{$gruda}nm_pai = NULL";
        $gruda = ', ';
      }

      if (is_string($this->nm_mae) && $this->nm_mae != "NULL") {
        $set .= "{$gruda}nm_mae = '{$this->nm_mae}'";
        $gruda = ', ';
      }
      elseif ($this->nm_mae == "NULL") {
        $set .= "{$gruda}nm_mae = NULL";
        $gruda = ', ';
      }

      if (is_string($this->tipo_responsavel) && sizeof($this->tipo_responsavel) <= 1) {
        $set .= "{$gruda}tipo_responsavel = '{$this->tipo_responsavel}'";
        $gruda = ', ';
      }
      elseif ($this->tipo_responsavel == '') {
        $set .= "{$gruda}tipo_responsavel = NULL";
        $gruda = ', ';
      }

      if ($this->aluno_estado_id) {
        $set .= "{$gruda}aluno_estado_id = '{$this->aluno_estado_id}'";
        $gruda = ', ';
      }
      else {
        $set .= "{$gruda}aluno_estado_id = NULL";
        $gruda = ', ';
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_aluno = '{$this->cod_aluno}'" );
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_aluno = null, $int_ref_cod_aluno_beneficio = null,
    $int_ref_cod_religiao = null, $int_ref_usuario_exc = null,
    $int_ref_usuario_cad = null, $int_ref_idpes = null, $date_data_cadastro_ini = null,
    $date_data_cadastro_fim = null, $date_data_exclusao_ini = null,
    $date_data_exclusao_fim = null, $int_ativo = null, $str_caminho_foto = null,
    $str_nome_aluno = null,$str_nome_responsavel = null, $int_cpf_responsavel = null,
    $int_analfabeto = null, $str_nm_pai = null, $str_nm_mae = null,
    $int_ref_cod_escola = null,$str_tipo_responsavel = null)
  {
    $filtros = '';
    $this->resetCamposLista();

    $this->_campos_lista .= "
      , (
          SELECT
            nome
          FROM
            cadastro.pessoa
          WHERE
            idpes = ref_idpes
         ) AS nome_aluno";

    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_aluno)) {
      $filtros .= "{$whereAnd} cod_aluno = '{$int_cod_aluno}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_aluno_beneficio)) {
      $filtros .= "{$whereAnd} ref_cod_aluno_beneficio = '{$int_ref_cod_aluno_beneficio}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_religiao)) {
      $filtros .= "{$whereAnd} ref_cod_religiao = '{$int_ref_cod_religiao}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_idpes)) {
      $filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if ($int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_caminho_foto)) {
      $filtros .= "{$whereAnd} caminho_foto LIKE '%{$str_caminho_foto}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_analfabeto)) {
      $filtros .= "{$whereAnd} analfabeto = '{$int_analfabeto}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_aluno)) {
      $filtros .= "
        {$whereAnd} exists (
          SELECT
            1
          FROM
            cadastro.pessoa
          WHERE
            cadastro.pessoa.idpes = ref_idpes
            AND TO_ASCII(LOWER(nome)) LIKE TO_ASCII(LOWER('%{$str_nome_aluno}%'))
        )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_responsavel)  || is_numeric($int_cpf_responsavel)) {
      $and_resp = '';

      if (is_string($str_nome_responsavel)) {
        $and_nome_pai_mae  = "OR UPPER(TO_ASCII(aluno.nm_pai)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%')) AND (aluno.tipo_responsavel = 'p')";

        $and_nome_pai_mae .= "OR UPPER(TO_ASCII(aluno.nm_mae)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%')) AND (aluno.tipo_responsavel = 'm')";

        $and_nome_resp     = "
          (UPPER(TO_ASCII(pai_mae.nome)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%'))) AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
          OR
          (UPPER(TO_ASCII(pai_mae.nome)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%'))) AND (aluno.tipo_responsavel = 'p') AND pai_mae.idpes = fisica_aluno.idpes_pai";

        $and_resp = ' AND ';
      }

      if (is_numeric($int_cpf_responsavel)) {
        $and_cpf_pai_mae = "and fisica_resp.cpf LIKE '$int_cpf_responsavel'";
      }

      $filtros .= "
        AND (EXISTS(
          SELECT
            1
          FROM
            cadastro.fisica fisica_resp,
            cadastro.fisica,
            cadastro.pessoa,
            cadastro.pessoa responsavel
          WHERE
            fisica.idpes_responsavel = fisica_resp.idpes
            AND pessoa.idpes = fisica.idpes
            AND responsavel.idpes = fisica.idpes_responsavel
            $and_cpf_pai_mae
            and aluno.ref_idpes = pessoa.idpes
          )
          $and_nome_pai_mae
          OR EXISTS (
            SELECT
              1
            FROM
              cadastro.fisica AS fisica_aluno,
              cadastro.pessoa As pai_mae,
              cadastro.fisica AS fisica_pai_mae
            WHERE
              fisica_aluno.idpes = aluno.ref_idpes
              AND (
                $and_nome_resp
                $and_resp
                (
                  fisica_pai_mae.idpes = fisica_aluno.idpes_pai
                  OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae
                )
              AND fisica_pai_mae.cpf LIKE '$int_cpf_responsavel'
              )
          )
        )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_pai)) {
      $filtros .= "{$whereAnd} TO_ASCII(LOWER(nm_pai)) nm_pai LIKE TO_ASCII(LOWER('%{$str_nm_pai}%'))";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_mae)) {
      $filtros .= "{$whereAnd} TO_ASCII(LOWER(nm_mae)) LIKE TO_ASCII(LOWER('%{$str_nm_mae}%'))";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1 )";
      $whereAnd = ' AND ';
    }

    if (is_numeric($str_tipo_responsavel)) {
      $filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    if (!$this->getOrderby()) {
      $this->setOrderby('nome_aluno');
    }

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();
    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

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
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista2($int_cod_aluno = NULL, $int_ref_cod_aluno_beneficio = NULL,
    $int_ref_cod_religiao = NULL, $int_ref_usuario_exc = NULL,
    $int_ref_usuario_cad = NULL, $int_ref_idpes = NULL, $date_data_cadastro_ini = NULL,
    $date_data_cadastro_fim = NULL, $date_data_exclusao_ini = NULL,
    $date_data_exclusao_fim = NULL, $int_ativo = NULL, $str_caminho_foto = NULL,
    $str_nome_aluno = NULL, $str_nome_responsavel = NULL, $int_cpf_responsavel = NULL,
    $int_analfabeto = NULL, $str_nm_pai = NULL, $str_nm_mae = NULL,
    $int_ref_cod_escola = NULL, $str_tipo_responsavel = NULL, $data_nascimento = NULL,
    $str_nm_pai2 = NULL, $str_nm_mae2 = NULL, $str_nm_responsavel2 = NULL, $cod_inep = NULL)
  {
    $filtros = '';
    $this->resetCamposLista();

    $this->_campos_lista .= '
       , (
         SELECT
           nome
         FROM
           cadastro.pessoa
         WHERE
           idpes = ref_idpes
         ) AS nome_aluno';

    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $whereAnd = ' WHERE ';

    if(is_numeric($int_cod_aluno)) {
      $filtros .= "{$whereAnd} cod_aluno = '{$int_cod_aluno}'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_cod_aluno_beneficio)) {
      $filtros .= "{$whereAnd} ref_cod_aluno_beneficio = '{$int_ref_cod_aluno_beneficio}'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_cod_religiao)) {
      $filtros .= "{$whereAnd} ref_cod_religiao = '{$int_ref_cod_religiao}'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = ' AND ';
    }

    if(is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_idpes)) {
      $filtros .= "{$whereAnd} ref_idpes = '{$int_ref_idpes}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = ' AND ';
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = ' AND ';
    }

    if ($int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_caminho_foto)) {
      $filtros .= "{$whereAnd} caminho_foto LIKE '%{$str_caminho_foto}%'";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_analfabeto)) {
      $filtros .= "{$whereAnd} analfabeto = '{$int_analfabeto}'";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_aluno)) {
      $str_nome_aluno = addslashes($str_nome_aluno);

      $filtros .= "{$whereAnd} EXISTS (
                     SELECT
                       1
                     FROM
                       cadastro.pessoa
                     WHERE
                       cadastro.pessoa.idpes = ref_idpes
                       AND TO_ASCII(LOWER(nome)) LIKE TO_ASCII(LOWER('%{$str_nome_aluno}%'))
                   )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nome_responsavel) || is_numeric($int_cpf_responsavel)) {
      $and_resp = '';

      if (is_string($str_nome_responsavel)) {
        $and_nome_pai_mae  = "OR UPPER(TO_ASCII(aluno.nm_pai)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%')) AND (aluno.tipo_responsavel = 'p')";

        $and_nome_pai_mae .= "OR UPPER(TO_ASCII(aluno.nm_mae)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%')) AND (aluno.tipo_responsavel = 'm')";

        $and_nome_resp     = "
          (UPPER(TO_ASCII(pai_mae.nome)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%'))) AND (aluno.tipo_responsavel = 'm') AND pai_mae.idpes = fisica_aluno.idpes_mae
          OR
          (UPPER(TO_ASCII(pai_mae.nome)) LIKE UPPER(TO_ASCII('%$str_nome_responsavel%'))) AND (aluno.tipo_responsavel = 'p') AND pai_mae.idpes = fisica_aluno.idpes_pai";

        $and_resp = 'AND';
      }

      if (is_numeric($int_cpf_responsavel)) {
        $and_cpf_pai_mae = "and fisica_resp.cpf LIKE '$int_cpf_responsavel'";
      }

      $filtros .= "
        AND (EXISTS(
          SELECT
            1
          FROM
            cadastro.fisica fisica_resp,
            cadastro.fisica,
            cadastro.pessoa,
            cadastro.pessoa responsavel
          WHERE
            fisica.idpes_responsavel = fisica_resp.idpes
            AND pessoa.idpes = fisica.idpes
            AND responsavel.idpes = fisica.idpes_responsavel
            $and_cpf_pai_mae
            and aluno.ref_idpes = pessoa.idpes)
          $and_nome_pai_mae
          OR EXISTS (
            SELECT
              1
            FROM
              cadastro.fisica AS fisica_aluno,
              cadastro.pessoa AS pai_mae,
              cadastro.fisica AS fisica_pai_mae
            WHERE
              fisica_aluno.idpes = aluno.ref_idpes
            AND (
              $and_nome_resp
              $and_resp
              (
                fisica_pai_mae.idpes = fisica_aluno.idpes_pai
                OR fisica_pai_mae.idpes = fisica_aluno.idpes_mae
              )
              AND fisica_pai_mae.cpf LIKE '$int_cpf_responsavel'
            )
          )
        )";

      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_pai)) {
      $filtros .= "{$whereAnd} TO_ASCII(LOWER(nm_pai)) nm_pai LIKE TO_ASCII(LOWER('%{$str_nm_pai}%'))";
      $whereAnd = ' AND ';
    }

    if (is_string($str_nm_mae)) {
      $filtros .= "{$whereAnd} TO_ASCII(LOWER(nm_mae)) LIKE TO_ASCII(LOWER('%{$str_nm_mae}%'))";
      $whereAnd = ' AND ';
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} cod_aluno IN ( SELECT ref_cod_aluno FROM pmieducar.matricula WHERE ref_ref_cod_escola = '{$int_ref_cod_escola}' AND ultima_matricula = 1)";
      $whereAnd = ' AND ';
    }

    if (is_numeric($str_tipo_responsavel)) {
      $filtros .= "{$whereAnd} tipo_responsavel = '{$str_tipo_responsavel}'";
      $whereAnd = ' AND ';
    }

    if (!empty($data_nascimento)) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM cadastro.fisica f WHERE f.idpes = ref_idpes AND TO_CHAR(data_nasc,'DD/MM/YYYY') = '{$data_nascimento}')";
      $whereAnd = ' AND ';
    }

    if (!empty($cod_inep) && is_numeric($cod_inep)) {
      $filtros .= "{$whereAnd} cod_aluno = ( SELECT cod_aluno FROM modules.educacenso_cod_aluno WHERE cod_aluno_inep = '{$cod_inep}')";
      $whereAnd = ' AND ';
    }

    if (!empty($str_nm_pai2) || !empty($str_nm_mae2) || !empty($str_nm_responsavel2)) {
      $complemento_letf_outer = '';
      $complemento_where      = '';
      $and_where              = '';

      if (!empty($str_nm_pai2)) {
        $str_nm_pai2 = addslashes($str_nm_pai2);

        $complemento_sql   .= ' LEFT OUTER JOIN cadastro.pessoa AS pessoa_pai ON (pessoa_pai.idpes = f.idpes_pai)';
        $complemento_where .= "{$and_where} (nm_pai ILIKE ('%{$str_nm_pai2}%') OR pessoa_pai.nome ILIKE ('%{$str_nm_pai2}%'))";
        $and_where          = ' AND ';
      }

      if (!empty($str_nm_mae2)) {
        $str_nm_mae2 = addslashes($str_nm_mae2);

        $complemento_sql   .= ' LEFT OUTER JOIN cadastro.pessoa AS pessoa_mae ON (pessoa_mae.idpes = f.idpes_mae)';
        $complemento_where .= "{$and_where} (nm_mae ILIKE ('%{$str_nm_mae2}%') OR pessoa_mae.nome ILIKE ('%{$str_nm_mae2}%'))";
        $and_where          = ' AND ';
      }

      if (!empty($str_nm_responsavel2)) {
        $str_nm_responsavel2 = addslashes($str_nm_responsavel2);

        $complemento_sql .= " LEFT OUTER JOIN cadastro.pessoa AS pessoa_responsavel ON (pessoa_responsavel.idpes = f.idpes_responsavel)";
        $complemento_where .= "{$and_where} (pessoa_responsavel.nome ILIKE ('%{$str_nm_responsavel2}%'))";
        $and_where = " AND ";
      }

      $filtros .= "
        {$whereAnd} EXISTS
          (SELECT 1 FROM cadastro.fisica f
             {$complemento_sql}
           WHERE
              f.idpes = ref_idpes
              AND ({$complemento_where}))";

      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    if (!$this->getOrderby()) {
      $this->setOrderby('nome_aluno');
    }

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();
    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} {$filtros}");

    $db->Consulta($sql);

    if ($countCampos > 1) {
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $tupla["_total"] = $this->_total;
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
    return false;
  }

  /**
   * Retorna um array com os dados de um registro
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
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
    if (is_numeric($this->cod_aluno)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_aluno = '{$this->cod_aluno}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }
    elseif (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function existePessoa()
  {
    if (is_numeric($this->ref_idpes)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE ref_idpes = '{$this->ref_idpes}'");
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  function getResponsavelAluno()
  {
    if ($this->cod_aluno) {
      $registro = $this->detalhe();

      $registro['nome_responsavel'] = null;

      if ($registro['tipo_responsavel'] == 'p'  ||
         (!$registro['nome_responsavel'] && $registro['tipo_responsavel'] == NULL)) {
        $obj_fisica= new clsFisica($registro['ref_idpes']);
        $det_fisica_aluno = $obj_fisica->detalhe();

        if ($det_fisica_aluno['idpes_pai']) {
          $obj_ref_idpes = new clsPessoa_($det_fisica_aluno['idpes_pai']);
          $det_ref_idpes = $obj_ref_idpes->detalhe();

          $obj_fisica= new clsFisica($det_fisica_aluno['idpes_pai']);
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if ($registro['tipo_responsavel'] == 'm' ||
         ($registro['nome_responsavel'] == null && $registro['tipo_responsavel'] == NULL)) {
        if (!$det_fisica_aluno) {
          $obj_fisica= new clsFisica($registro['ref_idpes']);
          $det_fisica_aluno = $obj_fisica->detalhe();
        }

        if ($det_fisica_aluno['idpes_mae'] ) {
          $obj_ref_idpes = new clsPessoa_( $det_fisica_aluno['idpes_mae'] );
          $det_ref_idpes = $obj_ref_idpes->detalhe();

          $obj_fisica= new clsFisica($det_fisica_aluno['idpes_mae']);
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if ($registro['tipo_responsavel'] == 'r' ||
         ($registro['nome_responsavel'] == null && $registro['tipo_responsavel'] == NULL)) {
        if (!$det_fisica_aluno) {
          $obj_fisica= new clsFisica($registro['ref_idpes']);
          $det_fisica_aluno = $obj_fisica->detalhe();
        }

        if ($det_fisica_aluno['idpes_responsavel']) {
          $obj_ref_idpes = new clsPessoa_($det_fisica_aluno['idpes_responsavel']);
          $obj_fisica = new clsFisica($det_fisica_aluno['idpes_responsavel']);

          $det_ref_idpes = $obj_ref_idpes->detalhe();
          $det_fisica = $obj_fisica->detalhe();

          $registro['nome_responsavel'] = $det_ref_idpes['nome'];

          if ($det_fisica['cpf']) {
            $registro['cpf_responsavel'] = int2CPF($det_fisica['cpf']);
          }
        }
      }

      if (!$registro['nome_responsavel']) {
        if ($registro['tipo_responsavel'] != NULL) {
          if ($registro['tipo_responsavel'] == 'p') {
            $registro['nome_responsavel'] = $registro['nm_pai'];
          }
          else {
            $registro['nome_responsavel'] = $registro['nm_mae'];
          }
        }
        else {
          if ($registro['nm_pai']) {
            $registro['nome_responsavel'] = $registro['nm_pai'];
          }
          else {
            $registro['nome_responsavel'] = $registro['nm_mae'];
          }
        }
      }

      return $registro;
    }

    return FALSE;
  }

  /**
   * Exclui um registro.
   * @return bool
   */
  function excluir()
  {
    if (is_numeric($this->cod_aluno) && is_numeric($this->ref_usuario_exc)) {
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
}