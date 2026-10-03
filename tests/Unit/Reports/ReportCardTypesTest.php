<?php

namespace Tests\Unit\Reports;

use iEducar\Reports\Contracts\ReportCardTypes;
use iEducar\Reports\DefaultReportCardTypes;
use Portabilis_Model_Report_TipoBoletim;
use Tests\TestCase;

class ReportCardTypesTest extends TestCase
{
    public function test_contract_resolves_to_default_implementation()
    {
        $this->assertInstanceOf(DefaultReportCardTypes::class, app(ReportCardTypes::class));
    }

    public function test_default_implementation_follows_report_package_availability()
    {
        $types = new DefaultReportCardTypes;

        if (class_exists(Portabilis_Model_Report_TipoBoletim::class)) {
            $tipoBoletim = Portabilis_Model_Report_TipoBoletim::getInstance();

            $this->assertSame($tipoBoletim->getEnums(), $types->getEnums());
            $this->assertSame($tipoBoletim->getReports(), $types->getReports());

            return;
        }

        $this->assertSame([], $types->getEnums());
        $this->assertSame([], $types->getReports());
    }

    public function test_binding_can_be_overridden_by_a_package()
    {
        $this->app->bind(ReportCardTypes::class, fn () => new class implements ReportCardTypes
        {
            public function getEnums(): array
            {
                return [1 => 'Modelo'];
            }

            public function getReports(): array
            {
                return [1 => 'report.card.model'];
            }
        });

        $this->assertSame([1 => 'Modelo'], app(ReportCardTypes::class)->getEnums());
        $this->assertSame([1 => 'report.card.model'], app(ReportCardTypes::class)->getReports());
    }
}
