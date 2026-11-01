<?php

use iEducar\Support\Navigation\Breadcrumb;

test('há páginas para exibir quando existe um caminho ou só a página atual', function () {
    expect((new Breadcrumb)->hasPages())->toBeFalse()
        ->and((new Breadcrumb)->current('Alunos')->hasPages())->toBeTrue();
});
