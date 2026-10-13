<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ResourceController;
use App\Models\LegacyDiscipline;
use App\Models\View\Discipline;
use App\Process;
use App\Rules\CanDeleteDiscipline;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DisciplineController extends ResourceController
{
    public function index(Discipline $discipline, Request $request): JsonResource
    {
        return $this->all($discipline, $request);
    }

    public function destroy(LegacyDiscipline $discipline, Request $request): JsonResource
    {
        $this->authorize('remove', [Process::DISCIPLINES]);

        try {
            return DB::transaction(fn () => $this->delete($discipline, $request));
        } catch (QueryException $exception) {
            if (!in_array($exception->getCode(), ['23001', '23503'], true)) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'discipline' => (new CanDeleteDiscipline)->message(),
            ]);
        }
    }

    protected function deleteRules(Model $model, Request $request)
    {
        return [
            new CanDeleteDiscipline,
        ];
    }
}
