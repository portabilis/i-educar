<?php

namespace iEducar\Modules\Unification;

use App\Models\Individual;
use App\Models\LogUnification;

class PersonLogUnification implements LogUnificationTypeInterface
{
    /**
     * @param LogUnification $logUnification
     *
     * @return string
     */
    public function getMainPersonName(LogUnification $logUnification)
    {
        if ($logUnification->main) {
            return $logUnification->main->real_name;
        }

        return 'Pessoa não encontrada';
    }

    /**
     * @param LogUnification $logUnification
     *
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
