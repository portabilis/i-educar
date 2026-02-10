<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    use AsView;

    public function up(): void
    {
        $this->dropView('cadastro.endereco_externo');
        $this->dropView('cadastro.endereco_pessoa');
        $this->dropView('cadastro.v_fone_pessoa');
        $this->dropView('cadastro.v_pessoa_fj');
        $this->dropView('cadastro.v_pessoafj_count');
    }
};
