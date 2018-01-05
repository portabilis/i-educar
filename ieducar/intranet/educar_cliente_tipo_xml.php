<?php
/**
 *
 * @version SVN: $Id$
 * @author  Prefeitura Municipal de Itajaí
 * @updated 29/03/2007
 * Pacote: i-PLB Software Público Livre e Brasileiro
 *
 * Copyright (C) 2006   PMI - Prefeitura Municipal de Itajaí
 *                  ctima@itajai.sc.gov.br
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

header('Content-type: text/xml; charset=UTF-8');

require_once('include/clsBanco.inc.php');
require_once('include/funcoes.inc.php');

require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<query xmlns="sugestoes">' . PHP_EOL;

if (is_numeric($_GET['bib'])) {
  $db = new clsBanco();

  if (is_numeric($_GET['exemplar_tipo_id']))
    $filtroTipoExemplar = "ref_cod_exemplar_tipo = {$_GET['exemplar_tipo_id']} AND";
  else
    $filtroTipoExemplar = '';

  $sql = "
    SELECT
      DISTINCT(cod_cliente_tipo),
      nm_tipo,
      dias_emprestimo
    FROM
      pmieducar.cliente_tipo LEFT JOIN pmieducar.cliente_tipo_exemplar_tipo ON (cod_cliente_tipo = ref_cod_cliente_tipo)
    WHERE
      ref_cod_biblioteca = %s AND
      %s
      ativo = 1
    ORDER BY
      nm_tipo ASC";

  $sql = sprintf($sql, $_GET['bib'], $filtroTipoExemplar);
  $db->Consulta($sql);

  // Array com os códigos do resultado do SELECT
  $codigos = array();

  while ($db->ProximoRegistro())
  {
    list($cod, $nome, $dias_emprestimo) = $db->Tupla();

    // Evita trazer dias emprestimo de outros cadastros, no cadastro novo tipo de exemplar
    if (! is_numeric($_GET['exemplar_tipo_id']))
      $dias_emprestimo = '';

    // Se o código já foi utilizado, vai para o próximo resultado
    if (isset($codigos[$cod]))
      continue;

    $cliente_tag = '<cliente_tipo cod_cliente_tipo="%s" dias_emprestimo="%s">%s</cliente_tipo>';
    print sprintf($cliente_tag, $cod, $dias_emprestimo, $nome) . PHP_EOL;

    // Evita que se imprima o mesmo código novamente
    $codigos[$cod] = TRUE;
  }
}

print '</query>';
