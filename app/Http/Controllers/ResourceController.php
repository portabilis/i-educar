<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

abstract class ResourceController extends Controller
{
    protected int $process = 0;
    protected bool $skipAuthorization = false;

    protected function skipAuthorization(): void
    {
        $this->skipAuthorization = true;
    }

    protected function can(string $ability): void
    {
        if (empty($this->process)) {
            return;
        }

        if ($this->skipAuthorization) {
            return;
        }

        $this->authorize($ability, [$this->process]);
    }

    protected function newCollection($resources): JsonResource
    {
        return JsonResource::collection($resources);
    }

    protected function newResource($resource): JsonResource
    {
        return JsonResource::make($resource);
    }

    protected function columns(Request $request, Builder $query): void
    {
        $columns = $request->query('only');

        if (empty($columns)) {
            return;
        }

        $columns = explode(',', $request->query('only'));
        $columns[] = $query->getModel()->getKeyName();

        $this->includeColumns($columns, $query);
    }

    protected function include(Request $request, Builder $query): void
    {
        $include = $request->query('include');
        $include = explode('|', $include);
        $include = array_filter($include);
        $include = array_unique($include);

        foreach ($include as $string) {
            [$relation, $columns] = explode(':', $string . ':');

            $query->with([
                $relation => function ($query) use ($columns) {
                    if ($columns) {
                        $columns = explode(',', $columns);
                        $columns[] = $query->getForeignKeyName();

                        $this->includeColumns($columns, $query);
                    }
                },
            ]);
        }
    }

    protected function includeColumns(array $columns, $query): void
    {
        $columns = array_unique($columns);
        $columns = array_filter($columns);
        $columns = array_map('trim', $columns);

        $query->select($columns);
    }

    protected function filter(Builder $builder, Request $request): void {}

    public function all(Model $model, Request $request): JsonResource
    {
        $this->can('view');

        $query = $model->newQuery();

        $this->columns($request, $query);
        $this->include($request, $query);

        $page = $request->query('page', 1);
        $show = $request->query('show', $query->getModel()->getPerPage());

        $this->filter($query, $request);

        return $this->newCollection(
            $query->paginate($show, page: $page)
        );
    }

    public function post(Model $model, Request $request): JsonResource
    {
        $this->can('modify');

        $model->fill($request->all());

        $this->validation($model, $this->createRules());

        $model->saveOrFail();

        return $this->newResource($model);
    }

    public function get(Model $model, Request $request): JsonResource
    {
        $this->can('view');

        return $this->newResource($model);
    }

    public function put(Model $model, Request $request): JsonResource
    {
        return $this->patch($model, $request);
    }

    public function patch(Model $model, Request $request): JsonResource
    {
        $this->can('modify');

        $model->fill($request->all());

        $this->validation($model, $this->updateRules());

        $model->saveOrFail();

        return $this->newResource($model);
    }

    public function delete(Model $model, Request $request): JsonResource
    {
        $this->can('remove');

        $model->delete();

        $this->validation($model, $this->deleteRules());

        return $this->newResource($model);
    }

    protected function validation(Model $model, array $rules)
    {
       $validator =  Validator::make(Arr::wrap($model),
            [$rules]
        );

        $validator->validate();
    }

    protected function rules(): array
    {
        return [];
    }

    protected function updateRules()
    {
        return $this->rules();
    }

    protected function createRules()
    {
        return $this->rules();
    }

    protected function deleteRules()
    {
        return $this->rules();
    }
}
