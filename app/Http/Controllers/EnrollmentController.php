<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnrollmentRequest;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\Frequencia;
use App\Models\FrequenciaAluno;
use App\Services\EnrollmentService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class EnrollmentController extends Controller
{
    /**
     * Renderiza a view da enturmação.
     *
     * @return View
     */
    public function viewEnroll(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $this->breadcrumb('Enturmar matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $enableCancelButton = $enrollmentService->isEnrolled($schoolClass, $registration);
        $anotherClassroomEnrollments = $enrollmentService->anotherClassroomEnrollments($schoolClass, $registration);

        return view('enrollments.enroll', [
            'registration' => $registration,
            'enrollments' => $registration->activeEnrollments()->get(),
            'schoolClass' => $schoolClass,
            'enableCancelButton' => $enableCancelButton,
            'anotherClassroomEnrollments' => $anotherClassroomEnrollments,
        ]);
    }

    public function viewEnrollManutencao(
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass,
        EnrollmentService $enrollmentService
    ) {
        $this->breadcrumb('Enturmar matrícula', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(578);

        $enableCancelButton = $enrollmentService->isEnrolled($schoolClass, $registration);
        $anotherClassroomEnrollments = $enrollmentService->anotherClassroomEnrollments($schoolClass, $registration);

        return view('enrollments.enrollmanutencao', [
            'registration' => $registration,
            'enrollments' => $registration->activeEnrollments()->get(),
            'schoolClass' => $schoolClass,
            'enableCancelButton' => $enableCancelButton,
            'anotherClassroomEnrollments' => $anotherClassroomEnrollments,
        ]);
    }

    /**
     * @return RedirectResponse
     */
    public function enroll(
        EnrollmentService $enrollmentService,
        EnrollmentRequest $request,
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass
    ) {
        if(!empty($request->input('cod_turma_origem'))){
        $tipoTurma = DB::table('pmieducar.turma')
            ->select(DB::raw('CASE WHEN tipo_atendimento = 5 THEN 1
                     ELSE 0 END'))
            ->where([['cod_turma', '=', $request->input('cod_turma_origem')]])
            ->get(); 

        if ($tipoTurma[0]->case == 0) {
            $dataUltimaFrequencia = DB::table('modules.frequencia')
                ->where([['ref_cod_turma', '=', $request->input('cod_turma_origem')]])->orderBy('data', 'desc')->get(['data'])->take(1);

            $data_solicitacao = dataToBanco($request->input('enrollment_date'));

            if ($data_solicitacao <= $dataUltimaFrequencia[0]->data) {
                return redirect()->back()->with('error', 'Não é possível realizar a operação, existem frequências registradas no período. Data de enturmação/saída: '.$data_solicitacao.'; Data da frequencia: '.$dataUltimaFrequencia[0]->data.'.');
                die();
            }
        }

        if ($tipoTurma[0]->case == 1) {

            $dataUltimoAtendimento = DB::table('modules.conteudo_ministrado_aee')
                ->where([['ref_cod_matricula', '=', $registration['cod_matricula']]])->orderBy('data', 'desc')->get(['data'])->take(1);

            $data_solicitacao = dataToBanco($request->input('enrollment_date'));

            if ($data_solicitacao <= $dataUltimoAtendimento[0]->data) {
                return redirect()->back()->with('error', 'AEE - Não é possível realizar a operação, existem frequências registradas no período. Data de enturmação/saída: '.$data_solicitacao.'; Data da frequencia: '.$dataUltimoAtendimento[0]->data.'.');
                die();
            }
        }
    }

        DB::beginTransaction();
        $date = Carbon::createFromFormat('d/m/Y', $request->input('enrollment_date'));

        if ($request->input('is_relocation') || $request->input('is_cancellation')) {
            $enrollmentFromId = $request->input('enrollment_from_id');
            $enrollment = $registration->enrollments()->whereKey($enrollmentFromId)->firstOrFail();

            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
            } catch (Throwable $throwable) {
                DB::rollback();

                return redirect()->back()->with('error', $throwable->getMessage());
            }
        }

        if ($request->input('is_cancellation')) {
            DB::commit();

            return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
        }

        $previousEnrollment = $enrollmentService->getPreviousEnrollmentAccordingToRelocationDate($registration);

        // Se for um remanejamento e a matrícula anterior tiver data de saída antes da data base (ou não houver data base)
        // marca a matrícula como "remanejada" e reordena o sequencial da turma de origem
        if ($request->input('is_relocation') && $previousEnrollment) {
            $enrollmentService->markAsRelocated($previousEnrollment);
            $enrollmentService->reorderSchoolClassAccordingToRelocationDate($previousEnrollment);
        }

        try {
            $enrollmentService->enroll($registration, $schoolClass, $date);
        } catch (Throwable $throwable) {
            DB::rollback();

            return redirect()->back()->with('error', $throwable->getMessage());
        }

        DB::commit();

        return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
    }

     /**
     * @return RedirectResponse
     */
    public function enrollmanutencao(
        EnrollmentService $enrollmentService,
        EnrollmentRequest $request,
        LegacyRegistration $registration,
        LegacySchoolClass $schoolClass
    ) {
        
        $tipoTurma = DB::table('pmieducar.turma')
            ->select(DB::raw('CASE WHEN tipo_atendimento = 5 THEN 1
                     ELSE 0 END'))
            ->where([['cod_turma', '=', $request->input('cod_turma_origem')]])
            ->get(); 

        if ($tipoTurma[0]->case == 0) {
            $dataUltimaFrequencia = DB::table('modules.frequencia')
                ->where([['ref_cod_turma', '=', $request->input('cod_turma_origem')]])->orderBy('data', 'desc')->get(['data'])->take(1);

            $data_solicitacao = dataToBanco($request->input('enrollment_date'));


            $frequencia = Frequencia::where('ref_cod_turma', $request->input('cod_turma_origem'))->where('data', '>=', $data_solicitacao)->orderBy('id', 'DESC')->get();
            foreach($frequencia as $list) {
                FrequenciaAluno::where('ref_frequencia',$list['id'])->where('ref_cod_matricula', $registration['cod_matricula'])->delete();
            }

           
        }

        if ($tipoTurma[0]->case == 1) {

            $dataUltimoAtendimento = DB::table('modules.conteudo_ministrado_aee')
                ->where([['ref_cod_matricula', '=', $registration['cod_matricula']]])->orderBy('data', 'desc')->get(['data'])->take(1);

            $data_solicitacao = dataToBanco($request->input('enrollment_date'));

            $frequencia = Frequencia::where('ref_cod_turma', $request->input('cod_turma_origem'))->where('data', '>=', $data_solicitacao)->orderBy('id', 'DESC')->get();
            foreach($frequencia as $list) {
                FrequenciaAluno::where('ref_frequencia',$list['id'])->where('ref_cod_matricula', $registration['cod_matricula'])->delete();
            }
        }

        DB::beginTransaction();
        $date = Carbon::createFromFormat('d/m/Y', $request->input('enrollment_date'));

        if ($request->input('is_relocation') || $request->input('is_cancellation')) {
            $enrollmentFromId = $request->input('enrollment_from_id');
            $enrollment = $registration->enrollments()->whereKey($enrollmentFromId)->firstOrFail();

            try {
                $enrollmentService->cancelEnrollment($enrollment, $date);
            } catch (Throwable $throwable) {
                DB::rollback();

                return redirect()->back()->with('error', $throwable->getMessage());
            }
        }

        if ($request->input('is_cancellation')) {
            DB::commit();

            return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
        }

        $previousEnrollment = $enrollmentService->getPreviousEnrollmentAccordingToRelocationDate($registration);

        // Se for um remanejamento e a matrícula anterior tiver data de saída antes da data base (ou não houver data base)
        // marca a matrícula como "remanejada" e reordena o sequencial da turma de origem
        if ($request->input('is_relocation') && $previousEnrollment) {
            $enrollmentService->markAsRelocated($previousEnrollment);
            $enrollmentService->reorderSchoolClassAccordingToRelocationDate($previousEnrollment);
        }

        try {
            $enrollmentService->enroll($registration, $schoolClass, $date);
        } catch (Throwable $throwable) {
            DB::rollback();

            return redirect()->back()->with('error', $throwable->getMessage());
        }

        DB::commit();

        return redirect('/intranet/educar_matricula_det.php?cod_matricula=' . $registration->id)->with('success', 'Enturmação feita com sucesso.');
    }
}
