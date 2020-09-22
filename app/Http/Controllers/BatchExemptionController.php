<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSchoolClassReportCardRequest;
use App\Models\LegacySchoolClass;
use App\Models\LegacyStageType;
use App\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Portabilis_Model_Report_TipoBoletim;

class BatchExemptionController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $this->breadcrumb('Dispensa em lote', [
            url('intranet/educar_configuracoes_index.php') => 'Configurações',
        ]);

        $this->menu(Process::BATCH_EXEMPTION);

        return view('exemption.batch', [
            'stageTypes' => LegacyStageType::active()->get()->keyBy('cod_modulo')->toJson(),
        ]);
    }

    public function exempt(Request $request)
    {

    }
}
