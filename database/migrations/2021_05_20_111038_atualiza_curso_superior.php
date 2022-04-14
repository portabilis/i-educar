<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AtualizaCursoSuperior extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            INSERT INTO modules.educacenso_curso_superior (curso_id, nome, classe_id, user_id, created_at, updated_at, grau_academico) VALUES  (\'0411G01\', \'Gestão fiscal e tributária\', 4, 1, \'NOW()\', NULL, 1);
            INSERT INTO modules.educacenso_curso_superior (curso_id, nome, classe_id, user_id, created_at, updated_at, grau_academico) VALUES  (\'0421S013\', \'Serviços jurídicos e cartoriais\', 4, 1, \'NOW()\', NULL, 1);
            INSERT INTO modules.educacenso_curso_superior (curso_id, nome, classe_id, user_id, created_at, updated_at, grau_academico) VALUES  (\'0421S014\', \'Serviços jurídicos e cartoriais\', 4, 1, \'NOW()\', NULL, 4);
            INSERT INTO modules.educacenso_curso_superior (curso_id, nome, classe_id, user_id, created_at, updated_at, grau_academico) VALUES  (\'0914A013\', \'Análises clínicas e toxicológicas\', 9, 1, \'NOW()\', NULL, 1);
            INSERT INTO modules.educacenso_curso_superior (curso_id, nome, classe_id, user_id, created_at, updated_at, grau_academico) VALUES  (\'0914A014\', \'Análises clínicas e toxicológicas\', 9, 1, \'NOW()\', NULL, 4);
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('
            DELETE FROM educacenso_curso_superior WHERE curso_id = \'0411G01\';
            DELETE FROM educacenso_curso_superior WHERE curso_id = \'0421S013\';
            DELETE FROM educacenso_curso_superior WHERE curso_id = \'0421S014\';
            DELETE FROM educacenso_curso_superior WHERE curso_id = \'0914A013\';
            DELETE FROM educacenso_curso_superior WHERE curso_id = \'0914A014\';
        ');
    }
}
