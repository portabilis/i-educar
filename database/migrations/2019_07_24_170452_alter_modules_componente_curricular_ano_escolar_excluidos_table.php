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
        $this->dropTriggerWhenDeleted('modules.componente_curricular_ano_escolar');
        Schema::dropIfExists('modules.componente_curricular_ano_escolar_excluidos');

        Schema::create('modules.componente_curricular_ano_escolar_excluidos', function (Blueprint $table) {
            $table->increments('id');
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
            'componente_curricular_id',
            'ano_escolar_id',
            'carga_horaria',
            'tipo_nota',
            'anos_letivos',
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
