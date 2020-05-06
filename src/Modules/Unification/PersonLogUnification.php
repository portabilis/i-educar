<?php

namespace iEducar\Modules\Unification;

use App\Models\Individual;
use App\Models\LogUnification;
use App\Models\Student;

class PersonLogUnification implements LogUnificationTypeInterface
{
    /**
     * @param LogUnification $logUnification
     * @return string
     */
    public function getMainPersonName(LogUnification $logUnification)
    {
        return $logUnification->main->real_name;
    }

    /**
     * @param LogUnification $logUnification
     * @return array
     */
    public function getDuplicatedPeopleName(LogUnification $logUnification)
    {
        return (array) json_decode($logUnification->duplicates_name);
    }

    /**
     * @return string
     */
    public static function getType()
    {
        return Individual::class;
    }

    public function undo(LogUnification $logUnification)
    {
        // TODO: Implement undo() method.
    }
}
