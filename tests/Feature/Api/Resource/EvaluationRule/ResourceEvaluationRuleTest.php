<?php

namespace Tests\Feature\Api\Resource\EvaluationRule;

use App\Models\LegacyAverageFormula;
use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceEvaluationRuleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyInstitution
     */
    private LegacyInstitution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitution::factory()->create();

        $average_formula = LegacyAverageFormula::factory()->create([
            'instituicao_id' => $this->institution->id
        ]);

        LegacyEvaluationRule::factory(2)->create([
            'formula_media_id' => $average_formula->id,
            'instituicao_id' => $this->institution->id
        ]);
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route('resource::api.evaluation-rule', ['institution' => $this->institution]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        $evaluation_rules = LegacyEvaluationRule::whereInstitution($this->institution->id)
            ->orderByName()
            ->get(['id', 'nome']);

        $response->assertJson(function (AssertableJson $json) use ($evaluation_rules) {
            $json->has(2);

            foreach ($evaluation_rules as $key => $evaluation_rule) {
                $json->has($key, function ($json) use ($evaluation_rule) {
                    $json->where('id', $evaluation_rule->id);
                    $json->where('name', $evaluation_rule->name);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.evaluation-rule', ['institution' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.evaluation-rule'));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route('resource::api.evaluation-rule', ['institution' => 'Instituição']));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }
}
