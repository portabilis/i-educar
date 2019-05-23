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
use Illuminate\Support\MessageBag;
use Illuminate\View\View;
use Throwable;

class StudentLogUnificationController extends Controller
{
    public function index(LogUnification $logUnification)
    {
        $this->breadcrumb('Log de unificações de aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(659);

        $unifications = $logUnification::all();
        return view('unification.student.index', ['unifications' => $unifications]);
    }
}
