<?php

use iEducar\Reports\Authenticate;
use iEducar\Reports\Html;

test('relatórios podem ser em HTML e autenticados quando pedido', function () {
    $report = new class
    {
        use Authenticate;
        use Html;

        public array $args = [];
    };

    expect($report->useHtml())->toBeTrue()
        ->and($report->authenticate())->toBeFalse();
});
