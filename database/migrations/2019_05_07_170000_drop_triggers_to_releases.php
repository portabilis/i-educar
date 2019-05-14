<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class DropTriggersToReleases extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS impede_duplicacao_falta_aluno ON modules.falta_aluno;');
        DB::unprepared('DROP TRIGGER IF EXISTS impede_duplicacao_nota_aluno ON modules.nota_aluno;');
        DB::unprepared('DROP TRIGGER IF EXISTS impede_duplicacao_parecer_aluno ON modules.parecer_aluno;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared(
            '
                create
                    trigger impede_duplicacao_nota_aluno before insert
                        or update
                            on
                            modules.nota_aluno for each row execute procedure modules.impede_duplicacao_nota_aluno();
            '
        );
        DB::unprepared(
            '
                create
                    trigger impede_duplicacao_falta_aluno before insert
                        or update
                            on
                            modules.falta_aluno for each row execute procedure modules.impede_duplicacao_falta_aluno();
            '
        );
        DB::unprepared(
            '
                create
                    trigger impede_duplicacao_parecer_aluno before insert
                        or update
                            on
                            parecer_aluno for each row execute procedure modules.impede_duplicacao_parecer_aluno();
            '
        );
    }
}
