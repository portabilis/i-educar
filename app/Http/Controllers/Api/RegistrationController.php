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
        $builder->orderByName()->filter($request->all());
    }
}
