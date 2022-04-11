<?php

namespace App\Http\Controllers;

use App\Models\LegacyDisciplineSchoolClass;
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
        $etapasUtilizadas = $request->get('etapas_utilizadas');
        $etapasEspecificas = $request->get('etapas_especificas');
        $codigoInepEducacenso = $request->get('codigo_inep_educacenso');
        $codTurmaRequest = $request->get('cod_turma');
        $originalMultiGradesInfo = $this->findOriginalMultiGradesInfo($codTurmaRequest);
        $originalGrade = $this->findOriginalGrade($codTurmaRequest);

        try {
            DB::beginTransaction();

            $schoolClassToStore = $this->prepareSchoolClassDataToStore($request);
            $schoolClass = $schoolClassService->storeSchoolClass($schoolClassToStore);

            $codTurma = $schoolClass->cod_turma;
            $codSerie = $schoolClass->ref_ref_cod_serie;
            $codEscola = $schoolClass->ref_ref_cod_escola;
            $schoolClass->originalMultiGradesInfo = $originalMultiGradesInfo;
            $schoolClass->originalGrade = $originalGrade;

            if (! empty($request->get('multiseriada'))) {
                $multSerieIds = $request->get('mult_serie_id');
                $multSerieId = is_array($multSerieIds) ? $multSerieIds : [];
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
                $this->deleteDisciplineSchoolClass($codTurma);
            } else {
                $this->atualizaComponentesCurriculares(
                    $codSerie,
                    $codEscola,
                    $codTurma,
                    $disciplinas,
                    $cargaHoraria,
                    $usarComponente,
                    $docenteVinculado,
                    $etapasUtilizadas,
                    $etapasEspecificas
                );
                $multiGradesService = new MultiGradesService();
                $multiGradesService->deleteAllGradesOfSchoolClass($schoolClass);
            }

            if ($codigoInepEducacenso) {
                $schoolClassInepService->store($codTurma, $codigoInepEducacenso);
            } else {
                $schoolClassInepService->delete($codTurma);
            }

            if ($datasInicioModulos[0] && $datasFimModulos[0]) {
                $schoolClassStageService->store(
                    $schoolClass,
                    $datasInicioModulos,
                    $datasFimModulos,
                    $diasLetivos,
                    $codModulo
                );
            }

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

        if (empty($params['multiseriada'])) {
            $params['multiseriada'] = 0;
        }

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

        if (isset($params['estrutura_curricular'])) {
            $params['estrutura_curricular'] = '{' . implode(',', $params['estrutura_curricular']) . '}';
        } else {
            $params['estrutura_curricular'] = null;
        }

        if (empty($params['formas_organizacao_turma'])) {
            $params['formas_organizacao_turma'] = null;
        }

        if (isset($params['unidade_curricular'])) {
            $params['unidade_curricular'] = '{' . implode(',', $params['unidade_curricular']) . '}';
        } else {
            $params['unidade_curricular'] = null;
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
        }

        if (empty($params['cod_turma'])) {
            $params['ref_usuario_cad'] = $pessoaLogada;
            $params['data_cadastro'] = now();
            $params['ref_usuario_exc'] = null;
            $params['ano'] = $params['ano_letivo'];
            $params['ref_ref_cod_serie'] = $params['ref_cod_serie'];
            $params['ref_ref_cod_escola'] = $params['ref_cod_escola'];
        } else {
            $params['ref_usuario_exc'] = $pessoaLogada;
            $params['ref_cod_curso'] = $params['ref_cod_curso_'];
            $params['ref_ref_cod_serie'] = $params['ref_cod_serie_'];
            $params['ref_ref_cod_escola'] = $params['ref_cod_escola_'];
        }

        $value = array_key_exists('nao_informar_educacenso', $params)
        && $params['nao_informar_educacenso'] === 'on' ? 1 : 0;
        $params['nao_informar_educacenso'] = $value;
        $params['ativo'] = 1;
        $params['visivel'] = array_key_exists('visivel', $params);

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
        $docente,
        $etapasUtilizadas,
        $etapasEspecificas
    ) {
        $this->deleteDisciplineSchoolClass($codTurma);

        if ($componentes) {
            $mapper = new ComponenteCurricular_Model_TurmaDataMapper();

            $componentesTurma = [];

            foreach ($componentes as $key => $value) {
                $carga = isset($usarComponente[$key]) ?
                    null : $cargaHoraria[$key];

                $docente_ = isset($docente[$key]) ?
                    1 : 0;

                $hasEspecifica = isset($etapasEspecificas[$key]) ?
                    1 : 0;

                $etapaUtilizada = ($hasEspecifica == 1) ? $etapasUtilizadas[$key] : null;

                $componentesTurma[] = [
                    'id' => $value,
                    'cargaHoraria' => $carga,
                    'docenteVinculado' => $docente_,
                    'etapasEspecificas' => $hasEspecifica,
                    'etapasUtilizadas' => $etapaUtilizada
                ];
            }

            $iDiarioService = $this->getIdiarioService();

            $mapper->bulkUpdate($codSerie, $codEscola, $codTurma, $componentesTurma, $iDiarioService);
        }
    }

    private function deleteDisciplineSchoolClass($codTurma)
    {
        return LegacyDisciplineSchoolClass::query()
            ->where('turma_id', $codTurma)
            ->delete();
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

    private function findOriginalMultiGradesInfo($codTurma)
    {
        if (!empty($codTurma)) {
            /** @var LegacySchoolClass|null $legacySchoolClass */
            $legacySchoolClass = LegacySchoolClass::query()->find($codTurma);

            if ($legacySchoolClass instanceof LegacySchoolClass) {
                return $legacySchoolClass->multiseriada;
            }
        }

        return null;
    }

    private function findOriginalGrade($codTurma)
    {
        if (!empty($codTurma)) {
            /** @var LegacySchoolClass|null $legacySchoolClass */
            $legacySchoolClass = LegacySchoolClass::query()->find($codTurma);

            if ($legacySchoolClass instanceof LegacySchoolClass) {
                return $legacySchoolClass->ref_ref_cod_serie;
            }
        }

        return null;
    }
}
