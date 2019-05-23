<?php

namespace App\Http\Controllers;

use App\Exceptions\Enrollment\ExistsActiveEnrollmentException;
use App\Http\Requests\BatchEnrollmentRequest;
use App\Http\Requests\CancelBatchEnrollmentRequest;
use App\Models\LegacySchoolClass;
use App\Models\LogUnification;
use App\Services\EnrollmentService;
use App\Services\RegistrationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Throwable;

class StudentLogUnificationController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Log de unificações de aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(999847);

        //todo: implementar paginação
        $unifications = LogUnification::query()->with('main.registrations')->student()->limit(20)->get();

        if ($request->get('ref_cod_escola')) {
            $schoolId = $request->get('ref_cod_escola');
            $unifications = $unifications->filter(function ($item) use ($schoolId) {
                return in_array($schoolId, $item->main->registrations->pluck('school_id')->all());
            });
        }

        return view('unification.student.index', ['unifications' => $unifications]);
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
