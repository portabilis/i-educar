<?php

namespace App\Http\Controllers;

use App\Http\Requests\SebExportRequest;
use App\Process;
use App\Services\SebExport\ExportService;
use Illuminate\Http\Response as ResponseReturn;
use Illuminate\View\View;
use Response;

class SebExportController extends Controller
{
    public function index(): View
    {
        $this->breadcrumb('Exportação para o SEB', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
        $this->menu(Process::SEB_EXPORT);

        return view('seb-export.index');
    }

    public function export(SebExportRequest $request, ExportService $exportService): ResponseReturn
    {
        $exportContent = $exportService->export($request->all());

        $headers = [
            'Content-type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="exportacao_seb.txt"',
            'Content-Length' => strlen($exportContent),
        ];

        return Response::make($exportContent, 200, $headers);
    }
}
