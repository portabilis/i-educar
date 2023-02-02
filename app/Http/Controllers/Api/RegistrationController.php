<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyRegistration;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RegistrationController extends ResourceController
{
    public int $process = 578;

    public function index(LegacyRegistration $registration, Request $request): JsonResource
    {
        $this->skipAuthorization();

        return $this->all($registration, $request);
    }

    public function filter(Builder $builder, Request $request): void
    {
        $builder->orderByName();
    }


    public function store(LegacyRegistration $registration, Request $request): JsonResource
    {
        return $this->post($registration, $request);
    }

    public function show(int $registration, Request $request): JsonResource
    {
        return $this->get($registration, $request, LegacyRegistration::class);
    }

    public function update(LegacyRegistration $registration, Request $request): JsonResource
    {
        return $this->patch($registration, $request);
    }

    public function destroy(LegacyRegistration $registration, Request $request): JsonResource
    {
        return $this->delete($registration, $request);
    }
}
