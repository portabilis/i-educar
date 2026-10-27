<?php

use App\Http\Requests\Api\Resource\Country\ResourceCountryRequest;
use App\Http\Requests\Api\Resource\EducationNetwork\ResourceEducationNetworkRequest;
use Tests\TestCase;

uses(TestCase::class);

test('as requisições de recurso de país e de rede de ensino validam e nomeiam os campos', function () {
    $country = new ResourceCountryRequest;
    $network = new ResourceEducationNetworkRequest;

    expect($country->rules())->toBe(['name' => ['nullable', 'min:1', 'max:255']])
        ->and($country->attributes())->toBe(['name' => 'Nome'])
        ->and($network->rules())->toBe(['institution' => ['required', 'integer', 'min:1']])
        ->and($network->attributes())->toBe(['institution' => 'Instituição']);
});
