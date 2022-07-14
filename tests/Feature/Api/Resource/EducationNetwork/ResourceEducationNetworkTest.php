<?php

namespace Tests\Feature\Api\Resource\EducationNetwork;

use App\Models\LegacyEducationNetwork;
use App\Models\LegacyInstitution;
use Database\Factories\LegacyInstitutionFactory;
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

    /**
     * @var string
     */
    private string $route = 'api.resource.education-network';

    protected function setUp(): void
    {
        parent::setUp();

        $this->institution = LegacyInstitutionFactory::new()->hasEducationNetworks(2)->create();
    }

    public function test_exact_json_match(): void
    {
        $response = $this->getJson(route($this->route, ['institution' => $this->institution]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
                ]
            ]
        ]);
        $education_networks = LegacyEducationNetwork::getResource(['institution' => $this->institution->id]);

        $response->assertJson(function (AssertableJson $json) use ($education_networks) {
            $json->has('data',2);

            foreach ($education_networks as $key => $education_network) {
                $json->has('data.'.$key, function ($json) use ($education_network) {
                    $json->where('id', $education_network['id']);
                    $json->where('name', $education_network['name']);
                });
            }
        });
    }

    public function test_required_parameters(): void
    {
        $response = $this->getJson(route($this->route));

        $response->assertOk();
        $response->assertJsonCount(0,'data');
    }

    public function test_invalid_parameters(): void
    {
        $response = $this->getJson(route($this->route, ['institution' => 'Instituição']));

        $response->assertOk();
        $response->assertJsonCount(0,'data');
    }

}
