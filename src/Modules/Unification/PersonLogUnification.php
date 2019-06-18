<?php

namespace iEducar\Modules\Unification;

use App\Models\Individual;
use App\Models\LogUnification;

class PersonLogUnification implements LogUnificationTypeInterface
{
    /**
     * @param LogUnification $logUnification
     * @return string
     */
    public function getMainPersonName(LogUnification $logUnification)
    {
        // TODO: Implement getMainPersonName() method.
    }

    /**
     * @param LogUnification $logUnification
     * @return array
     */
    public function getDuplicatedPeopleName(LogUnification $logUnification)
    {
        // TODO: Implement getDuplicatedPeopleName() method.
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