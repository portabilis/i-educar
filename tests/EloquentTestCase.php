<?php

namespace Tests;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        return $factory->make()->toArray();
    }

    /**
     * Return attributes to be used in update action.
     *
     * @return array
     */
    protected function getAttributesForUpdate()
    {
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        return $factory->make()->toArray();
    }

    /**
     * Instance a new Eloquent model.
     *
     * @return Model
     */
    protected function instanceNewEloquentModel()
    {
        $model = $this->getEloquentModelName();

        return new $model();
    }

    /**
     * Create a new Eloquent model.
     *
     * @return Model
     *
     * @see Model::save()
     *
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

        $this->assertDatabaseHas($modelCreated->getTable(), $modelCreated->getAttributes());
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

        $this->assertDatabaseMissing($modelUpdated->getTable(), $modelCreated->getAttributes());
        $this->assertDatabaseHas($modelUpdated->getTable(), $modelUpdated->getAttributes());
    }

    /**
     * Delete a Eloquent model.
     *
     * @return void
     *
     * @throws Exception
     *
     */
    public function testDeleteUsingEloquent()
    {
        $modelCreated = $this->createNewModel();

        $this->assertDatabaseHas($modelCreated->getTable(), $modelCreated->getAttributes());

        $modelCreated->delete();

        if (in_array(SoftDeletes::class, class_uses($modelCreated), true) || in_array(LegacySoftDeletes::class, class_uses($modelCreated), true)) {
            $this->assertSoftDeleted($modelCreated, deletedAtColumn: $modelCreated->getDeletedAtColumn());
        } else {
            $this->assertDatabaseMissing($modelCreated->getTable(), $modelCreated->getAttributes());
        }
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

        $created = $modelCreated->getAttributes();
        $found = $modelFound->getAttributes();

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
        $factory = Factory::factoryForModel(
            $this->getEloquentModelName()
        );

        if (empty($this->relations)) {
            $this->assertTrue(true);
        }

        foreach ($this->relations as $relation => $class) {
            if (is_array($class)) {
                $method = 'has' . ucfirst($relation);
                $model = $factory->{$method}()->create();

                $this->assertCount(1, $model->$relation);
                $this->assertInstanceOf($class[0], $model->$relation->first());
            } else {
                $model = $factory->create();
                $this->assertInstanceOf($class, $model->{$relation});
            }
        }
    }
}
