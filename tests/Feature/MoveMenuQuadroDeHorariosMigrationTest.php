<?php

namespace Tests\Feature;

use App\Menu;
use App\Process;
use App\Services\MenuCacheService;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MoveMenuQuadroDeHorariosMigrationTest extends TestCase
{
    use DatabaseTransactions;

    private Menu $employeeRegisters;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employeeRegisters = MenuFactory::new()->create(['old' => 999911, 'process' => null]);
    }

    private function migration(): object
    {
        return require base_path('database/migrations/data/2026_10_06_223947_move_menu_quadro_de_horarios.php');
    }

    private function runMigration(): void
    {
        $this->migration()->up();
    }

    public function test_remove_menu_antigo_com_as_permissoes()
    {
        $legacy = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);
        $other = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 635]);

        $permission = LegacyMenuUserTypeFactory::new()->create(['menu_id' => $legacy->getKey()]);
        LegacyMenuUserTypeFactory::new()->create(['menu_id' => $legacy->getKey(), 'visualiza' => 0]);
        LegacyMenuUserTypeFactory::new()->create([
            'menu_id' => $other->getKey(),
            'ref_cod_tipo_usuario' => $permission->ref_cod_tipo_usuario,
        ]);

        $this->runMigration();

        $this->assertDatabaseMissing('public.menus', ['id' => $legacy->getKey()]);
        $this->assertDatabaseMissing('pmieducar.menu_tipo_usuario', ['menu_id' => $legacy->getKey()]);
        $this->assertDatabaseHas('public.menus', ['id' => $other->getKey()]);
        $this->assertDatabaseHas('pmieducar.menu_tipo_usuario', ['menu_id' => $other->getKey()]);
    }

    public function test_move_menu_novo_para_cadastros_de_servidores()
    {
        $schoolTools = MenuFactory::new()->create(['old' => Process::MENU_SCHOOL_TOOLS, 'process' => null]);
        $timetable = MenuFactory::new()->create([
            'parent_id' => $schoolTools->getKey(),
            'parent_old' => Process::MENU_SCHOOL_TOOLS,
            'process' => Process::TIMETABLE,
            'order' => 99,
        ]);

        $this->runMigration();

        $timetable->refresh();

        $this->assertSame($this->employeeRegisters->getKey(), $timetable->parent_id);
        $this->assertSame(999911, $timetable->parent_old);
        $this->assertSame(3, $timetable->order);
    }

    public function test_sem_menu_novo_remove_so_o_antigo()
    {
        $legacy = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);

        $this->runMigration();

        $this->assertDatabaseMissing('public.menus', ['id' => $legacy->getKey()]);
        $this->assertDatabaseMissing('public.menus', ['process' => Process::TIMETABLE]);
    }

    public function test_sem_menu_pai_nao_move_o_menu_novo()
    {
        $this->employeeRegisters->delete();

        $timetable = MenuFactory::new()->create(['process' => Process::TIMETABLE, 'order' => 99]);

        $this->runMigration();

        $timetable->refresh();

        $this->assertNull($timetable->parent_id);
        $this->assertSame(99, $timetable->order);
    }

    public function test_desfaz_a_mudanca_do_menu_novo()
    {
        $schoolTools = MenuFactory::new()->create(['old' => Process::MENU_SCHOOL_TOOLS, 'process' => null]);
        $timetable = MenuFactory::new()->create([
            'parent_id' => $schoolTools->getKey(),
            'parent_old' => Process::MENU_SCHOOL_TOOLS,
            'process' => Process::TIMETABLE,
            'order' => 99,
        ]);

        $this->runMigration();
        $this->migration()->down();

        $timetable->refresh();

        $this->assertSame($schoolTools->getKey(), $timetable->parent_id);
        $this->assertSame(Process::MENU_SCHOOL_TOOLS, $timetable->parent_old);
        $this->assertSame(99, $timetable->order);
    }

    public function test_limpa_o_cache_quando_so_move_o_menu_novo()
    {
        Cache::flush();

        MenuFactory::new()->create(['process' => Process::TIMETABLE]);
        MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 988102]);

        app(MenuCacheService::class)->getProcessMenuData(988102);

        $this->runMigration();

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData(988102);

        $this->assertNotCount(0, DB::getQueryLog());
    }

    public function test_limpa_o_cache_de_menu()
    {
        Cache::flush();

        MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);
        MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 988101]);

        app(MenuCacheService::class)->getProcessMenuData(988101);

        $this->runMigration();

        DB::enableQueryLog();
        DB::flushQueryLog();

        app(MenuCacheService::class)->getProcessMenuData(988101);

        $this->assertNotCount(0, DB::getQueryLog());
    }
}
