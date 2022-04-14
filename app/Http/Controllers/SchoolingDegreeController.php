<?php

namespace App\Http\Controllers;

use App\Models\LegacySchoolingDegree;

class SchoolingDegreeController extends Controller
{
    /**
     * @param LegacySchoolingDegree $schoolingDegree
     *
     * @return View
     */
    public function show(LegacySchoolingDegree $schoolingDegree)
    {
        return response()->json($schoolingDegree);
    }
}
