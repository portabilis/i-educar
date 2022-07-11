<?php

namespace Tests\Feature\Api\Resource\EducationNetwork;

use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ResourceEducationNetworkTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var LegacyInstitution
     */
    private LegacyInstitution $institution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitution::factory()->hasEducationNetworks(2)->create();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route('resource::api.education-network', ['institution' => $this->institution]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'name'
            ]
        ]);

        $education_networks = LegacyEducationNetwork::whereInstitution($this->institution->id)
            ->active()
            ->orderByName()
            ->get(['cod_escola_rede_ensino', 'nm_rede']);

        $response->assertJson(function (AssertableJson $json) use ($education_networks) {
            $json->has(2);

            foreach ($education_networks as $key => $education_network) {
                $json->has($key, function ($json) use ($education_network) {
                    $json->where('id', $education_network->id);
                    $json->where('name', $education_network->name);
                });
            }
        });
    }

    public function test_not_found(): void
    {
        $response = $this->getJson(route('resource::api.education-network', ['institution' => 0]));

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has(0);
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route('resource::api.education-network'));

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
