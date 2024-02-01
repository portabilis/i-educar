<?php

namespace Tests\Unit\Services;

use App\Jobs\EducacensoInepImportJob;
use App\Models\EducacensoInepImport;
use App\Models\Employee;
use App\Models\EmployeeInep;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStudent;
use App\Models\SchoolClassInep;
use App\Models\StudentInep;
use App\Services\Educacenso\EducacensoImportInepService;
use Database\Factories\EducacensoInepImportFactory;
use Database\Factories\EmployeeFactory;
use Database\Factories\LegacyGradeFactory;
use Database\Factories\LegacyInstitutionFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyStudentFactory;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class EducacensoInepImportServiceTest extends TestCase
{
    use DatabaseTransactions;

    private EducacensoInepImport $import;

    private Generator $schoolsData;

    private LegacySchoolClass $schoolClass;

    private Employee $employee1;

    private Employee $employee2;

    private Employee $employee3;

    private LegacyStudent $student;

    public function setUp(): void
    {
        parent::setUp();
        $this->import = EducacensoInepImportFactory::new()->create();
        $institution = LegacyInstitutionFactory::new()->create();
        $school = LegacySchoolFactory::new()->create([
            'ref_cod_instituicao' => $institution->id,
        ]);
        $this->student = LegacyStudentFactory::new()->create();
        $grade = LegacyGradeFactory::new()->create();
        LegacySchoolGradeFactory::new()->create([
            'ref_cod_escola' => $school->id,
            'ref_cod_serie' => $grade->id,
        ]);
        $this->schoolClass = LegacySchoolClassFactory::new()->create([
            'ref_ref_cod_serie' => $grade->id,
            'ref_ref_cod_escola' => $school->id,
            'ref_cod_instituicao' => $institution->id,
        ]);
        $this->employee1 = EmployeeFactory::new()->create();
        $this->employee2 = EmployeeFactory::new()->create();
        $this->employee3 = EmployeeFactory::new()->create();
        $content = "00|11111111|1|06/02/2023|21/12/2023|ESCOLA 1|37195000|3158300|5|RUA TESTE|808||CENTRO|35|38581632||ESCOLA1@TESTE.COM|00027|1|7|3|1|0|0|0||||||||0|0|1|||||||||||||||1|0|0|1|0||
10|11111111|1|0|0|0|0|0|1|0|||||||1|1|0|0|0|0|1|0|0|0|1|0|0|0|1|0|0|0|0|1|0|0|0|0|0|0|1|0|0|1|1|0|1|1|0|0|0|0|0|1|0|1|0|0|0|1|1|0|0|0|0|1|0|1|0|0|0|1|0|0|0|1|0|0|1|1|0|0|0|0|4|1|||0|1|0|0|1|0|0|3||4||1||||1|1|0|0|0|0|0|1|1|0|0|3|5||||||||||||1||||1|1|1|0|0|0|1|1|0|1|0|0|0|0|0|0||||||0|||||||0|0|0|0|0|0|0|0|1|1
20|11111111|{$this->schoolClass->getKey()}|1|TURMA 1|1|07|00|17|00|0|1|1|1|1|1|0|1|0|0|0|0|1|||||||0|1|1||||||||||||||||||||||||||||||||||||||||||||0
20|11111111|2|2|TURMA 1|1|07|00|17|00|0|1|1|1|1|1|0|1|0|0|0|0|1|||||||0|1|1||||||||||||||||||||||||||||||||||||||||||||0
30|11111111|{$this->employee1->getKey()}|3|88699999999|SERVIDOR 1|17/09/1971|1|NOME 1||2|1|1|76|3169406|0||||||||||||||||||||||||||||||6|2|0113P011|1991|9999999||||||||||1|1|1991|||||||||||||||||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|1|NOME1@GMAIL.COM
40|11111111|{$this->employee2->getKey()}|4|1|2|1
50|11111111|{$this->employee3->getKey()}|5|92||1|2||||||||||||||||||||||||||||||||||
50|11111111|4|6|92||1|2||||||||||||||||||||||||||||||||||
60|11111111|{$this->student->getKey()}|7|90|||||||||||||||||||||||||||||1|0|||||||||||
60|11111111|2|8|90|||||||||||||||||||||||||||||1|0|||||||||||
00|22222222|1|06/02/2023|21/12/2023|ESCOLA 2|37195000|3158300|5|RUA TESTE|808||CENTRO|35|38581632||ESCOLA1@TESTE.COM|00027|1|7|3|1|0|0|0||||||||0|0|1|||||||||||||||1|0|0|1|0||
10|22222222|1|0|0|0|0|0|1|0|||||||1|1|0|0|0|0|1|0|0|0|1|0|0|0|1|0|0|0|0|1|0|0|0|0|0|0|1|0|0|1|1|0|1|1|0|0|0|0|0|1|0|1|0|0|0|1|1|0|0|0|0|1|0|1|0|0|0|1|0|0|0|1|0|0|1|1|0|0|0|0|4|1|||0|1|0|0|1|0|0|3||4||1||||1|1|0|0|0|0|0|1|1|0|0|3|5||||||||||||1||||1|1|1|0|0|0|1|1|0|1|0|0|0|0|0|0||||||0|||||||0|0|0|0|0|0|0|0|1|1
20|22222222|89||TURMA 2|1|07|00|17|00|0|1|1|1|1|1|0|1|0|0|0|0|1|||||||0|1|1||||||||||||||||||||||||||||||||||||||||||||0
30|22222222|42|191159986907|88699999999|SERVIDOR 2|17/09/1971|1|NOME 2||2|1|1|76|3169406|0||||||||||||||||||||||||||||||6|2|0113P011|1991|9999999||||||||||1|1|1991|||||||||||||||||0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|0|1|NOME1@GMAIL.COM
40|22222222|42|191159986907|1|2|1
50|22222222|40|121241456223|92||1|2||||||||||||||||||||||||||||||||||
60|11111111|1467||90|||||||||||||||||||||||||||||1|0|||||||||||";
        $uploadFile = UploadedFile::fake()->createWithContent('file.txt', $content);
        $this->schoolsData = EducacensoImportInepService::getDataBySchool($uploadFile);
    }

    public function testInepImportService(): void
    {
        $this->assertDatabaseMissing(SchoolClassInep::class, [
            'cod_turma' => $this->schoolClass->getKey(),
        ]);
        $this->assertDatabaseMissing(EmployeeInep::class, [
            'cod_servidor' => $this->employee1->getKey(),
        ]);
        $this->assertDatabaseMissing(EmployeeInep::class, [
            'cod_servidor' => $this->employee2->getKey(),
        ]);
        $this->assertDatabaseMissing(EmployeeInep::class, [
            'cod_servidor' => $this->employee3->getKey(),
        ]);
        $this->assertDatabaseMissing(StudentInep::class, [
            'cod_aluno' => $this->student->getKey(),
        ]);
        //turma não cadastrada
        $this->assertDatabaseMissing(SchoolClassInep::class, [
            'cod_turma' => 2,
        ]);
        //servidor não cadastrado
        $this->assertDatabaseMissing(EmployeeInep::class, [
            'cod_servidor' => 4,
        ]);
        //aluno não cadastrado
        $this->assertDatabaseMissing(StudentInep::class, [
            'cod_aluno' => 2,
        ]);
        Queue::fake();
        Queue::assertNothingPushed();
        Queue::assertNotPushed(EducacensoInepImportJob::class);
        foreach ($this->schoolsData as $schoolData) {
            EducacensoInepImportJob::dispatch($this->import, $schoolData);
            $service = new EducacensoImportInepService($this->import, $schoolData);
            $service->execute();
        }
        Queue::assertPushed(EducacensoInepImportJob::class, 2);
        $this->assertDatabaseHas(SchoolClassInep::class, [
            'cod_turma' => $this->schoolClass->getKey(),
            'cod_turma_inep' => 1,
        ]);
        $this->assertDatabaseHas(EmployeeInep::class, [
            'cod_servidor' => $this->employee1->getKey(),
            'cod_docente_inep' => 3,
        ]);
        $this->assertDatabaseHas(EmployeeInep::class, [
            'cod_servidor' => $this->employee2->getKey(),
            'cod_docente_inep' => 4,
        ]);
        $this->assertDatabaseHas(EmployeeInep::class, [
            'cod_servidor' => $this->employee3->getKey(),
            'cod_docente_inep' => 5,
        ]);
        $this->assertDatabaseHas(StudentInep::class, [
            'cod_aluno' => $this->student->getKey(),
            'cod_aluno_inep' => 7,
        ]);
        //turma não cadastrada
        $this->assertDatabaseMissing(SchoolClassInep::class, [
            'cod_turma' => 2,
        ]);
        //servidor não cadastrado
        $this->assertDatabaseMissing(EmployeeInep::class, [
            'cod_servidor' => 4,
        ]);
        //aluno não cadastrado
        $this->assertDatabaseMissing(StudentInep::class, [
            'cod_aluno' => 2,
        ]);
    }
}
