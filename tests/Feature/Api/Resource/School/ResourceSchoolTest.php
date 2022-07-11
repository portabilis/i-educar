<?php

namespace Tests\Feature\Api\Resource\School;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolCourse;
use App\Models\LegacySchoolGrade;
use Illuminate\Database\Eloquent\HigherOrderBuilderProxy;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceSchoolTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyInstitution
     */
    private LegacyInstitution $institution;
    /**
     * @var LegacySchool
     */
    private LegacySchool $school;


    protected function setUp(): void
    {
        parent::setUp();

        //instituição
        $this->institution = LegacyInstitution::factory()->create();

        //escolas
        $schools = LegacySchool::factory(2)->create([
            'ref_cod_instituicao' => $this->institution->id
        ]);

        //escola
        $this->school = $schools->first();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route('resource::api.school', ['institution' => $this->institution]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        $schools = LegacySchool::joinOrganization()->select(['cod_escola as id', 'fantasia as name'])
            ->whereInstitution($this->institution->id)
            ->active()
            ->orderByName()
            ->get();

        $response->assertJson(function (AssertableJson $json) use ($schools) {
            $json->has(2);

            foreach ($schools as $key => $school) {
                $json->has($key, function ($json) use ($school) {
                    $json->where('id', $school->id);
                    $json->where('name', $school->name);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.school', ['institution' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.school', ['institution' => 'Instituição']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
