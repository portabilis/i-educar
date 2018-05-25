<?php

use Phinx\Migration\AbstractMigration;

class AjustesTabelaOrgaoEmissorEducacenso extends AbstractMigration
{
    public function up()
    {
        $this->execute(
           "UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'M MILITAR'
             WHERE sigla = 'MMil';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'MAE'
             WHERE sigla = 'MAer';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'MEX'
             WHERE sigla = 'MExe';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'MMA'
             WHERE sigla = 'MMar';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'DPF'
             WHERE sigla = 'PF';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'I CLA'
             WHERE sigla = 'CIC';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CRAS'
             WHERE sigla = 'CRESS';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CRE'
             WHERE sigla = 'CONRE';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CREFIT'
             WHERE sigla = 'CREFITO';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CRV'
             WHERE sigla = 'CRMV';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CRPRE'
             WHERE sigla = 'CONRERP';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'CRRC'
             WHERE sigla = 'CORE';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'OUTRO'
             WHERE sigla = 'OEmi';

            UPDATE cadastro.orgao_emissor_rg
               SET sigla = 'EST'
             WHERE sigla = 'DExt';"
        );

        $this->execute("SELECT SETVAL('cadastro.orgao_emissor_rg_idorg_rg_seq', (SELECT MAX(idorg_rg) + 1 FROM cadastro.orgao_emissor_rg));");

        $this->execute(
            "INSERT INTO cadastro.orgao_emissor_rg (codigo_educacenso, descricao, sigla, situacao)
             VALUES (83, 'Departamento Estadual de Trânsito', 'DETRAN', 'I');"
        );

        $this->execute(
            "UPDATE cadastro.orgao_emissor_rg
                SET descricao = 'Secretaria de Segurança Pública'
              WHERE codigo_educacenso = 10;"
        );
    }
}
