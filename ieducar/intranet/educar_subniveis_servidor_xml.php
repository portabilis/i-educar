<?php

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (is_numeric($_GET['cod_nivel'])) {
    $obj_nivel = new clsPmieducarSubnivel();
    $lst_nivel = $obj_nivel->buscaSequenciaSubniveis($_GET['cod_nivel']);
    if ($lst_nivel) {
        foreach ($lst_nivel as $subnivel) {
            echo "  <subnivel cod_subnivel=\"{$subnivel['cod_subnivel']}\">{$subnivel['nm_subnivel']}</subnivel>\n";
        }
    }
}
echo '</query>';
