<?php

namespace App\Http\Controllers;

use App\Models\LogUnification;
use App\Models\Student;
use App\Services\StudentUnificationService;
use Illuminate\Http\Request;
use Throwable;

class StudentLogUnificationController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Log de unificações de aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(999847);

        $unificationsQuery = LogUnification::query()->with('main.registrations');
        $unificationsQuery->where('type', Student::class);

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
        $this->breadcrumb('Detalhe da unificação', [
            url('intranet/educar_index.php') => 'Escola',
            route('student-log-unification.index') => 'Log de unificações de aluno',
        ]);

        $this->menu(999847);

        return view('unification.student.show', ['unification' => $unification]);
    }

    public function undo(LogUnification $unification, StudentUnificationService $unificationService)
    {
        try {
            $unificationService->undo($unification);
        } catch (Throwable $exception) {
            return redirect(
                route('student-log-unification.show', ['unification' => $unification->id]))
                ->withErrors([$exception->getMessage()]
                );
        }

        return redirect(route('student-log-unification.index'))->with('success', 'Unificação desfeita com sucesso');
    }
}
