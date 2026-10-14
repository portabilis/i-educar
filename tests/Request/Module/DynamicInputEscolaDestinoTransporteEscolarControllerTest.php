<?php

use Tests\Request\Support\LegacyApiScenario;

beforeEach(function () {
    LegacyApiScenario::create($this);
});

LegacyApiScenario::registerResourceTests('/module/DynamicInput/EscolaDestinoTransporteEscolar', [
    'escola_destino_transporte_escolar',
]);
