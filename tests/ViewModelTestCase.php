<?php

namespace Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Base dos testes Pest de modelos que leem views do banco.
 *
 * Cada arquivo configura o modelo no beforeEach com `$this->viewModel(...)` e
 * registra os testes comuns com `ViewModelTestCase::registerModelTests()`:
 *
 *     uses(ViewModelTestCase::class);
 *
 *     beforeEach(fn () => $this->viewModel(Student::class, relations: [
 *         'person' => Person::class,
 *     ]));
 *
 *     ViewModelTestCase::registerModelTests();
 *
 * Os testes comuns não podem ser métodos desta classe: o Pest não executa
 * métodos de teste herdados.
 */
abstract class ViewModelTestCase extends TestCase
{
    use DatabaseTransactions;

    protected Factory $factory;

    protected Model $model;

    /** @var class-string<Model> */
    protected string $viewModelClass;

    protected array $relations = [];

    /**
     * Define o modelo testado e monta, pela factory, um registro que aparece na view.
     *
     * @param class-string<Model> $model
     */
    public function viewModel(string $model, array $relations = []): Model
    {
        $this->viewModelClass = $model;
        $this->relations = $relations;
        $this->factory = Factory::factoryForModel($model)->new();

        return $this->model = $this->makeNewModel();
    }

    /**
     * Registra os testes comuns a todos os modelos de view.
     */
    public static function registerModelTests(): void
    {
        test('find using eloquent', function () {
            $modelFound = $this->instanceNewViewModel()
                ->newQuery()
                ->find($this->model->getKey());

            $created = $this->model->getAttributes();
            $found = $modelFound->getAttributes();
            $expected = array_intersect_key($created, $found);

            $this->assertNotEmpty($expected);
            $this->assertEquals($expected, $created);
        });

        test('relationships', function () {
            if (empty($this->relations)) {
                $this->assertTrue(true);
            }

            foreach ($this->relations as $relation => $class) {
                $model = $this->makeNewModel();
                $this->makeRelation($model, $relation, $class);
                $type = $model->{$relation}();
                $related = $model->{$relation};

                if ($type instanceof BelongsTo || $type instanceof HasOne || $type instanceof MorphOne) {
                    $this->assertInstanceOf($class, $related);
                } elseif ($type instanceof HasMany) {
                    $this->assertCount(1, $related);
                    $this->assertInstanceOf($class, $related->first());
                }
            }
        });
    }

    public function instanceNewViewModel(): Model
    {
        return new $this->viewModelClass;
    }

    public function makeNewModel(): Model
    {
        if (method_exists($this->factory, 'forView')) {
            return $this->factory->forView()->make();
        }

        return $this->factory->make();
    }

    protected function makeRelation(Model $model, string $relation, string $class): void
    {
        $type = $this->instanceNewViewModel()->{$relation}();

        if ($type instanceof HasOne || $type instanceof HasMany) {
            $instance = Factory::factoryForModel($class)::new();

            if (method_exists($instance, 'forView')) {
                $instance->forView($model->getKey())->make();
            }
        } elseif ($type instanceof MorphOne) {
            Factory::factoryForModel($class)->new()->create([
                $type->getMorphType() => $type->getMorphClass(),
                $type->getForeignKeyName() => $model->getKey(),
            ]);
        }
    }
}
