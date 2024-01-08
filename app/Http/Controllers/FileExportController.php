<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentFileExportRequest;
use App\Jobs\StudentFileExporterJob;
use App\Models\FileExport;
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

    public function store(StudentFileExportRequest $request)
    {
        $studentFileExport = FileExport::create([
            'user_id' => $request->user()->getKey(),
            'filename' => 'Alunos'
        ]);
        StudentFileExporterJob::dispatchSync(
            studentFileExport: $studentFileExport,
            args: [
                'year' => $request->get('ano'),
                'school' => $request->get('ref_cod_escola'),
                'course' => $request->get('ref_cod_curso'),
                'grade' => $request->get('ref_cod_serie'),
                'schoolClass' => $request->get('ref_cod_turma')
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
}
