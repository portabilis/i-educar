<?php

namespace App\Http\Controllers\Educacenso;

use App\Exceptions\Educacenso\ImportException;
use App\Http\Controllers\Controller;
use App\Http\Requests\EducacensoImportRequest;
use App\Models\EducacensoImport;
use App\Process;
use App\Services\Educacenso\HandleFileService;
use App\Services\Educacenso\ImportServiceFactory;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ImportController extends Controller
{
    public function import(EducacensoImportRequest $request)
    {
        $file = $request->file('arquivo');

        try {
            $yearImportService = ImportServiceFactory::createImportService(
                $request->get('ano'),
                DateTime::createFromFormat('d/m/Y', $request->get('data_entrada_matricula'))
            );

            $importFileService = new HandleFileService($yearImportService, Auth::user());

            $importFileService->handleFile($file);
        } catch (ImportException $exception) {
            return redirect('/intranet/educar_importacao_educacenso.php')->with('error', $exception->getMessage());
        }

        return redirect()->route('educacenso.history');
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Historico de importações', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->menu(Process::EDUCACENSO_IMPORT_HISTORY);

        $imports = EducacensoImport::orderBy('created_at', 'desc')->get();

        return view('educacenso.import.index', compact('imports'));
    }
}
