<?php

$iesMapper = new Educacenso_Model_IesDataMapper();
$iesUf = $iesMapper->findAll([], ['uf' => $_GET['uf']], ['nome' => 'ASC']);

// Adiciona "INSTITUIÇÃO NÃO CADASTRADA" nos resultados.
$iesUf = array_merge($iesUf, [$iesMapper->find(['ies' => 9999999])]);

header('Content-type: text/xml');

echo '<?xml version="1.0" encoding=""?>' . PHP_EOL;
echo '<query xmlns="sugestoes">' . PHP_EOL;

foreach ($iesUf as $ies) {
    echo sprintf('  <ies id="%d">%s</ies>', $ies->id, htmlspecialchars($ies->nome)) . PHP_EOL;
}

echo '</query>';
