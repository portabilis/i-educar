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
     * @return string
     *
     * @throws Exception
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
     * @return Builder
     *
     * @throws Exception
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
     * @return EloquentModel
     */
    public function find($identifier)
    {
        try {
            return $this->newQuery()->findOrFail($identifier);
        } catch (Throwable) {
            $this->redirectToIndex();
        }
    }

    /**
     * @param int          $limit
     * @param int          $offset
     * @return array
     *
     * @throws Exception
     */
    public function paginate($limit, $offset, ?Closure $modifier = null)
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
     * @return bool
     *
     * @throws Exception
     */
    public function create($attributes)
    {
        try {
            $this->newQuery()->create($attributes);
        } catch (Throwable) {
            $this->mensagem = 'Cadastro não realizado.<br>';

            return false;
        }

        $this->redirectToIndex();
    }

    /**
     * @param int   $identifier
     * @param array $attributes
     * @return bool
     *
     * @throws Exception
     */
    public function update($identifier, $attributes)
    {
        $model = $this->find($identifier);

        try {
            $model->update($attributes);
        } catch (Throwable) {
            $this->mensagem = 'Edição não realizada.<br>';

            return false;
        }

        $this->redirectToIndex();
    }

    /**
     * @param int $identifier
     * @return bool
     *
     * @throws Exception
     */
    public function delete($identifier)
    {
        $model = $this->find($identifier);

        try {
            $model->findOrFail($identifier)->delete;
        } catch (Throwable) {
            $this->mensagem = 'Exclusão não realizada.<br>';

            return false;
        }

        $this->redirectToIndex();
    }
}
