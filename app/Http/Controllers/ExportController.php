<?php

namespace App\Http\Controllers;

use App\Jobs\DatabaseToCsvExporter;
use App\Models\Exporter\Export;
use App\Models\Exporter\Student;
use App\Models\Person;
use App\Process;
use Carbon\Carbon;
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
            'filename' => 'alunos.csv',
        ])->only([
            'model', 'fields', 'hash', 'user_id', 'filename',
        ]);

        if ($status = $request->input('situacao_matricula')) {
            $data['filters'][] = [
                'column' => 'exporter_student.status',
                'operator' => '=',
                'value' => $status,
            ];
        }

        if ($year = $request->input('ano')) {
            $data['filters'][] = [
                'column' => 'exporter_student.year',
                'operator' => '=',
                'value' => intval($year),
            ];
        }

        if ($request->input('ref_cod_escola')) {
            $data['filters'][] = [
                'column' => 'exporter_student.school_id',
                'operator' => 'in',
                'value' => [$request->input('ref_cod_escola')]
            ];
        } elseif ($request->user()->isSchooling()) {
            $data['filters'][] = [
                'column' => 'exporter_student.school_id',
                'operator' => 'in',
                'value' => $request->user()->schools->pluck('cod_escola')->all(),
            ];
        }

        $export = Export::create($data);

        $this->dispatch(new DatabaseToCsvExporter($export));

        return redirect()->route('export.index');
    }
}
