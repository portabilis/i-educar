<?php
/**
 * Retorna um XML formatado com UF e estado de um determinado país.
 *
 * @author  Eriksen Costa <eriksen.paixao_bs@cobra.com.br>
 * @version SVN: $Id$
 */

require_once 'include/pessoa/clsUf.inc.php';

// Id do país na tabela public.pais
$id = isset($_GET['pais']) ? $_GET['pais'] : NULL;

header('Content-type: text/xml; charset=iso-8859-1');
print '<?xml version="1.0" encoding="iso-8859-1"?>' . PHP_EOL;
print '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {
  $uf = new clsUf();
  $ufs = $uf->lista(NULL, NULL, $id, NULL, NULL, 'sigla_uf');

  foreach ($ufs as $uf) {
    print sprintf('  <estado sigla_uf="%s">%s</estado>' . PHP_EOL,
      $uf['sigla_uf'], $uf['nome']);
  }
}

print '</query>';