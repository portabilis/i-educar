<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\District;
use App\Rules\DistrictRestricOperationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictController extends ResourceController
{

    public int $process = 759;

    public function index(District $district, Request $request): JsonResource
    {
        return $this->all($district, $request);
    }

    public function store(District $district, Request $request): JsonResource
    {
        return $this->post($district, $request);
    }

    public function show(District $district, Request $request): JsonResource
    {
        return $this->get($district, $request);
    }

    public function update(District $district, Request $request): JsonResource
    {
        return $this->patch($district, $request);
    }

    public function destroy(District $district, Request $request): JsonResource
    {
        return $this->delete($district, $request);
    }

    public function rules($district, Request $request): array
    {
        $accessLevel = $request->user()->getLevel();
        return [
            new DistrictRestricOperationRule($accessLevel)
        ];
    }
}
