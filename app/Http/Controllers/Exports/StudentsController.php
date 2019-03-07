<?php

namespace App\Http\Controllers\Exports;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentsExport as Request;
use Maatwebsite\Excel\Facades\Excel;

class StudentsController extends Controller
{
    public function export(Request $request)
    {
        $params = $request->allWithTranslatedKeys();
        $export = new StudentsExport($params);

        return Excel::download($export, 'alunos.xlsx');
    }
}
