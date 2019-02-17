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
     * Atualiza a coluna updated_at com a data atual.
     *
     * @param string $table
     *
     * @return void
     */
    private function runUpdateColumn($table)
    {
        $definition = 'UPDATE %s SET updated_at = now();';

        $sql = sprintf($definition, $table);

        DB::unprepared($sql);
    }

    /**
     * Adiciona a trigger a tabela.
     *
     * @param string $table
     *
     * @return void
     */
    private function runCreateTrigger($table)
    {
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
     * Remove a trigger da tabela.
     *
     * @param string $table
     *
     * @return void
     */
    private function runDropTrigger($table)
    {
        $definition = 'DROP TRIGGER %s ON %s;';

        $sql = sprintf(
            $definition,
            $this->generateTriggerName($table),
            $table
        );

        DB::unprepared($sql);
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
            $table->timestamp('updated_at')->nullable()->default(DB::raw('NOW()'));
        });

        $this->runUpdateColumn($table);
        $this->runCreateTrigger($table);
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
        $this->runDropTrigger($table);

        Schema::table($table, function (Blueprint $table) {
            $table->dropColumn('updated_at');
        });
    }
}
