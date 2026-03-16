<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('cadastro.endereco_pessoa');
        $this->dropView('public.bairro');
        $this->dropView('public.distrito');
        $this->dropView('public.logradouro');
        $this->dropView('public.municipio');
        $this->dropView('public.pais');
        $this->dropView('public.uf');
        $this->dropView('cadastro.v_endereco');
        $this->dropView('cadastro.endereco_externo');
    }
};
