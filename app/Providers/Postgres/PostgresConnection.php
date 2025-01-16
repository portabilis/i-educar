<?php

namespace App\Providers\Postgres;

use Illuminate\Database\PostgresConnection as ParentPostgresConnection;

class PostgresConnection extends ParentPostgresConnection
{
    public function publicRun($query, $bindings = [], $forceUseWritePdo = false)
    {
        return parent::run($query, $bindings, function ($query, $bindings) use ($forceUseWritePdo) {
            if ($this->pretending()) {
                return [];
            }

            $statement = $this->prepared($this->getPdoForSelect($forceUseWritePdo)
                ->prepare($query));
            $this->bindValues($statement, $this->prepareBindings($bindings));
            $statement->execute();

            $lower = strtolower($query);
            if (str_contains($lower, 'insert') || str_contains($lower, 'update') || str_contains($lower, 'delete')) {
                $this->recordsHaveBeenModified();
            }

            return $statement;
        });
    }

    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;
    }
}
