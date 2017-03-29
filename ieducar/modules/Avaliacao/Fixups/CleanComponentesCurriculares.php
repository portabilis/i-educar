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

require_once 'lib/Portabilis/Utils/Database.php';

/**
 * CleanComponentesCurriculares class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */

/* Ao desvincular componente curriculares, as notas, medias e faltas lançadas para estes continuam na base de dados
  impedindo que o aluno seja promovido, caso todas notas ou faltas destes componentes não tenham sido lançadas.
  para impedir isto, é removido as notas, medias e faltas lançadas para os componentes removidos.
*/

class CleanComponentesCurriculares {

  public static function destroyOldResources($anoEscolar, $cod_matricula = NULL) {
    self::destroyOldNotas($anoEscolar, $cod_matricula);
    self::destroyOldNotasMedias($anoEscolar, $cod_matricula);
    self::destroyOldFaltas($anoEscolar, $cod_matricula);
  }

  protected static function destroyOldNotas($anoEscolar, $cod_matricula) {
    $filtro = "";

    if (is_Numeric($cod_matricula))
      $filtro .= " m.cod_matricula = {$cod_matricula} AND ";

    $sql = "delete from modules.nota_componente_curricular where id in (
              select ncc.id from modules.nota_componente_curricular as ncc,
                     modules.nota_aluno as na,
                     pmieducar.matricula as m,
                     pmieducar.matricula_turma as mt

              where ncc.nota_aluno_id = na.id and
                    m.cod_matricula = na.matricula_id and
                    m.cod_matricula = mt.ref_cod_matricula and
                    m.ativo = 1 and
                    mt.ativo = m.ativo and
                    m.ano = $1 and
                    {$filtro}
                    --m.aprovado = 3 and

                    CASE WHEN (select 1 from modules.componente_curricular_turma
                               WHERE componente_curricular_turma.turma_id = mt.ref_cod_turma AND
                               componente_curricular_turma.escola_id = m.ref_ref_cod_escola limit 1) = 1 THEN

                      ncc.componente_curricular_id not in (select cct.componente_curricular_id from modules.componente_curricular_turma as cct where
                                                           cct.turma_id = mt.ref_cod_turma)


                    ELSE
                      ncc.componente_curricular_id not in (select ccs.ref_cod_disciplina from pmieducar.escola_serie_disciplina as ccs where
                                                           ccs.ref_ref_cod_serie = m.ref_ref_cod_serie and
                                                           ccs.ref_ref_cod_escola = m.ref_ref_cod_escola and ccs.ativo = 1)
                    END
            );";

    self::fetchPreparedQuery($sql, array('params' => $anoEscolar));
  }

  protected static function destroyOldNotasMedias($anoEscolar, $cod_matricula) {
    $filtro = "";

    if (is_Numeric($cod_matricula))
      $filtro .= " m.cod_matricula = {$cod_matricula} AND ";

    $sql = "delete from modules.nota_componente_curricular_media where nota_aluno_id::varchar||componente_curricular_id in (
              select nccm.nota_aluno_id::varchar|| nccm.componente_curricular_id from modules.nota_componente_curricular_media as nccm,
                     modules.nota_aluno as na,
                     pmieducar.matricula as m,
                     pmieducar.matricula_turma as mt

              where nccm.nota_aluno_id = na.id and
                    m.cod_matricula = na.matricula_id and
                    m.cod_matricula = mt.ref_cod_matricula and
                    m.ativo = 1 and
                    mt.ativo = m.ativo and
                    m.ano = $1 and
                    {$filtro}
                    --m.aprovado = 3 and

                    CASE WHEN (select 1 from modules.componente_curricular_turma
                               WHERE componente_curricular_turma.turma_id = mt.ref_cod_turma AND
                               componente_curricular_turma.escola_id = m.ref_ref_cod_escola limit 1) = 1 THEN

                      nccm.componente_curricular_id not in (select cct.componente_curricular_id from modules.componente_curricular_turma as cct where
                                                           cct.turma_id = mt.ref_cod_turma)


                    ELSE
                      nccm.componente_curricular_id not in (select ccs.ref_cod_disciplina from pmieducar.escola_serie_disciplina as ccs where
                                                           ccs.ref_ref_cod_serie = m.ref_ref_cod_serie and
                                                           ccs.ref_ref_cod_escola = m.ref_ref_cod_escola and ccs.ativo = 1)
                    END
            );";

    self::fetchPreparedQuery($sql, array('params' => $anoEscolar));
  }

  protected static function destroyOldFaltas($anoEscolar, $cod_matricula) {
    $filtro = "";

    if (is_Numeric($cod_matricula))
      $filtro .= " m.cod_matricula = {$cod_matricula} AND ";

    $sql = "delete from modules.falta_componente_curricular where id in (
              select fcc.id from modules.falta_componente_curricular as fcc,
                     modules.falta_aluno as fa,
                     pmieducar.matricula as m,
                     pmieducar.matricula_turma as mt

              where fcc.falta_aluno_id = fa.id and
                    m.cod_matricula = fa.matricula_id and
                    m.cod_matricula = mt.ref_cod_matricula and
                    m.ativo = 1 and
                    mt.ativo = m.ativo and
                    m.ano = $1 and
                    {$filtro}
                    --m.aprovado = 3 and

                    CASE WHEN (select 1 from modules.componente_curricular_turma
                               WHERE componente_curricular_turma.turma_id = mt.ref_cod_turma AND
                               componente_curricular_turma.escola_id = m.ref_ref_cod_escola limit 1) = 1 THEN

                      fcc.componente_curricular_id not in (select cct.componente_curricular_id from modules.componente_curricular_turma as cct where
                                                           cct.turma_id = mt.ref_cod_turma)

                    ELSE
                      fcc.componente_curricular_id not in (select ccs.ref_cod_disciplina from pmieducar.escola_serie_disciplina as ccs where
                                                           ccs.ref_ref_cod_serie = m.ref_ref_cod_serie and
                                                           ccs.ref_ref_cod_escola = m.ref_ref_cod_escola and ccs.ativo = 1)
                    END

                    );";
    self::fetchPreparedQuery($sql, array('params' => $anoEscolar));
  }

  // wrappers for Portabilis*Utils*

  protected static function fetchPreparedQuery($sql, $options = array()) {
    return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
  }

}
?>
