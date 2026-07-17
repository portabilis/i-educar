<?php

namespace Tests\Educacenso;

use App\Models\Educacenso\Registro10;
use iEducar\Modules\Educacenso\Model\RedeLocal;
use Tests\TestCase;

class Registro10RedeLocalTest extends TestCase
{
    public function test_rede_local_converte_cada_opcao_para_o_codigo_do_inep()
    {
        $registro = new Registro10;

        $registro->redeLocal = [RedeLocal::NENHUMA];
        $this->assertSame(0, $registro->redeLocal());

        $registro->redeLocal = [RedeLocal::A_CABO];
        $this->assertSame(1, $registro->redeLocal());

        $registro->redeLocal = [RedeLocal::WIRELESS];
        $this->assertSame(2, $registro->redeLocal());

        $registro->redeLocal = [RedeLocal::A_CABO_E_WIRELESS];
        $this->assertSame(3, $registro->redeLocal());
    }

    public function test_rede_local_sem_selecao_retorna_null()
    {
        $registro = new Registro10;
        $registro->redeLocal = [];

        $this->assertNull($registro->redeLocal());
    }
}
