<?php

header('Content-type: text/xml; charset=UTF-8');

Portabilis_Utils_DeprecatedXmlApi::returnEmptyQueryUnlessUserIsLoggedIn();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<query xmlns=\"sugestoes\">\n";

$componentes = [];

// Seleciona os componentes de um curso ou série
if (is_numeric($_GET['cur']) || is_numeric($_GET['ser'])) {
    $mapper = new ComponenteCurricular_Model_AnoEscolarDataMapper();

    if (is_numeric($_GET['cur'])) {
        $componentes = $mapper->findComponentePorCurso($_GET['cur']);
    } elseif (is_numeric($_GET['ser'])) {
        $componentes = $mapper->findComponentePorSerie($_GET['ser']);
    }
}

// Seleciona os componentes de uma escola-série
if (is_numeric($_GET['esc']) && is_numeric($_GET['ser'])) {
    $componentes = App_Model_IedFinder::getEscolaSerieDisciplina(
        $_GET['ser'],
        $_GET['esc']
    );
}

foreach ($componentes as $componente) {
    print sprintf(
        ' <disciplina cod_disciplina="%d" carga_horaria="%d" docente_vinculado="%d">%s</disciplina>%s',
        $componente->id,
        $componente->cargaHoraria,
        $componente->docenteVinculado,
        $componente,
        PHP_EOL
    );
}

echo '</query>';
