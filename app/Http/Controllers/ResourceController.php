<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
                        $columns[] = $query instanceof BelongsTo ? $query->getOwnerKeyName() : $query->getForeignKeyName();

                        $this->includeColumns($columns, $query);
                    }
                },
            ]);
        }
    }

    protected function order(Request $request, Builder $query): void
    {
        $order = $request->query('order');
        if (empty($order)) {
            return;
        }

        $columns = array_filter(explode('|', $order));

        $columns = array_map(static function ($columns) {
            return array_filter(explode(',', $columns));
        }, $columns);

        foreach ($columns as $column) {
            $query->orderBy($column[0], $column[1] ?? 'asc');
        }
    }

    protected function includeColumns(array $columns, $query): void
    {
        $columns = array_unique($columns);
        $columns = array_filter($columns);
        $columns = array_map('trim', $columns);

        $query->select($columns);
    }

    protected function filter(Builder $builder, Request $request): void
    {
        if (method_exists($builder, 'filter')) {
            $builder->filter($request->except('only', 'include', 'order', 'page'));
        }
    }

    public function all(Model $model, Request $request): JsonResource
    {
        $this->can('view');

        $query = $model->newQuery();

        $this->columns($request, $query);
        $this->include($request, $query);
        $this->order($request, $query);

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

        $this->validation($model, $this->createRules($model, $request));

        $model->saveOrFail();

        return $this->newResource($model);
    }

    public function get(Model|int $model, Request $request, ?string $class = null): JsonResource
    {
        $this->can('view');

        if ($model instanceof Model) {
            $id = $model->id;
            $query = $model->newQuery();
        } else {
            $id = $model;
            $query = (new $class())->newQuery();
        }

        $this->columns($request, $query);
        $this->include($request, $query);
        $model = $query->findOrFail($id);

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

        $this->validation($model, $this->updateRules($model, $request));

        $model->saveOrFail();

        return $this->newResource($model);
    }

    public function delete(Model $model, Request $request): JsonResource
    {
        $this->can('remove');

        $this->validation($model, $this->deleteRules($model, $request));

        $model->delete();

        return $this->newResource($model);
    }

    protected function validation(Model $model, array $rules)
    {
        $validator = Validator::make(
            Arr::wrap($model),
            [$rules]
        );

        $validator->validate();
    }

    protected function rules(Model $model, Request $request): array
    {
        return [];
    }

    protected function updateRules(Model $model, Request $request)
    {
        return $this->rules($model, $request);
    }

    protected function createRules(Model $model, Request $request)
    {
        return $this->rules($model, $request);
    }

    protected function deleteRules(Model $model, Request $request)
    {
        return $this->rules($model, $request);
    }
}
