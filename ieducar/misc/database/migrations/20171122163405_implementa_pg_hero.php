<?php

use Phinx\Migration\AbstractMigration;

class ImplementaPgHero extends AbstractMigration
{

    public function change()
    {
        $this->execute("CREATE TABLE \"pghero_query_stats\" (\"id\" serial PRIMARY KEY,
                                                       \"database\" text, \"user\" text, \"query\" text, \"query_hash\" bigint, \"total_time\" float, \"calls\" bigint, \"captured_at\" TIMESTAMP);

CREATE INDEX ON \"pghero_query_stats\" (\"database\",
                                      \"captured_at\");");
    }
}
