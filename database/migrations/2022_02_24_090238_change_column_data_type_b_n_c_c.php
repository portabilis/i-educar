<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnDataTypeBNCC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE modules.bncc SET campo_experiencia = '10001' WHERE campo_experiencia = 'Traços, sons, cores e formas'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = '10002' WHERE campo_experiencia = 'O eu, o outro e o nós'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = '10003' WHERE campo_experiencia = 'Corpo, gestos e movimentos'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = '10004' WHERE campo_experiencia = 'Escuta, fala, pensamento e imaginação'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = '10005' WHERE campo_experiencia = 'Espaços, tempos, quantidades, relações e transformações'");

        DB::statement("ALTER TABLE modules.bncc ALTER COLUMN campo_experiencia TYPE INT USING campo_experiencia::integer");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE modules.bncc SET campo_experiencia = 'Traços, sons, cores e formas' WHERE campo_experiencia = '10001'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = 'O eu, o outro e o nós' WHERE campo_experiencia = '10002'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = 'Corpo, gestos e movimentos' WHERE campo_experiencia = '10003'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = 'Escuta, fala, pensamento e imaginação' WHERE campo_experiencia = '10004'");
        DB::statement("UPDATE modules.bncc SET campo_experiencia = 'Espaços, tempos, quantidades, relações e transformações' WHERE campo_experiencia = '10005'");

        DB::statement("ALTER TABLE modules.bncc ALTER COLUMN campo_experiencia TYPE VARCHAR USING campo_experiencia::char");
    }
}
