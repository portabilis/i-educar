<?php

use App\Models\City;

header('Content-type: text/xml');

require_once 'include/clsBanco.inc.php';
require_once 'include/funcoes.inc.php';
require_once 'Portabilis/Utils/DeprecatedXmlApi.php';

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";
if (is_numeric($_GET['uf'])) {
    $cities = City::query()
        ->orderBy('name')
        ->where('state_id', $_GET['uf'])
        ->pluck('name', 'id');

    foreach ($cities as $id => $name) {
        echo " <municipio idmun=\"{$id}\">{$name}</municipio>\n";
    }
}
echo '</query>';
