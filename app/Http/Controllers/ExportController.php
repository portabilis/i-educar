<?php

namespace App\Http\Controllers;

use App\Jobs\DatabaseToCsvExporter;
use App\Models\Exporter\Export;
use App\Models\Exporter\Student;
use App\Models\Person;
use App\Process;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExportController extends Controller
{
    /**
     * @return View
     */
    public function index()
    {
        $this->breadcrumb('Exportações', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::DATA_EXPORT);

        $query = Export::query();

        $query->orderByDesc('created_at');

        return view('export.index', [
            'exports' => $query->paginate(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function form(Request $request)
    {
        $this->breadcrumb('Nova Exportação', [
            url('/intranet/educar_configuracoes_index.php') => 'Configurações',
            route('export.index') => 'Exportações',
        ]);

        $this->menu(Process::DATA_EXPORT);

        $type = $request->query('type', 1);

        switch ($type) {
            case 2:
                $export = new Person();
                break;

            case 1:
            default:
                $export = new Student();
        }

        return view('export.new', [
            'export' => $export,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function export(Request $request)
    {
        $data = $request->merge([
            'hash' => md5(time()),
            'user_id' => $request->user()->getKey(),
            'filename' => 'alunos-' . date('Ymd') . '.csv',
        ])->only([
            'model', 'fields', 'hash', 'user_id', 'filename',
        ]);

        $export = Export::create($data);

        $this->dispatch(new DatabaseToCsvExporter($export));

        return redirect()->route('export.index');
    }
}
