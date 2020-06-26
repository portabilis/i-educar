<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleasePeriodRequest;
use App\Models\LegacyStageType;
use App\Models\ReleasePeriod;
use App\Models\ReleasePeriodDate;
use App\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReleasePeriodController extends Controller
{
    public function index(Request $request)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

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

        return view('release-period.index', ['releasePeriods' => $query->paginate(20)]);
    }

    /**
     * @return View
     */
    public function new()
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        return view('release-period.new', ['stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson()]);
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

        DB::commit();

        return redirect()
            ->route('release-period.index')
            ->with('success', 'Período cadastrado com sucesso.');
    }

    /**
     * @param ReleasePeriod $releasePeriod
     * @param $schools
     */
    private function createReleasePeriodSchools(ReleasePeriod $releasePeriod, $schools)
    {
        foreach ($schools as $school) {
            $releasePeriod->schools()->attach($school[0]);
        }
    }

    /**
     * @param ReleasePeriod $releasePeriod
     * @param $startDateArray
     * @param $endDateArray
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
     * @param ReleasePeriod $releasePeriod
     */
    public function show(ReleasePeriod $releasePeriod)
    {
        $this->breadcrumb('Período de lançamento de notas e faltas por etapa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::RELEASE_PERIOD);

        return view('release-period.show', ['releasePeriod' => $releasePeriod]);
    }
}
