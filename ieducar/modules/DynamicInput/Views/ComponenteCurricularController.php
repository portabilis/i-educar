<?php

use App\Services\SchoolGradeDisciplineService;

class ComponenteCurricularController extends ApiCoreController
{
    protected function canGetComponentesCurriculares()
    {
        return
            $this->validatesId('turma') &&
            $this->validatesPresenceOf('ano');
    }

    protected function canGetComponentesCurricularesEscolaSerie()
    {
        return $this->validatesPresenceOf('escola') &&
            $this->validatesPresenceOf('serie');
    }

    private function agrupaComponentesCurriculares($componentesCurriculares)
    {
        $options = [];

        foreach ($componentesCurriculares as $componenteCurricular) {
            $areaConhecimento = (($componenteCurricular['secao_area_conhecimento'] != '') ? $componenteCurricular['secao_area_conhecimento'] . ' - ' : '') . $componenteCurricular['area_conhecimento'];
            $options[
                '__' . $componenteCurricular['id']
            ] = [
                'value' => mb_strtoupper($componenteCurricular['nome'], 'UTF-8'),
                'group' => mb_strtoupper($areaConhecimento, 'UTF-8')
            ];
        }

        return $options;
    }

    protected function getComponentesCurricularesForDiario()
    {
        if ($this->canGetComponentesCurriculares()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $instituicaoId = $this->getRequest()->instituicao_id;
            $turmaId = $this->getRequest()->turma_id;
            $ano = $this->getRequest()->ano;
            $etapa = $this->getRequest()->etapa;

            if ($etapa == '') {
                $etapa = '0';
            }

            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

            if ($isOnlyProfessor) {
                $componentesCurriculares = Portabilis_Business_Professor::componentesCurricularesAlocado($instituicaoId, $turmaId, $ano, $userId);
            } else {
                $sql = '
                    SELECT
                        cc.id,
                        cc.nome,
                        ac.nome as area_conhecimento,
                        ac.secao as area_conhecimento_secao,
                        cc.ordenamento
                    FROM
                        pmieducar.turma,
                        modules.componente_curricular_turma as cct,
                        modules.componente_curricular as cc,
                        modules.area_conhecimento as ac,
                        pmieducar.escola_ano_letivo as al
                    WHERE turma.cod_turma = $1
                        AND cct.turma_id = turma.cod_turma
                        AND cct.componente_curricular_id = cc.id
                        AND al.ano = $2
                        AND turma.ref_ref_cod_escola = al.ref_cod_escola
                        AND cc.area_conhecimento_id = ac.id
                        AND (turma.ref_cod_disciplina_dispensada <> cc.id OR turma.ref_cod_disciplina_dispensada is null)
                        AND (
                            CASE
                                WHEN cct.etapas_especificas = 1
                                    THEN $3 = ANY (string_to_array(cct.etapas_utilizadas,\',\')::int[])
                                ELSE true
                            END
                        )
                    ORDER BY
                        ac.secao,
                        ac.nome,
                        cc.ordenamento,
                        cc.nome
                ';

                $componentesCurriculares = $this->fetchPreparedQuery($sql, [$turmaId, $ano, $etapa]);

                if (count($componentesCurriculares) < 1) {
                    $sql = '
                        SELECT
                            cc.id,
                            cc.nome,
                            ac.nome as area_conhecimento,
                            ac.secao as secao_area_conhecimento,
                            cc.ordenamento
                        FROM pmieducar.turma as t
                        INNER JOIN pmieducar.escola_serie_disciplina esd on (esd.ref_ref_cod_escola = t.ref_ref_cod_escola)
                        INNER JOIN modules.componente_curricular cc on (esd.ref_cod_disciplina = cc.id)
                        INNER JOIN modules.area_conhecimento ac on (cc.area_conhecimento_id = ac.id)
                        INNER JOIN pmieducar.escola_ano_letivo al on (esd.ref_ref_cod_escola = al.ref_cod_escola)
                        WHERE t.cod_turma = $1
                            AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                            AND al.ano = $2
                            AND t.ativo = 1
                            AND esd.ativo = 1
                            AND al.ativo = 1
                            AND $2 = ANY(esd.anos_letivos)
                            AND (t.ref_cod_disciplina_dispensada <> cc.id OR t.ref_cod_disciplina_dispensada is null)
                            AND (
                                CASE
                                    WHEN esd.etapas_especificas = 1
                                        THEN $3 = ANY (string_to_array(esd.etapas_utilizadas,\',\')::int[])
                                    ELSE true
                                END
                            )
                        ORDER BY
                            ac.secao,
                            ac.nome,
                            cc.ordenamento,
                            cc.nome
                    ';

                    $componentesCurriculares = $this->fetchPreparedQuery($sql, [$turmaId, $ano, $etapa]);
                }
            }

            $options = [];
            $options = $this->agrupaComponentesCurriculares($componentesCurriculares);

            return ['options' => $options];
        }
    }

    protected function getComponentesCurriculares()
    {
        if ($this->canGetComponentesCurriculares()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $instituicaoId = $this->getRequest()->instituicao_id || 1;
            $turmaId = $this->getRequest()->turma_id;
            $ano = $this->getRequest()->ano;

            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

            if ($isOnlyProfessor) {
                $componentesCurriculares = Portabilis_Business_Professor::componentesCurricularesAlocado($instituicaoId, $turmaId, $ano, $userId);
            } else {
                $sql = '
                    SELECT
                        cc.id,
                        cc.nome,
                        ac.nome as area_conhecimento,
                        ac.secao as area_conhecimento_secao,
                        cc.ordenamento
                    FROM
                        pmieducar.turma,
                        modules.componente_curricular_turma as cct,
                        modules.componente_curricular as cc,
                        modules.area_conhecimento as ac,
                        pmieducar.escola_ano_letivo as al
                    WHERE turma.cod_turma = $1
                        AND cct.turma_id = turma.cod_turma
                        AND cct.escola_id = turma.ref_ref_cod_escola
                        AND cct.componente_curricular_id = cc.id
                        AND al.ano = $2
                        AND cct.escola_id = al.ref_cod_escola
                        AND cc.area_conhecimento_id = ac.id
                    ORDER BY
                        ac.secao,
                        ac.nome,
                        cc.ordenamento,
                        cc.nome
                ';

                $componentesCurriculares = $this->fetchPreparedQuery($sql, [$turmaId, $ano]);

                if (count($componentesCurriculares) < 1) {
                    $sql = '
                        SELECT
                            cc.id,
                            cc.nome,
                            ac.nome as area_conhecimento,
                            ac.secao as secao_area_conhecimento,
                            cc.ordenamento
                        FROM
                            pmieducar.turma as t,
                            pmieducar.escola_serie_disciplina as esd,
                            modules.componente_curricular as cc,
                            modules.area_conhecimento as ac,
                            pmieducar.escola_ano_letivo as al
                        WHERE t.cod_turma = $1
                            AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                            AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                            AND esd.ref_cod_disciplina = cc.id
                            AND al.ano = $2
                            AND esd.ref_ref_cod_escola = al.ref_cod_escola
                            AND t.ativo = 1
                            AND esd.ativo = 1
                            AND al.ativo = 1
                            AND $2 = ANY(esd.anos_letivos)
                            AND cc.area_conhecimento_id = ac.id
                        ORDER BY
                            ac.secao,
                            ac.nome,
                            cc.ordenamento,
                            cc.nome
                    ';

                    $componentesCurriculares = $this->fetchPreparedQuery($sql, [$turmaId, $ano]);
                }
            }

            $options = [];
            $options = $this->agrupaComponentesCurriculares($componentesCurriculares);

            return ['options' => $options];
        }
    }

    protected function getComponentesCurricularesEscolaSerie()
    {
        if (!$this->canGetComponentesCurricularesEscolaSerie()) {
            return;
        }

        $escola = $this->getRequest()->escola;
        $serie = $this->getRequest()->serie;

        $componentesCurriculares = (new SchoolGradeDisciplineService)->getDisciplines($escola, $serie);

        $options = $this->agrupaComponentesCurriculares($componentesCurriculares->toArray());

        return ['options' => $options];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'componentesCurriculares')) {
            $this->appendResponse($this->getComponentesCurriculares());
        } elseif ($this->isRequestFor('get', 'componentesCurricularesForDiario')) {
            $this->appendResponse($this->getComponentesCurricularesForDiario());
        } elseif ($this->isRequestFor('get', 'componentesCurricularesEscolaSerie')) {
            $this->appendResponse($this->getComponentesCurricularesEscolaSerie());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
