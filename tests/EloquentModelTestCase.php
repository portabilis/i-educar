<?php

namespace Tests;

use App\Models\Concerns\SoftDeletes\LegacySoftDeletes;
use App\Models\LegacyModel;
use Closure;
use Faker\UniqueGenerator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Base dos testes Pest de modelos Eloquent.
 *
 * Cada arquivo configura o modelo no beforeEach com `$this->eloquent(...)` e
 * registra os testes comuns com `EloquentModelTestCase::registerModelTests()`:
 *
 *     uses(EloquentModelTestCase::class);
 *
 *     beforeEach(fn () => $this->eloquent(LegacyStudent::class, relations: [
 *         'person' => LegacyPerson::class,
 *     ]));
 *
 *     EloquentModelTestCase::registerModelTests();
 *
 * Os testes comuns não podem ser métodos desta classe: o Pest não executa
 * métodos de teste herdados.
 */
abstract class EloquentModelTestCase extends TestCase
{
    use DatabaseTransactions;

    protected Model $model;

    /** @var class-string<Model> */
    protected string $eloquentModel;

    /**
     * Relações a conferir: nome da relação => classe do modelo relacionado, ou
     * [classe, dados do pivot] para relações BelongsToMany.
     */
    protected array $relations = [];

    protected array $legacyAttributes = [];

    protected ?Closure $factoryModifier = null;

    /**
     * Ajusta os atributos gerados para o teste de atualização: recebe o array
     * e devolve o array alterado.
     */
    protected ?Closure $updateAttributes = null;

    private array $uniqueAttributeSets = [];

    /**
     * Define o modelo testado e cria um registro dele em `$this->model`.
     *
     * @param class-string<Model> $model
     */
    public function eloquent(
        string $model,
        array $relations = [],
        array $legacyAttributes = [],
        ?Closure $factoryModifier = null,
        ?Closure $updateAttributes = null,
    ): Model {
        $this->eloquentModel = $model;
        $this->relations = $relations;
        $this->legacyAttributes = $legacyAttributes;
        $this->factoryModifier = $factoryModifier;
        $this->updateAttributes = $updateAttributes;

        return $this->model = $this->createNewModel();
    }

    /**
     * Registra os testes comuns a todos os modelos.
     */
    public static function registerModelTests(): void
    {
        test('create using eloquent', function () {
            $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());
        });

        test('update using eloquent', function () {
            $modelUpdated = clone $this->model;
            $modelUpdated->fill($this->getAttributesForUpdate());
            $modelUpdated->save();

            $this->assertDatabaseMissing($modelUpdated->getTable(), $this->model->getAttributes());
            $this->assertDatabaseHas($modelUpdated->getTable(), $this->removeTimestamps($modelUpdated->getAttributes()));
        });

        test('delete using eloquent', function () {
            $this->assertDatabaseHas($this->model->getTable(), $this->model->getAttributes());

            $this->model->delete();

            if (in_array(SoftDeletes::class, class_uses($this->model), true) || in_array(LegacySoftDeletes::class, class_uses($this->model), true)) {
                $this->assertSoftDeleted($this->model, deletedAtColumn: $this->model->getDeletedAtColumn());
            } else {
                $this->assertDatabaseMissing($this->model->getTable(), $this->model->getAttributes());
            }
        });

        test('find using eloquent', function () {
            $modelFound = $this->instanceNewEloquentModel()
                ->newQuery()
                ->find($this->model->getKey());

            $created = $this->model->getAttributes();
            $found = $modelFound->getAttributes();
            $expected = array_intersect_key($created, $found);

            $this->assertEquals($expected, $created);
        });

        test('relationships', function () {
            $this->assertRelationships();
        });

        test('has legacy attributes', function () {
            if (!empty($this->legacyAttributes) && get_parent_class($this->eloquentModel) === LegacyModel::class) {
                $this->assertEquals($this->createNewModel()->legacy, $this->legacyAttributes);
            }

            $this->assertTrue(true);
        });
    }

    public function getAttributesForCreate(): array
    {
        return $this->buildAttributes();
    }

    public function getAttributesForUpdate(): array
    {
        $attributes = $this->buildAttributes();

        return $this->updateAttributes ? ($this->updateAttributes)($attributes) : $attributes;
    }

    public function instanceNewEloquentModel(): Model
    {
        return new $this->eloquentModel;
    }

    public function createNewModel(): Model
    {
        $model = $this->instanceNewEloquentModel();
        $model->fill($this->getAttributesForCreate());
        $model->save();

        return $model;
    }

    protected function newFactory(): Factory
    {
        $factory = Factory::factoryForModel($this->eloquentModel);

        if ($this->factoryModifier) {
            $factory = ($this->factoryModifier)($factory);
        }

        return $factory;
    }

    /**
     * Gera atributos pela factory sem repetir combinações já usadas no teste,
     * para não violar restrições de unicidade.
     */
    protected function buildAttributes(): array
    {
        $generator = new class($this->newFactory())
        {
            public function __construct(private Factory $factory) {}

            public function attributes(): array
            {
                return $this->factory->make()->toArray();
            }
        };

        $unique = new UniqueGenerator($generator, 50, $this->uniqueAttributeSets);

        return $unique->attributes();
    }

    protected function removeTimestamps(array $attributes): array
    {
        unset($attributes['updated_at']);

        return $attributes;
    }

    protected function assertRelationships(): void
    {
        if (empty($this->relations)) {
            $this->assertTrue(true);
        }

        $factory = $this->newFactory();

        foreach ($this->relations as $relation => $class) {
            $type = $this->model->{$relation}();

            if ($type instanceof BelongsToMany) {
                [$modelClass, $pivotData] = is_array($class) ? $class : [$class, []];

                $model = $factory->hasAttached(Factory::factoryForModel($modelClass)->new(), $pivotData, $relation)->create();

                $this->assertCount(1, $model->{$relation});
                $this->assertInstanceOf($modelClass, $model->{$relation}->first());
            } elseif ($type instanceof HasMany || $type instanceof HasOne) {
                $method = 'has' . ucfirst($relation);
                $model = $factory->{$method}()->create();

                if ($type instanceof HasMany) {
                    $this->assertCount(1, $model->{$relation});
                    $this->assertInstanceOf($class, $model->{$relation}->first());
                } else {
                    $this->assertInstanceOf($class, $model->{$relation});
                }
            } elseif ($type instanceof BelongsTo) {
                $model = $factory->create();

                $this->assertInstanceOf($class, $model->{$relation}, sprintf('Failed asserting that relation [%s] is an instance of class [%s].', $relation, $class));
            }
        }
    }
}
