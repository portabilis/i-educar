<?php

namespace Tests\Unit\Eloquent;

use App\Models\ReportsCount;
use Tests\EloquentTestCase;

class ReportsCountTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return ReportsCount::class;
    }

    /** @test  */
    public function validIncrementCount()
    {
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
    }
}
