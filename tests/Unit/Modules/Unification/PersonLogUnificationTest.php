<?php

use App\Models\LogUnification;
use iEducar\Modules\Unification\PersonLogUnification;

test('sem pessoa principal o nome vem como não encontrada e desfazer não faz nada', function () {
    $unification = (new LogUnification)->setRelation('main', null);
    $adapter = new PersonLogUnification;

    expect($adapter->getMainPersonName($unification))->toBe('Pessoa não encontrada')
        ->and($adapter->undo($unification))->toBeNull();
});
