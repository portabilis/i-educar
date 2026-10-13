<?php

use App\Models\ReportsCount;
use Tests\EloquentModelTestCase;

uses(EloquentModelTestCase::class);

beforeEach(fn () => $this->eloquent(ReportsCount::class));

EloquentModelTestCase::registerModelTests();

test('valid increment count', function () {
    ReportsCount::updateOrCreate([
        'render' => 'html',
        'template' => 'relatorio_html',
        'success' => true,
        'date' => now(),
    ]);

    ReportsCount::updateOrCreate([
        'render' => 'html',
        'template' => 'relatorio_html',
        'success' => true,
        'date' => now(),
    ]);

    ReportsCount::updateOrCreate([
        'render' => 'html',
        'template' => 'relatorio_html',
        'success' => false,
        'date' => now(),
    ]);

    ReportsCount::updateOrCreate([
        'render' => 'html',
        'template' => 'relatorio_html',
        'success' => true,
        'date' => now(),
    ]);

    ReportsCount::updateOrCreate([
        'render' => 'html',
        'template' => 'relatorio_html',
        'success' => false,
        'date' => now(),
    ]);

    $this->assertDatabaseHas(
        ReportsCount::class,
        [
            'render' => 'html',
            'template' => 'relatorio_html',
            'success' => true,
            'date' => now(),
            'count' => 3,
        ]
    )->assertDatabaseHas(
        ReportsCount::class,
        [
            'render' => 'html',
            'template' => 'relatorio_html',
            'success' => false,
            'date' => now(),
            'count' => 2,
        ]
    );
});
