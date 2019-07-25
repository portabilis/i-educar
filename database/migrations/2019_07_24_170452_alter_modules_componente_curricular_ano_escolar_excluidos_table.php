<?php

use App\Support\Database\WhenDeleted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterModulesComponenteCurricularAnoEscolarExcluidosTable extends Migration
{
    use WhenDeleted;

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('modules.componente_curricular_ano_escolar_excluidos');
        DB::statement('drop trigger trigger_when_deleted_modules_componente_curricular_ano_escolar on modules.componente_curricular_ano_escolar');
        DB::statement('drop function public.when_deleted_modules_componente_curricular_ano_escolar()');

        Schema::create('modules.componente_curricular_ano_escolar_excluidos', function (Blueprint $table) {
            $table->integer('id')->index();
            $table->integer('componente_curricular_id');
            $table->integer('ano_escolar_id');
            $table->decimal('carga_horaria',7,3)->nullable();
            $table->integer('tipo_nota')->nullable();
            $table->string('anos_letivos');
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });

        DB::statement("ALTER TABLE modules.componente_curricular_ano_escolar_excluidos ALTER COLUMN anos_letivos TYPE smallint[] USING anos_letivos::smallint[]");
        DB::statement("ALTER TABLE modules.componente_curricular_ano_escolar_excluidos ALTER COLUMN anos_letivos SET DEFAULT '{}'::smallint[]");

        $this->whenDeletedMoveTo('modules.componente_curricular_ano_escolar', 'modules.componente_curricular_ano_escolar_excluidos', [
            'id',
            'componente_curricular_id',
            'ano_escolar_id',
            'carga_horaria',
            'servidor_id',
            'tipo_nota',
            'anos_letivos',
            'updated_at',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropTriggerWhenDeleted('modules.componente_curricular_ano_escolar');

        Schema::dropIfExists('modules.componente_curricular_ano_escolar_excluidos');
    }

}
