<?php

namespace Tests\Unit\Services\Educacenso;

use App\Models\LegacySchool;
use App\Models\SchoolInep;
use App\Services\Educacenso\Version2019\Registro00Import;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class Registro00ImportTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var string
     */
    private $importString;

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $importFile = file_get_contents(base_path('tests/Unit/assets/Educacenso/registro00'));
        $this->importString = explode("/n", $importFile)[0];
        $this->user = factory(User::class, 'admin')->make();
    }

    public function testExistingSchoolShouldNotDuplicate()
    {
        $school = factory(LegacySchool::class)->create();
        $inep = factory(SchoolInep::class)->create([
            'cod_escola' => $school,
            'cod_escola_inep' => '43012375',
        ]);

        $service = new Registro00Import();
        $service->import();

        $schoolInep = SchoolInep::where('cod_escola_inep', $inep->cod_escola_inep)->first();

        $this->assertInstanceOf(SchoolInep::class, $schoolInep);
    }

    public function testCreateSchool()
    {
        $service = new Registro00Import();
        $service->import($this->importString, now()->format('Y'));

        //'42039142'
        $schoolInep = SchoolInep::where('cod_escola_inep', '43012375')->first();

        $this->assertInstanceOf(SchoolInep::class, $schoolInep);
    }
}
