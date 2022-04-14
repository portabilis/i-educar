<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleasePeriodRequest;
use App\Models\LegacyInstitution;
use App\Models\LegacyStageType;
use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use App\Process;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Throwable;

class ReleasePeriodController extends Controller
{
    public const INFO_MESSAGE = '<b>Atenção!</b> Este recurso foi atualizado e agora funciona diferente!
Cadastre os períodos que deseja liberar o lançamento de notas e faltas por etapa, podendo criar mais de 1 período de lançamento em todas as etapas';

    public function __construct()
    {
        $this->beta = true;
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        Session::flash('notice', self::INFO_MESSAGE);

        return view(
            'release-period.form',
            [
                'stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson(),
                'releasePeriod' => new ReleasePeriod(),
                'data' => $this->applyFilters($request),
                'canView' => $request->user()->can('view', Process::RELEASE_PERIOD),
                'canModify' => $request->user()->can('modify', Process::RELEASE_PERIOD),
                'canRemove' => $request->user()->can('remove', Process::RELEASE_PERIOD),
            ]
        );
    }

    /**
     * @param ReleasePeriod $releasePeriod
     *
     * @return View
     */
    public function form(ReleasePeriod $releasePeriod)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        $this->fillData($releasePeriod);

        Session::flash('notice', self::INFO_MESSAGE);

        return view(
            'release-period.form',
            [
                'stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson(),
                'releasePeriod' => $releasePeriod,
                'data' => $this->applyFilters(request()),
                'canView' => request()->user()->can('view', Process::RELEASE_PERIOD),
                'canModify' => request()->user()->can('modify', Process::RELEASE_PERIOD),
                'canRemove' => request()->user()->can('remove', Process::RELEASE_PERIOD),
            ]
        );
    }

    /**
     * @param ReleasePeriodRequest $request
     */
    public function create(ReleasePeriodRequest $request)
    {
        DB::beginTransaction();

        /** @var ReleasePeriod $releasePeriod */
        $releasePeriod = ReleasePeriod::create([
            'year' => $request->get('ano'),
            'stage_type_id' => $request->get('stage_type'),
            'stage' => $request->get('stage'),
        ]);

        $this->createReleasePeriodSchools($releasePeriod, $request->get('escola'));
        $this->createReleasePeriodDates($releasePeriod, $request->get('start_date'), $request->get('end_date'));

        try {
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            return redirect()
                ->route('release-period.index')
                ->with('error', 'Não foi possível cadastrar o período.');
        }

        return redirect()
            ->route('release-period.index')
            ->with('success', 'Período cadastrado com sucesso.');
    }

    /**
     * @param ReleasePeriod $releasePeriod
     *
     * @return View
     */
    public function show(ReleasePeriod $releasePeriod)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        return view('release-period.show', [
            'releasePeriod' => $releasePeriod,
            'canView' => request()->user()->can('view', Process::RELEASE_PERIOD),
            'canModify' => request()->user()->can('modify', Process::RELEASE_PERIOD),
            'canRemove' => request()->user()->can('remove', Process::RELEASE_PERIOD),
        ]);
    }

    /**
     * @param ReleasePeriod        $releasePeriod
     * @param ReleasePeriodRequest $request
     *
     * @return RedirectResponse
     */
    public function update(ReleasePeriod $releasePeriod, ReleasePeriodRequest $request)
    {
        DB::beginTransaction();

        $releasePeriod->schools()->sync([]);
        $releasePeriod->periodDates()->delete();

        $releasePeriod->update([
            'year' => $request->get('ano'),
            'stage_type_id' => $request->get('stage_type'),
            'stage' => $request->get('stage'),
        ]);

        $this->createReleasePeriodSchools($releasePeriod, $request->get('escola'));
        $this->createReleasePeriodDates($releasePeriod, $request->get('start_date'), $request->get('end_date'));

        try {
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            return redirect()
                ->route('release-period.index')
                ->with('error', 'Não foi possível atualizar o período.');
        }

        return redirect()
            ->route('release-period.index')
            ->with('success', 'Período atualizado com sucesso.');
    }

    /**
     * @param ReleasePeriod $releasePeriod
     *
     * @return RedirectResponse
     */
    public function delete(Request $request)
    {
        DB::beginTransaction();

        foreach ($request->get('periods') as $periodId) {
            $releasePeriod = ReleasePeriod::find($periodId);
            $releasePeriod->schools()->sync([]);
            $releasePeriod->periodDates()->delete();
            $releasePeriod->delete();
        }

        try {
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();

            return redirect()
                ->route('release-period.index')
                ->with('error', 'Não foi possível excluir o período.');
        }

        return redirect()
            ->route('release-period.index')
            ->with('success', 'Período(s) excluído(s) com sucesso.');
    }

    /**
     * Popula os campos
     *
     * @param ReleasePeriod $releasePeriod
     */
    private function fillData(ReleasePeriod $releasePeriod)
    {
        if (!$releasePeriod->exists) {
            return;
        }

        $schools = $releasePeriod->schools->pluck('cod_escola')->toArray();

        request()->request->add([
            'ano' => $releasePeriod->year,
            'ref_cod_instituicao' => app(LegacyInstitution::class)->getKey(),
            'stage_type' => $releasePeriod->stage_type_id,
            'stage' => $releasePeriod->stage,
            'escola' => $schools,
        ]);
    }

    /**
     * @param ReleasePeriod $releasePeriod
     * @param               $schools
     */
    private function createReleasePeriodSchools(ReleasePeriod $releasePeriod, $schools)
    {
        foreach ($schools as $school) {
            $releasePeriod->schools()->attach($school[0]);
        }
    }

    /**
     * @param ReleasePeriod $releasePeriod
     * @param               $startDateArray
     * @param               $endDateArray
     */
    private function createReleasePeriodDates(ReleasePeriod $releasePeriod, $startDateArray, $endDateArray)
    {
        foreach ($startDateArray as $key => $startDate) {
            $endDate = $endDateArray[$key];
            ReleasePeriodDate::create([
                'release_period_id' => $releasePeriod->getKey(),
                'start_date' => \DateTime::createFromFormat('d/m/Y', $startDate),
                'end_date' => \DateTime::createFromFormat('d/m/Y', $endDate),
            ]);
        }
    }

    /**
     * @param $request
     *
     * @return LengthAwarePaginator
     */
    private function applyFilters($request)
    {
        $query = ReleasePeriod::query();

        $ano = $request->get('ano');
        $query->when($ano, function ($query) use ($ano) {
            $query->where('year', $ano);
        });

        $school = $request->get('ref_cod_escola');
        $query->when($school, function ($query) use ($school) {
            $query->whereHas('schools', function ($schoolsQuery) use ($school) {
                $schoolsQuery->where('cod_escola', $school);
            });
        });

        $stage = $request->get('stage');
        $query->when($stage, function ($query) use ($stage) {
            $query->where('stage', $stage);
        });

        return $query->paginate(20);
    }
}
