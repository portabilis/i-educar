<?php

namespace Tests\Feature\Api\Resource\EvaluationRule;

use App\Models\LegacyEvaluationRule;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyAverageFormulaFactory;
use Database\Factories\LegacyEvaluationRuleFactory;
use Database\Factories\LegacyInstitutionFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceEvaluationRuleTest extends TestCase
{
    use DatabaseTransactions;

    private LegacyInstitution $institution;

    private string $route = 'api.resource.evaluation-rule';

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitutionFactory::new()->unique()->make();

        $average_formula = LegacyAverageFormulaFactory::new()->create([
            'instituicao_id' => $this->institution->id,
        ]);

        LegacyEvaluationRuleFactory::new()->count(2)->create([
            'formula_media_id' => $average_formula->id,
            'instituicao_id' => $this->institution->id,
        ]);
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route($this->route, ['institution' => $this->institution]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);

        $evaluation_rules = LegacyEvaluationRule::getResource([
            'institution' => $this->institution->id,
        ]);

        $response->assertJson(function (AssertableJson $json) use ($evaluation_rules) {
            $json->has('data', 2);

            foreach ($evaluation_rules as $key => $evaluation_rule) {
                $json->has('data.'.$key, function ($json) use ($evaluation_rule) {
                    $json->where('id', $evaluation_rule['id']);
                    $json->where('name', $evaluation_rule['name']);
                });
            }
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route($this->route));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route($this->route, ['institution' => 'Instituição']));

        $response->assertOk();
        $response->assertJsonCount(0, 'data');
    }
}
