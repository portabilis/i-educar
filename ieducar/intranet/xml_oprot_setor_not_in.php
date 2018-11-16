<?php

require_once 'includes/bootstrap.php';
require_once 'Portabilis/Utils/DeprecatedXmlApi.php';
require_once 'include/protocol/geral.inc.php';

header('Content-type: text/xml');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryForDisabledApi();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

if (isset($_GET["setor_pai"])) {
    // Seleciona Filas de atendimento da instituicao
    $obj = new clsPmioprotResponsavelSetor();
    //$not_in = "select ref_cod_setor from pmioprot.responsavel_setor";
    $lista = $obj->lista(null, $_GET["setor_pai"], null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, $not_in);
    if ($lista) {
        foreach ($lista as $linha) {
            echo "  <item>{$linha['sgl_setor']}</item>\n";
            echo "  <item>{$linha['cod_setor']}</item>\n";
        }
    }
}

echo "</query>";
