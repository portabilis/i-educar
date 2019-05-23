<?php

namespace App\Services;

use App\Exceptions\Unification\InactiveMainUnification;
use App\Exceptions\Unification\UndoInactiveUnification;
use App\Models\LogUnification;
use Illuminate\Support\Facades\Auth;

class StudentUnificationService
{
    /**
     * @param LogUnification $unification
     */
    public function undo(LogUnification $unification)
    {
        $this->canUndo($unification);

        $adapter = $unification->getAdapter();

        $adapter->undo($unification);

        $unification->active = false;
        $unification->updated_by = Auth::user()->cod_usuario;
        $unification->save();
    }

    /**
     * @param LogUnification $unification
     */
    public function canUndo(LogUnification $unification)
    {
        if (!$unification->active) {
            throw new UndoInactiveUnification();
        }

        if (empty($unification->main)) {
            throw new InactiveMainUnification($unification);
        }
    }

}
