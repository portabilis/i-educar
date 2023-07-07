<?php

use App\Support\Database\AsView;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class() extends Migration
{
    use AsView;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->dropView('religions');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao ADD COLUMN deleted_at timestamp without time zone;');
        DB::unprepared('UPDATE pmieducar.religiao SET deleted_at = coalesce(data_exclusao, now()) WHERE ativo != 1;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME cod_religiao TO id;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao DROP COLUMN IF EXISTS ref_usuario_exc');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao DROP COLUMN IF EXISTS ref_usuario_cad');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME nm_religiao TO name;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME data_cadastro TO created_at;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME data_exclusao TO updated_at;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao DROP COLUMN IF EXISTS ativo;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME TO religions;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religions RENAME TO religiao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao ADD COLUMN ativo smallint DEFAULT 1;');
        DB::unprepared('UPDATE pmieducar.religiao SET ativo = 0 WHERE deleted_at IS NOT NULL;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao DROP COLUMN IF EXISTS deleted_at;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME id TO cod_religiao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME name TO nm_religiao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME created_at TO data_cadastro;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao RENAME updated_at TO data_exclusao;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao ADD COLUMN ref_usuario_exc integer;');
        DB::unprepared('ALTER TABLE IF EXISTS pmieducar.religiao ADD COLUMN ref_usuario_cad integer;');
        $this->createView('religions');
    }
};
