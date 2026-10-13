<?php

use App\Models\LogUnification;
use App\Models\LogUnificationOldData;
use App\Models\Student;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LogUnificationFactory;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->adapter = new StudentLogUnification;

    $this->unification = function (array $attributes = []): LogUnification {
        return LogUnificationFactory::new()->create($attributes + [
            'type' => Student::class,
            'main_id' => LegacyStudentFactory::new()->create(),
            'duplicates_id' => '[]',
        ]);
    };

    $this->student = function (string $socialName, int $active = 1): int {
        return LegacyStudentFactory::new()->create([
            'ref_idpes' => LegacyIndividualFactory::new()->create(['nome_social' => $socialName]),
            'ativo' => $active,
            'data_exclusao' => $active ? null : now(),
        ])->getKey();
    };
});

test('main person name is the student name or a notice when the student is gone', function () {
    $this->assertSame('Ana Social', $this->adapter->getMainPersonName(($this->unification)(['main_id' => ($this->student)('Ana Social')])));

    $gone = ($this->unification)(['main_id' => ($this->student)('Bia Social', active: 0)]);

    $this->assertSame("Aluno removido pela unificação de pessoas ({$gone->main_id})", $this->adapter->getMainPersonName($gone));
});

test('duplicated people names include the removed students', function () {
    $ana = ($this->student)('Ana Social');
    $bia = ($this->student)('Bia Social', active: 0);

    $this->assertSame(['Ana Social', 'Bia Social'], $this->adapter->getDuplicatedPeopleName(($this->unification)([
        'duplicates_id' => json_encode([$ana, $bia]),
    ])));

    $this->assertSame(['Aluno(s) removido(s) pela unificação de pessoas (999999998,999999999)'], $this->adapter->getDuplicatedPeopleName(($this->unification)([
        'duplicates_id' => json_encode([999999998, 999999999]),
    ])));
});

test('type is the student view model', function () {
    $this->assertSame(Student::class, StudentLogUnification::getType());
});

test('undo writes back every logged row', function () {
    $unification = ($this->unification)();
    $students = [
        LegacyStudentFactory::new()->create(['ativo' => 0])->getKey(),
        LegacyStudentFactory::new()->create(['ativo' => 0])->getKey(),
    ];

    foreach ($students as $student) {
        $old = new LogUnificationOldData;
        $old->unification_id = $unification->getKey();
        $old->table = 'pmieducar.aluno';
        $old->keys = json_encode([['cod_aluno' => $student]]);
        $old->old_data = json_encode(['ativo' => 1, 'codigo_sistema' => "EXT-{$student}"]);
        $old->save();
    }

    $this->adapter->undo($unification);

    foreach ($students as $student) {
        $row = DB::table('pmieducar.aluno')->where('cod_aluno', $student)->first();

        $this->assertSame(1, $row->ativo);
        $this->assertSame("EXT-{$student}", $row->codigo_sistema);
    }
});
