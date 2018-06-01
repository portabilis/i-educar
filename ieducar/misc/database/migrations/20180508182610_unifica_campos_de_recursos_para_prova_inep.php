<?php

use Phinx\Migration\AbstractMigration;

class UnificaCamposDeRecursosParaProvaInep extends AbstractMigration
{
    public function up()
    {
        $this->execute('ALTER TABLE pmieducar.aluno ADD COLUMN recursos_prova_inep INTEGER[];');

        $this->execute(
            'UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 1)
              WHERE recurso_prova_inep_aux_ledor = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 2)
              WHERE recurso_prova_inep_aux_transcricao = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 3)
              WHERE recurso_prova_inep_guia_interprete = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 4)
              WHERE recurso_prova_inep_interprete_libras = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 5)
              WHERE recurso_prova_inep_leitura_labial = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 6)
              WHERE recurso_prova_inep_prova_ampliada_16 = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 7)
              WHERE recurso_prova_inep_prova_ampliada_20 = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 8)
              WHERE recurso_prova_inep_prova_ampliada_24 = 1;
            
             UPDATE pmieducar.aluno
                SET recursos_prova_inep = array_append(recursos_prova_inep, 9)
              WHERE recurso_prova_inep_prova_braille = 1;'
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE pmieducar.aluno DROP COLUMN recursos_prova_inep;');
    }
}
