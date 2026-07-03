<?php

namespace Tests\Unit\Model;

use App\Models\LegacyOrganization;
use App\Models\LegacySchool;
use Tests\TestCase;

class LegacyOrganizationCnpjTest extends TestCase
{
    public function teste_mutator_normaliza_cnpj_ao_atribuir(): void
    {
        $organizacao = new LegacyOrganization;
        $organizacao->cnpj = '12.abc.345/01de-35';

        $this->assertSame('12ABC34501DE35', $organizacao->cnpj);
    }

    public function teste_mutator_converte_vazio_em_null(): void
    {
        $organizacao = new LegacyOrganization;
        $organizacao->cnpj = '';

        $this->assertNull($organizacao->cnpj);
    }

    public function teste_mutator_da_mantenedora_normaliza_cnpj(): void
    {
        $escola = new LegacySchool;
        $escola->cnpj_mantenedora_principal = '00.222.333/0001-81';

        $this->assertSame('00222333000181', $escola->cnpj_mantenedora_principal);
    }

    public function teste_where_cnpj_filtra_por_busca_parcial_normalizada(): void
    {
        $sql = LegacyOrganization::query()->whereCnpj('12.abc')->toRawSql();

        $this->assertStringContainsString("cnpj LIKE '%12ABC%'", $sql);
    }

    public function teste_when_com_valor_vazio_nao_aplica_filtro(): void
    {
        $sql = LegacyOrganization::query()
            ->when(limpaCnpj('./-'), fn ($query, $cnpj) => $query->whereCnpj($cnpj))
            ->toRawSql();

        $this->assertStringNotContainsString('cnpj', $sql);
    }
}
