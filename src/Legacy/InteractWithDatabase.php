<?php

namespace iEducar\Legacy;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Throwable;

trait InteractWithDatabase
{
    /**
     * @throws Exception
     *
     * @return string
     */
    public function model()
    {
        throw new Exception('Missing model name.');
    }

    public function index()
    {
        throw new Exception('Missing index page name.');
    }

    /**
     * @throws Exception
     *
     * @return Builder
     */
    public function newQuery()
    {
        $model = $this->model();

        return $model::query();
    }

    public function redirectToIndex()
    {
        $this->simpleRedirect($this->index());
    }

    /**
     * @param int $identifier
     *
     * @return EloquentModel
     */
    public function find($identifier)
    {
        try {
            return $this->newQuery()->findOrFail($identifier);
        } catch (Throwable $throwable) {
            $this->redirectToIndex();
        }
    }

    /**
     * @param int          $limit
     * @param int          $offset
     * @param Closure|null $modifier
     *
     * @throws Exception
     *
     * @return array
     */
    public function paginate($limit, $offset, Closure $modifier = null)
    {
        $query = $this->newQuery();

        if ($modifier) {
            $modifier($query);
        }

        $count = $query->count();
        $data = $query->limit($limit)->offset($offset)->get();

        return [$data, $count];
    }

    /**
     * @param array $attributes
     *
     * @throws Exception
     *
     * @return bool
     */
    public function create($attributes)
    {
        try {
            $this->newQuery()->create($attributes);
        } catch (Throwable $throwable) {
            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
        }

        $this->redirectToIndex();
    }

    /**
     * @param int   $identifier
     * @param array $attributes
     *
     * @throws Exception
     *
     * @return bool
     */
    public function update($identifier, $attributes)
    {
        $model = $this->find($identifier);

        try {
            $model->update($attributes);
        } catch (Throwable $throwable) {
            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        $this->redirectToIndex();
    }

    /**
     * @param int $identifier
     *
     * @throws Exception
     *
     * @return bool
     */
    public function delete($identifier)
    {
        $model = $this->find($identifier);

        try {
            $model->findOrFail($identifier)->delete;
        } catch (Throwable $throwable) {
            $this->mensagem = 'Exclusão não realizada.<br>';

            return false;
        }

        $this->redirectToIndex();
    }
}
