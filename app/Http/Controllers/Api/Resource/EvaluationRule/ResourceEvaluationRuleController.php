<?php

namespace App\Http\Controllers\Api\Resource\EvaluationRule;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Resource\EvaluationRule\ResourceEvaluationRuleRequest;
use App\Models\LegacyEvaluationRule;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceEvaluationRuleController extends Controller
{
    public function index(ResourceEvaluationRuleRequest $request): JsonResource
    {
        return JsonResource::collection(LegacyEvaluationRule::query()->getResource($request->all()));
    }
}
