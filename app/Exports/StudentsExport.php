<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StudentsExport implements FromView
{
    public function view(array $params): View
    {
        return view('exports.students', [
            'students' => []
        ]);
    }
}
