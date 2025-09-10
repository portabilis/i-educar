<?php

namespace App\Http\Controllers;

use App\Process;
use App\Services\FinalStatusImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinalStatusImportController extends Controller
{
    protected FinalStatusImportService $service;

    public function __construct(FinalStatusImportService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $this->menu(Process::FINAL_STATUS_IMPORT);
        $this->breadcrumb('Importação de Situação Final', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);
        $situations = $this->service->getStatus();
        $expectedColumns = $this->service->getExpectedColumns();

        return view('final-status-import.index', [
            'expectedColumns' => $expectedColumns,
            'situations' => $situations,
            'user' => request()->user(),
        ]);
    }

    public function upload(Request $request)
    {
        $this->authorize('modify', Process::FINAL_STATUS_IMPORT);
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv|max:20480',
        ], attributes: ['file' => 'Arquivo']);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $file = $request->file('file');

            $analysis = $this->service->analyzeUploadedFile($file);

            session([
                'import_analysis' => $analysis,
                'import_file_temp' => $file->getRealPath(),
                'import_original_name' => $file->getClientOriginalName(),
            ]);

            return redirect()->route('final-status-import.analysis')
                ->with('success', 'Arquivo enviado e analisado com sucesso!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => 'Erro ao processar o arquivo: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function analysis()
    {
        $this->authorize('view', Process::FINAL_STATUS_IMPORT);

        if (!session('import_analysis')) {
            return redirect()->route('final-status-import.index')
                ->withErrors(['error' => 'Nenhum arquivo foi enviado. Por favor, faça o upload novamente.']);
        }

        $this->menu(Process::FINAL_STATUS_IMPORT);
        $this->breadcrumb('Análise do Arquivo', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('final-status-import.index') => 'Importação de Situação Final',
        ]);

        $analysis = session('import_analysis');

        return view('final-status-import.analysis', compact('analysis'));
    }

    public function showMapping()
    {
        $this->authorize('modify', Process::FINAL_STATUS_IMPORT);

        if (!session('import_analysis')) {
            return redirect()->route('final-status-import.index')
                ->withErrors(['error' => 'Sessão expirada. Por favor, faça o upload do arquivo novamente.']);
        }

        $this->menu(Process::FINAL_STATUS_IMPORT);
        $this->breadcrumb('Mapeamento de Colunas', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('final-status-import.index') => 'Importação de Situação Final',
        ]);

        $analysis = session('import_analysis');
        $headers = $analysis['headers'];
        $expectedColumns = $this->service->getExpectedColumns();

        $autoMapping = $this->service->autoMapColumns($headers);

        return view('final-status-import.mapping', compact('headers', 'expectedColumns', 'autoMapping'));
    }

    public function import(Request $request)
    {
        $this->authorize('modify', Process::FINAL_STATUS_IMPORT);

        if (!session('import_analysis')) {
            return redirect()->route('final-status-import.index')
                ->withErrors(['error' => 'Sessão expirada. Por favor, faça o upload do arquivo novamente.']);
        }

        $validator = Validator::make($request->all(), [
            'column_mapping' => 'required|array',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $analysis = session('import_analysis');
            $columnMapping = $request->input('column_mapping');
            $ignoreApproved = $request->boolean('ignore_approved');

            $requiredColumns = $this->service->getRequiredColumns();
            $missingColumns = [];

            foreach ($requiredColumns as $column) {
                if (empty($columnMapping[$column])) {
                    $missingColumns[] = $column;
                }
            }

            if (!empty($missingColumns)) {
                $columnTranslations = $this->service->getRequiredColumnsTranslations();
                $missingColumnNames = array_map(function ($column) use ($columnTranslations) {
                    return $columnTranslations[$column] ?? $column;
                }, $missingColumns);

                return redirect()->back()
                    ->withErrors(['column_mapping' => 'Campos obrigatórios não mapeados: ' . implode(', ', $missingColumnNames)])
                    ->withInput();
            }

            $result = $this->service->processImport($analysis, $columnMapping, auth()->user(), $ignoreApproved);

            session(['import_result' => $result]);

            return redirect()->route('final-status-import.status')
                ->with('success', 'Importação concluída!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Erro ao processar importação: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function status()
    {
        $this->authorize('view', Process::FINAL_STATUS_IMPORT);

        if (!session('import_result')) {
            return redirect()->route('final-status-import.index')
                ->withErrors(['error' => 'Nenhuma importação foi processada.']);
        }

        $this->menu(Process::FINAL_STATUS_IMPORT);
        $this->breadcrumb('Status da Importação', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('final-status-import.index') => 'Importação de Situação Final',
        ]);

        $result = session('import_result');

        session()->forget(['import_analysis', 'import_file_temp', 'import_original_name', 'import_result']);

        return view('final-status-import.status', compact('result'));
    }
}
