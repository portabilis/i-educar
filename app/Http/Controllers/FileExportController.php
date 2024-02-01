<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileExportRequest;
use App\Jobs\FileExporterJob;
use App\Models\FileExport;
use App\Models\LegacyRegistration;
use App\Models\LegacySchool;
use App\Models\LegacySchoolClass;
use App\Process;
use Illuminate\Http\Request;

class FileExportController extends Controller
{
    public function index(Request $request)
    {
        $this->menu(Process::DOCUMENT_EXPORT);
        $this->breadcrumb('Exportações', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $exports = FileExport::query()
            ->where('user_id', $request->user()->getKey())
            ->orderByDesc('created_at')
            ->paginate();

        return view('file_export.index', [
            'exports' => $exports,
        ]);
    }

    public function store(FileExportRequest $request)
    {
        $fileExport = FileExport::create([
            'user_id' => $request->user()->getKey(),
            'filename' => $this->buildFileName(
                schoolId: $request->get('ref_cod_escola'),
                schoolClassId: $request->get('ref_cod_turma'),
                registrationId: $request->get('matricula')
            ),
        ]);

        FileExporterJob::dispatch(
            fileExport: $fileExport,
            args: [
                'year' => $request->get('ano'),
                'school' => $request->get('ref_cod_escola'),
                'course' => $request->get('ref_cod_curso'),
                'grade' => $request->get('ref_cod_serie'),
                'schoolClass' => $request->get('ref_cod_turma'),
                'registration' => $request->get('matricula'),
            ]
        );

        return redirect()->route('file.export.index');
    }

    public function create()
    {
        $this->menu(Process::DOCUMENT_EXPORT);
        $this->breadcrumb('Nova Exportação', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('file.export.index') => 'Exportações',
        ]);

        return view('file_export.create');
    }

    private function removeSpecialCharacters($string): string
    {
        return preg_replace('/[^\w\s]/u', '', $string);
    }

    private function buildFileName(int $schoolId, int $schoolClassId, ?int $registrationId): string
    {
        if ($registrationId) {
            $registration = LegacyRegistration::query()->find($registrationId, [
                'cod_matricula',
                'ref_cod_aluno',
            ]);

            return mb_strtoupper($this->removeSpecialCharacters($registration->name)) . " ({$registration->ref_cod_aluno})";
        }

        $school = LegacySchool::query()->with([
            'person:idpes,nome',
            'organization:idpes,fantasia',
        ])->find($schoolId, [
            'cod_escola',
            'ref_idpes',
        ]);

        $schoolClass = LegacySchoolClass::query()
            ->whereKey($schoolClassId)
            ->first([
                'nm_turma',
                'ano',
            ]);

        $schoolName = mb_strtoupper($this->removeSpecialCharacters($school->name));
        $schoolClassName = mb_strtoupper($this->removeSpecialCharacters($schoolClass->nm_turma)) . ' (' . $schoolClass->ano . ')';

        return $schoolName . ' - ' . $schoolClassName;
    }
}
