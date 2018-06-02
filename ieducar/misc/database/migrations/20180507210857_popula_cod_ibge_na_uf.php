<?php

use Phinx\Migration\AbstractMigration;

class PopulaCodIbgeNaUf extends AbstractMigration
{
    public function change()
    {
        $this->execute("
            UPDATE public.uf SET cod_ibge = '11' WHERE idpais = 45 AND sigla_uf = 'RO';
            UPDATE public.uf SET cod_ibge = '12' WHERE idpais = 45 AND sigla_uf = 'AC';
            UPDATE public.uf SET cod_ibge = '13' WHERE idpais = 45 AND sigla_uf = 'AM';
            UPDATE public.uf SET cod_ibge = '14' WHERE idpais = 45 AND sigla_uf = 'RR';
            UPDATE public.uf SET cod_ibge = '15' WHERE idpais = 45 AND sigla_uf = 'PA';
            UPDATE public.uf SET cod_ibge = '16' WHERE idpais = 45 AND sigla_uf = 'AP';
            UPDATE public.uf SET cod_ibge = '17' WHERE idpais = 45 AND sigla_uf = 'TO';
            UPDATE public.uf SET cod_ibge = '21' WHERE idpais = 45 AND sigla_uf = 'MA';
            UPDATE public.uf SET cod_ibge = '22' WHERE idpais = 45 AND sigla_uf = 'PI';
            UPDATE public.uf SET cod_ibge = '23' WHERE idpais = 45 AND sigla_uf = 'CE';
            UPDATE public.uf SET cod_ibge = '24' WHERE idpais = 45 AND sigla_uf = 'RN';
            UPDATE public.uf SET cod_ibge = '25' WHERE idpais = 45 AND sigla_uf = 'PB';
            UPDATE public.uf SET cod_ibge = '26' WHERE idpais = 45 AND sigla_uf = 'PE';
            UPDATE public.uf SET cod_ibge = '27' WHERE idpais = 45 AND sigla_uf = 'AL';
            UPDATE public.uf SET cod_ibge = '28' WHERE idpais = 45 AND sigla_uf = 'SE';
            UPDATE public.uf SET cod_ibge = '29' WHERE idpais = 45 AND sigla_uf = 'BA';
            UPDATE public.uf SET cod_ibge = '31' WHERE idpais = 45 AND sigla_uf = 'MG';
            UPDATE public.uf SET cod_ibge = '32' WHERE idpais = 45 AND sigla_uf = 'ES';
            UPDATE public.uf SET cod_ibge = '33' WHERE idpais = 45 AND sigla_uf = 'RJ';
            UPDATE public.uf SET cod_ibge = '35' WHERE idpais = 45 AND sigla_uf = 'SP';
            UPDATE public.uf SET cod_ibge = '41' WHERE idpais = 45 AND sigla_uf = 'PR';
            UPDATE public.uf SET cod_ibge = '42' WHERE idpais = 45 AND sigla_uf = 'SC';
            UPDATE public.uf SET cod_ibge = '43' WHERE idpais = 45 AND sigla_uf = 'RS';
            UPDATE public.uf SET cod_ibge = '50' WHERE idpais = 45 AND sigla_uf = 'MS';
            UPDATE public.uf SET cod_ibge = '51' WHERE idpais = 45 AND sigla_uf = 'MT';
            UPDATE public.uf SET cod_ibge = '52' WHERE idpais = 45 AND sigla_uf = 'GO';
            UPDATE public.uf SET cod_ibge = '53' WHERE idpais = 45 AND sigla_uf = 'DF';
        ");
    }
}
