<?php

use iEducar\Reports\Contracts\ReportCardTypes;
use iEducar\Reports\DefaultReportCardTypes;
use Tests\TestCase;

uses(TestCase::class);

test('contract resolves to default implementation', function () {
    $this->assertInstanceOf(DefaultReportCardTypes::class, app(ReportCardTypes::class));
});

test('default implementation follows report package availability', function () {
    $types = new DefaultReportCardTypes;

    if (class_exists(Portabilis_Model_Report_TipoBoletim::class)) {
        $tipoBoletim = Portabilis_Model_Report_TipoBoletim::getInstance();

        $this->assertSame($tipoBoletim->getEnums(), $types->getEnums());
        $this->assertSame($tipoBoletim->getReports(), $types->getReports());

        return;
    }

    $this->assertSame([], $types->getEnums());
    $this->assertSame([], $types->getReports());
});

test('binding can be overridden by a package', function () {
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
});
