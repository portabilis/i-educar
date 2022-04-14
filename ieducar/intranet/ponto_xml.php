<?php

// Id do país na tabela public.pais
$id = isset($_GET['rota']) ? $_GET['rota'] : null;

header('Content-type: text/xml; charset=UTF-8');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

print '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
print '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {
    $obj = new clsModulesItinerarioTransporteEscolar();
    $obj->setOrderBy(' seq asc ');
    $pontos = $obj->listaPontos($id);

    $c=0;
    foreach ($pontos as $reg) {
        print sprintf(
            '  <ponto cod_ponto="%s">%s</ponto>' . PHP_EOL,
            $reg['ref_cod_ponto_transporte_escolar'],
            $reg['descricao'].' - '.($reg['tipo']=='I'?'Ida':'Volta')
        );
    }
}

print '</query>';
