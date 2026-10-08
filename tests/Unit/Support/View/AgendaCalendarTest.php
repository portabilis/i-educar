<?php

use App\Support\View\AgendaCalendar;

test('sem data usa hoje e sem parâmetros no endereço começa a query com interrogação', function () {
    $calendar = new AgendaCalendar(null, 'agenda.php');

    expect($calendar->data)->toBe(date('d/m/Y'))
        ->and($calendar->url_uniao)->toBe('?');
});

test('no dia 31 os links dos meses mais curtos caem no último dia deles', function () {
    $html = (new AgendaCalendar(mktime(12, 0, 0, 1, 31, 2030), 'agenda.php'))->gera_calendario();

    expect($html)->toContain('value="' . mktime(12, 0, 0, 2, 28, 2030) . '"');
});
