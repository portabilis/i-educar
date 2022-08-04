<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LegacyBuilder extends Builder
{
    /**
     * Colunas adicionadas no recurso, mas não na query
     *
     * @var array
     */
    private array $additional = [];

    /**
     * Colunas em query, mas não no recurso
     *
     * @var array
     */
    private array $except = [];

    /**
     * Filtros
     *
     * @var array
     */
    private array $filters = [];

    /**
     * Filtra por parametros
     *
     * @param array $data
     *
     * @return $this
     */
    public function filter(array $data = []): LegacyBuilder
    {
        $this->setFilters($data);
        $this->executeFilters();

        return $this;
    }

    /**
     * Retorna um recurso collection
     *
     * @param array $columns
     * @param array $additional
     *
     * @return Collection
     */
    public function resource(array $columns = ['*'], array $additional = []): Collection
    {
        $this->setAdditional($additional);

        $columnsNotExcept = $columns;
        $columns = array_merge($columns, $this->except);
        $columns = $this->replaceAttribute($columns);

        //original do laravel
        $resource = $this->get($columns);

        return $this->mapResource($resource, $columnsNotExcept);
    }

    /**
     * Transforma o recurso com os novos parametros
     *
     * @param Collection $resource
     * @param array      $columnsNotExcept
     *
     * @return Collection
     */
    private function mapResource(Collection $resource, array $columnsNotExcept): Collection
    {
        return $resource->map(function (Model $item) use ($columnsNotExcept) {
            $resource = [];

            foreach ($columnsNotExcept as $key) {
                $resource[$key] = $item->{$key};
            }

            foreach ($this->additional as $add) {
                $resource[$add] = $item->{$add} ?? null;
            }

            return $resource;
        });
    }

    /**
     * Colunas adicionais que não estão na query, mas é adicionado no recurso
     *
     * @param array $additional
     *
     * @return LegacyBuilder
     */
    public function setAdditional(array $additional): LegacyBuilder
    {
        $this->additional = $additional;

        return $this;
    }

    /**
     * Colunas a serem adicionadas na query, mas não retorna no recurso
     *
     * @param array $except
     *
     * @return LegacyBuilder
     */
    public function setExcept(array $except): LegacyBuilder
    {
        $this->except = $except;

        return $this;
    }

    /**
     * Executa os filtros
     *
     * @return void
     */
    private function executeFilters(): void
    {
        foreach ($this->filters as $filter => $parameter) {
            $method = 'where' . $filter;
            $this->{$method}($parameter);
        }
    }

    /**
     * Substitui os atributos legados
     */
    private function replaceAttribute(array $columns): array
    {
        //parametro definido no model
        if (!property_exists($this->getModel(), 'legacy')) {
            return $columns;
        }

        $legacy = $this->getModel()->legacy;
        if (!is_array($legacy) || empty($legacy)) {
            return $columns;
        }

        $data = [];

        foreach ($columns as $key) {
            $data[] = $legacy[$key] ?? $key;
        }

        if (!empty($data)) {
            $columns = $data;
        }

        return $columns;
    }

    /**
     * Insere os filtros personalizados ou do request
     *
     * @param array $filters
     *
     * @return void
     */
    private function setFilters(array $filters): void
    {
        $data = [];
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $data[$this->getFilterName($key)] = $value;
            }
        }

        $this->filters = $data;
    }

    /**
     * Transforma o nome do parametro para o nome de filtro
     *
     * @param $name
     *
     * @return string
     */
    private function getFilterName($name): string
    {
        return Str::camel($name);
    }

    /**
     * Filtro Padrão a todos os Builders
     *
     * @param int|null $limit
     *
     * @return $this
     */
    public function whereLimit(int $limit = null): self
    {
        return $this->when($limit, fn ($q) => $q->limit($limit));
    }

    /**
     * Obtem o valor de um filtro
     *
     * @param string          $name
     * @param int|string|null $default
     *
     * @return mixed
     */
    public function getFilter(string $name, mixed $default = null): mixed
    {
        return Arr::get($this->filters, $this->getFilterName($name), $default);
    }
}
