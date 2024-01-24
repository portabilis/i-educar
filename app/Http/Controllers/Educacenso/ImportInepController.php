<?php

namespace App\Http\Controllers\Educacenso;

use App\Exceptions\Educacenso\ImportException;
use App\Exceptions\Educacenso\InvalidFileDate;
use App\Exceptions\Educacenso\InvalidFileYear;
use App\Exceptions\Educacenso\InvalidSchoolInep;
use App\Http\Controllers\Controller;
use App\Http\Requests\EducacensoImportInepRequest;
use App\Jobs\EducacensoInepImportJob;
use App\Models\EducacensoInepImport;
use App\Models\SchoolInep;
use App\Process;
use App\Services\Educacenso\EducacensoImportInepService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportInepController extends Controller
{

    public function index()
    {
        $this->breadcrumb('Importação INEPs', [
            url('intranet/educar_educacenso_index.php') => 'Educacenso',
        ]);
        $this->menu(Process::EDUCACENSO_IMPORT_INEP);
        $imports = EducacensoInepImport::query()
            ->orderByDesc('created_at')
            ->paginate();

        return view('educacenso.import-inep.index', [
            'imports' => $imports
        ]);
    }

    public function store(EducacensoImportInepRequest $request)
    {
        $files = $request->file('arquivos');
        $jobs = [];
        $schoolCount = 0;
        try {
            DB::beginTransaction();
            foreach ($files as $file) {
                $schoolsData = EducacensoImportInepService::getDataBySchool($file);
                foreach ($schoolsData as $schoolData) {
                    $schoolLine = explode('|', $schoolData[0]);
                    $fileDate = $schoolLine[3];
                    $year = $request->get('ano');
                    $this->validateFileYear($fileDate, $year);
                    $schoolInep = $schoolLine[1];
                    $schoolName = mb_strtoupper($schoolLine[5]);
                    $this->validateSchoolInep($schoolInep, $schoolName);
                    $educacensoInepImport = EducacensoInepImport::create([
                        'year' => $year,
                        'user_id' => $request->user()->getKey(),
                        'school_name' => $schoolName
                    ]);
                    array_walk_recursive($schoolData, static fn (&$item) => $item = mb_convert_encoding($item, 'HTML-ENTITIES', 'UTF-8'));
                    $schoolCount++;
                    $jobs[] = [
                        $educacensoInepImport,
                        $schoolData
                    ];
                }
            }
            DB::commit();
            foreach ($jobs as $job) {
                EducacensoInepImportJob::dispatch(...$job);
            }
        } catch (Exception $exception) {
            return redirect(route('educacenso.import.inep.create'))
                ->with('error', $exception instanceof ImportException ? $exception->getMessage() : 'Não foi possível realizar a importação!');
        }

        return redirect()->route('educacenso.import.inep.index')->with('success', "Iniciado o processamento dos INEPs de {$schoolCount} escolas.");
    }

    private function validateSchoolInep(int $inep, string $schoolName): void
    {
        $doesntExist = SchoolInep::query()->where('cod_escola_inep', $inep)->doesntExist();
        if ($doesntExist) {
            throw new InvalidSchoolInep($inep, $schoolName);
        }
    }

    private function validateFileYear(string $fileDate, int $year): void
    {
        $validator = Validator::make(['year' => $fileDate], [
            'year' => [
                'required',
                'date_format:d/m/Y'
            ]
        ]);
        if ($validator->fails()) {
            throw new InvalidFileDate();
        }
        $fileYear = Carbon::createFromFormat('d/m/Y', $fileDate)->year;
        if ($year !== $fileYear) {
            throw new InvalidFileYear($fileYear, $year);
        }
    }

    public function create()
    {
        $this->menu(Process::EDUCACENSO_IMPORT_INEP);
        $this->breadcrumb('Importação INEPs', [
            url('intranet/educar_educacenso_index.php') => 'Educacenso',
        ]);

        return view('educacenso.import-inep.create');
    }
}
