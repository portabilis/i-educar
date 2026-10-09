<?php

namespace Tests\Unit\Eloquent;

use App\Models\Employee;
use App\Models\LegacyPeriod;
use App\Models\LegacySchoolClass;
use App\Models\LegacySchoolClassTeacher;
use App\Models\LegacySchoolClassTeacherDiscipline;
use iEducar\Modules\Servidores\Model\TipoVinculo;
use Tests\EloquentTestCase;

class LegacySchoolClassTeacherTest extends EloquentTestCase
{
    protected $relations = [
        'schoolClass' => LegacySchoolClass::class,
        'schoolClassTeacherDisciplines' => LegacySchoolClassTeacherDiscipline::class,
        'period' => LegacyPeriod::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacySchoolClassTeacher::class;
    }

    public function test_link_type_name(): void
    {
        $names = [TipoVinculo::EFETIVO => 'Efetivo', TipoVinculo::TEMPORARIO => 'Temporário', TipoVinculo::TERCEIRIZADO => 'Terceirizado', TipoVinculo::CLT => 'CLT', 99 => null];

        foreach ($names as $type => $name) {
            $this->model->tipo_vinculo = $type;

            $this->assertSame($name, $this->model->linkTypeName);
        }
    }

    public function test_employee_relation(): void
    {
        $this->assertInstanceOf(Employee::class, $this->model->employee);
    }
}
