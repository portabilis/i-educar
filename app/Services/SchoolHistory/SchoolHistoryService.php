<?php

namespace App\Services\SchoolHistory;

use App\Services\SchoolHistory\Objects\SchoolHistory;

class SchoolHistoryService
{
    private $schoolHistory;

    public function __construct(SchoolHistory $schoolHistory)
    {
        $this->schoolHistory = $schoolHistory;
    }

    public function addData($data)
    {
        $this->schoolHistory->addDataGroupByDiscipline($data);
    }

    public function getSchoolHistory()
    {
        return $this->schoolHistory;    
    }
}
