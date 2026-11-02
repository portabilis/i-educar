<?php

use App\Exceptions\Unification\InactiveMainUnification;
use App\Exceptions\Unification\UndoInactiveUnification;
use App\Exceptions\Unification\WithoutPermission;
use App\Models\LogUnification;
use App\Models\LogUnificationOldData;
use App\Models\Student;
use App\Services\StudentUnificationService;
use App\User;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LogUnificationFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Desfazer uma unificação de alunos: o que volta ao banco, o que marca o log
 * e quem pode desfazer o que outro usuário fez.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->service = function (Factory $factory): StudentUnificationService {
        return new StudentUnificationService(User::query()->findOrFail($factory->create()->getKey()));
    };

    $this->unification = function (array $attributes = []): LogUnification {
        return LogUnificationFactory::new()->create($attributes + [
            'type' => Student::class,
            'main_id' => LegacyStudentFactory::new()->create(),
            'duplicates_id' => '[]',
            'active' => true,
        ]);
    };
});

test('undo restores the logged rows and closes the unification', function () {
    $service = ($this->service)(LegacyUserFactory::new()->admin());
    $duplicate = LegacyStudentFactory::new()->create(['ativo' => 0]);
    $unification = ($this->unification)(['duplicates_id' => json_encode([$duplicate->getKey()])]);

    $old = new LogUnificationOldData;
    $old->unification_id = $unification->getKey();
    $old->table = 'pmieducar.aluno';
    $old->keys = json_encode([['cod_aluno' => $duplicate->getKey()]]);
    $old->old_data = json_encode(['ativo' => 1, 'codigo_sistema' => 'EXT-1']);
    $old->save();

    $service->undo($unification);

    $row = DB::table('pmieducar.aluno')->where('cod_aluno', $duplicate->getKey())->first();

    $this->assertSame(1, $row->ativo);
    $this->assertSame('EXT-1', $row->codigo_sistema);

    $unification->refresh();

    $this->assertFalse($unification->active);
    $this->assertSame($unification->updatedBy?->getKey(), $unification->updated_by);
});

test('cannot undo twice', function () {
    $this->expectException(UndoInactiveUnification::class);
    $this->expectExceptionMessage('A unificação já foi desfeita anteriormente.');

    ($this->service)(LegacyUserFactory::new()->admin())->canUndo(($this->unification)(['active' => false]));
});

test('cannot undo when the main student is gone', function () {
    $this->expectException(InactiveMainUnification::class);
    $this->expectExceptionMessage('O aluno está inativo ou foi unificado com outro aluno.');

    ($this->service)(LegacyUserFactory::new()->admin())->canUndo(($this->unification)([
        'main_id' => LegacyStudentFactory::new()->create(['ativo' => 0, 'data_exclusao' => now()]),
    ]));
});

test('school user undoes only what a user of the same school did', function () {
    $school = LegacySchoolFactory::new()->create();
    $service = ($this->service)(LegacyUserFactory::new()->school()->withSchool($school));
    $colleague = LegacyUserFactory::new()->school()->withSchool($school)->create();
    $stranger = LegacyUserFactory::new()->school()->withSchool(LegacySchoolFactory::new()->create())->create();

    $unification = ($this->unification)(['created_by' => $colleague->getKey()]);
    $service->undo($unification);

    $this->assertFalse($unification->refresh()->active);

    $this->expectException(WithoutPermission::class);
    $this->expectExceptionMessage('Você não tem permissão para desfazer essa unificação.');

    $service->canUndo(($this->unification)(['created_by' => $stranger->getKey()]));
});

test('institutional user undoes what institutional or school users did', function () {
    $service = ($this->service)(LegacyUserFactory::new()->institutional());
    $institutional = LegacyUserFactory::new()->institutional()->create();
    $schooling = LegacyUserFactory::new()->school()->create();
    $admin = LegacyUserFactory::new()->admin()->create();

    foreach ([$institutional, $schooling] as $owner) {
        $unification = ($this->unification)(['created_by' => $owner->getKey()]);
        $service->undo($unification);

        $this->assertFalse($unification->refresh()->active);
    }

    $this->expectException(WithoutPermission::class);

    $service->canUndo(($this->unification)(['created_by' => $admin->getKey()]));
});

test('admin and unifications without an owner have no permission check', function () {
    $admin = ($this->service)(LegacyUserFactory::new()->admin());
    $byAdmin = ($this->unification)(['created_by' => LegacyUserFactory::new()->admin()->create()->getKey()]);
    $admin->undo($byAdmin);

    $this->assertFalse($byAdmin->refresh()->active);

    // O log aponta para uma pessoa que não é usuário: ninguém é dono.
    $school = ($this->service)(LegacyUserFactory::new()->school()->withSchool(LegacySchoolFactory::new()->create()));
    $orphan = ($this->unification)();
    $school->undo($orphan);

    $this->assertFalse($orphan->refresh()->active);
});
