<?php

namespace Database\Custom;

use Illuminate\Database\Schema\Grammars\PostgresGrammar;
use Illuminate\Support\Fluent;

class TypeIntergerArray extends PostgresGrammar
{
    public function __construct(public ?int $size = null)
    {
    }

    /**
     * Create the column definition for a type array.
     *
     * @param Fluent $column
     *
     * @return string
     */
    protected function typeInt_Array(Fluent $column): string
    {
        if ($this->size === null) {
            return 'int[]';
        }

        return 'int' . $this->size . '[]';
    }
}
