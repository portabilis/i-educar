<?php

namespace Tests\Unit\Models\Educacenso;

use App\Models\Educacenso\Registro50;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use Tests\TestCase;

class Registro50Test extends TestCase
{
    public function test_get_property_returns_register_type_column(): void
    {
        $registro50 = new Registro50;
        $registro50->registro = '50';

        self::assertSame('50', $registro50->getProperty(1));
    }

    public function test_get_property_returns_first_curricular_component_column(): void
    {
        $registro50 = new Registro50;
        $registro50->componentes = ['1101'];

        self::assertSame('1101', $registro50->getProperty(9));
    }

    public function test_get_property_returns_last_curricular_component_column(): void
    {
        $registro50 = new Registro50;
        $registro50->componentes = array_fill(0, 25, '');
        $registro50->componentes[24] = '1220';

        self::assertSame('1220', $registro50->getProperty(33));
    }

    public function test_get_property_returns_itinerary_area_column(): void
    {
        $registro50 = new Registro50;
        $registro50->areaItinerario = [TipoItinerarioFormativo::MATEMATICA];

        self::assertSame(1, $registro50->getProperty(35));
    }

    public function test_get_property_returns_zero_when_itinerary_area_is_not_selected(): void
    {
        $registro50 = new Registro50;
        $registro50->areaItinerario = [TipoItinerarioFormativo::MATEMATICA];

        self::assertSame(0, $registro50->getProperty(34));
    }

    public function test_get_property_returns_empty_when_itinerary_area_is_not_informed(): void
    {
        $registro50 = new Registro50;
        $registro50->areaItinerario = null;

        self::assertSame('', $registro50->getProperty(34));
    }

    public function test_get_property_returns_teaches_technical_itinerary_column(): void
    {
        $registro50 = new Registro50;
        $registro50->lecionaItinerarioTecnicoProfissional = 1;

        self::assertSame(1, $registro50->getProperty(38));
    }

    public function test_get_property_returns_null_when_column_is_not_mapped(): void
    {
        $registro50 = new Registro50;

        self::assertNull($registro50->getProperty(39));
    }
}
