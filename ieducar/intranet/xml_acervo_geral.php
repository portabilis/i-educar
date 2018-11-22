<?php

require_once 'includes/bootstrap.php';
require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmiacervo/geral.inc.php';
require_once 'include/Geral.inc.php';

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryForDisabledApi();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if ($_GET['ref_cod_fundo']) {
    $fundo = @$_GET['ref_cod_fundo'];
}

if ($_GET['ref_cod_grupo']) {
    $grupo = @$_GET['ref_cod_grupo'];
}

if ($_GET['ref_cod_serie']) {
    $serie = @$_GET['ref_cod_serie'];
}

if ($serie) {
    $Objcaixa = new clsPmiacervoCaixa();
    $ListaCaixas = $Objcaixa->lista(null, $serie, $fundo, $grupo);

    if ($ListaCaixas) {
        foreach ($ListaCaixas as $campo) {
            echo "<item>{$campo['cod_caixa']}</item>";
            echo "<item>{$campo['identificacao_caixa']}</item>";

        }
    }

} elseif (!isset($_GET['ref_cod_serie']) && $grupo) {
    $Objserie = new clsPmiacervoSerie();

    $Listaserie = $Objserie->lista(null, $grupo, $fundo);
    if ($Listaserie) {
        foreach ($Listaserie as $campo) {
            echo "<item>{$campo['cod_serie']}</item>";
            echo "<item>{$campo['sigla_serie']} - {$campo['nm_serie']}</item>";
        }
    }

} elseif (!isset($_GET['ref_cod_grupo']) && $fundo) {

    $ObjGrupo = new clsPmiacervoGrupo();

    $ListaGrupo = $ObjGrupo->lista(null, $fundo);
    if ($ListaGrupo) {
        foreach ($ListaGrupo as $campo) {
            echo "<item>{$campo['cod_grupo']}</item>";
            echo "<item>{$campo['sigla_grupo']} - {$campo['nm_grupo']}</item>";
        }
    }
}

echo "</query>";
