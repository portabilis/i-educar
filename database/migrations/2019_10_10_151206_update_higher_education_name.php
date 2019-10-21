<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateHigherEducationName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::update("
            UPDATE modules.educacenso_curso_superior
            SET nome = CASE curso_id
                WHEN '142C01' THEN 'Pedagogia (Ciências da Educação)'
                WHEN '145F01' THEN 'Ciências Biológicas'
                WHEN '145F02' THEN 'Ciências Naturais'
                WHEN '145F05' THEN 'Educação Religiosa'
                WHEN '145F08' THEN 'Filosofia'
                WHEN '145F09' THEN 'Física'
                WHEN '145F10' THEN 'Geografia'
                WHEN '145F11' THEN 'História'
                WHEN '145F15' THEN 'Letras - Língua Portuguesa'
                WHEN '145F18' THEN 'Matemática'
                WHEN '145F21' THEN 'Química'
                WHEN '145F24' THEN 'Ciências Sociais'
                WHEN '146P01' THEN 'Licenciatura para a Educação Profissional e Tecnológica'
                WHEN '210A01' THEN 'Bacharelado Interdisciplinar em Artes'
                WHEN '220H01' THEN 'Bacharelado Interdisciplinar Ciências Humanas'
                WHEN '314E02' THEN 'Ciências Econômicas'
                WHEN '623E01' THEN 'Engenharia Florestal'
                WHEN '720S01' THEN 'Bacharelado Interdisciplinar Ciências da Saúde'
            END
            WHERE curso_id IN (
                '142C01', '145F01', '145F02', '145F05', '145F08', '145F09',
                '145F10', '145F11', '145F18', '145F21', '145F24', '146P01',
                '210A01', '220H01', '314E02', '623E01', '720S01', '145F15'
            )
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
