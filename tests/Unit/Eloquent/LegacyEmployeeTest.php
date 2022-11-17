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
    public function attributes()
    {
        $this->assertEquals($this->model->matricula, $this->model->login);
        $this->assertEquals($this->model->senha, $this->model->password);
        $this->model->password = 'password';
        $this->assertEquals('password', $this->model->password);
        $this->assertEquals($this->model->ref_cod_setor_new, $this->model->department_id);
        $this->assertEquals($this->model->tipo_menu, $this->model->menu_type);
        $this->assertEquals($this->model->status_token, $this->model->remember_token);
        $this->model->remember_token = 'token';
        $this->assertEquals('token', $this->model->remember_token);
        $this->assertEquals($this->model->ativo, $this->model->active);
        $this->assertEquals($this->model->data_reativa_conta, $this->model->getEnabledUserDate());
        $this->assertEquals($this->model->data_troca_senha, $this->model->getPasswordUpdatedDate());
    }
}
