<?php

namespace Tests\Unit\Modules\Unification;

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

class StudentLogUnificationTest extends TestCase
{
    use DatabaseTransactions;

    private StudentLogUnification $adapter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = new StudentLogUnification;
    }

    private function unification(array $attributes = []): LogUnification
    {
        return LogUnificationFactory::new()->create($attributes + [
            'type' => Student::class,
            'main_id' => LegacyStudentFactory::new()->create(),
            'duplicates_id' => '[]',
        ]);
    }

    private function student(string $socialName, int $active = 1): int
    {
        return LegacyStudentFactory::new()->create([
            'ref_idpes' => LegacyIndividualFactory::new()->create(['nome_social' => $socialName]),
            'ativo' => $active,
            'data_exclusao' => $active ? null : now(),
        ])->getKey();
    }

    public function test_main_person_name_is_the_student_name_or_a_notice_when_the_student_is_gone(): void
    {
        $this->assertSame('Ana Social', $this->adapter->getMainPersonName($this->unification(['main_id' => $this->student('Ana Social')])));

        $gone = $this->unification(['main_id' => $this->student('Bia Social', active: 0)]);

        $this->assertSame("Aluno removido pela unificação de pessoas ({$gone->main_id})", $this->adapter->getMainPersonName($gone));
    }

    public function test_duplicated_people_names_include_the_removed_students(): void
    {
        $ana = $this->student('Ana Social');
        $bia = $this->student('Bia Social', active: 0);

        $this->assertSame(['Ana Social', 'Bia Social'], $this->adapter->getDuplicatedPeopleName($this->unification([
            'duplicates_id' => json_encode([$ana, $bia]),
        ])));

        $this->assertSame(['Aluno(s) removido(s) pela unificação de pessoas (999999998,999999999)'], $this->adapter->getDuplicatedPeopleName($this->unification([
            'duplicates_id' => json_encode([999999998, 999999999]),
        ])));
    }

    public function test_type_is_the_student_view_model(): void
    {
        $this->assertSame(Student::class, StudentLogUnification::getType());
    }

    public function test_undo_writes_back_every_logged_row(): void
    {
        $unification = $this->unification();
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
    }
}
