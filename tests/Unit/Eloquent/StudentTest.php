<?php

namespace Tests\Unit\App\Models;

use App\Models\Student;
use Tests\EloquentTestCase;

class StudentTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return Student::class;
    }
}
