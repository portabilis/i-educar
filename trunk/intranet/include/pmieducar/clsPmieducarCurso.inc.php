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
 * clsPmieducarSerie class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarCurso
{
  var $cod_curso;
  var $ref_usuario_cad;
  var $ref_cod_tipo_regime;
  var $ref_cod_nivel_ensino;
  var $ref_cod_tipo_ensino;
  var $nm_curso;
  var $sgl_curso;
  var $qtd_etapas;
  var $carga_horaria;
  var $ato_poder_publico;
  var $objetivo_curso;
  var $publico_alvo;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_usuario_exc;
  var $ref_cod_instituicao;
  var $padrao_ano_escolar;
  var $hora_falta;

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
   *
   * @todo Os parâmetros $frequencia_minima, $edicao_final,
   *   $ref_cod_tipo_avaliacao, $media, $media_exame, $falta_ch_globalizada e
   *   $avaliacao_globalizada serão removidos do construtor. Seus valores são
   *   ignorados.
   */
  function clsPmieducarCurso($cod_curso = NULL, $ref_usuario_cad = NULL,
    $ref_cod_tipo_regime = NULL, $ref_cod_nivel_ensino = NULL,
    $ref_cod_tipo_ensino = NULL, $ref_cod_tipo_avaliacao = NULL, $nm_curso = NULL,
    $sgl_curso = NULL, $qtd_etapas = NULL, $frequencia_minima = NULL, $media = NULL,
    $media_exame = NULL, $falta_ch_globalizada = NULL, $carga_horaria = NULL,
    $ato_poder_publico = NULL, $edicao_final = NULL, $objetivo_curso = NULL,
    $publico_alvo = NULL, $data_cadastro = NULL, $data_exclusao = NULL,
    $ativo = NULL, $ref_usuario_exc = NULL, $ref_cod_instituicao = NULL,
    $padrao_ano_escolar = NULL, $hora_falta = NULL, $avaliacao_globalizada = NULL, $multi_seriado = NULL)
  {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'curso';

    $this->_campos_lista = $this->_todos_campos = 'cod_curso, ref_usuario_cad, ref_cod_tipo_regime, ref_cod_nivel_ensino, ref_cod_tipo_ensino, nm_curso, sgl_curso, qtd_etapas, carga_horaria, ato_poder_publico, objetivo_curso, publico_alvo, data_cadastro, data_exclusao, ativo, ref_usuario_exc, ref_cod_instituicao, padrao_ano_escolar, hora_falta, multi_seriado';

    if (is_numeric($ref_cod_instituicao)) {
      if (class_exists('clsPmieducarInstituicao')) {
        $tmp_obj = new clsPmieducarInstituicao($ref_cod_instituicao);
        if (method_exists( $tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_instituicao = $ref_cod_instituicao;
          }
        }
        else if(method_exists($tmp_obj, 'detalhe')) {
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

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_exc );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        else if( method_exists($tmp_obj, 'detalhe')) {
          if($tmp_obj->detalhe()) {
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

    if (is_numeric($ref_cod_tipo_regime))  {
      if (class_exists('clsPmieducarTipoRegime')) {
        $tmp_obj = new clsPmieducarTipoRegime( $ref_cod_tipo_regime );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_tipo_regime = $ref_cod_tipo_regime;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_tipo_regime = $ref_cod_tipo_regime;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.tipo_regime WHERE cod_tipo_regime = '{$ref_cod_tipo_regime}'")) {
          $this->ref_cod_tipo_regime = $ref_cod_tipo_regime;
        }
      }
    }

    if (is_numeric($ref_cod_nivel_ensino)) {
      if (class_exists('clsPmieducarNivelEnsino')) {
        $tmp_obj = new clsPmieducarNivelEnsino($ref_cod_nivel_ensino);
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_nivel_ensino = $ref_cod_nivel_ensino;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_nivel_ensino = $ref_cod_nivel_ensino;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.nivel_ensino WHERE cod_nivel_ensino = '{$ref_cod_nivel_ensino}'")) {
          $this->ref_cod_nivel_ensino = $ref_cod_nivel_ensino;
        }
      }
    }

    if (is_numeric($ref_cod_tipo_ensino)) {
      if( class_exists('clsPmieducarTipoEnsino')) {
        $tmp_obj = new clsPmieducarTipoEnsino( $ref_cod_tipo_ensino );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_tipo_ensino = $ref_cod_tipo_ensino;
          }
        }
        else if(method_exists($tmp_obj, 'detalhe')) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_tipo_ensino = $ref_cod_tipo_ensino;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.tipo_ensino WHERE cod_tipo_ensino = '{$ref_cod_tipo_ensino}'")) {
          $this->ref_cod_tipo_ensino = $ref_cod_tipo_ensino;
        }
      }
    }

    if (is_numeric($ref_usuario_cad)) {
      if (class_exists('clsPmieducarUsuario')) {
        $tmp_obj = new clsPmieducarUsuario( $ref_usuario_cad );
        if (method_exists($tmp_obj, 'existe')) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        else if (method_exists($tmp_obj, 'detalhe')) {
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

    if (is_numeric($cod_curso)) {
      $this->cod_curso = $cod_curso;
    }

    if (is_string($nm_curso)) {
      $this->nm_curso = $nm_curso;
    }

    if (is_string($sgl_curso)) {
      $this->sgl_curso = $sgl_curso;
    }

    if (is_numeric($qtd_etapas)) {
      $this->qtd_etapas = $qtd_etapas;
    }

    if (is_numeric($carga_horaria)) {
      $this->carga_horaria = $carga_horaria;
    }

    if (is_string($ato_poder_publico)) {
      $this->ato_poder_publico = $ato_poder_publico;
    }

    if (is_string($objetivo_curso)) {
      $this->objetivo_curso = $objetivo_curso;
    }

    if (is_string($publico_alvo)) {
      $this->publico_alvo = $publico_alvo;
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

    if (is_numeric($padrao_ano_escolar)) {
      $this->padrao_ano_escolar = $padrao_ano_escolar;
    }

    if (is_numeric($hora_falta)) {
      $this->hora_falta = $hora_falta;
    }

    $this->multi_seriado = $multi_seriado;
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_nivel_ensino) &&
      is_numeric($this->ref_cod_tipo_ensino) && is_string($this->nm_curso) &&
      is_string($this->sgl_curso) && is_numeric($this->qtd_etapas) &&
      is_numeric($this->carga_horaria) && is_numeric($this->ref_cod_instituicao))
    {
      $db = new clsBanco();

      $campos  = '';
      $valores = '';
      $gruda   = '';

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_tipo_regime)) {
        $campos .= "{$gruda}ref_cod_tipo_regime";
        $valores .= "{$gruda}'{$this->ref_cod_tipo_regime}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_nivel_ensino)) {
        $campos .= "{$gruda}ref_cod_nivel_ensino";
        $valores .= "{$gruda}'{$this->ref_cod_nivel_ensino}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_tipo_ensino)) {
        $campos .= "{$gruda}ref_cod_tipo_ensino";
        $valores .= "{$gruda}'{$this->ref_cod_tipo_ensino}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_curso)) {
        $campos .= "{$gruda}nm_curso";
        $valores .= "{$gruda}'{$this->nm_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->sgl_curso)) {
        $campos .= "{$gruda}sgl_curso";
        $valores .= "{$gruda}'{$this->sgl_curso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->qtd_etapas)) {
        $campos .= "{$gruda}qtd_etapas";
        $valores .= "{$gruda}'{$this->qtd_etapas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria)) {
        $campos .= "{$gruda}carga_horaria";
        $valores .= "{$gruda}'{$this->carga_horaria}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_poder_publico)) {
        $campos .= "{$gruda}ato_poder_publico";
        $valores .= "{$gruda}'{$this->ato_poder_publico}'";
        $gruda = ", ";
      }

      if (is_string($this->objetivo_curso)) {
        $campos .= "{$gruda}objetivo_curso";
        $valores .= "{$gruda}'{$this->objetivo_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->publico_alvo)) {
        $campos .= "{$gruda}publico_alvo";
        $valores .= "{$gruda}'{$this->publico_alvo}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      if (is_numeric( $this->ref_cod_instituicao)) {
        $campos .= "{$gruda}ref_cod_instituicao";
        $valores .= "{$gruda}'{$this->ref_cod_instituicao}'";
        $gruda = ", ";
      }

      if (is_numeric( $this->padrao_ano_escolar)) {
        $campos .= "{$gruda}padrao_ano_escolar";
        $valores .= "{$gruda}'{$this->padrao_ano_escolar}'";
        $gruda = ", ";
      }

      if (is_numeric( $this->hora_falta)) {
        $campos .= "{$gruda}hora_falta";
        $valores .= "{$gruda}'{$this->hora_falta}'";
        $gruda = ", ";
      }

      if (is_numeric($multi_seriado)) {
				$campos .= "{$gruda}multi_seriado";
				$valores .= "{$gruda}'{$this->multi_seriado}'";
				$gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ( $campos ) VALUES( $valores )");
      return $db->InsertId("{$this->_tabela}_cod_curso_seq");
    }

    return FALSE;
  }

  /**
   * Edita os dados de um registro
   * @return bool
   */
  function edita()
  {
    if (is_numeric($this->cod_curso) && is_numeric($this->ref_usuario_exc)) {

      $db = new clsBanco();
      $set = '';

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_tipo_regime)) {
        $set .= "{$gruda}ref_cod_tipo_regime = '{$this->ref_cod_tipo_regime}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_nivel_ensino)) {
        $set .= "{$gruda}ref_cod_nivel_ensino = '{$this->ref_cod_nivel_ensino}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_tipo_ensino)) {
        $set .= "{$gruda}ref_cod_tipo_ensino = '{$this->ref_cod_tipo_ensino}'";
        $gruda = ", ";
      }

      if (is_string($this->nm_curso)) {
        $set .= "{$gruda}nm_curso = '{$this->nm_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->sgl_curso)) {
        $set .= "{$gruda}sgl_curso = '{$this->sgl_curso}'";
        $gruda = ", ";
      }

      if (is_numeric($this->qtd_etapas)) {
        $set .= "{$gruda}qtd_etapas = '{$this->qtd_etapas}'";
        $gruda = ", ";
      }

      if (is_numeric($this->carga_horaria)) {
        $set .= "{$gruda}carga_horaria = '{$this->carga_horaria}'";
        $gruda = ", ";
      }

      if (is_string($this->ato_poder_publico)) {
        $set .= "{$gruda}ato_poder_publico = '{$this->ato_poder_publico}'";
        $gruda = ", ";
      }

      if (is_string($this->objetivo_curso)) {
        $set .= "{$gruda}objetivo_curso = '{$this->objetivo_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->publico_alvo)) {
        $set .= "{$gruda}publico_alvo = '{$this->publico_alvo}'";
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

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_instituicao)) {
        $set .= "{$gruda}ref_cod_instituicao = '{$this->ref_cod_instituicao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->padrao_ano_escolar)) {
        $set .= "{$gruda}padrao_ano_escolar = '{$this->padrao_ano_escolar}'";
        $gruda = ", ";
      }

      if (is_numeric($this->hora_falta)) {
        $set .= "{$gruda}hora_falta = '{$this->hora_falta}'";
        $gruda = ", ";
      }
      else {
        $set .= "{$gruda}hora_falta = 0";
        $gruda = ", ";
      }

			if( is_numeric( $this->multi_seriado))
			{
				$set .= "{$gruda}multi_seriado = '{$this->multi_seriado}'";
				$gruda = ", ";
			}

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_curso = '{$this->cod_curso}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   * @todo Os argumentos $int_ref_cod_tipo_avaliacao, $int_media,
   *   $int_media_exame, $int_falta_ch_globalizada, $int_edicao_final,
   *   $bool_avaliacao_globalizada serão removidas da assinatura do método.
   *   Por enquanto, seus valores são ignorados.
   */
  function lista($int_cod_curso = NULL, $int_ref_usuario_cad = NULL,
    $int_ref_cod_tipo_regime = NULL, $int_ref_cod_nivel_ensino = NULL,
    $int_ref_cod_tipo_ensino = NULL, $int_ref_cod_tipo_avaliacao = NULL,
    $str_nm_curso = NULL, $str_sgl_curso = NULL, $int_qtd_etapas = NULL,
    $int_frequencia_minima = NULL, $int_media = NULL, $int_media_exame = NULL,
    $int_falta_ch_globalizada = NULL, $int_carga_horaria = NULL,
    $str_ato_poder_publico = NULL, $int_edicao_final = NULL,
    $str_objetivo_curso = NULL, $str_publico_alvo = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL, $int_ref_usuario_exc = NULL,
    $int_ref_cod_instituicao = NULL, $int_padrao_ano_escolar = NULL,
    $int_hora_falta = NULL, $bool_avaliacao_globalizada = NULL)
  {
    $sql = "SELECT {$this->_campos_lista} FROM {$this->_tabela}";
    $filtros = '';

    $whereAnd = ' WHERE ';

    if (is_numeric($int_cod_curso)) {
      $filtros .= "{$whereAnd} cod_curso = '{$int_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_tipo_regime)) {
      $filtros .= "{$whereAnd} ref_cod_tipo_regime = '{$int_ref_cod_tipo_regime}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_nivel_ensino)) {
      $filtros .= "{$whereAnd} ref_cod_nivel_ensino = '{$int_ref_cod_nivel_ensino}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_tipo_ensino)) {
      $filtros .= "{$whereAnd} ref_cod_tipo_ensino = '{$int_ref_cod_tipo_ensino}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_nm_curso)) {
      $filtros .= "{$whereAnd} nm_curso LIKE '%{$str_nm_curso}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_sgl_curso)) {
      $filtros .= "{$whereAnd} sgl_curso LIKE '%{$str_sgl_curso}%'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_qtd_etapas)) {
      $filtros .= "{$whereAnd} qtd_etapas = '{$int_qtd_etapas}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_carga_horaria)) {
      $filtros .= "{$whereAnd} carga_horaria = '{$int_carga_horaria}'";
      $whereAnd = " AND ";
    }

    if (is_string($str_ato_poder_publico)) {
      $filtros .= "{$whereAnd} ato_poder_publico LIKE '%{$str_ato_poder_publico}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_objetivo_curso)) {
      $filtros .= "{$whereAnd} objetivo_curso LIKE '%{$str_objetivo_curso}%'";
      $whereAnd = " AND ";
    }

    if (is_string($str_publico_alvo)) {
      $filtros .= "{$whereAnd} publico_alvo LIKE '%{$str_publico_alvo}%'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_padrao_ano_escolar)) {
      $filtros .= "{$whereAnd} padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_hora_falta)) {
      $filtros .= "{$whereAnd} hora_falta = '{$int_hora_falta}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

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

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro.
   * @return array
   */
  function detalhe()
  {
    if (is_numeric($this->cod_curso)) {
      $db = new clsBanco();
      $db->Consulta( "SELECT {$this->_todos_campos},fcn_upper(nm_curso) as nm_curso_upper FROM {$this->_tabela} WHERE cod_curso = '{$this->cod_curso}'" );
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

  /**
   * Retorna um array com os dados de um registro ou FALSE caso não exista.
   * @return array|bool
   */
  function existe()
  {
    if (is_numeric($this->cod_curso)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_curso = '{$this->cod_curso}'");
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
    if (is_numeric($this->cod_curso) && is_numeric($this->ref_usuario_exc)) {
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
    if (is_string($strNomeCampo) && $strNomeCampo) {
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
