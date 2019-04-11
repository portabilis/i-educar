<?php

namespace Tests;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class EloquentTestCase extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * Return the Eloquent model name to be used in tests.
     *
     * @return string
     */
    abstract protected function getEloquentModelName();

    /**
     * Return attributes to be used in create action.
     *
     * @return array
     */
    protected function getAttributesForCreate()
    {
        return factory($this->getEloquentModelName())->make()->toArray();
    }

    /**
     * Return attributes to be used in update action.
     *
     * @return array
     */
    protected function getAttributesForUpdate()
    {
        return factory($this->getEloquentModelName())->make()->toArray();
    }

    /**
     * Instance a new Eloquent model.
     *
     * @return Model
     */
    protected function instanceNewEloquentModel()
    {
        $model = $this->getEloquentModelName();

        return new $model;
    }

    /**
     * Create a new Eloquent model.
     *
     * @see Model::save()
     *
     * @return Model
     */
    protected function createNewModel()
    {
        $model = $this->instanceNewEloquentModel();

        $model->fill($this->getAttributesForCreate());
        $model->save();

        return $model;
    }

    /**
     * Create a Eloquent model.
     *
     * @return void
     */
    public function testCreateUsingEloquent()
    {
        $modelCreated = $this->createNewModel();

        $this->assertDatabaseHas($modelCreated->getTable(), $modelCreated->toArray());
    }

    /**
     * Update a Eloquent model.
     *
     * @return void
     */
    public function testUpdateUsingEloquent()
    {
        $modelCreated = $this->createNewModel();

        $modelUpdated = clone $modelCreated;

        $modelUpdated->fill($this->getAttributesForUpdate());
        $modelUpdated->save();

        $this->assertDatabaseMissing($modelUpdated->getTable(), $modelCreated->toArray());
        $this->assertDatabaseHas($modelUpdated->getTable(), $modelUpdated->toArray());
    }

    /**
     * Delete a Eloquent model.
     *
     * @return void
     *
     * @throws Exception
     */
    public function testDeleteUsingEloquent()
    {
        $modelCreated = $this->createNewModel();

        $this->assertDatabaseHas($modelCreated->getTable(), $modelCreated->toArray());

        $modelCreated->delete();

        $this->assertDatabaseMissing($modelCreated->getTable(), $modelCreated->toArray());
    }

    /**
     * Find a Eloquent model.
     *
     * @return void
     */
    public function testFindUsingEloquent()
    {
        $modelCreated = $this->createNewModel();

        $modelFound = $this->instanceNewEloquentModel()
            ->newQuery()
            ->find($modelCreated->getKey());

        $created = $modelCreated->toArray();
        $found = $modelFound->toArray();

        $expected = array_intersect_key($created, $found);

        $this->assertEquals($expected, $created);
    }

    /**
     * Relations.
     *
     * @return void
     */
    public function testRelationships()
    {
        $model = factory($this->getEloquentModelName())->create();

        foreach ($this->relations as $relation => $class) {
            $this->assertInstanceOf($class, $model->{$relation});
        }

        $this->assertInstanceOf($this->getEloquentModelName(), $model);
    }
}
