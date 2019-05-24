<?php

namespace App\Http\Controllers;

use App\Models\LogUnification;
use Illuminate\Http\Request;

class StudentLogUnificationController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Log de unificações de aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(999847);

        $unificationsQuery = LogUnification::query()->with('main.registrations');

        if ($request->get('ref_cod_escola')) {
            $schoolId = $request->get('ref_cod_escola');
            $unificationsQuery->whereHas('studentMain', function ($studentQuery) use ($schoolId) {
                $studentQuery->whereHas('registrations', function ($registrationsQuery) use ($schoolId){
                    $registrationsQuery->where('school_id', $schoolId);
                });
            });
        }

        return view('unification.student.index', ['unifications' => $unificationsQuery->paginate(20)]);
    }

    public function show(LogUnification $unification)
    {
        $this->breadcrumb('Unificação de aluno', [
            url('intranet/educar_index.php') => 'Escola',
            route('student_log_unification.index') => 'Log de unificações de aluno',
        ]);

        $this->menu(999847);

        return view('unification.student.show', ['unification' => $unification]);
    }

    public function undo(LogUnification $unification)
    {

    }
}
