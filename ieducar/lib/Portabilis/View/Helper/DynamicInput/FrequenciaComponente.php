<?php

class Portabilis_View_Helper_DynamicInput_FrequenciaComponente extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_frequencia_componente';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];
        $instituicaoId = $this->getInstituicaoId($options['instituicaoId'] ?? null);
        $escolaId = $this->getEscolaId($options['escolaId'] ?? null);
        $serieId = $this->getSerieId($options['serieId'] ?? null);
        $turmaId = $this->getTurmaId($options['turmaId'] ?? null);
        $anoLetivo = $this->getAno($options['ano'] ?? null);

        $userId = $this->getCurrentUserId();

        if ($turmaId and $anoLetivo and empty($resources)) {
            $isOnlyProfessor = Portabilis_Business_Professor::isOnlyProfessor($instituicaoId, $userId);

            if ($isOnlyProfessor) {
                $componentesCurriculares = Portabilis_Business_Professor::componentesCurricularesAlocado($instituicaoId, $turmaId, $ano, $userId);
            } else {
                $sql = "
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
                        WHERE turma.cod_turma = {$turmaId}
                            AND cct.turma_id = turma.cod_turma
                            AND cct.escola_id = turma.ref_ref_cod_escola
                            AND cct.componente_curricular_id = cc.id
                            AND al.ano = {$anoLetivo}
                            AND cct.escola_id = al.ref_cod_escola
                            AND cc.area_conhecimento_id = ac.id
                        ORDER BY
                            ac.secao,
                            ac.nome,
                            cc.ordenamento,
                        cc.nome
                ";

                $db = new clsBanco();
                $db->Consulta($sql);

                while ($db->ProximoRegistro()) {
                    $componentesCurriculares[] = $db->Tupla();
                }

                if (!$componentesCurriculares) {
                    $sql = "
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
                                WHERE t.cod_turma = {$turmaId}
                                    AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                                    AND esd.ref_ref_cod_serie = t.ref_ref_cod_serie
                                    AND esd.ref_cod_disciplina = cc.id
                                    AND al.ano = {$anoLetivo}
                                    AND esd.ref_ref_cod_escola = al.ref_cod_escola
                                    AND t.ativo = 1
                                    AND esd.ativo = 1
                                    AND al.ativo = 1
                                    AND {$anoLetivo} = ANY(esd.anos_letivos)
                                    AND cc.area_conhecimento_id = ac.id
                                ORDER BY
                                    ac.secao,
                                    ac.nome,
                                    cc.ordenamento,
                                    cc.nome
                            ";

                    $db = new clsBanco();
                    $db->Consulta($sql);

                    while ($db->ProximoRegistro()) {
                        $componentesCurriculares[] = $db->Tupla();
                    }
                }
            }
            $ultimo_nome ='';
            foreach ($componentesCurriculares as $key => $componentesCurricular) {
                $resources[$componentesCurricular['id']] = 'teste';
                $ultimo_nome = $componentesCurricular['nome'];
            }
            
        } 

        return $this->insertOption(null,  $ultimo_nome, $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'turmaId' => null,
            'options' => [],
            'resources' => []
        ];
    }

    public function frequenciaComponente($options = [])
    {
        parent::select($options);
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
}
