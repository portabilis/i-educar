<?php

namespace Tests\Api;

use App\Process;
use Database\Factories\LegacyDisciplineAcademicYearFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class LegacyDisciplineListTest extends TestCase
{
    use DatabaseTransactions;

    private const ROUTE = '/intranet/educar_componente_curricular_lst.php';

    public function test_show_delete_action_for_discipline_without_grade(): void
    {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Livre 1177']);

        $this->get(self::ROUTE . '?' . http_build_query(['nome' => 'Componente Livre 1177']))
            ->assertOk()
            ->assertSee('data-id="' . $discipline->getKey() . '"', false);
    }

    public function test_show_blocked_action_for_discipline_with_grade(): void
    {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Vinculado 1177']);
        LegacyDisciplineAcademicYearFactory::new()->create(['componente_curricular_id' => $discipline]);

        $this->get(self::ROUTE . '?' . http_build_query(['nome' => 'Componente Vinculado 1177']))
            ->assertOk()
            ->assertDontSee('data-id="' . $discipline->getKey() . '"', false)
            ->assertSee('data-toggle="tooltip" title="Este componente está vinculado a uma ou mais séries e não pode ser excluído para preservar o histórico."', false);
    }

    public function test_escape_discipline_name_in_delete_action(): void
    {
        $this->actingAs(LegacyUserFactory::new()->admin()->create());

        LegacyDisciplineFactory::new()->create(['name' => 'Arte "Visual" <b>1177</b>']);

        $this->get(self::ROUTE . '?' . http_build_query(['nome' => 'Arte']))
            ->assertOk()
            ->assertSee('data-nome="Arte &quot;Visual&quot; &lt;b&gt;1177&lt;/b&gt;"', false);
    }

    public function test_show_delete_action_for_user_with_permission(): void
    {
        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current()->getKey(),
            'active_on_ieducar' => 1,
        ]);

        $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES)->create());

        $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Permitido 1177']);

        $this->get(self::ROUTE . '?' . http_build_query(['nome' => 'Componente Permitido 1177']))
            ->assertOk()
            ->assertSee('data-id="' . $discipline->getKey() . '"', false);
    }

    public function test_hide_delete_action_for_user_without_permission(): void
    {
        LegacyGeneralConfigurationFactory::new()->create([
            'ref_cod_instituicao' => LegacyInstitutionFactory::new()->current()->getKey(),
            'active_on_ieducar' => 1,
        ]);

        $this->actingAs(LegacyUserFactory::new()->institutional()->withAccess(Process::DISCIPLINES, remove: false)->create());

        $discipline = LegacyDisciplineFactory::new()->create(['name' => 'Componente Sem Permissao 1177']);

        $this->get(self::ROUTE . '?' . http_build_query(['nome' => 'Componente Sem Permissao 1177']))
            ->assertOk()
            ->assertSee('Componente Sem Permissao 1177')
            ->assertDontSee('data-id="' . $discipline->getKey() . '"', false);
    }
}
