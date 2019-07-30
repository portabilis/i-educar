<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

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
    public function createUpdatedAtTrigger($table)
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
    public function dropUpdatedAtTrigger($table)
    {
        $definition = 'DROP TRIGGER %s ON %s;';

        $sql = sprintf(
            $definition,
            $this->generateTriggerName($table),
            $table
        );

        DB::unprepared($sql);
    }
}
