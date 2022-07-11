<?php

namespace App\Http\Controllers\Api\Resource\School;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\School\ResourceSchoolRequest;
use App\Models\LegacySchool;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSchoolController extends Controller
{
    public function index(ResourceSchoolRequest $request): JsonResource
    {
        $institution = $request->get('institution');

        $schools = LegacySchool::joinOrganization()->select(['cod_escola as id', 'fantasia as name'])
            ->whereInstitution($institution)
            ->active()
            ->orderByName()
            ->get();

        JsonResource::withoutWrapping();
        return JsonResource::collection($schools);
    }
}
