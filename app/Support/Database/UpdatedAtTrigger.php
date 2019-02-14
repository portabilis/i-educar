<?php

namespace App\Support\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait UpdatedAtTrigger
{
    /**
     * Retorna o nome da função que irá atualizar a coluna.
     *
     * @return string
     */
    private function getUpdateFunctionName()
    {
        return 'public.update_updated_at()';
    }

    /**
     * Retorna o nome da trigger para a tabela.
     *
     * @param string $table
     *
     * @return string
     */
    private function generateTriggerName($table)
    {
        return sprintf('update_%s', str_replace('.', '_', $table));
    }

    /**
     * Cria a coluna "updated_at" e a trigger para atualizar seu valor cada vez
     * que o registro for atualizado.
     *
     * @param string $table
     *
     * @return void
     */
    public function createUpdatedAtTrigger($table)
    {
        Schema::table($table, function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        $definition = 'CREATE TRIGGER %s BEFORE UPDATE ON %s FOR EACH ROW EXECUTE PROCEDURE %s;';

        $sql = sprintf(
            $definition,
            $this->generateTriggerName($table),
            $table,
            $this->getUpdateFunctionName()
        );

        DB::unprepared($sql);
    }

    /**
     * Remove a trigger e exclui a coluna "updated_at" da tabela.
     *
     * @param string $table
     *
     * @return void
     */
    public function dropUpdatedAtTrigger($table)
    {
        $definition = 'DROP TRIGGER %s ON %s;';

        $sql = sprintf(
            $definition,
            $this->generateTriggerName($table),
            $table
        );

        DB::unprepared($sql);

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
