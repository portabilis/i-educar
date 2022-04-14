<?php

namespace App\Support\Lighthouse;

use Exception;

abstract class AddSchema
{
    /**
     * Schema filename.
     *
     * @var string
     */
    protected $schema;

    /**
     * Add schema into root schema.
     *
     * @throws Exception
     *
     * @return string
     */
    public function handle()
    {
        if (empty($this->schema)) {
            throw new Exception('Schema filename not defined.');
        }

        if (file_exists($this->schema) === false) {
            throw new Exception("File {$this->schema} does not exists.");
        }

        return file_get_contents($this->schema);
    }
}
