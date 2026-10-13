<?php

use App\Models\State;

header('Content-type: text/xml; charset=UTF-8');

$id = $_GET['pais'] ?? null;
$abbreviation = $_GET['abbreviation'] ?? null;

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
echo '<query>' . PHP_EOL;

if ($id == strval(intval($id))) {
    $key = 'id';

    if ($abbreviation) {
        $key = 'abbreviation';
    }

    $states = State::query()->where('country_id', $id)->pluck('name', $key);

    foreach ($states as $id => $name) {
        echo sprintf(
            '  <estado id="%s">%s</estado>' . PHP_EOL,
            htmlspecialchars($id, ENT_XML1 | ENT_QUOTES),
            htmlspecialchars($name, ENT_XML1)
        );
    }
}

echo '</query>';
