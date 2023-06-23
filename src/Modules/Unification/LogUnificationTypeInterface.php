<?php

namespace iEducar\Modules\Unification;

use App\Models\LogUnification;

interface LogUnificationTypeInterface
{
    /**
     * @return string
     */
    public function getMainPersonName(LogUnification $logUnification);

    /**
     * @return array
     */
    public function getDuplicatedPeopleName(LogUnification $logUnification);

    /**
     * @return string
     */
    public static function getType();

    public function undo(LogUnification $logUnification);
}
