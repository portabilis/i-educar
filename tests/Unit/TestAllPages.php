<?php

namespace Tests\Unit;

use Database\Factories\LegacySchoolClassTypeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TestAllPages extends TestCase
{
    use DatabaseTransactions;
    use WithoutMiddleware;

    private array $pages = [
        LegacySchoolClassTypeFactory::class => [
            'educar_turma_tipo_lst.php',
            'educar_turma_tipo_cad.php',
            'educar_turma_tipo_cad.php?cod_turma_tipo',
            'educar_turma_tipo_det.php?cod_turma_tipo',
        ]
    ];

    public function setUp(): void
    {
        parent::setUp();
        $user = LegacyUserFactory::new()->admin()->create();
        $this->actingAs($user);
    }

    /** @test */
    public function allTestPage()
    {
        foreach ($this->pages as $factory => $pages) {
            $model = $factory::new()->create();
            foreach ($pages as $page) {
                if (str_contains($page, '?')) {
                    $query = explode('?', $page);
                    $_GET = [
                        $query[1] => $model->getKey()
                    ];
                }
                $response = $this->get('intranet/' . $page);
                $response->assertSuccessful();
            }
        }
    }
}
