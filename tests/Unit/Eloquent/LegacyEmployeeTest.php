<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployee;
use Tests\EloquentTestCase;

class LegacyEmployeeTest extends EloquentTestCase
{
    private LegacyEmployee $employee;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEmployee::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->employee = $this->createNewModel();
    }

    /** @test */
    public function getLoginAttribute()
    {
        $this->assertEquals($this->employee->getLoginAttribute(), $this->employee->login);
    }

    /** @test */
    public function getPasswordAttribute()
    {
        $this->assertEquals($this->employee->getPasswordAttribute(), $this->employee->password);
    }

    /** @test */
    public function setPasswordAttribute()
    {
        $this->employee->setPasswordAttribute('password');
        $this->assertEquals($this->employee->getPasswordAttribute(), 'password');
    }

    /** @test */
    public function getDepartmentIdAttribute()
    {
        $this->assertEquals($this->employee->getDepartmentIdAttribute(), $this->employee->ref_cod_setor_new);
    }

    /** @test */
    public function menuTypeAttribute()
    {
        $this->assertEquals($this->employee->getMenuTypeAttribute(), $this->employee->tipo_menu);
    }

    /** @test */
    public function getRememberTokenAttribute()
    {
        $this->assertEquals($this->employee->getRememberTokenAttribute(), $this->employee->status_token);
    }

    /** @test */
    public function setRememberTokenAttribute()
    {
        $this->employee->setRememberTokenAttribute('token');
        $this->assertEquals($this->employee->getRememberTokenAttribute(), 'token');
    }

    /** @test */
    public function getActiveAttribute()
    {
        $this->assertEquals($this->employee->getActiveAttribute(), $this->employee->ativo);
    }

    /** @test */
    public function getEnabledUserDate()
    {
        $this->assertEquals($this->employee->getEnabledUserDate(), $this->employee->data_reativa_conta);
    }

    /** @test */
    public function getPasswordUpdatedDate()
    {
        $this->assertEquals($this->employee->getPasswordUpdatedDate(), $this->employee->data_troca_senha);
    }
}
