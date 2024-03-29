<?php

namespace App\Support\Database;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait PrimaryKey
{
    /**
     * Create a new column ID with primary key
     *
     *
     * @return void
     */
    public function createPrimaryKey($tableName)
    {
        $primaryKey = $this->getPrimaryKeyInfo($tableName);
        if ($primaryKey) {
            $keyName = $primaryKey->constraint_name;
            $arrayColumns = json_decode($primaryKey->columns, true);

            DB::statement("alter table {$tableName} drop constraint if exists " . $keyName);

            Schema::table($tableName, function (Blueprint $table) use ($arrayColumns) {
                $table->unique($arrayColumns);
            });
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->increments('id');
        });
    }

    public function removePrimaryKey($tableName, $keyColumns)
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('id');
        });

        Schema::table($tableName, function (Blueprint $table) use ($keyColumns) {
            if ($keyColumns) {
                $table->dropUnique($keyColumns);

                $table->primary($keyColumns);
            }
        });
    }

    /**
     * @return mixed
     */
    private function getPrimaryKeyInfo($table)
    {
        $sql = <<<'SQL'
            SELECT tco.constraint_name,
                   array_to_json(array_agg(kcu.column_name::text)) AS columns
            FROM information_schema.table_constraints tco
            JOIN information_schema.key_column_usage kcu ON kcu.constraint_name = tco.constraint_name
                                                        AND kcu.constraint_schema = tco.constraint_schema
                                                        AND kcu.constraint_name = tco.constraint_name
            WHERE kcu.table_schema = :schema
              AND kcu.table_name = :table
              AND tco.constraint_type = 'PRIMARY KEY'
            GROUP BY tco.constraint_name
SQL;

        $arrayTableName = explode('.', $table);
        $schema = $arrayTableName[0];
        $name = $arrayTableName[1];

        return DB::selectOne($sql, [$schema, $name]);
    }

    /**
     * @return mixed
     */
    public function getTableName($table)
    {
        return explode('.', $table)[1];
    }

    public function dropForeignKey($tableName, $foreignKey)
    {
        Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
            $table->dropForeign($foreignKey);
        });
    }

    public function createUniqueIndex($tableName, $columnsForeignKey)
    {
        Schema::table($tableName, function (Blueprint $table) use ($columnsForeignKey) {
            $table->unique($columnsForeignKey);
        });
    }

    /**
     * @param $schema
     */
    public function createConstraint($table, $columnsForeignKey, $foreignKey): void
    {
        $collumns = implode(', ', $columnsForeignKey);
        $sql = "ALTER TABLE {$table} ADD CONSTRAINT {$foreignKey} PRIMARY KEY({$collumns});";

        DB::unprepared($sql);
    }
}
