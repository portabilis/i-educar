<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEmployee;
use Tests\EloquentTestCase;

class LegacyEmployeeTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEmployee::class;
    }

    /** @test */
    public function getLoginAttribute()
    {
        $this->assertEquals($this->model->matricula, $this->model->login);
    }

    /** @test */
    public function getPasswordAttribute()
    {
        $this->assertEquals($this->model->senha, $this->model->password);
    }

    /** @test */
    public function setPasswordAttribute()
    {
        $this->model->password = 'password';
        $this->assertEquals('password', $this->model->password);
    }

    /** @test */
    public function getDepartmentIdAttribute()
    {
        $this->assertEquals($this->model->ref_cod_setor_new, $this->model->department_id);
    }

    /** @test */
    public function menuTypeAttribute()
    {
        $this->assertEquals($this->model->tipo_menu, $this->model->menu_type);
    }

    /** @test */
    public function getRememberTokenAttribute()
    {
        $this->assertEquals($this->model->status_token, $this->model->remember_token);
    }

    /** @test */
    public function setRememberTokenAttribute()
    {
        $this->model->remember_token = 'token';
        $this->assertEquals('token', $this->model->remember_token);
    }

    /** @test */
    public function getActiveAttribute()
    {
        $this->assertEquals($this->model->ativo, $this->model->active);
    }

    /** @test */
    public function getEnabledUserDate()
    {
        $this->assertEquals($this->model->data_reativa_conta, $this->model->getEnabledUserDate());
    }

    /** @test */
    public function getPasswordUpdatedDate()
    {
        $this->assertEquals($this->model->data_troca_senha, $this->model->getPasswordUpdatedDate());
    }
}
