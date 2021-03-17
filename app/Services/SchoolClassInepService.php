<?php

namespace App\Services;

use App\Models\SchoolClassInep;

class SchoolClassInepService
{
    /**
     * @param SchoolClassInep $schoolClassInep
     *
     * @return SchoolClassInep
     */
    public function store(SchoolClassInep $schoolClassInep)
    {
        $schoolClassInep->save();

        return $schoolClassInep;
    }
}
