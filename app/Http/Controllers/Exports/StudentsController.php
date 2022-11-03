<?php

namespace App\Http\Controllers\Exports;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentsExport as Request;
use iEducar\Support\Repositories\StudentRepository;
use Maatwebsite\Excel\Facades\Excel;
 
class StudentsController extends Controller
{
    protected $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    public function export(Request $request)
    {
        $collection = $this->studentRepository->list($request->allWithTranslatedKeys());
        $export = new StudentsExport($collection);

        return Excel::download($export, 'alunos.xlsx');
    }
}
 