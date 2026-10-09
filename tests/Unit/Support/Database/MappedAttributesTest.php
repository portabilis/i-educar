<?php

use App\Support\Database\MappedAttributes;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->abstract = new class extends Model
    {
        use MappedAttributes;

        protected $fillable = [
            'cod',
            'nome',
        ];

        public function getMappedAttributes()
        {
            return [
                'cod' => 'id',
                'nome' => 'name',
            ];
        }
    };
});

test('get mapped attribute', function () {
    $model = new $this->abstract([
        'cod' => 1,
        'nome' => 'Mapped',
    ]);

    $this->assertEquals(1, $model->id);
    $this->assertEquals('Mapped', $model->name);
});

test('set mapped attribute', function () {
    $model = new $this->abstract([
        'cod' => 1,
        'nome' => 'Mapped',
    ]);

    $model->id = 2;
    $model->name = 'Attribute';

    $this->assertEquals(2, $model->id);
    $this->assertEquals('Attribute', $model->name);
});

test('get translate mapped attribute method', function () {
    $model = new $this->abstract;

    $this->assertEquals('id', $model->getTranslateMappedAttribute('cod'));
    $this->assertEquals('name', $model->getTranslateMappedAttribute('nome'));
});

test('sem mapa definido nao ha atributos mapeados', function () {
    $model = new class extends Model
    {
        use MappedAttributes;
    };

    $this->assertSame([], $model->getMappedAttributes());
});
