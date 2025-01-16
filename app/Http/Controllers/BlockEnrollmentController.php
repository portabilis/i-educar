<?php

namespace App\Http\Controllers;

use App\Http\Requests\BlockEnrollmentRequest;
use App\Models\LegacySchoolGrade;
use App\Process;
use iEducar\Support\Exceptions\Exception;
use Illuminate\Support\Facades\DB;

class BlockEnrollmentController extends Controller
{
    public function update(BlockEnrollmentRequest $request)
    {
        $query = LegacySchoolGrade::query()
            ->whereHas('school', fn ($q) => $q->whereInstitution($request->get('ref_cod_instituicao')))
            ->when($request->get('ref_cod_escola'), fn ($q, $school) => $q->where('ref_cod_escola', $school))
            ->when($request->get('ref_cod_curso'), function ($q, $course) {
                $q->whereHas('grade', fn ($q) => $q->whereCourse($course));
            })
            ->whereRaw('anos_letivos @> ?', ['{' . $request->get('ano') . '}'])
            ->where('ativo', 1);

        $gradesCount = $query->count();

        if ($gradesCount === 0) {
            return redirect()->route('block-enrollment.edit')->withInput()->with('error', 'Nenhuma série da escola encontrada com os filtros selecionados');
        }

        if (empty($request->get('confirmation'))) {
            return redirect()->route('block-enrollment.edit')->withInput()->with('show-confirmation', $gradesCount);
        }

        try {
            DB::beginTransaction();
            $query->update([
                'bloquear_enturmacao_sem_vagas' => $request->get('bloquear_enturmacao_sem_vagas'),
                'updated_at' => now(),
            ]);
            DB::commit();
            session()->flash('success', 'Atualização em lote efetuada com sucesso.');
        } catch (Exception) {
            DB::rollBack();
            session()->flash('error', 'Atualização em lote não realizada.');
        }

        return redirect()->route('block-enrollment.edit');
    }

    public function edit()
    {
        $this->menu(Process::BLOCK_ENROLLMENT);
        $this->breadcrumb('Bloquear enturmação em lote', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        return view('block-enrollment.edit', ['user' => request()->user()]);
    }
}
