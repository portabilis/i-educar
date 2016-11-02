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

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';

header('Content-type: text/xml');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
print '<query xmlns="sugestoes">' . "\n";

if (is_numeric($_GET['curso'])) {
  $cod_curso = $_GET['curso'];

  $db = new clsBanco();
  $consulta  = sprintf('SELECT padrao_ano_escolar FROM pmieducar.curso WHERE cod_curso = \'%d\'', $cod_curso);

  $padrao_ano_escolar = $db->CampoUnico($consulta);

  if ($padrao_ano_escolar == 1) {
    $ano = is_numeric($_GET['ano']) ? sprintf(' AND ref_ano = \'%d\'', $_GET['ano']) : '';

    $db->Consulta(sprintf("
      SELECT
        cod_modulo,
        sequencial || 'º ' || nm_tipo || ' - de ' || to_char(data_inicio,'dd/mm/yyyy') || ' até ' || to_char(data_fim,'dd/mm/yyyy'),
        ref_ano,
        sequencial
      FROM
        pmieducar.ano_letivo_modulo,
        pmieducar.modulo
      WHERE
        modulo.cod_modulo = ano_letivo_modulo.ref_cod_modulo
        AND modulo.ativo = 1
        %s
        AND ref_ref_cod_escola = '%s'
      ORDER BY
        data_inicio,
        data_fim ASC
    ", $ano, $_GET['esc']));

    if ($db->numLinhas()) {
      while ($db->ProximoRegistro()) {
        list($cod, $nome, $ano, $sequencial) = $db->Tupla();
        print sprintf('  <ano_letivo_modulo sequencial="%d" ano="%d" cod_modulo="%d">%s</ano_letivo_modulo>%s',
          $sequencial, $ano, $cod, $nome, "\n");
      }
    }
  }
  else {
    $ano       = $_GET['ano'];
    $cod_turma = $_GET['turma'];

    if (is_numeric($cod_turma)) {
      $db->Consulta(sprintf("
        SELECT
          ref_cod_modulo,
          nm_tipo || ' - de ' || to_char(data_inicio,'dd/mm/yyyy') || ' até ' || to_char(data_fim,'dd/mm/yyyy'),
          sequencial
        FROM
          pmieducar.turma_modulo,
          pmieducar.modulo
        WHERE
          modulo.cod_modulo = turma_modulo.ref_cod_modulo
          AND ref_cod_turma = '%d'
          AND to_char(data_inicio,'yyyy') = %d
        ORDER BY
          data_inicio,
          data_fim ASC
      ", $cod_turma, $ano));
    }
    if ($db->numLinhas()) {
      while ($db->ProximoRegistro()) {
        list($cod, $nome,$sequencial) = $db->Tupla();
        print sprintf('  <ano_letivo_modulo sequencial="%d" ano="{%d}" cod_modulo="%d">%s</ano_letivo_modulo>%s',
          $sequencial, $ano, $cod, $nome, "\n");
      }
    }
  }
}

print '</query>';