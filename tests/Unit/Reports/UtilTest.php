<?php

use App\Services\Reports\Util;
use Tests\TestCase;

uses(TestCase::class);

test('format workload null', function () {
    $this->assertEquals('00:00', Util::formatWorkload(null));
});

test('format workload minute5', function () {
    $this->assertEquals('00:05', Util::formatWorkload(0.08));
});

test('format workload minute10', function () {
    $this->assertEquals('00:10', Util::formatWorkload(0.16));
});

test('format workload minute15', function () {
    $this->assertEquals('00:15', Util::formatWorkload(0.25));
});

test('format workload minute20', function () {
    $this->assertEquals('00:20', Util::formatWorkload(0.33));
});

test('format workload minute25', function () {
    $this->assertEquals('00:25', Util::formatWorkload(0.41));
});

test('format workload minute30', function () {
    $this->assertEquals('00:30', Util::formatWorkload(0.5));
});

test('format workload minute35', function () {
    $this->assertEquals('00:35', Util::formatWorkload(0.58));
});

test('format workload minute40', function () {
    $this->assertEquals('00:40', Util::formatWorkload(0.67));
});

test('format workload minute45', function () {
    $this->assertEquals('00:45', Util::formatWorkload(0.75));
});

test('format workload minute50', function () {
    $this->assertEquals('00:50', Util::formatWorkload(0.83));
});

test('format workload minute55', function () {
    $this->assertEquals('00:55', Util::formatWorkload(0.91));
});

test('format workload minute60', function () {
    $this->assertEquals('01:00', Util::formatWorkload(1));
});

test('nomeia as etapas pelo numero de modulos', function () {
    $this->assertSame([['step' => 'An', 'name' => 'ANUAL']], Util::moduleName());
    $this->assertSame([['step' => 1, 'name' => 'ANUAL', 'abbreviation' => 'ANUAL']], Util::moduleName(collect([1])));
    $this->assertSame('2° SEMESTRE', Util::moduleName(collect([1, 2]))[1]['name']);
    $this->assertSame('3° TRIM.', Util::moduleName(collect([1, 2, 3]))[2]['abbreviation']);
    $this->assertSame('4° BIMESTRE', Util::moduleName(collect([1, 2, 3, 4]))[3]['name']);
    $this->assertSame([], Util::moduleName(collect([1, 2, 3, 4, 5]))[0]);
});

test('aplica a mascara e formata o cep com oito digitos', function () {
    $this->assertSame('123.456', Util::mask('123456789', '###.###'));
    $this->assertSame('88000-000', Util::formatPostcode('88000000'));
    $this->assertSame('8800', Util::formatPostcode('8800'));
    $this->assertNull(Util::formatPostcode(null));
});

test('a situacao final da matricula prevalece sobre a do componente', function () {
    $this->assertSame('Transferido', Util::disciplineSituation('Aprovado', 'Transferido'));
    $this->assertSame('Remanejado', Util::disciplineSituation('Remanejado', 'Cursando'));
    $this->assertSame('Aprovado', Util::disciplineSituation('Aprovado', 'Cursando'));
});
