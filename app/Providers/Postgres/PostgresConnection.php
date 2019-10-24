<?php

namespace App\Providers\Postgres;

use Illuminate\Database\PostgresConnection as ParentPostgresConnection;
use Illuminate\Support\Str;

class PostgresConnection extends ParentPostgresConnection
{
    public function publicRun($query, $bindings = [], $useReadPdo = false)
    {
        return parent::run($query, $bindings, function ($query, $bindings) use ($useReadPdo) {
            if ($this->pretending()) {
                return [];
            }

            if (Str::startsWith(trim($query), 'SELECT')) {
                $useReadPdo = true;
            }

            $statement = $this->prepared($this->getPdoForSelect($useReadPdo)
                ->prepare($query));
            $this->bindValues($statement, $this->prepareBindings($bindings));
            $statement->execute();
            return $statement;
        });
    }

    public function setFetchMode($fetchMode)
    {
        $this->fetchMode = $fetchMode;
    }
}
