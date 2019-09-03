<?php

namespace Tests\Unit\Support\Database;

use App\Support\Database\MappedAttributes;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class MappedAttributesTest extends TestCase
{
    /**
     * @var Model
     */
    private $abstract;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->abstract = new class extends Model {
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
    }

    /**
     * @return void
     */
    public function testGetMappedAttribute()
    {
        $model = new $this->abstract([
            'cod' => 1,
            'nome' => 'Mapped',
        ]);

        $this->assertEquals(1, $model->id);
        $this->assertEquals('Mapped', $model->name);
    }

    /**
     * @return void
     */
    public function testSetMappedAttribute()
    {
        $model = new $this->abstract([
            'cod' => 1,
            'nome' => 'Mapped',
        ]);

        $model->id = 2;
        $model->name = 'Attribute';

        $this->assertEquals(2, $model->id);
        $this->assertEquals('Attribute', $model->name);
    }

    /**
     * @return void
     */
    public function testGetTranslateMappedAttributeMethod()
    {
        $model = new $this->abstract();

        $this->assertEquals('id', $model->getTranslateMappedAttribute('cod'));
        $this->assertEquals('name', $model->getTranslateMappedAttribute('nome'));
    }
}
