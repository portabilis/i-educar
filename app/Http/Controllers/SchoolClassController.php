<?php

namespace App\Http\Controllers;

use App\Models\LegacySchoolClass;
use App\Services\iDiarioService;
use App\Services\SchoolClass\MultiGradesService;
use App\Services\SchoolClass\SchoolClassService;
use App\Services\SchoolClassInepService;
use App\Services\SchoolClassStageService;
use ComponenteCurricular_Model_TurmaDataMapper;
use Exception;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SchoolClassController extends Controller
{
    public function index()
    {
        return [];
    }

    public function store(Request $request)
    {
        $response = ['msg' => 'Edição efetuada com sucesso.'];
        $schoolClassService = new SchoolClassService();
        $schoolClassInepService = new SchoolClassInepService();
        $schoolClassStageService = new SchoolClassStageService();

        $codModulo = $request->get('ref_cod_modulo');
        $diasLetivos = $request->get('dias_letivos');
        $datasFimModulos = $request->get('data_fim');
        $datasInicioModulos = $request->get('data_inicio');

        $disciplinas = $request->get('disciplinas');
        $cargaHoraria = $request->get('carga_horaria');
        $usarComponente = $request->get('usar_componente');
        $docenteVinculado = $request->get('docente_vinculado');
        $codigoInepEducacenso = $request->get('codigo_inep_educacenso');

        try {
            DB::beginTransaction();

            $schoolClassToStore = $this->prepareSchoolClassDataToStore($request);
            $schoolClass = $schoolClassService->storeSchoolClass($schoolClassToStore);

            $codTurma = $schoolClass->cod_turma;
            $codSerie = $schoolClass->ref_ref_cod_serie;
            $codEscola = $schoolClass->ref_ref_cod_escola;

            if ($schoolClass->multiseriada) {
                $multSerieId = $request->get('mult_serie_id');
                $multBoletimId = $request->get('mult_boletim_id');
                $multBoletimDiferenciadoId = $request->get('mult_boletim_diferenciado_id');

                $schoolClassGrades = [];
                foreach ($multSerieId as $key => $serieId) {
                    $schoolClassGrades[] = [
                        'escola_id' => $codEscola,
                        'serie_id' => $serieId,
                        'turma_id' => $codTurma,
                        'boletim_id' => $multBoletimId[$key],
                        'boletim_diferenciado_id' => $multBoletimDiferenciadoId[$key],
                    ];
                }
                $multiGradesService = new MultiGradesService();
                $multiGradesService->storeSchoolClassGrade($schoolClass, $schoolClassGrades);
            } else {
                $this->atualizaComponentesCurriculares(
                    $codSerie,
                    $codEscola,
                    $codTurma,
                    $disciplinas,
                    $cargaHoraria,
                    $usarComponente,
                    $docenteVinculado
                );
            }

            if ($codigoInepEducacenso) {
                $schoolClassInepService->store($codTurma, $codigoInepEducacenso);
            } else {
                $schoolClassInepService->delete($codTurma);
            }

            $schoolClassStageService->store(
                $schoolClass,
                $datasInicioModulos,
                $datasFimModulos,
                $diasLetivos,
                $codModulo
            );

            DB::commit();
        } catch (ValidationException $ex) {
            DB::rollBack();

            return response()->json(['msg' => $ex->validator->errors()->first()], 422);
        } catch (Exception $ex) {
            return response()->json(['msg' => $ex->getMessage()], 500);
        }
        session()->flash('success', $response['msg']);

        return response()->json($response);
    }

    public function delete(Request $request)
    {
        $response = ['msg' => 'Exclusão efetuada com sucesso.'];
        $schoolClassService = new SchoolClassService();

        try {
            DB::beginTransaction();
            $schoolClassToDelete = LegacySchoolClass::find($request->get('cod_turma'));
            $schoolClassService->deleteSchoolClass($schoolClassToDelete);
        } catch (ValidationException $ex) {
            DB::rollBack();

            return response()->json(['msg' => $ex->validator->errors()->first()], 422);
        } catch (Exception $ex) {
            return response()->json(['msg' => $ex->getMessage()], 500);
        }
        session()->flash('success', $response['msg']);

        return response()->json($response);
    }

    /**
     * @param Request $request
     *
     * @return LegacySchoolClass
     */
    private function prepareSchoolClassDataToStore(Request $request)
    {
        $params = $request->all();
        $legacySchoolClass = new LegacySchoolClass();
        if (!empty($params['cod_turma'])) {
            $legacySchoolClass = LegacySchoolClass::find($params['cod_turma']);
        }
        $pessoaLogada = $request->user()->id;

        if (isset($params['dias_semana'])) {
            $params['dias_semana'] = '{' . implode(',', $params['dias_semana']) . '}';
        } else {
            $params['dias_semana'] = null;
        }

        if (isset($params['atividades_complementares'])) {
            $params['atividades_complementares'] = '{' . implode(',', $params['atividades_complementares']) . '}';
        } else {
            $params['atividades_complementares'] = null;
        }

        if (isset($params['cod_curso_profissional'])) {
            $params['cod_curso_profissional'] = $params['cod_curso_profissional'][0];
        } else {
            $params['cod_curso_profissional'] = null;
        }

        if ($params['tipo_atendimento'] != TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR) {
            $params['atividades_complementares'] = '{}';
        }

        $etapasCursoTecnico = [30, 31, 32, 33, 34, 39, 40, 64, 74];

        if (isset($params['etapa_educacenso'])
            && !in_array($params['etapa_educacenso'], $etapasCursoTecnico)) {
            $params['cod_curso_profissional'] = null;
        } else {
            $params['etapa_educacenso'] = null;
        }

        if (empty($params['cod_turma'])) {
            $params['ref_usuario_cad'] = $pessoaLogada;
            $params['data_cadastro'] = now();
            $params['ref_usuario_exc'] = null;
            $params['ref_ref_cod_serie'] = $params['ref_cod_serie'];
            $params['ref_ref_cod_escola'] = $params['ref_cod_escola'];
            $params['ano'] = $params['ano_letivo'];
        } else {
            $params['ref_usuario_exc'] = $pessoaLogada;
        }

        if (isset($params['nao_informar_educacenso']) && $params['nao_informar_educacenso'] == 'on') {
            $params['nao_informar_educacenso'] = 1;
        } else {
            $params['nao_informar_educacenso'] = 0;
        }

        $params['ativo'] = 1;

        $legacySchoolClass->fill($params);

        return $legacySchoolClass;
    }

    private function atualizaComponentesCurriculares(
        $codSerie,
        $codEscola,
        $codTurma,
        $componentes,
        $cargaHoraria,
        $usarComponente,
        $docente
    ) {
        if ($componentes) {
            $mapper = new ComponenteCurricular_Model_TurmaDataMapper();

            $componentesTurma = [];

            foreach ($componentes as $key => $value) {
                $carga = isset($usarComponente[$key]) ?
                    null : $cargaHoraria[$key];

                $docente_ = isset($docente[$key]) ?
                    1 : 0;

                $etapasEspecificas = isset($this->etapas_especificas[$key]) ?
                    1 : 0;

                $etapasUtilizadas = ($etapasEspecificas == 1) ? $this->etapas_utilizadas[$key] : null;

                $componentesTurma[] = [
                    'id' => $value,
                    'cargaHoraria' => $carga,
                    'docenteVinculado' => $docente_,
                    'etapasEspecificas' => $etapasEspecificas,
                    'etapasUtilizadas' => $etapasUtilizadas
                ];
            }

            $idiarioService = $this->getIdiarioService();

            $mapper->bulkUpdate($codSerie, $codEscola, $codTurma, $componentesTurma, $idiarioService);
        }
    }

    /**
     * Retorna instância do iDiarioService
     *
     * @return iDiarioService|null
     */
    private function getIdiarioService()
    {
        if (iDiarioService::hasIdiarioConfigurations()) {
            return app(iDiarioService::class);
        }

        return null;
    }
}
