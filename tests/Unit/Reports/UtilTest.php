<?php

namespace Tests\Unit\Reports;

use App\Services\Reports\Util;
use Tests\TestCase;

class UtilTest extends TestCase
{
    public function test_format_workload_null()
    {
        $this->assertEquals('00:00', Util::formatWorkload(null));
    }

    public function test_format_workload_minute5()
    {
        $this->assertEquals('00:05', Util::formatWorkload(0.08));
    }

    public function test_format_workload_minute10()
    {
        $this->assertEquals('00:10', Util::formatWorkload(0.16));
    }

    public function test_format_workload_minute15()
    {
        $this->assertEquals('00:15', Util::formatWorkload(0.25));
    }

    public function test_format_workload_minute20()
    {
        $this->assertEquals('00:20', Util::formatWorkload(0.33));
    }

    public function test_format_workload_minute25()
    {
        $this->assertEquals('00:25', Util::formatWorkload(0.41));
    }

    public function test_format_workload_minute30()
    {
        $this->assertEquals('00:30', Util::formatWorkload(0.5));
    }

    public function test_format_workload_minute35()
    {
        $this->assertEquals('00:35', Util::formatWorkload(0.58));
    }

    public function test_format_workload_minute40()
    {
        $this->assertEquals('00:40', Util::formatWorkload(0.67));
    }

    public function test_format_workload_minute45()
    {
        $this->assertEquals('00:45', Util::formatWorkload(0.75));
    }

    public function test_format_workload_minute50()
    {
        $this->assertEquals('00:50', Util::formatWorkload(0.83));
    }

    public function test_format_workload_minute55()
    {
        $this->assertEquals('00:55', Util::formatWorkload(0.91));
    }

    public function test_format_workload_minute60()
    {
        $this->assertEquals('01:00', Util::formatWorkload(1));
    }

    public function test_nomeia_as_etapas_pelo_numero_de_modulos(): void
    {
        $this->assertSame([['step' => 'An', 'name' => 'ANUAL']], Util::moduleName());
        $this->assertSame([['step' => 1, 'name' => 'ANUAL', 'abbreviation' => 'ANUAL']], Util::moduleName(collect([1])));
        $this->assertSame('2° SEMESTRE', Util::moduleName(collect([1, 2]))[1]['name']);
        $this->assertSame('3° TRIM.', Util::moduleName(collect([1, 2, 3]))[2]['abbreviation']);
        $this->assertSame('4° BIMESTRE', Util::moduleName(collect([1, 2, 3, 4]))[3]['name']);
        $this->assertSame([], Util::moduleName(collect([1, 2, 3, 4, 5]))[0]);
    }

    public function test_aplica_a_mascara_e_formata_o_cep_com_oito_digitos(): void
    {
        $this->assertSame('123.456', Util::mask('123456789', '###.###'));
        $this->assertSame('88000-000', Util::formatPostcode('88000000'));
        $this->assertSame('8800', Util::formatPostcode('8800'));
        $this->assertNull(Util::formatPostcode(null));
    }

    public function test_a_situacao_final_da_matricula_prevalece_sobre_a_do_componente(): void
    {
        $this->assertSame('Transferido', Util::disciplineSituation('Aprovado', 'Transferido'));
        $this->assertSame('Remanejado', Util::disciplineSituation('Remanejado', 'Cursando'));
        $this->assertSame('Aprovado', Util::disciplineSituation('Aprovado', 'Cursando'));
    }
}
