<?php

namespace App\Support\Database;

use Illuminate\Support\Facades\DB;

trait WhenDeleted
{
    /**
     * Cria a função e a trigger que irá mover os dados excluídos da tabela
     * $from para a tabela $to.
     *
     * @param string $from
     * @param string $to
     * @param array  $columns
     *
     * @return void
     */
    public function whenDeletedMoveTo($from, $to, $columns)
    {
        DB::unprepared(
            $this->getCreateFunctionSql($from, $to, $columns)
        );

        DB::unprepared(
            $this->getCreateTriggerSql($from)
        );
    }

    /**
     * Apaga a trigger e a função que movem os dados excluídos da $table.
     *
     * @param string $table
     *
     * @return void
     */
    public function dropTriggerWhenDeleted($table)
    {
        DB::unprepared(
            $this->getDropTriggerSql($table)
        );

        DB::unprepared(
            $this->getDropFunctionSql($table)
        );
    }

    /**
     * Retorna o nome da função para a tabela.
     *
     * @param string $table
     *
     * @return string
     */
    public function getFunctionName($table)
    {
        return 'when_deleted_' . str_replace('.', '_', $table);
    }

    /**
     * Retorna o nome da trigger para a tabela.
     *
     * @param string $table
     *
     * @return string
     */
    public function getTriggerName($table)
    {
        return 'trigger_when_deleted_' . str_replace('.', '_', $table);
    }

    /**
     * Retorna as colunas que deverem ser inseridas.
     *
     * @param array $columns
     *
     * @return string
     */
    public function getColumns($columns)
    {
        $columns[] = 'updated_at';
        $columns[] = 'deleted_at';

        return implode(', ', $columns);
    }

    /**
     * Retorna os valores que deverão ser inseridos na tabela.
     *
     * @param array $columns
     *
     * @return string
     */
    public function getColumnsValues($columns)
    {
        $columns = array_map(function ($column) {
            return 'OLD.' . $column;
        }, $columns);

        $columns[] = 'NOW()';
        $columns[] = 'NOW()';

        return implode(', ', $columns);
    }

    /**
     * Retorna o SQL que cria a função.
     *
     * @param string $from
     * @param string $to
     * @param array  $fromColumns
     *
     * @return string
     */
    public function getCreateFunctionSql($from, $to, $fromColumns)
    {
        $function = $this->getFunctionName($from);
        $columns = $this->getColumns($fromColumns);
        $values = $this->getColumnsValues($fromColumns);

        return "
            CREATE FUNCTION {$function}() 
            RETURNS TRIGGER 
            LANGUAGE plpgsql AS
            $$
            BEGIN
                INSERT INTO {$to} ({$columns}) VALUES ({$values});
                RETURN OLD;
            END;
            $$;
        ";
    }

    /**
     * Retorna o SQL que apaga a função.
     *
     * @param string $table
     *
     * @return string
     */
    public function getDropFunctionSql($table)
    {
        $function = $this->getFunctionName($table);

        return "DROP FUNCTION IF EXISTS {$function}();";
    }

    /**
     * Retorna o SQL que cria a trigger.
     *
     * @param string $table
     *
     * @return string
     */
    public function getCreateTriggerSql($table)
    {
        $function = $this->getFunctionName($table);
        $trigger = $this->getTriggerName($table);

        return "
            CREATE TRIGGER {$trigger} AFTER DELETE
            ON {$table} FOR EACH ROW
            EXECUTE PROCEDURE {$function}();
        ";
    }

    /**
     * Retorna o SQL que cria a trigger.
     *
     * @param string $table
     *
     * @return string
     */
    public function getDropTriggerSql($table)
    {
        $trigger = $this->getTriggerName($table);

        return "DROP TRIGGER IF EXISTS {$trigger} on {$table};";
    }
}
