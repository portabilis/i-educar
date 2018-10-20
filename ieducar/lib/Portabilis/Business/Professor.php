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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'include/pmieducar/clsPmieducarServidorAlocacao.inc.php';

/**
 * Portabilis_Business_Professor class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_Business_Professor {

  public static function isProfessor($instituicaoId, $userId) {
    if(is_numeric($instituicaoId)){
      $sql     = "select funcao.professor from pmieducar.servidor_funcao, pmieducar.funcao
                  where funcao.cod_funcao = servidor_funcao.ref_cod_funcao and funcao.professor = 1 and
                  servidor_funcao.ref_ref_cod_instituicao = $1 and servidor_funcao.ref_cod_servidor = $2";
      $options = array('params' => array($instituicaoId, $userId), 'return_only' => 'first-field');
    }else{
      $sql = "select funcao.professor from pmieducar.servidor_funcao, pmieducar.funcao
              where funcao.cod_funcao = servidor_funcao.ref_cod_funcao and funcao.professor = 1 and
              servidor_funcao.ref_cod_servidor = $1";
      $options = array('params' => array($userId), 'return_only' => 'first-field');
    }

    return self::fetchPreparedQuery($sql, $options) == '1';
  }


  public static function escolasAlocado($instituicaoId, $userId) {
    if (self::necessarioVinculoTurma($instituicaoId)){
      $sql = "SELECT e.cod_escola as id,
        (select juridica.fantasia from pmieducar.escola, cadastro.juridica
          where cod_escola = e.cod_escola and escola.ref_idpes = juridica.idpes limit 1) as nome,
                        ref_servidor as servidor_id,
        e.ref_cod_instituicao AS instituicao_id                
                        
        FROM pmieducar.quadro_horario qh
        INNER JOIN pmieducar.quadro_horario_horarios qhh ON (qh.cod_quadro_horario = qhh.ref_cod_quadro_horario)
        INNER JOIN pmieducar.escola e ON (e.cod_escola = qhh.ref_cod_escola)
        WHERE e.ref_cod_instituicao = $1
        AND qhh.ref_servidor = $2
        AND qh.ativo = 1
        AND qhh.ativo = 1
        ORDER BY nome";

    }else{
      $sql = "select ref_cod_escola as id, ref_cod_servidor as servidor_id, ref_ref_cod_instituicao as
                instituicao_id, (select juridica.fantasia from escola, cadastro.juridica
                where cod_escola = ref_cod_escola and escola.ref_idpes = juridica.idpes limit 1
                ) as nome, carga_horaria, periodo, hora_final, hora_inicial, dia_semana
                from pmieducar.servidor_alocacao where ref_ref_cod_instituicao = $1 and ref_cod_servidor  = $2
                and ativo = 1";
    }

    $options = array('params' => array($instituicaoId, $userId));
    return self::fetchPreparedQuery($sql, $options);
  }


  public static function cursosAlocado($instituicaoId, $escolaId, $userId){
    if (self::necessarioVinculoTurma($instituicaoId)){
      $sql = "SELECT c.cod_curso as id, c.nm_curso as nome
                FROM pmieducar.quadro_horario qh
                INNER JOIN pmieducar.quadro_horario_horarios qhh ON (qh.cod_quadro_horario = qhh.ref_cod_quadro_horario)
                INNER JOIN pmieducar.turma t ON (t.cod_turma = qh.ref_cod_turma)
                INNER JOIN pmieducar.serie s ON (t.ref_ref_cod_serie = s.cod_serie)
                INNER JOIN pmieducar.curso c ON (c.cod_curso = s.ref_cod_curso)
                WHERE qhh.ref_cod_escola = $1
                AND qhh.ref_servidor = $2
                AND qhh.ativo = 1
                AND qh.ativo = 1
                ORDER BY c.nm_curso";
      $options = array('params' => array($escolaId, $userId));
    }else{
      $sql = "select cod_curso as id, nm_curso as nome from pmieducar.servidor_curso_ministra,
              pmieducar.curso, pmieducar.escola_curso, pmieducar.escola
              where escola.ref_cod_instituicao = $1 and escola.cod_escola = $2
              and escola_curso.ref_cod_curso = cod_curso and escola_curso.ref_cod_escola = cod_escola
              and servidor_curso_ministra.ref_cod_curso = curso.cod_curso and ref_cod_servidor = $3";
      $options = array('params' => array($instituicaoId, $escolaId, $userId));
    }
    
    return self::fetchPreparedQuery($sql, $options);
  }

  public static function seriesAlocado($instituicaoId, $escolaId, $cursoId, $userId) {
      $instituicaoId = $instituicaoId ?: 0;
      $escolaId  = $escolaId ?: 0;
      $cursoId = $cursoId ?: 0;

    if (self::canLoadSeriesAlocado($instituicaoId)){
      $sql = "SELECT s.cod_serie as id, s.nm_serie as nome                
              FROM pmieducar.quadro_horario qh
              INNER JOIN pmieducar.quadro_horario_horarios qhh ON (qh.cod_quadro_horario = qhh.ref_cod_quadro_horario)
              INNER JOIN pmieducar.turma t ON (t.cod_turma = qh.ref_cod_turma)
              INNER JOIN pmieducar.serie s ON (t.ref_ref_cod_serie = s.cod_serie)
              INNER JOIN pmieducar.escola e ON (e.cod_escola = qhh.ref_cod_escola)
              WHERE e.ref_cod_instituicao = $1
              AND e.cod_escola = $2
              AND s.ref_cod_curso = $3
              AND qhh.ref_servidor = $4
              ORDER BY s.nm_serie";
      return self::fetchPreparedQuery($sql, array('params' => array($instituicaoId, $escolaId, $cursoId, $userId) ));
    }
  }

  public static function canLoadSeriesAlocado($instituicaoId){
    return self::necessarioVinculoTurma($instituicaoId);
  }

  public static function turmasAlocado($instituicaoId, $escolaId, $serieId, $userId) {
    if (self::necessarioVinculoTurma($instituicaoId)){
      $sql = "SELECT cod_turma as id, nm_turma as nome, t.ano
                FROM pmieducar.quadro_horario qh
                INNER JOIN pmieducar.quadro_horario_horarios qhh ON (qh.cod_quadro_horario = qhh.ref_cod_quadro_horario)
                INNER JOIN pmieducar.turma t ON (t.cod_turma = qh.ref_cod_turma)
                WHERE qhh.ref_cod_escola = $1
                AND qhh.ref_cod_serie = $2
                AND qhh.ref_servidor = $3
                AND qhh.ativo = 1
                AND qh.ativo = 1
                ORDER BY t.nm_turma ASC";
    }else{
      # Feito gambiarra para que quando professor tenha alocação liste todas turmas do turno integral
      $sql = "SELECT cod_turma as id, nm_turma as nome from pmieducar.turma where ref_ref_cod_escola = $1
              and (ref_ref_cod_serie = $2 or ref_ref_cod_serie_mult = $2) and ativo = 1 and
              visivel != 'f' and (turma_turno_id in ( select periodo from servidor_alocacao where
              ref_cod_escola = ref_ref_cod_escola and ref_cod_servidor = $3 and ativo = 1) OR ( turma_turno_id = 4 AND (select 1 from servidor_alocacao where
                            ref_cod_escola = ref_ref_cod_escola and ref_cod_servidor = $3 and ativo = 1 LIMIT 1) IS NOT NULL ))
              order by nm_turma asc";
    }

    return self::fetchPreparedQuery($sql, array('params' => array($escolaId, $serieId, $userId)));    
  }


  public static function componentesCurricularesAlocado($instituicaoId, $turmaId, $anoLetivo, $userId) {
    if (self::necessarioVinculoTurma($instituicaoId)){
      $sql = "SELECT cc.id, cc.nome, ac.nome as area_conhecimento, ac.secao as secao_area_conhecimento
                FROM pmieducar.quadro_horario qh
                INNER JOIN pmieducar.quadro_horario_horarios qhh ON (qh.cod_quadro_horario = qhh.ref_cod_quadro_horario)
                INNER JOIN modules.componente_curricular cc ON (cc.id = qhh.ref_cod_disciplina)
                INNER JOIN modules.area_conhecimento ac ON (cc.area_conhecimento_id = ac.id)
                WHERE qh.ref_cod_turma = $1
                AND qhh.ref_servidor = $2
                AND qhh.ativo = 1
                AND qh.ativo = 1
                ORDER BY ac.secao, ac.nome, cc.nome";
      $componentes = self::fetchPreparedQuery($sql, array('params' => array($turmaId, $userId)));
    }else{
      $componentes = self::componentesCurricularesTurmaAlocado($turmaId, $anoLetivo, $userId);

      if (empty($componentes))
        $componentes = self::componentesCurricularesCursoAlocado($turmaId, $anoLetivo, $userId);
    }
    return $componentes;
  }


 protected static function componentesCurricularesTurmaAlocado($turmaId, $anoLetivo, $userId) {
    $sql = "select cc.id, cc.nome, ac.nome as area_conhecimento, ac.secao as secao_area_conhecimento
            from modules.componente_curricular_turma as cct, pmieducar.turma, modules.componente_curricular as cc, modules.area_conhecimento as ac,
            pmieducar.escola_ano_letivo as al, pmieducar.servidor_disciplina as scc
            where turma.cod_turma = $1  and cct.turma_id = turma.cod_turma and cct.escola_id = turma.ref_ref_cod_escola
            and cct.componente_curricular_id = cc.id and al.ano = $2 and cct.escola_id = al.ref_cod_escola and
            scc.ref_ref_cod_instituicao = turma.ref_cod_instituicao and scc.ref_cod_servidor = $3 and
            scc.ref_cod_curso = turma.ref_cod_curso and scc.ref_cod_disciplina = cc.id and cc.area_conhecimento_id = ac.id
            order by ac.secao, ac.nome, cc.nome";

    $options = array('params' => array($turmaId, $anoLetivo, $userId));

    return self::fetchPreparedQuery($sql, $options);
  }


  protected static function componentesCurricularesCursoAlocado($turmaId, $anoLetivo, $userId) {
    $sql = "select cc.id as id, cc.nome as nome, ac.nome as area_conhecimento, ac.secao as secao_area_conhecimento from pmieducar.serie, pmieducar.escola_serie_disciplina as esd,
            pmieducar.turma, modules.componente_curricular as cc, modules.area_conhecimento as ac, pmieducar.escola_ano_letivo as al,
            pmieducar.servidor_disciplina as scc where turma.cod_turma = $1 and serie.cod_serie =
            turma.ref_ref_cod_serie and esd.ref_ref_cod_escola = turma.ref_ref_cod_escola and esd.ref_ref_cod_serie =
            serie.cod_serie and esd.ref_cod_disciplina = cc.id and al.ano = $2 and esd.ref_ref_cod_escola =
            al.ref_cod_escola and serie.ativo = 1 and esd.ativo = 1 and al.ativo = 1 and scc.ref_ref_cod_instituicao =
            turma.ref_cod_instituicao and scc.ref_cod_servidor = $3 and scc.ref_cod_curso = serie.ref_cod_curso and
            scc.ref_cod_disciplina = cc.id and cc.area_conhecimento_id = ac.id
            order by ac.secao, ac.nome, cc.nome";

    $options = array('params' => array($turmaId, $anoLetivo, $userId));

    return self::fetchPreparedQuery($sql, $options);
  }


  /*public static function alocacoes($instituicaoId, $escolaId, $userId) {
    $alocacoes = new ClsPmieducarServidorAlocacao();
    return $alocacoes->lista(null, $instituicaoId, null, null, $escolaId, $userId);
  }*/

  // wrappers for Portabilis*Utils*

  protected static function fetchPreparedQuery($sql, $options = array()) {
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }

  private static function necessarioVinculoTurma($instituicaoId){
    $sql = "SELECT exigir_vinculo_turma_professor FROM pmieducar.instituicao WHERE cod_instituicao = $1";
    return self::fetchPreparedQuery($sql, array('params' => array($instituicaoId), 'return_only' => 'first-field')) == 1;
  }
}