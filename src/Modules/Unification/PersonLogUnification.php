<?php

namespace iEducar\Modules\Unification;

use App\Models\Individual;
use App\Models\LogUnification;

class PersonLogUnification implements LogUnificationTypeInterface
{
    public function getMainPersonName(LogUnification $logUnification)
    {
        // TODO: Implement getMainPersonName() method.
    }

    public function getDuplicatedPeopleName(LogUnification $logUnification)
    {
        // TODO: Implement getDuplicatedPeopleName() method.
    }

    public static function getType()
    {
        return Individual::class;
    }
}