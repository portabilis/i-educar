<?php

namespace Tests\Educacenso;

use iEducar\Modules\Educacenso\Formatters;
use Tests\TestCase;

class FormattersTest extends TestCase
{
    private function formatter(): object
    {
        return new class
        {
            use Formatters;
        };
    }

    public function test_texto_alfabetico_fica_sem_acentos_numeros_e_espacos_repetidos(): void
    {
        $this->assertSame('JOAO DA SILVA', $this->formatter()->convertStringToAlpha('  João 2 da   Silva! '));
    }

    public function test_texto_do_censo_mantem_numeros_ordinais_e_hifen(): void
    {
        $this->assertSame('RUA 7 - 1ª QUADRA', $this->formatter()->convertStringToCenso(' Rua 7 -   1ª quadra; '));
    }

    public function test_certidao_no_formato_novo_mantem_so_numeros_e_x(): void
    {
        $this->assertSame('1234X56', $this->formatter()->convertStringToCertNovoFormato('12.34-x/56'));
    }

    public function test_email_mantem_os_caracteres_validos(): void
    {
        $this->assertSame('ANA.ALVES-1@ESCOLA.BR', $this->formatter()->convertEmailToCenso('ána.alves-1@escola.br '));
    }
}
