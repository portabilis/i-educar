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
require_once 'Avaliacao/Fixups/CleanComponentesCurriculares.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'include/services/matricula/SequencialEnturmacao.php';
require_once 'lib/App/Model/Educacenso.php';

/**
 * clsPmieducarMatriculaTurma class.
 *
 * @author    Prefeitura Municipal de Itajaí <ctima@itajai.sc.gov.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   iEd_Pmieducar
 * @since     Classe disponível desde a versão 1.0.0
 * @version   @@package_version@@
 */
class clsPmieducarMatriculaTurma
{
  var $ref_cod_matricula;
  var $ref_cod_turma;
  var $ref_usuario_exc;
  var $ref_usuario_cad;
  var $data_cadastro;
  var $data_exclusao;
  var $ativo;
  var $ref_cod_turma_transf;
  var $sequencial;
  var $data_enturmacao;
  var $sequencial_fechamento;
  var $removerSequencial;
  var $reabrirMatricula;
  var $etapa_educacenso;
  var $turma_unificada;
  var $remanejado;

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
  function __construct($ref_cod_matricula = NULL,
    $ref_cod_turma = NULL, $ref_usuario_exc = NULL, $ref_usuario_cad = NULL,
    $data_cadastro = NULL, $data_exclusao = NULL, $ativo = NULL,
    $ref_cod_turma_transf = NULL,$sequencial = NULL, $data_enturmacao = NULL,
    $removerSequencial = FALSE, $reabrirMatricula = FALSE, $remanejado = FALSE
  ) {
    $db = new clsBanco();
    $this->_schema = "pmieducar.";
    $this->_tabela = "{$this->_schema}matricula_turma";

    @session_start();
    $this->pessoa_logada = $_SESSION['id_pessoa'];
    session_write_close();

    $this->_campos_lista = $this->_todos_campos = "mt.ref_cod_matricula, mt.abandono, mt.reclassificado, mt.remanejado, mt.transferido, mt.falecido, mt.ref_cod_turma, mt.etapa_educacenso, mt.turma_unificada, mt.ref_usuario_exc, mt.ref_usuario_cad, mt.data_cadastro, mt.data_exclusao, mt.ativo, mt.sequencial, mt.data_enturmacao, (SELECT pes.nome FROM cadastro.pessoa pes, pmieducar.aluno alu, pmieducar.matricula mat WHERE pes.idpes = alu.ref_idpes AND mat.ref_cod_aluno = alu.cod_aluno AND mat.cod_matricula = mt.ref_cod_matricula ) AS nome, (SELECT (pes.nome) FROM cadastro.pessoa pes, pmieducar.aluno alu, pmieducar.matricula mat WHERE pes.idpes = alu.ref_idpes AND mat.ref_cod_aluno = alu.cod_aluno AND mat.cod_matricula = mt.ref_cod_matricula ) AS nome_ascii";

    if (is_numeric($ref_usuario_exc)) {
      if (class_exists("clsPmieducarUsuario")) {
        $tmp_obj = new clsPmieducarUsuario($ref_usuario_exc);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_usuario_exc = $ref_usuario_exc;
          }
        }
        else if (method_exists($tmp_obj, "detalhe")) {
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
        else if (method_exists($tmp_obj, "detalhe")) {
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

    if (is_numeric($ref_cod_turma)) {
      if (class_exists("clsPmieducarTurma")) {
        $tmp_obj = new clsPmieducarTurma($ref_cod_turma);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_turma = $ref_cod_turma;
          }
        }
        else if (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_turma = $ref_cod_turma;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.turma WHERE cod_turma = '{$ref_cod_turma}'")) {
          $this->ref_cod_turma = $ref_cod_turma;
        }
      }
    }

    if (is_numeric($ref_cod_matricula)) {
      if (class_exists("clsPmieducarMatricula")) {
        $tmp_obj = new clsPmieducarMatricula($ref_cod_matricula);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_matricula = $ref_cod_matricula;
          }
        }
        else if (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_matricula = $ref_cod_matricula;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.matricula WHERE cod_matricula = '{$ref_cod_matricula}'"))
        {
          $this->ref_cod_matricula = $ref_cod_matricula;
        }
      }
    }

    if (!empty($data_cadastro)) {
      $this->data_cadastro = $data_cadastro;
    }

    if (!empty($data_exclusao)) {
      $this->data_exclusao = $data_exclusao;
    }

    if (is_numeric($ativo)) {
      $this->ativo = $ativo;
    }

      if ($remanejado) {
          $this->remanejado = $remanejado;
      }

    if (is_numeric($ref_cod_turma_transf)) {
      if (class_exists("clsPmieducarTurma")) {
        $tmp_obj = new clsPmieducarTurma($ref_cod_turma_transf);
        if (method_exists($tmp_obj, "existe")) {
          if ($tmp_obj->existe()) {
            $this->ref_cod_turma_transf = $ref_cod_turma_transf;
          }
        }
        else if (method_exists($tmp_obj, "detalhe")) {
          if ($tmp_obj->detalhe()) {
            $this->ref_cod_turma_transf = $ref_cod_turma_transf;
          }
        }
      }
      else {
        if ($db->CampoUnico("SELECT 1 FROM pmieducar.turma WHERE cod_turma = '{$ref_cod_turma_transf}'")) {
          $this->ref_cod_turma_transf = $ref_cod_turma_transf;
        }
      }
    }

    if (is_numeric($sequencial)) {
      $this->sequencial = $sequencial;
    }

    if (is_string($data_enturmacao)) {
      $this->data_enturmacao = $data_enturmacao;
    }
  }

  /**
   * Cria um novo registro.
   * @return bool
   */
  function cadastra()
  {
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
      is_numeric($this->ref_usuario_cad))
    {
      $db = new clsBanco();

      $campos = "";
      $valores = "";
      $gruda = "";

      if (is_numeric($this->ref_cod_matricula)) {
        $campos .= "{$gruda}ref_cod_matricula";
        $valores .= "{$gruda}'{$this->ref_cod_matricula}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_cod_turma)) {
        $campos .= "{$gruda}ref_cod_turma";
        $valores .= "{$gruda}'{$this->ref_cod_turma}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $campos .= "{$gruda}ref_usuario_cad";
        $valores .= "{$gruda}'{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if(is_numeric($this->etapa_educacenso)) {
        $campos .= "{$gruda}etapa_educacenso";
        $valores .= "{$gruda}{$this->etapa_educacenso}";
        $gruda = ", ";
      }

      if(is_numeric($this->turma_unificada)) {
        $campos .= "{$gruda}turma_unificada";
        $valores .= "{$gruda}{$this->turma_unificada}";
        $gruda = ", ";
      }

      $this->sequencial = $this->buscaSequencialMax();

      $campos .= "{$gruda}sequencial";
      $valores .= "{$gruda}'{$this->sequencial}'";
      $gruda = ", ";

      $campos .= "{$gruda}data_cadastro";
      $valores .= "{$gruda}NOW()";
      $gruda = ", ";

      $campos .= "{$gruda}ativo";
      $valores .= "{$gruda}'1'";
      $gruda = ", ";

      if (is_string($this->data_enturmacao)) {
        $campos .= "{$gruda}data_enturmacao";
        $valores .= "{$gruda}'{$this->data_enturmacao}'";
        $gruda = ", ";
      }

      $sequencialEnturmacao = new SequencialEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma, $this->data_enturmacao, $this->sequencial);
      $this->sequencial_fechamento = $sequencialEnturmacao->ordenaSequencialNovaMatricula();

      if(is_numeric($this->sequencial_fechamento)){
        $campos .= "{$gruda}sequencial_fechamento";
        $valores .= "{$gruda}'{$this->sequencial_fechamento}'";
        $gruda = ", ";
      }

      $db->Consulta("INSERT INTO {$this->_tabela} ($campos) VALUES ($valores)");

      $detalhe = $this->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("matricula_turma", $this->pessoa_logada, $this->ref_cod_matricula);
      $auditoria->inclusao($detalhe);

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
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
      is_numeric($this->ref_usuario_exc) && is_numeric($this->sequencial)) {
      $db = new clsBanco();
      $set = "";

      if (is_numeric($this->ref_usuario_exc)) {
        $set .= "{$gruda}ref_usuario_exc = '{$this->ref_usuario_exc}'";
        $gruda = ", ";
      }

      if (is_numeric($this->ref_usuario_cad)) {
        $set .= "{$gruda}ref_usuario_cad = '{$this->ref_usuario_cad}'";
        $gruda = ", ";
      }

      if (is_string($this->data_cadastro)) {
        $set .= "{$gruda}data_cadastro = '{$this->data_cadastro}'";
        $gruda = ", ";
      }
      if (is_string($this->data_exclusao)) {
        $set .= "{$gruda}data_exclusao = '{$this->data_exclusao}'";
        $gruda = ", ";
      }elseif(is_null($this->data_exclusao) || empty($this->data_exclusao)){
        $set .= "{$gruda}data_exclusao = NULL";
        $gruda = ", ";
      }

      if ($this->etapa_educacenso === 0) {
        $set .= "{$gruda}etapa_educacenso = NULL";
        $gruda = ", ";
      }elseif(is_numeric($this->etapa_educacenso)){
        $set .= "{$gruda}etapa_educacenso = {$this->etapa_educacenso}";
        $gruda = ", ";
      }

      if (is_numeric($this->turma_unificada)){
        $set .= "{$gruda}turma_unificada = {$this->turma_unificada}";
        $gruda = ", ";
      }

      if (is_numeric($this->ativo)) {
        $set .= "{$gruda}ativo = '{$this->ativo}'";
        $gruda = ", ";
        if ($this->ativo == 1){
          $set .= "{$gruda}remanejado = null, transferido = null";
          $gruda = ", ";
        }
      }

      if (! $this->ativo) {
          if ($this->remanejado) {
              $set .= "{$gruda}remanejado = true";
              $gruda = ", ";
          }
      }

      if (is_numeric($this->ref_cod_turma_transf)) {
        $set .= "{$gruda}ref_cod_turma= '{$this->ref_cod_turma_transf}'";
        $gruda = ", ";
      }

      if (is_string($this->data_enturmacao)) {
        $set .= "{$gruda}data_enturmacao = '{$this->data_enturmacao}'";
        $gruda = ", ";
      }

      if ($this->reabrirMatricula){
        $det = $this->detalhe();
        $this->ref_usuario_cad = $det['ref_usuario_cad'];
        return $this->cadastra();
      }

      if ($this->removerSequencial){
        $sequencialEnturmacao = new SequencialEnturmacao($this->ref_cod_matricula, $this->ref_cod_turma, $this->data_enturmacao, $this->sequencial);
        $this->sequencial_fechamento = $sequencialEnturmacao->ordenaSequencialExcluiMatricula();
      }

      if(is_numeric($this->sequencial_fechamento)){
        $campos .= "{$gruda}sequencial_fechamento";
        $valores .= "{$gruda}'{$this->sequencial_fechamento}'";
        $gruda = ", ";
      }

      if ($set) {
        $detalheAntigo = $this->detalhe();
        $db->Consulta("UPDATE {$this->_tabela} SET $set WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' and sequencial = '$this->sequencial' ");

        $auditoria = new clsModulesAuditoriaGeral("matricula_turma", $this->pessoa_logada, $this->ref_cod_matricula);
        $auditoria->alteracao($detalheAntigo, $this->detalhe());

        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * Retorna uma lista de registros filtrados de acordo com os parâmetros.
   * @return array
   */
  function lista($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL, $int_ativo = NULL,
    $int_ref_cod_serie = NULL, $int_ref_cod_curso = NULL, $int_ref_cod_escola = NULL,
    $int_ref_cod_instituicao = NULL, $int_ref_cod_aluno = NULL, $mes = NULL,
    $aprovado = NULL, $mes_menor_que = NULL, $int_sequencial = NULL,
    $int_ano_matricula = NULL, $tem_avaliacao = NULL, $bool_get_nome_aluno = FALSE,
    $bool_aprovados_reprovados = NULL, $int_ultima_matricula = NULL,
    $bool_matricula_ativo = NULL, $bool_escola_andamento = FALSE,
    $mes_matricula_inicial = FALSE, $get_serie_mult = FALSE,
    $int_ref_cod_serie_mult = NULL, $int_semestre = NULL,
    $pegar_ano_em_andamento = FALSE, $parar=NULL, $diario = FALSE,
    $int_turma_turno_id = FALSE, $int_ano_turma = FALSE, $dependencia = NULL,
    $apenasTurmasMultiSeriadas = FALSE, $apenasTurmasUnificadas = FALSE)
  {
    if ($bool_get_nome_aluno === true) {
      $nome = " ,(SELECT (nome)
                        FROM cadastro.pessoa
                           WHERE idpes = a.ref_idpes
                    ) as nome_aluno";
      $tab_aluno = ", {$this->_schema}aluno a";

      $where_nm_aluno = " AND a.cod_aluno = m.ref_cod_aluno";
    }

    if ( $bool_escola_andamento) {
      if ($pegar_ano_em_andamento) {
        $from = ", pmieducar.escola_ano_letivo eal ";

        $where = " AND eal.ref_cod_escola = m.ref_ref_cod_escola
              AND eal.ano = (select max(ano) from pmieducar.escola_ano_letivo let where
                      let.ref_cod_escola= eal.ref_cod_escola and andamento=1)
              AND eal.ano = m.ano
              AND eal.andamento = '1' ";
      }
      else {
        $ano = date("Y");

        $from = ", pmieducar.escola_ano_letivo eal ";

        $where = " AND eal.ref_cod_escola = m.ref_ref_cod_escola
              AND eal.ano = '{$ano}'
              AND eal.ano = m.ano
              AND eal.andamento = '1' ";
      }
    }

    $sql = "SELECT {$this->_campos_lista}, mt.sequencial_fechamento, c.nm_curso, t.nm_turma, i.nm_instituicao, m.ref_ref_cod_serie, m.ref_cod_curso, m.ref_ref_cod_escola, c.ref_cod_instituicao, m.ref_cod_aluno,t.hora_inicial, mt.turma_unificada, t.etapa_educacenso as etapa_ensino $nome FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t,{$this->_schema}aluno al, {$this->_schema}instituicao i{$tab_aluno} {$from}, cadastro.pessoa ";

    $whereAnd = " AND ";
    $filtros = " WHERE mt.ref_cod_matricula = m.cod_matricula AND idpes = al.ref_idpes AND al.cod_aluno = m.ref_cod_aluno AND al.ativo=1 AND m.ref_cod_curso = c.cod_curso AND t.cod_turma = mt.ref_cod_turma AND i.cod_instituicao = c.ref_cod_instituicao {$where_nm_aluno} {$where}";

    if (is_numeric($int_ref_cod_matricula)) {
      $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_turma)) {
      $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ativo)) {
      if ($int_ativo == 1) {
        $filtros .= "{$whereAnd} mt.ativo = '1'";
        $whereAnd = " AND ";
      }elseif ($int_ativo == 2) {
        $filtros .= "{$whereAnd} (mt.ativo = '1' OR (mt.transferido OR
                                                     mt.remanejado OR
                                                     mt.reclassificado OR
                                                     mt.abandono OR
                                                     mt.falecido) AND
                                                     (NOT EXISTS(SELECT 1
                                                                   FROM pmieducar.matricula_turma
                                                                  WHERE matricula_turma.ativo = 1 AND
                                                                        matricula_turma.ref_cod_matricula = mt.ref_cod_matricula AND
                                                                        matricula_turma.ref_cod_turma = mt.ref_cod_turma)))";
        $whereAnd = " AND ";
      }
      else {
        $filtros .= "{$whereAnd} mt.ativo = '0'";
        $whereAnd = " AND ";
      }
    }

    if (!is_null($bool_matricula_ativo) && is_bool($bool_matricula_ativo)) {
      if ($bool_matricula_ativo) {
        $filtros .= "{$whereAnd} m.ativo = '1'";
        $whereAnd = " AND ";
      }
      else {
        $filtros .= "{$whereAnd} m.ativo = '0'";
        $whereAnd = " AND ";
      }
    }

    if (is_numeric($int_ref_cod_serie)) {
      if (!is_numeric($int_ref_cod_serie_mult)) {
        $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
        $whereAnd = " AND ";
      }
      else {
        $filtros .= "{$whereAnd} (m.ref_ref_cod_serie = '{$int_ref_cod_serie}' OR ref_ref_cod_serie_mult='{$int_ref_cod_serie_mult}')";
        $whereAnd = " AND ";
      }
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_aluno)) {
      $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ultima_matricula)) {
      $filtros .= "{$whereAnd} m.ultima_matricula = '{$int_ultima_matricula}'";
      $whereAnd = " AND ";
    }

    if ($apenasTurmasMultiSeriadas === TRUE) {
        $etapas = implode(',', App_Model_Educacenso::etapas_multisseriadas());
        $filtros .= "{$whereAnd} t.etapa_educacenso IN ({$etapas}) ";
        $whereAnd = " AND ";
    }

    if ($apenasTurmasUnificadas === TRUE) {
      $etapas = implode(',', App_Model_Educacenso::etapasEnsinoUnificadas());
      $filtros .= "{$whereAnd} t.etapa_educacenso IN ({$etapas}) ";
      $whereAnd = " AND ";
    }

    if (is_array($aprovado)) {
      $filtros .= "{$whereAnd} ( ";
      $whereAnd = "";

      foreach ($aprovado as $value) {
        $filtros .= "{$whereAnd} m.aprovado = '{$value}'";
        $whereAnd = " OR ";
      }

      $filtros .= " )";
      $whereAnd = " AND ";
    }
    elseif (is_numeric($aprovado)) {
      $filtros .= "{$whereAnd} m.aprovado = '{$aprovado}' ";
      $whereAnd = " AND ";
    }

    if (is_bool($bool_aprovados_reprovados)) {
      if ($bool_aprovados_reprovados == true) {
        $filtros .= "{$whereAnd} ( m.aprovado = '1'";
        $whereAnd = " OR ";
        $filtros .= "{$whereAnd} m.aprovado = '2' )";
        $whereAnd = " AND ";
      }
    }

    if ($int_ano_matricula) {
      $int_ano_matricula = (int) $int_ano_matricula;
      $filtros .= "{$whereAnd} m.ano = '{$int_ano_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_semestre))  {
      $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
      $whereAnd = " AND ";
    }

    if ($mes) {
      $mes = (int) $mes;
        $filtros .= "{$whereAnd} ( to_char(mt.data_cadastro,'MM')::int = '$mes'
                      OR to_char(mt.data_exclusao,'MM')::int = '$mes' )";
      $whereAnd = " AND ";
    }

    if ($mes_menor_que) {
      $mes_menor_que = (int) $mes_menor_que;
        $filtros .= "{$whereAnd} ( ( to_char(mt.data_cadastro,'MM')::int < '$mes_menor_que' AND mt.data_exclusao  IS NULL )
                    OR ( to_char(mt.data_exclusao,'MM')::int < '$mes_menor_que'  AND mt.data_exclusao  IS NOT NULL ) )";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_sequencial)) {
      $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_turma_turno_id)) {
      $filtros .= "{$whereAnd} t.turma_turno_id = '{$int_turma_turno_id}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ano_turma)) {
      $filtros .= "{$whereAnd} t.ano = '{$int_ano_turma}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($mes_matricula_inicial)) {
      $filtros .= "AND ((TO_CHAR(mt.data_cadastro,'MM')::int < '{$mes_matricula_inicial}'
                  AND NOT EXISTS (SELECT 1
                                FROM pmieducar.transferencia_solicitacao ts
                             WHERE ref_cod_matricula_saida = m.cod_matricula
                               AND to_char(ts.data_cadastro,'MM')::int < '{$mes_matricula_inicial}' )
                    )
                    OR (TO_CHAR(mt.data_cadastro,'MM')::int < '{$mes_matricula_inicial}'
                         AND EXISTS (SELECT 1
                           FROM pmieducar.transferencia_solicitacao
                          WHERE ref_cod_matricula_saida = m.cod_matricula
                            AND (TO_CHAR(data_transferencia,'MM')::int = '{$mes_matricula_inicial}' OR m.aprovado = 3))
                    )
              )
              and not(TO_CHAR(mt.data_exclusao,'MM')::int < '$mes_matricula_inicial' and mt.ativo = 0)
              ";
    }

    if ($diario){
      $filtros .= "{$whereAnd} (m.aprovado <> 6 OR mt.abandono)";
      $whereAnd = " AND ";
    }
    if (is_string($dependencia)) {
      $filtros .= "{$whereAnd} m.dependencia = '{$dependencia}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();
    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    if ($parar) {
      die($sql);
    }

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} mt, cadastro.pessoa , {$this->_schema}matricula m, {$this->_schema}aluno al, {$this->_schema}curso c, {$this->_schema}turma t, {$this->_schema}instituicao i{$tab_aluno} {$from} {$filtros} {$where}");
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

  function lista2($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL, $int_ref_cod_serie = NULL, $int_ref_cod_curso = NULL,
    $int_ref_cod_escola = NULL, $int_ref_cod_instituicao = NULL,
    $int_ref_cod_aluno = NULL, $em_andamento = TRUE, $mes = NULL, $aprovado = NULL,
    $mes_menor_que = NULL, $int_sequencial = NULL, $int_ano_matricula = NULL
  )
  {
    $sql = "SELECT {$this->_campos_lista}, m.ref_ref_cod_serie, m.ref_cod_curso, m.ref_ref_cod_escola, c.ref_cod_instituicao, i.nm_instituicao, m.ref_cod_aluno,t.nm_turma,s.nm_serie,c.nm_curso FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}instituicao i";
    $filtros = "";

    $whereAnd = " WHERE mt.ref_cod_matricula = m.cod_matricula AND m.ref_cod_curso = c.cod_curso AND mt.ref_cod_turma = t.cod_turma AND c.ref_cod_instituicao = i.cod_instituicao AND";

    if (is_numeric($int_ref_cod_matricula)) {
      $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_turma)) {
      $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} mt.ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} mt.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_serie)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_aluno)) {
      $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
      $whereAnd = " AND ";
    }

    if (!is_numeric($aprovado)) {
      if ($em_andamento == TRUE) {
        $filtros .= "{$whereAnd} (m.aprovado = '3'";
        $whereAnd = " OR ";
        $filtros .= "{$whereAnd} m.aprovado = '7')";
        $whereAnd = " AND ";
      }
    }

    if($int_ano_matricula) {
      $int_ano_matricula = (int) $int_ano_matricula;
      $filtros .= "{$whereAnd} (to_char(m.ano,'YYYY')::int = '$int_ano_matricula')";
      $whereAnd = " AND ";
    }

    if ($mes) {
      $mes = (int) $mes;
        $filtros .= "{$whereAnd} (to_char(mt.data_cadastro,'MM')::int = '$mes'
                      OR to_char(mt.data_exclusao,'MM')::int = '$mes')";
      $whereAnd = " AND ";
    }

    if($mes_menor_que) {
      $mes_menor_que = (int) $mes_menor_que;
        $filtros .= "{$whereAnd} (to_char(mt.data_cadastro,'MM')::int < '$mes_menor_que'
                      OR to_char(mt.data_exclusao,'MM')::int < '$mes_menor_que')";
      $whereAnd = " AND ";
    }

    if(is_numeric($aprovado)) {
        $filtros .= "{$whereAnd} m.aprovado = '$aprovado'";
        $whereAnd = " AND ";
    }

    if (is_numeric($int_sequencial)) {
      $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}turma t left outer join {$this->_schema}serie s on (t.ref_ref_cod_serie = s.cod_serie), {$this->_schema}instituicao i {$filtros}" );

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

  function lista3($int_ref_cod_matricula = NULL, $int_ref_cod_turma = NULL,
    $int_ref_usuario_exc = NULL, $int_ref_usuario_cad = NULL,
    $date_data_cadastro_ini = NULL, $date_data_cadastro_fim = NULL,
    $date_data_exclusao_ini = NULL, $date_data_exclusao_fim = NULL,
    $int_ativo = NULL, $int_ref_cod_serie = NULL, $int_ref_cod_curso = NULL,
    $int_ref_cod_escola = NULL, $int_ref_cod_aluno = NULL, $aprovado = NULL,
    $int_sequencial = NULL, $int_ano_matricula = NULL, $int_ultima_matricula = NULL,
    $int_matricula_ativo = NULL, $int_semestre = NULL
  ) {
    $sql = "SELECT {$this->_campos_lista}, c.nm_curso, s.nm_serie, t.nm_turma, c.ref_cod_instituicao, m.ref_ref_cod_escola, m.ref_cod_curso, m.ref_ref_cod_serie, m.ref_cod_aluno, p.nome,a.tipo_responsavel,f.data_nasc FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}serie s, {$this->_schema}turma t, {$this->_schema}aluno a, cadastro.pessoa p, cadastro.fisica f {$join}";
    $filtros = "";

    $whereAnd = " WHERE mt.ref_cod_matricula = m.cod_matricula AND m.ref_cod_curso = c.cod_curso AND t.cod_turma = mt.ref_cod_turma AND s.cod_serie = m.ref_ref_cod_serie AND a.cod_aluno = m.ref_cod_aluno AND p.idpes = a.ref_idpes AND p.idpes = f.idpes AND";

    if (is_numeric($int_ref_cod_matricula)) {
      $filtros .= "{$whereAnd} mt.ref_cod_matricula = '{$int_ref_cod_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_turma)) {
      $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$int_ref_cod_turma}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_exc)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_exc = '{$int_ref_usuario_exc}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_usuario_cad)) {
      $filtros .= "{$whereAnd} mt.ref_usuario_cad = '{$int_ref_usuario_cad}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_ini)) {
      $filtros .= "{$whereAnd} mt.data_cadastro >= '{$date_data_cadastro_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_cadastro_fim)) {
      $filtros .= "{$whereAnd} mt.data_cadastro <= '{$date_data_cadastro_fim}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_ini)) {
      $filtros .= "{$whereAnd} mt.data_exclusao >= '{$date_data_exclusao_ini}'";
      $whereAnd = " AND ";
    }

    if (is_string($date_data_exclusao_fim)) {
      $filtros .= "{$whereAnd} mt.data_exclusao <= '{$date_data_exclusao_fim}'";
      $whereAnd = " AND ";
    }

    if (is_null($int_ativo) || $int_ativo) {
      $filtros .= "{$whereAnd} mt.ativo = '1'";
      $whereAnd = " AND ";
    }
    else {
      $filtros .= "{$whereAnd} mt.ativo = '0'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_serie)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$int_ref_cod_serie}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_curso)) {
      $filtros .= "{$whereAnd} m.ref_cod_curso = '{$int_ref_cod_curso}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_escola)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$int_ref_cod_escola}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_instituicao)) {
      $filtros .= "{$whereAnd} c.ref_cod_instituicao = '{$int_ref_cod_instituicao}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ref_cod_aluno)) {
      $filtros .= "{$whereAnd} m.ref_cod_aluno = '{$int_ref_cod_aluno}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_ultima_matricula)) {
      $filtros .= "{$whereAnd} m.ultima_matricula = '{$int_ultima_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_array($aprovado)) {
      $filtros .= "{$whereAnd} ( ";
      $whereAnd = "";

      foreach ($aprovado as $value) {
        $filtros .= "{$whereAnd} m.aprovado = '{$value}'";
        $whereAnd = " OR ";
      }

      $filtros .= " )";
      $whereAnd = " AND ";
    }
    elseif(is_numeric($aprovado)) {
      $filtros .= "{$whereAnd} m.aprovado = '{$aprovado}' ";
      $whereAnd = " AND ";
    }

    if($int_ano_matricula)
    {
      $int_ano_matricula = (int) $int_ano_matricula;
        $filtros .= "{$whereAnd} m.ano = '{$int_ano_matricula}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($int_sequencial)) {
      $filtros .= "{$whereAnd} mt.sequencial = '{$int_sequencial}'";
      $whereAnd = " AND ";
    }

    if (!is_null($int_matricula_ativo) && is_bool($int_matricula_ativo)) {
      if ($int_matricula_ativo) {
        $filtros .= "{$whereAnd} m.ativo = '1'";
        $whereAnd = " AND ";
      }
      else {
        $filtros .= "{$whereAnd} m.ativo = '0'";
        $whereAnd = " AND ";
      }
    }

    if (is_numeric($int_semestre)) {
      $filtros .= "{$whereAnd} m.semestre = '{$int_semestre}'";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count(explode(',', $this->_campos_lista));
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico("SELECT COUNT(0) FROM {$this->_tabela} mt, {$this->_schema}matricula m, {$this->_schema}curso c, {$this->_schema}serie s, {$this->_schema}turma t, {$this->_schema}aluno a, cadastro.pessoa p, cadastro.fisica f {$join} {$filtros}");
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

  function lista4($escolaId = NULL, $cursoId = NULL, $serieId = NULL, $turmaId = NULL, $ano = NULL, $saida_escola = FALSE){

    $sql = "SELECT {$this->_campos_lista}, ref_cod_aluno, m.aprovado
              FROM {$this->_tabela} mt
             INNER JOIN {$this->_schema}matricula m ON (m.cod_matricula = mt.ref_cod_matricula)";
    $filtros = " WHERE m.ativo = 1 AND mt.ativo = 1 ";

    $whereAnd = " AND ";

    if (is_numeric($escolaId)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_escola = '{$escolaId}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($cursoId)) {
      $filtros .= "{$whereAnd} m.ref_cod_curso = '{$cursoId}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($serieId)) {
      $filtros .= "{$whereAnd} m.ref_ref_cod_serie = '{$serieId}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($turmaId)) {
      $filtros .= "{$whereAnd} mt.ref_cod_turma = '{$turmaId}'";
      $whereAnd = " AND ";
    }

    if (is_numeric($ano)) {
      $filtros .= "{$whereAnd} m.ano = '{$ano}'";
      $whereAnd = " AND ";
    }

    if ($saida_escola == 1) {
      $filtros .= "{$whereAnd} m.saida_escola = TRUE";
      $whereAnd = " AND ";
    }

    $db = new clsBanco();
    $countCampos = count( explode( ",", $this->_campos_lista ) );
    $resultado = array();

    $sql .= $filtros . $this->getOrderby() . $this->getLimite();

    $this->_total = $db->CampoUnico( "SELECT COUNT(0) FROM  {$this->_schema}matricula_turma mt
                                       INNER JOIN {$this->_schema}matricula m ON (m.cod_matricula = mt.ref_cod_matricula) {$filtros}" );

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

  function listaPorSequencial($codTurma) {

      $db = new clsBanco();
      $sql ="SELECT nome,
                    sequencial_fechamento,
                    ref_cod_matricula
                FROM cadastro.pessoa
              INNER JOIN pmieducar.aluno ON (aluno.ref_idpes = pessoa.idpes)
              INNER JOIN pmieducar.matricula ON (matricula.ref_cod_aluno = aluno.cod_aluno)
              INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
              WHERE matricula.ativo = 1
                AND (CASE WHEN matricula_turma.ativo = 1 THEN TRUE
                    WHEN matricula_turma.transferido THEN TRUE
                    WHEN matricula_turma.remanejado THEN TRUE
                    WHEN matricula.dependencia THEN TRUE
                    WHEN matricula_turma.abandono THEN TRUE
                    WHEN matricula_turma.reclassificado THEN TRUE
                    ELSE FALSE END)
                AND ref_cod_turma = {$codTurma}
                AND matricula_turma.sequencial = (SELECT MAX(sequencial)
                                    FROM pmieducar.matricula_turma mt
                                    WHERE mt.ref_cod_matricula = matricula_turma.ref_cod_matricula
                                    AND mt.ref_cod_turma = matricula_turma.ref_cod_turma)
                ORDER BY sequencial_fechamento, nome";

    $db->Consulta($sql);

    while ($db->ProximoRegistro()) {
      $tupla = $db->Tupla();

      $tupla["_total"] = $this->_total;
      $resultado[] = $tupla;
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
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
      is_numeric($this->sequencial)) {

      $db = new clsBanco();
      $db->Consulta("SELECT {$this->_todos_campos} FROM {$this->_tabela} mt WHERE mt.ref_cod_matricula = '{$this->ref_cod_matricula}' AND mt.ref_cod_turma = '{$this->ref_cod_turma}' AND mt.sequencial = '{$this->sequencial}'");
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
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
      is_numeric($this->sequencial)) {
      $db = new clsBanco();
      $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' AND sequencial = '{$this->sequencial}' AND ativo = 1 " );
      $db->ProximoRegistro();
      return $db->Tupla();
    }

    return FALSE;
  }

    /**
     * Retorna se existe alguma enturmação ativa para matrícula e turma informada.
     * @return bool
     */
    function existeEnturmacaoAtiva()
    {
        if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) ) {
            $db = new clsBanco();
            $db->Consulta( "SELECT 1 FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}' AND ref_cod_turma = '{$this->ref_cod_turma}' AND ativo = 1 " );
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
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma) &&
      is_numeric($this->ref_usuario_exc) && is_numeric($this->sequencial)) {
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

  function buscaSequencialMax()
  {
    if (is_numeric($this->ref_cod_matricula) && is_numeric($this->ref_cod_turma)) {
      $db = new clsBanco();
      $max = $db->CampoUnico("SELECT COALESCE(MAX(sequencial),0) + 1 AS MAX FROM {$this->_tabela} WHERE ref_cod_matricula = '{$this->ref_cod_matricula}'");

      //removido filtro pois tornou-se possivel enturmar uma matricula em mais de uma turma
      //AND ref_cod_turma = '{$this->ref_cod_turma}'");

      return $max;
    }

    return FALSE;
  }

  function alunosNaoEnturmados($ref_cod_escola = NULL, $ref_cod_serie = NULL,
    $ref_cod_curso = NULL, $ano = NULL)
  {
    if ((is_numeric($ref_cod_escola) && is_numeric($ref_cod_serie)) ||
      is_numeric($ref_cod_curso)) {
      $db = new clsBanco();

      $sql = "SELECT
            m.cod_matricula
          FROM
            pmieducar.matricula m
          WHERE
            m.cod_matricula NOT IN
            (
              SELECT
                ref_cod_matricula
              FROM
                {$this->_tabela} mt,
                   pmieducar.turma t
              WHERE
                t.cod_turma = mt.ref_cod_turma
                AND t.ref_ref_cod_escola = '{$ref_cod_escola}'
                AND mt.ativo = '1'
                AND t.ativo  = '1'";

      if ($ref_cod_curso) {
        $sql .= " AND m.ref_cod_curso = t.ref_cod_curso  ";
      }

      if ($ref_cod_serie) {
        $sql .= " AND m.ref_ref_cod_serie = t.ref_ref_cod_serie";
      }

      $sql .= ")
          AND m.ativo = '1'
          AND m.ultima_matricula = '1'
          AND
          (
            m.aprovado = '1'
            OR m.aprovado = '2'
            OR m.aprovado = '3'
          )";

      if ($ref_cod_curso) {
        $sql .= " AND m.ref_cod_curso = '{$ref_cod_curso}'";
      }

      if ($ref_cod_escola && $ref_cod_serie) {
        $sql .= " AND m.ref_ref_cod_serie = '{$ref_cod_serie}'
            AND m.ref_ref_cod_escola = '{$ref_cod_escola}'";
      }

      if (is_numeric($ano)) {
        $sql .= " AND m.ano = {$ano}";
      }

      $db->Consulta($sql);

      $resultado = array();
      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        $resultado[] = $tupla["cod_matricula"];
      }

      return $resultado;
    }

    return FALSE;
  }

  function getInstituicao(){
    if (is_numeric($this->ref_cod_matricula)){
      $db = new clsBanco();
      return $db->CampoUnico("SELECT ref_cod_instituicao from pmieducar.escola
                                              INNER JOIN pmieducar.matricula ON (ref_ref_cod_escola = cod_escola)
                                              WHERE cod_matricula = {$this->ref_cod_matricula}");
    }
    return false;
  }

  function getDataSaidaEnturmacaoAnterior($ref_matricula, $sequencial){
    if (is_numeric($ref_matricula) && is_numeric($sequencial)){
      $db = new clsBanco();
      return $db->CampoUnico("SELECT to_char(data_exclusao, 'YYYY-MM-DD')
                                FROM $this->_tabela
                               WHERE ref_cod_matricula = $ref_matricula
                                 AND sequencial < $sequencial
                               GROUP BY data_exclusao");
    }
    return false;
  }

  public function getDataExclusaoUltimaEnturmacao(int $codMatricula)
  {
    $db = new clsBanco();

    return $db->CampoUnico("
        select
            to_char(data_exclusao, 'YYYY-MM-DD')
        from
            pmieducar.matricula_turma
        where true
            and ref_cod_matricula = $codMatricula
            and data_exclusao is not null
        order by
            data_exclusao desc
        limit 1
    ");
  }


  public function getMaiorDataEnturmacao(int $codMatricula)
  {
    $db = new clsBanco();

    return $db->CampoUnico("
        select
            to_char(data_enturmacao, 'YYYY-MM-DD')
        from
            pmieducar.matricula_turma
        where true
            and ref_cod_matricula = $codMatricula
            and data_enturmacao is not null
        order by
            data_enturmacao desc
        limit 1
    ");
  }

 function getUltimaEnturmacao($ref_matricula){
    if (is_numeric($ref_matricula)){
      $db = new clsBanco();
      return $db->CampoUnico("SELECT MAX(matricula_turma.sequencial)
                                FROM $this->_tabela
                               INNER JOIN pmieducar.matricula ON (matricula.cod_matricula = matricula_turma.ref_cod_matricula)
                               INNER JOIN relatorio.view_situacao ON (view_situacao.cod_matricula = matricula.cod_matricula
                                                                      AND view_situacao.cod_turma = matricula_turma.ref_cod_turma
                                                                      AND view_situacao.sequencial = matricula_turma.sequencial)
                               WHERE ref_cod_matricula = $ref_matricula");
    }
    return false;
  }

  function getDataBaseRemanejamento(){
    if ($this->ref_cod_matricula){
      $cod_instituicao = $this->getInstituicao();
      $db = new clsBanco();
      return $db->CampoUnico("SELECT data_base_remanejamento
                                                    FROM pmieducar.instituicao WHERE cod_instituicao = {$cod_instituicao}");
    }
    return false;
  }

  function getDataBaseTransferencia(){
    if ($this->ref_cod_matricula){
      $cod_instituicao = $this->getInstituicao();
      $db = new clsBanco();
      return $db->CampoUnico("SELECT data_base_transferencia
                                                  FROM pmieducar.instituicao WHERE cod_instituicao = {$cod_instituicao}");
    }
    return false;
  }

  function marcaAlunoRemanejado($data = null){

    if ($this->ref_cod_matricula && $this->sequencial){

      $dataBaseRemanejamento = $this->getDataBaseRemanejamento();
      $data = $data ? $data : date('Y-m-d');
        if (is_null($dataBaseRemanejamento) || strtotime($dataBaseRemanejamento) < strtotime($data) ) {
          $db = new clsBanco();
          $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = true, abandono = false, reclassificado = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
        }
    }
  }

  function marcaAlunoTransferido($data = null){

    if ($this->ref_cod_matricula && $this->sequencial){

      $dataBaseTransferencia = $this->getDataBaseTransferencia();
      $data = $data ? $data : date('Y-m-d');
      if (is_null($dataBaseTransferencia) || strtotime($dataBaseTransferencia) < strtotime($data)) {
        $db = new clsBanco();
        $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = true, remanejado = false, abandono = false, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
      }else {
        $db = new clsBanco();
        $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = true, remanejado = false, abandono = false, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
      }
    }
  }

  function marcaAlunoReclassificado($data = null){
    $data = $data ? $data : date('Y-m-d');
    if ($this->ref_cod_matricula){
        $db = new clsBanco();
        $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = false, reclassificado = true, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND ativo = 1");
    }
  }

  function marcaAlunoAbandono($data = null){
    $data =  $data ? implode( '-', array_reverse( explode( '/', $data ) ) ) : date('Y-m-d');
    if ($this->ref_cod_matricula && $this->sequencial){
        $db = new clsBanco();
        $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = true, reclassificado = false, falecido = false, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
    }
  }

  function marcaAlunoFalecido($data = null){
    $data =  $data ? implode( '-', array_reverse( explode( '/', $data ) ) ) : date('Y-m-d');
    if ($this->ref_cod_matricula && $this->sequencial){
        $db = new clsBanco();
        $db->CampoUnico("UPDATE pmieducar.matricula_turma SET transferido = false, remanejado = false, abandono = false, reclassificado = false, falecido = true, data_exclusao = '$data' WHERE ref_cod_matricula = {$this->ref_cod_matricula} AND sequencial = {$this->sequencial}");
    }
  }

  function dadosAlunosNaoEnturmados($ref_cod_escola = NULL, $ref_cod_serie = NULL,
    $ref_cod_curso = NULL, $int_ano = NULL, $verificar_multiseriado = FALSE,
    $semestre = NULL)
  {
    if (is_numeric($int_ano) && (is_numeric($ref_cod_escola) ||
      is_numeric($ref_cod_serie) || is_numeric($ref_cod_curso))) {
      $db = new clsBanco();
      $complemento_sql = '';

      if ($verificar_multiseriado) {
        $complemento_sql = ", m.ref_ref_cod_escola
                  , m.cod_matricula";
      }

      $sql = "
      SELECT
        a.cod_aluno
        , p.nome
        , m.ref_ref_cod_serie
        , s.ref_cod_curso
        , s.nm_serie
        , c.nm_curso
        , f.sexo
        , f.data_nasc
        , a.tipo_responsavel
        {$complemento_sql}
      FROM
        pmieducar.matricula m
        , pmieducar.aluno a
        , cadastro.pessoa p
        , cadastro.fisica f
        , pmieducar.curso c
        , pmieducar.serie s
      WHERE
        m.cod_matricula NOT IN
        (
          SELECT
            ref_cod_matricula
          FROM
            pmieducar.matricula_turma mt
            , pmieducar.turma t
          WHERE
            t.cod_turma = mt.ref_cod_turma
            AND mt.ativo = '1'
            AND m.ref_cod_curso = t.ref_cod_curso
            AND m.ref_ref_cod_serie = t.ref_ref_cod_serie
        )
        AND a.ref_idpes = p.idpes
        AND p.idpes = f.idpes
        AND a.cod_aluno = m.ref_cod_aluno
        AND m.ref_ref_cod_serie = s.cod_serie
        AND s.ref_cod_curso = c.cod_curso
        AND m.ativo = '1'
        AND m.ano = '{$int_ano}'
        AND m.aprovado IN (1,2,3)
        AND m.ultima_matricula = '1'";

      if ($ref_cod_curso) {
        $sql .= " AND m.ref_cod_curso = '{$ref_cod_curso}'";
      }

      if ($ref_cod_serie) {
        $sql .= " AND m.ref_ref_cod_serie = '{$ref_cod_serie}'";
      }

      if ($ref_cod_escola) {
        $sql .= " AND m.ref_ref_cod_escola = '{$ref_cod_escola}'";
      }

      if (is_numeric($semestre)) {
        $sql .= " AND m.semestre = {$semestre} ";
      }

      $db->Consulta($sql . $this->getOrderby());

      $resultado = array();

      while ($db->ProximoRegistro()) {
        $tupla = $db->Tupla();
        if ($verificar_multiseriado) {
          if (is_numeric($tupla["ref_ref_cod_serie"]) && is_numeric($tupla["ref_ref_cod_escola"]) && is_numeric($tupla["cod_matricula"])) {
            $sql = "SELECT
                  1
                FROM
                  pmieducar.matricula_turma mt,
                  pmieducar.turma t
                WHERE
                  mt.ativo = 1
                  AND t.ativo = 1
                  AND t.ref_ref_cod_serie_mult = {$tupla["ref_ref_cod_serie"]}
                  AND t.ref_ref_cod_escola = {$tupla["ref_ref_cod_escola"]}
                  AND t.cod_turma = mt.ref_cod_turma
                  AND mt.ref_cod_matricula = {$tupla["cod_matricula"]}";
            $db3 = new clsBanco();
            $aluno_esta_enturmado = $db3->CampoUnico($sql);
          }

          if (!is_numeric($aluno_esta_enturmado)) {
            $resultado[] = $tupla;
          }
        }
        else {
          $resultado[] = $tupla;
        }
      }

      return $resultado;
    }

    return FALSE;
  }

  function reclassificacao($data = null)
  {
    if (is_numeric($this->ref_cod_matricula)) {
      $this->marcaAlunoReclassificado($data);
      $db = new clsBanco();
      $consulta = "UPDATE {$this->_tabela} SET ativo = 0 WHERE ref_cod_matricula = '{$this->ref_cod_matricula}'";
      $db->Consulta($consulta);

      return TRUE;
    }

    return FALSE;
  }

  function getAnoMatricula(){
    if (is_numeric($this->ref_cod_matricula)){
      $db = new clsBanco();
      return $db->CampoUnico("SELECT ano FROM pmieducar.matricula WHERE cod_matricula = {$this->ref_cod_matricula}");
    }
  }

  function enturmacoesSemDependencia($turmaId){
      $sql = "SELECT COUNT(1) FROM {$this->_tabela} mt
              INNER JOIN matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
              WHERE m.dependencia = 'f'
                AND mt.ativo = 1
                AND mt.ref_cod_turma = $turmaId";
      $db = new clsBanco();
      $db->Consulta($sql);
      $db->ProximoRegistro();
      return $db->Tupla();
  }

  function verficaEnturmacaoDeDependencia($matriculaId, $turmaId){
      $sql = "SELECT 1 FROM {$this->_tabela} mt
              INNER JOIN matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
              WHERE mt.ref_cod_matricula = $matriculaId
                AND m.dependencia = 't'
                AND mt.ativo = 1
                AND mt.ref_cod_turma = $turmaId";
      $db = new clsBanco();
      $db->Consulta($sql);
      $db->ProximoRegistro();
      return $db->Tupla();
  }

  function getMaxSequencialEnturmacao($matriculaId)
  {
      $db = new clsBanco();
      $sql = 'select max(sequencial) from pmieducar.matricula_turma where ref_cod_matricula = $1';

      if ($db->execPreparedQuery($sql, $matriculaId) != false) {
        $db->ProximoRegistro();
        $sequencial = $db->Tupla();
        return $sequencial[0];
      }
      return 0;
  }

  function getUltimaTurmaEnturmacao($matriculaId)
  {
      $sequencial = $this->getMaxSequencialEnturmacao($matriculaId);
      $db = new clsBanco();
      $sql = 'select ref_cod_turma from pmieducar.matricula_turma where ref_cod_matricula = $1 and sequencial = $2';

      if ($db->execPreparedQuery($sql, [$matriculaId, $sequencial]) != false) {
          $db->ProximoRegistro();
          $ultima_turma = $db->Tupla();
          return $ultima_turma[0];
      }
      return NULL;
  }

}
