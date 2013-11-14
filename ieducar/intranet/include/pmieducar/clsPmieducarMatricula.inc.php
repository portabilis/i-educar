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
 * clsPmieducarMatricula class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarMatricula
{
  var $cod_matricula;
  var $ref_cod_reserva_vaga;
  var $ref_ref_cod_escola;
  var $ref_ref_cod_serie;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $ref_cod_aluno;
  var $aprovado;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ano;
  var $ultima_matricula;
  var $modulo;
  var $descricao_reclassificacao;
  var $matricula_reclassificacao;
  var $formando;
  var $ref_cod_curso;
  var $semestre;

  /**
   * caso seja a primeira matricula do aluno
   * marcar como true este atributo
   * necessário para contabilizar como admitido por transferência
   * no relatorio de movimentacao mensal
   *
   * @var bool
   */

  var $matricula_transferencia;

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
  function clsPmieducarMatricula($cod_matricula = NULL, $ref_cod_reserva_vaga = NULL,
    $ref_ref_cod_escola = NULL, $ref_ref_cod_serie = NULL, $ref_usuario_exc = NULL,
    $ref_usuario_cad = NULL, $ref_cod_aluno = NULL, $aprovado = NULL,
    $data_cadastro = NULL, $data_exclusao = NULL, $ativo = NULL, $ano = NULL,
    $ultima_matricula = NULL, $modulo = NULL, $formando = NULL,
    $descricao_reclassificacao = NULL, $matricula_reclassificacao = NULL,
    $ref_cod_curso = NULL, $matricula_transferencia = NULL, $semestre = NULL
  ) {
    $db = new clsBanco();
    $this->_schema = 'pmieducar.';
    $this->_tabela = $this->_schema . 'matricula';

    $this->_campos_lista = $this->_todos_campos = "m.cod_matricula, m.ref_cod_reserva_vaga, m.ref_ref_cod_escola, m.ref_ref_cod_serie, m.ref_usuario_exc, m.ref_usuario_cad, m.ref_cod_aluno, m.aprovado, m.data_cadastro, m.data_exclusao, m.ativo, m.ano, m.ultima_matricula, m.modulo,formando,descricao_reclassificacao,matricula_reclassificacao, m.ref_cod_curso,m.matricula_transferencia,m.semestre";

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
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
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_cad);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_cad = $ref_usuario_cad;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
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

    if (is_numeric($ref_cod_reserva_vaga)) {
      if (class_exists("clsPmieducarReservaVaga")) {
        $tmp_obj = new clsPmieducarReservaVaga($ref_cod_reserva_vaga);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.reserva_vaga WHERE cod_reserva_vaga = '{$ref_cod_reserva_vaga}'")) {
          $this->ref_cod_reserva_vaga = $ref_cod_reserva_vaga;
        }
      }
    }

    if (is_numeric($ref_cod_aluno)) {
      if (class_exists("clsPmieducarAluno")) {
        $tmp_obj = new clsPmieducarAluno($ref_cod_aluno);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_aluno = $ref_cod_aluno;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_aluno = $ref_cod_aluno;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.aluno WHERE cod_aluno = '{$ref_cod_aluno}'")) {
          $this->ref_cod_aluno = $ref_cod_aluno;
        }
      }
    }

    if (is_numeric($ref_cod_curso)) {
      if (class_exists("clsPmieducarCurso")) {
        $tmp_obj = new clsPmieducarCurso($ref_cod_curso);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_curso = $ref_cod_curso;
          }
        }
        elseif (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_curso = $ref_cod_curso;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.curso WHERE cod_curso = '{$ref_cod_curso}'")) {
          $this->ref_cod_curso = $ref_cod_curso;
        }
      }
    }

    if (is_numeric($cod_matricula)) {
      $this->cod_matricula = $cod_matricula;
    }

    if (is_numeric($ref_ref_cod_escola)) {
      $this->ref_ref_cod_escola = $ref_ref_cod_escola;
    }

    if (is_numeric($ref_ref_cod_serie)) {
      $this->ref_ref_cod_serie = $ref_ref_cod_serie;
    }

    if (is_numeric($aprovado)) {
      $this->aprovado = $aprovado;
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

    if (is_numeric($ano)) {
      $this->ano = $ano;
    }

    if (is_numeric($ultima_matricula)) {
      $this->ultima_matricula = $ultima_matricula;
    }

    if (is_numeric($modulo)) {
      $this->modulo = $modulo;
    }

    if (is_numeric($formando)) {
      $this->formando = $formando;
    }

    if (is_string($descricao_reclassificacao)) {
      $this->descricao_reclassificacao = $descricao_reclassificacao;
    }

    if (is_numeric( $matricula_reclassificacao)) {
      $this->matricula_reclassificacao = $matricula_reclassificacao;
    }

    if (dbBool($matricula_transferencia)) {
      $this->matricula_transferencia = dbBool($matricula_transferencia) ? "t" : "f";
    }

    if (is_numeric($semestre)) {
      $this->semestre = $semestre;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_usuario_cad) && is_numeric($this->ref_cod_aluno) &&
      is_numeric($this->aprovado) && is_numeric($this->ano) &&
      is_numeric($this->ultima_matricula) && is_numeric($this->ref_cod_curso)
    ) {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if (is_numeric($this->ref_cod_reserva_vaga)) {
        $campos .= "{$gruda}ref_cod_reserva_vaga";
        $valores .= "{$gruda}'{$this->ref_cod_reserva_vaga}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_escola)) {
        $campos .= "{$gruda}ref_ref_cod_escola";
        $valores .= "{$gruda}'{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_serie)) {
        $campos .= "{$gruda}ref_ref_cod_serie";
        $valores .= "{$gruda}'{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_aluno)) {
        $campos .= "{$gruda}ref_cod_aluno";
        $valores .= "{$gruda}'{$this->ref_cod_aluno}'";
        $gruda = ", ";
      }

      if (is_numeric($this->aprovado)) {
        $campos .= "{$gruda}aprovado";
        $valores .= "{$gruda}'{$this->aprovado}'";
        $gruda = ", ";
      }

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      if (is_numeric($this->ano)) {
        $campos .= "{$gruda}ano";
        $valores .= "{$gruda}'{$this->ano}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ultima_matricula)) {
        $campos .= "{$gruda}ultima_matricula";
        $valores .= "{$gruda}'{$this->ultima_matricula}'";
        $gruda = ", ";
      }

      if (is_numeric($this->modulo)) {
        $campos .= "{$gruda}modulo";
        $valores .= "{$gruda}'{$this->modulo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->formando)) {
        $campos .= "{$gruda}formando";
        $valores .= "{$gruda}'{$this->formando}'";
        $gruda = ", ";
      }

      if (is_numeric($this->matricula_reclassificacao)) {
        $campos .= "{$gruda}matricula_reclassificacao";
        $valores .= "{$gruda}'{$this->matricula_reclassificacao}'";
        $gruda = ", ";
      }

      if (is_string($this->descricao_reclassificacao)) {
        $campos .= "{$gruda}descricao_reclassificacao";
        $valores .= "{$gruda}'{$this->descricao_reclassificacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_curso)) {
        $campos .= "{$gruda}ref_cod_curso";
        $valores .= "{$gruda}'{$this->ref_cod_curso}'";
        $gruda = ", ";
      }

      if (dbBool($this->matricula_transferencia)) {
        $campos .= "{$gruda}matricula_transferencia";
        $valores .= "{$gruda}'{$this->matricula_transferencia}'";
        $gruda = ", ";
      }

      if (is_numeric($this->semestre)) {
        $campos .= "{$gruda}semestre";
        $valores .= "{$gruda}'{$this->semestre}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");
      return $db->InsertId("{$this->_tabela}_cod_matricula_seq");
    }

    return FALSE;
  }

  function avancaModulo()
  {
    if( is_numeric($this->cod_matricula) && is_numeric($this->ref_usuario_exc)) {
      $db = new clsBanco();
      $db->Consulta("UPDATE {$this->_tabela} SET modulo = modulo + 1, data_exclusao = NOW(), ref_usuario_exc = '{$this->ref_usuario_exc}' WHERE cod_matricula = '{$this->cod_matricula}'" );
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
    if (is_numeric($this->cod_matricula) && is_numeric($this->ref_usuario_exc))
    {

      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_cod_reserva_vaga)) {
        $set .= "{$gruda}ref_cod_reserva_vaga = '{$this->ref_cod_reserva_vaga}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_escola)) {
        $set .= "{$gruda}ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_ref_cod_serie)) {
        $set .= "{$gruda}ref_ref_cod_serie = '{$this->ref_ref_cod_serie}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_aluno)) {
        $set .= "{$gruda}ref_cod_aluno = '{$this->ref_cod_aluno}'";
        $gruda = ", ";
      }

      if (is_numeric($this->aprovado)) {
        $set .= "{$gruda}aprovado = '{$this->aprovado}'";
        $gruda = ", ";
      }

      $set .= "{$gruda}data_exclusao = NOW()";
      $gruda = ", ";

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ano)) {
        $set .= "{$gruda}ano = '{$this->ano}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ultima_matricula)) {
        $set .= "{$gruda}ultima_matricula = '{$this->ultima_matricula}'";
        $gruda = ", ";
      }

      if (is_numeric($this->modulo)) {
        $set .= "{$gruda}modulo = '{$this->modulo}'";
        $gruda = ", ";
      }

      if (is_numeric($this->formando)) {
        $set .= "{$gruda}formando = '{$this->formando}'";
        $gruda = ", ";
      }

      if (is_numeric($this->matricula_reclassificacao)) {
        $set .= "{$gruda}matricula_reclassificacao = '{$this->matricula_reclassificacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_curso)) {
        $set .= "{$gruda}ref_cod_curso = '{$this->ref_cod_curso}'";
        $gruda = ", ";
      }

      if (is_string($this->descricao_reclassificacao)) {
        $set .= "{$gruda}descricao_reclassificacao = '{$this->descricao_reclassificacao}'";
        $gruda = ", ";
      }

      if (is_numeric($this->semestre)) {
        $set .= "{$gruda}semestre = '{$this->semestre}'";
        $gruda = ", ";
      }

      if ($set) {
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE cod_matricula = '{$this->cod_matricula}'");
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_cod_matricula = NULL, $int_ref_cod_reserva_vaga = NULL,
    $int_ref_ref_cod_escola = NULL, $int_ref_ref_cod_serie = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
    $int_ref_cod_aluno = NULL, $int_aprovado = NULL, $date_data_cadastro_ini = NULL,
    $date_data_cadastro_fim = NULL, $date_data_exclusao_ini = NULL,
    $date_data_exclusao_fim = NULL, $int_ativo = NULL, $int_ano = NULL,
    $int_ref_cod_curso2 = NULL, $int_ref_cod_instituicao = NULL,
    $int_ultima_matricula = NULL, $int_modulo = NULL,
    $int_padrao_ano_escolar = NULL, $int_analfabeto = NULL, $int_formando = NULL,
    $str_descricao_reclassificacao = NULL, $int_matricula_reclassificacao = NULL,
    $boo_com_deficiencia = NULL, $int_ref_cod_curso = NULL, $bool_curso_sem_avaliacao = NULL,
    $arr_int_cod_matricula = NULL, $int_mes_defasado = NULL, $boo_data_nasc = NULL,
    $boo_matricula_transferencia = NULL, $int_semestre = NULL, $int_ref_cod_turma = NULL)
  {
    if ($boo_data_nasc) {
      $this->_campos_lista .= " ,(SELECT data_nasc
                        FROM cadastro.fisica
                           WHERE idpes = ref_idpes
                    ) as data_nasc";
    }

    $sql = "SELECT {$this->_campos_lista}, c.ref_cod_instituicao, p.nome, a.cod_aluno, a.ref_idpes, c.cod_curso, m.observacao FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p ";

    $whereAnd = " AND ";
    $filtros = " WHERE m.ref_cod_aluno = a.cod_aluno AND a.ativo = 1 AND m.ref_cod_curso = c.cod_curso AND p.idpes = a.ref_idpes ";

    if (is_numeric($int_cod_matricula)) {
      $filtros .= "{$whereAnd} m.cod_matricula = '{$int_cod_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_reserva_vaga)) {
      $filtros .= "{$whereAnd} m.ref_cod_reserva_vaga = '{$int_ref_cod_reserva_vaga}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_escola)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_ref_cod_serie)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} m.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} m.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_aluno)) {
      $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_aprovado)) {
      $filtros .= "{$whereAnd} m.aprovado = '{$int_aprovado}'";
      $whereAnd = " AND ";
    }
    elseif (is_array($int_aprovado)) {
      $int_aprovado = implode(",",$int_aprovado);
      $filtros .= "{$whereAnd} m.aprovado in ({$int_aprovado})";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} m.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} m.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} m.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} m.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if ($int_ativo) {
      $filtros .= "{$whereAnd} m.ativo = '1'";
      $whereAnd = " AND ";
    }
    elseif (!is_null($int_ativo) && is_numeric($int_ativo)) {
      $filtros .= "{$whereAnd} m.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ano)) {
      $filtros .= "{$whereAnd} m.ano = '{$int_ano}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_padrao_ano_escolar)) {
      $filtros .= "{$whereAnd} c.padrao_ano_escolar = '{$int_padrao_ano_escolar}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ultima_matricula)) {
      $filtros .= "{$whereAnd} ultima_matricula = '{$int_ultima_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_modulo)) {
      $filtros .= "{$whereAnd} m.modulo = '{$int_modulo}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_analfabeto)) {
      $filtros .= "{$whereAnd} a.analfabeto = '{$int_analfabeto}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_formando)) {
      $filtros .= "{$whereAnd} a.formando = '{$int_formando}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_matricula_reclassificacao)) {
      $filtros .= "{$whereAnd} m.matricula_reclassificacao = '{$int_matricula_reclassificacao}'";
      $whereAnd = " AND ";
    }

    if (dbBool($boo_matricula_transferencia)) {
      $boo_matricula_transferencia = dbBool($boo_matricula_transferencia) ? 't' : 'f';
      $filtros .= "{$whereAnd} m.matricula_transferencia = '{$boo_matricula_transferencia}'";
      $whereAnd = " AND ";
    }

    if (is_string($int_matricula_reclassificacao)) {
      $filtros .= "{$whereAnd} to_ascii(a.matricula_reclassificacao) like to_ascii('%{$int_matricula_reclassificacao}%')";
      $whereAnd = " AND ";
    }

    if (is_bool($boo_com_deficiencia)) {
      $not = $boo_com_deficiencia === true ? "" : "NOT";
      $filtros .= "{$whereAnd} $not EXISTS (SELECT 1 FROM cadastro.fisica_deficiencia fd, pmieducar.aluno a WHERE a.cod_aluno = m.ref_cod_aluno AND fd.ref_idpes = a.ref_idpes)";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_semestre)) {
      $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_turma)) {
      $filtros .= "{$whereAnd} EXISTS (SELECT 1 FROM pmieducar.matricula_turma mt WHERE mt.ativo = 1 AND mt.ref_cod_turma = {$int_ref_cod_turma} AND mt.ref_cod_matricula = m.cod_matricula)";
      $whereAnd = " AND ";
    }

    if (is_array($arr_int_cod_matricula) && count($arr_int_cod_matricula)) {
      $filtros .= "{$whereAnd} cod_matricula IN (". implode(',', $arr_int_cod_matricula) . ")";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_mes_defasado)) {
      $primeiroDiaDoMes = mktime(0, 0, 0, $int_mes_defasado, 1, $int_ano);
      $NumeroDiasMes    = date('t', $primeiroDiaDoMes);
      $ultimoDiaMes     = date('d/m/Y', mktime(0, 0, 0, $int_mes_defasado, $NumeroDiasMes, $int_ano));
      $ultimoDiaMes     = dataToBanco($ultimoDiaMes, FALSE);

      $primeiroDiaDoMes = date('d/m/Y', $primeiroDiaDoMes);
      $primeiroDiaDoMes = dataToBanco($primeiroDiaDoMes, FALSE);

      $filtroAux = "{$whereAnd} ((aprovado IN (1,2,3) AND m.data_cadastro <= '$ultimoDiaMes')
                         OR  (aprovado IN (1,2,3,4) AND m.data_exclusao >= '$primeiroDiaDoMes' AND m.data_exclusao <= '$ultimoDiaMes')
                       )";

      $filtros .= $filtroAux;
      $whereAnd = ' AND ';
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} m, {$this->_schema}curso c, {$this->_schema}aluno a, cadastro.pessoa p {$filtros}");

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
    if (is_numeric($this->cod_matricula)) {
      $sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p WHERE m.cod_matricula = '{$this->cod_matricula}' AND a.cod_aluno = m.ref_cod_aluno AND p.idpes = a.ref_idpes ";
      if ($this->ativo) {
        $sql .= " AND m.ativo = {$this->ativo}";
      }

      if ($this->ultima_matricula) {
        $sql .= " AND m.ultima_matricula = {$this->ultima_matricula}";
      }

      $db = new clsBanco();
      $db->Consulta($sql);
      $db->ProximoRegistro();

      return $db->Tupla();
    }

    if (!$this->cod_matricula && is_numeric($this->ref_ref_cod_escola)) {
      $sql = "SELECT {$this->_todos_campos}, p.nome,(p.nome) as nome_upper FROM {$this->_tabela} m, {$this->_schema}aluno a, cadastro.pessoa p WHERE m.ref_ref_cod_escola = '{$this->ref_ref_cod_escola}'";

      $db = new clsBanco();
      $db->Consulta($sql);
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
    if (is_numeric($this->cod_matricula)) {
      $db = new clsBanco();
      $db->Consulta("SELECT 1 FROM {$this->_tabela} WHERE cod_matricula = '{$this->cod_matricula}'");
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
    if (is_numeric($this->cod_matricula) && is_numeric($this->ref_usuario_exc)) {
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

  function isSequencia( $origem, $destino )
  {
    $obj = new clsPmieducarSequenciaSerie();
    $sequencia = $obj->lista($origem, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    $achou = FALSE;

    if($sequencia) {
        do {
          if ($lista['ref_serie_origem'] == $destino) {
            $achou = TRUE;
            break;
          }

          if( $lista['ref_serie_destino'] == $destino ) {
            $achou = TRUE;
            break;
          }

          $sequencia_ = $obj->lista($lista['ref_serie_destino'], NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, 1);

          if (!$lista) {
            $achou = FALSE;
            break;
          }

        } while ($achou != FALSE);
    }

    return $achou;
  }

  function getInicioSequencia()
  {
    $db = new clsBanco();
    $sql = "SELECT o.ref_serie_origem
        FROM pmieducar.sequencia_serie o
        WHERE NOT EXISTS
        (
          SELECT 1
             FROM pmieducar.sequencia_serie d
                WHERE o.ref_serie_origem = d.ref_serie_destino
              )";

    $db->Consulta($sql);

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $resultado[] = $tupla;
    }

    return $resultado;
  }

  function getFimSequencia()
  {
    $db = new clsBanco();
    $sql = "SELECT o.ref_serie_destino
        FROM pmieducar.sequencia_serie o
        WHERE NOT EXISTS
        (
          SELECT 1
             FROM pmieducar.sequencia_serie d
                WHERE o.ref_serie_destino = d.ref_serie_origem
              )";

    $db->Consulta($sql);

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();
      $resultado[] = $tupla;
    }

    return $resultado;
  }

  /**
   * Retorna os dados de um registro.
   * @return array
   */
  function numModulo($int_ref_ref_cod_serie, $int_ref_ref_cod_escola,
    $int_ref_ref_cod_turma, $int_ref_cod_turma, $int_ref_ref_cod_matricula)
  {
    $db = new clsBanco();

    $sql = "SELECT CASE WHEN FLOOR( ( SELECT COUNT(*)
                        FROM pmieducar.nota_aluno
                       WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                         AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                         AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
                         AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
                         AND ref_ref_cod_turma       = {$int_ref_cod_turma} ) / ( ( SELECT COUNT(*)
                                                         FROM pmieducar.disciplina_serie
                                                         WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(0)
                                                                                    FROM pmieducar.dispensa_disciplina
                                                                                   WHERE ref_ref_cod_turma        = {$int_ref_cod_turma}
                                                                                 AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
                                                                                 AND disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
                                                                                 AND disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                                                                                 AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} ) ) ) = 0
                    THEN 0
                      ELSE FLOOR( ( SELECT COUNT(*)
                            FROM pmieducar.nota_aluno
                           WHERE disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                             AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola}
                             AND disc_ref_cod_turma      = {$int_ref_ref_cod_turma}
                             AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
                             AND ref_ref_cod_turma       = {$int_ref_cod_turma} ) / ( ( SELECT COUNT(*)
                                                             FROM pmieducar.disciplina_serie
                                                             WHERE ref_cod_serie = {$int_ref_ref_cod_serie} ) - ( SELECT COUNT(0)
                                                                                          FROM pmieducar.dispensa_disciplina
                                                                                         WHERE ref_ref_cod_turma        = {$int_ref_cod_turma}
                                                                                       AND ref_ref_cod_matricula   = {$int_ref_ref_cod_matricula}
                                                                                       AND disc_ref_ref_cod_turma  = {$int_ref_ref_cod_turma}
                                                                                       AND disc_ref_ref_cod_serie  = {$int_ref_ref_cod_serie}
                                                                                       AND disc_ref_ref_cod_escola = {$int_ref_ref_cod_escola} ) ) )
                      END";

    return $db->CampoUnico($sql);
  }

  /**
  * Seta a matricula para abandono e seta a observação passada por parâmetro 
  * @author lucassch
  * @return boolean
  */
  function cadastraObs($obs){
      
    if (is_numeric($this->cod_matricula)){
      if (trim($obs)=='')
        $obs = "Não informado";
      $db = new clsBanco();
      $consulta = "UPDATE {$this->_tabela} SET aprovado = 6, observacao = '$obs' WHERE cod_matricula = $this->cod_matricula";
      $db->Consulta($consulta);

      return TRUE;
    }

    return false;

  }

  function aprova_matricula_andamento_curso_sem_avaliacao()
  {
    if (is_numeric($this->ref_ref_cod_escola)) {
      $db = new clsBanco();
      $consulta = "UPDATE {$this->_tabela} SET aprovado = 1 , ref_usuario_exc = {$this->ref_usuario_exc} , data_exclusao = NOW() WHERE ano = {$this->ano} AND ref_ref_cod_escola = {$this->ref_ref_cod_escola} AND exists (SELECT 1 FROM {$this->_schema}curso c WHERE c.cod_curso = ref_cod_curso)";
      $db->Consulta($consulta);

      return TRUE;
    }

    return FALSE;
  }

  function getTotalAlunosEscola($cod_escola, $cod_curso, $cod_serie, $ano = NULL,
    $semestre = NULL)
  {
    if (is_numeric($cod_escola) && is_numeric($cod_curso)) {
      if (!is_numeric($ano)) {
        $ano = date('Y');
      }

      if (is_numeric($cod_serie)) {
        $where = " AND ref_ref_cod_serie = {$cod_serie} ";
      }

      if (is_numeric($semestre)) {
        $where .= " AND semestre = {$semestre} ";
      }

      $select = "SELECT count(1) as total_alunos_serie
                ,ref_ref_cod_serie as cod_serie
                ,nm_serie
              FROM pmieducar.matricula
                   ,pmieducar.serie
             WHERE serie.cod_serie = ref_ref_cod_serie
               AND ref_ref_cod_escola = {$cod_escola}
               AND serie.ref_cod_curso = {$cod_curso}
               AND ano = {$ano}
               $where
               AND ultima_matricula = 1
               AND aprovado IN (1,2,3)
               AND matricula.ativo = 1
             GROUP BY ref_ref_cod_serie
                      ,ref_ref_cod_escola
                      ,nm_serie";

      $db= new clsBanco();
      $db->Consulta($select);
      $total_registros = $db->Num_Linhas();

      if (!$total_registros) {
        return FALSE;
      }

      $resultados = array();
      $total = 0;

      while($db->ProximoRegistro()) {
        $registro = $db->Tupla();
        $total += $registro['total_alunos_serie'];
        $resultados[$registro['cod_serie']] = $registro;
      }

      $array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();

      $db = new clsBanco();

      foreach ($array_inicio_sequencias as $serie_inicio) {
        $serie_inicio = $serie_inicio[0];
        $seq_ini = $serie_inicio;
        $seq_correta = FALSE;
        $series[$cod_serie] = $cod_serie;

        do {
          $sql = "SELECT o.ref_serie_origem
                         ,s.nm_serie
                     ,o.ref_serie_destino
                     ,s.ref_cod_curso as ref_cod_curso_origem
                     ,sd.ref_cod_curso as ref_cod_curso_destino
                FROM pmieducar.sequencia_serie o
                     ,pmieducar.serie s
                     ,pmieducar.serie sd
               WHERE s.cod_serie = o.ref_serie_origem
                 AND s.cod_serie = $seq_ini
                     AND sd.cod_serie = o.ref_serie_destino
              ";

          $db->Consulta($sql);
          $db->ProximoRegistro();
          $tupla = $db->Tupla();
          $serie_origem = $tupla['ref_serie_origem'];

          $seq_ini = $serie_destino = $tupla['ref_serie_destino'];

          $series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];

          $sql = "SELECT 1
                FROM pmieducar.sequencia_serie s
               WHERE s.ref_serie_origem = $seq_ini
                ";
          $true = $db->CampoUnico($sql);

        } while ($true);

        $obj_serie = new clsPmieducarSerie($serie_destino);
        $det_serie = $obj_serie->detalhe();

        if($cod_serie == $serie_destino)
          $seq_correta = true;

        if($seq_correta == false) {
        }
        else {
        }
      }

      if($series) {
        $resultados2 = array();

        foreach ($series as $key => $serie) {
          if (key_exists($key,$resultados)) {
            $resultados[$key]['_total'] = $total;
            $resultados2[] = $resultados[$key];
          }
        }
      }

      return $resultados2;
    }

    return FALSE;
  }

  function getTotalAlunosIdadeSexoEscola($cod_escola, $cod_curso, $cod_serie,
    $ano = NULL, $semestre = NULL)
  {
    if (is_numeric($cod_escola) && is_numeric($cod_curso)) {
      if (!is_numeric($ano)) {
        $ano = date('Y');
      }

      if (is_numeric($cod_serie)) {
        $where = " AND ref_ref_cod_serie = {$cod_serie} ";
      }

      if (is_numeric($semestre)) {
        $where .= " AND m.semestre = {$semestre} ";
      }

      $select = "SELECT m.ref_ref_cod_serie as cod_serie
                ,nm_serie
                 ,COUNT(1) as total_alunos_serie
                   , COALESCE ( EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )::text , '-' ) as idade
                   ,f.sexo
              FROM pmieducar.aluno a
                   ,pmieducar.matricula m
                   ,cadastro.fisica f
                   ,pmieducar.serie
             WHERE a.cod_aluno = m.ref_cod_aluno
               AND a.ref_idpes = idpes
               AND ref_ref_cod_serie = cod_serie
               AND m.ref_ref_cod_escola = $cod_escola
               AND ano = $ano
               AND ultima_matricula = 1
               AND aprovado IN ( 1,2,3)
               AND m.ref_cod_curso = $cod_curso
               $where
            GROUP BY m.ref_ref_cod_serie
                   ,nm_serie
                     ,EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )
                     ,f.sexo
            ORDER BY EXTRACT ( YEAR FROM ( age(now(),data_nasc) ) )
                     ,f.sexo";

      $db= new clsBanco();
      $db->Consulta($select);
      $total_registros = $db->Num_Linhas();

      if (!$total_registros) {
        return FALSE;
      }

      $resultados = array();
      $total = 0;

      while ($db->ProximoRegistro()) {
        $registro = $db->Tupla();
        $total += $registro['total_alunos_serie'];
        $resultados[] = $registro;
      }

      $array_inicio_sequencias = clsPmieducarMatricula::getInicioSequencia();
      $db = new clsBanco();

      foreach ($array_inicio_sequencias as $serie_inicio) {
        $serie_inicio = $serie_inicio[0];
        $seq_ini = $serie_inicio;
        $seq_correta = false;
        $series[$cod_serie] = $cod_serie;

        do {
          $sql = "SELECT o.ref_serie_origem
                         ,s.nm_serie
                     ,o.ref_serie_destino
                     ,s.ref_cod_curso as ref_cod_curso_origem
                     ,sd.ref_cod_curso as ref_cod_curso_destino
                FROM pmieducar.sequencia_serie o
                     ,pmieducar.serie s
                     ,pmieducar.serie sd
               WHERE s.cod_serie = o.ref_serie_origem
                 AND s.cod_serie = $seq_ini
                     AND sd.cod_serie = o.ref_serie_destino
              ";

          $db->Consulta($sql);
          $db->ProximoRegistro();
          $tupla = $db->Tupla();
          $serie_origem = $tupla['ref_serie_origem'];

          $seq_ini = $serie_destino = $tupla['ref_serie_destino'];

          $series[$tupla['ref_serie_destino']] = $tupla['ref_serie_destino'];

          $sql = "SELECT 1
                FROM pmieducar.sequencia_serie s
               WHERE s.ref_serie_origem = $seq_ini
                ";

          $true = $db->CampoUnico($sql);

        } while ($true);

        $obj_serie = new clsPmieducarSerie($serie_destino);
        $det_serie = $obj_serie->detalhe();

        if($cod_serie == $serie_destino) {
          $seq_correta = TRUE;
        }

        if ($seq_correta == false)
        {
        }
        else
        {
        }
      }

      if($series) {
        $resultados2 = array();

        foreach ($series as $key => $serie) {
          foreach ($resultados as $key2 => $resultado) {
            if($key == $resultado['cod_serie']) {
              $resultados[$key2]['_total'] = $total;
              $resultados2[] = $resultados[$key2];
              unset($resultados[$key2]);
            }
          }
        }
      }

      return $resultados2;
    }

    return FALSE;
  }
}