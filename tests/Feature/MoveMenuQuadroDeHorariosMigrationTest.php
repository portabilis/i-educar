<?php

use App\Process;
use App\Services\MenuCacheService;
use Database\Factories\LegacyMenuUserTypeFactory;
use Database\Factories\MenuFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

function migrationMoveMenuQuadroDeHorarios(): object
{
    return require base_path('database/migrations/data/2026_10_06_223947_move_menu_quadro_de_horarios.php');
}

beforeEach(function () {
    $this->employeeRegisters = MenuFactory::new()->create(['old' => 999911, 'process' => null]);
});

test('remove menu antigo com as permissoes', function () {
    $legacy = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);
    $other = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 635]);

    $permission = LegacyMenuUserTypeFactory::new()->create(['menu_id' => $legacy->getKey()]);
    LegacyMenuUserTypeFactory::new()->create(['menu_id' => $legacy->getKey(), 'visualiza' => 0]);
    LegacyMenuUserTypeFactory::new()->create([
        'menu_id' => $other->getKey(),
        'ref_cod_tipo_usuario' => $permission->ref_cod_tipo_usuario,
    ]);

    migrationMoveMenuQuadroDeHorarios()->up();

    $this->assertDatabaseMissing('public.menus', ['id' => $legacy->getKey()]);
    $this->assertDatabaseMissing('pmieducar.menu_tipo_usuario', ['menu_id' => $legacy->getKey()]);
    $this->assertDatabaseHas('public.menus', ['id' => $other->getKey()]);
    $this->assertDatabaseHas('pmieducar.menu_tipo_usuario', ['menu_id' => $other->getKey()]);
});

test('move menu novo para cadastros de servidores', function () {
    $schoolTools = MenuFactory::new()->create(['old' => Process::MENU_SCHOOL_TOOLS, 'process' => null]);
    $timetable = MenuFactory::new()->create([
        'parent_id' => $schoolTools->getKey(),
        'parent_old' => Process::MENU_SCHOOL_TOOLS,
        'process' => Process::TIMETABLE,
        'order' => 99,
    ]);

    migrationMoveMenuQuadroDeHorarios()->up();

    $timetable->refresh();

    $this->assertSame($this->employeeRegisters->getKey(), $timetable->parent_id);
    $this->assertSame(999911, $timetable->parent_old);
    $this->assertSame(3, $timetable->order);
});

test('sem menu novo remove so o antigo', function () {
    $legacy = MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);

    migrationMoveMenuQuadroDeHorarios()->up();

    $this->assertDatabaseMissing('public.menus', ['id' => $legacy->getKey()]);
    $this->assertDatabaseMissing('public.menus', ['process' => Process::TIMETABLE]);
});

test('sem menu pai nao move o menu novo', function () {
    $this->employeeRegisters->delete();

    $timetable = MenuFactory::new()->create(['process' => Process::TIMETABLE, 'order' => 99]);

    migrationMoveMenuQuadroDeHorarios()->up();

    $timetable->refresh();

    $this->assertNull($timetable->parent_id);
    $this->assertSame(99, $timetable->order);
});

test('desfaz a mudanca do menu novo', function () {
    $schoolTools = MenuFactory::new()->create(['old' => Process::MENU_SCHOOL_TOOLS, 'process' => null]);
    $timetable = MenuFactory::new()->create([
        'parent_id' => $schoolTools->getKey(),
        'parent_old' => Process::MENU_SCHOOL_TOOLS,
        'process' => Process::TIMETABLE,
        'order' => 99,
    ]);

    migrationMoveMenuQuadroDeHorarios()->up();
    migrationMoveMenuQuadroDeHorarios()->down();

    $timetable->refresh();

    $this->assertSame($schoolTools->getKey(), $timetable->parent_id);
    $this->assertSame(Process::MENU_SCHOOL_TOOLS, $timetable->parent_old);
    $this->assertSame(99, $timetable->order);
});

test('limpa o cache quando so move o menu novo', function () {
    Cache::flush();

    MenuFactory::new()->create(['process' => Process::TIMETABLE]);
    MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 988102]);

    app(MenuCacheService::class)->getProcessMenuData(988102);

    migrationMoveMenuQuadroDeHorarios()->up();

    DB::enableQueryLog();
    DB::flushQueryLog();

    app(MenuCacheService::class)->getProcessMenuData(988102);

    $this->assertNotCount(0, DB::getQueryLog());
});

test('limpa o cache de menu', function () {
    Cache::flush();

    MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 641]);
    MenuFactory::new()->create(['parent_id' => $this->employeeRegisters->getKey(), 'process' => 988101]);

    app(MenuCacheService::class)->getProcessMenuData(988101);

    migrationMoveMenuQuadroDeHorarios()->up();

    DB::enableQueryLog();
    DB::flushQueryLog();

    app(MenuCacheService::class)->getProcessMenuData(988101);

    $this->assertNotCount(0, DB::getQueryLog());
});
