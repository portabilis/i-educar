<?php

use Carbon\Carbon;

class Portabilis_View_Helper_DynamicInput_TodasTurmas extends Portabilis_View_Helper_DynamicInput_CoreSelect
{
    protected function inputName()
    {
        return 'ref_cod_turma';
    }

    protected function inputOptions($options)
    {
        $resources = $options['resources'];

        $userId = $this->getCurrentUserId();
        $anoLetivo = $this->getAno($options['options']['ano'] ?? date('Y'));

        if (empty($resources) && $userId and $anoLetivo) {
            $obj_servidor = new clsPmieducarServidor(
                $userId,
                null,
                null,
                null,
                null,
                null,
                1,      //  Ativo
                1,      //  Fixado na instituição de ID 1
            );
            $eh_professor = $obj_servidor->isProfessor();

            if($eh_professor){
                $obj_professor = new clsModulesProfessorTurma();
                $professor_turmas = $obj_professor->lista(
                    $userId,
                    1          // Fixado na instituição de ID 1
                );

                $objTurmaModulo      = new clsPmieducarTurmaModulo();
                $objTurmaModulo->setOrderBy('data_fim DESC');
                $objTurmaModulo->setLimite(1);

                foreach ($professor_turmas as $key => $professor_turma) {
                    $etapa = $objTurmaModulo->lista($professor_turma['ref_cod_turma']);

                    if ($etapa[0]) {
                        $anoDataInicialEtapa = Carbon::createFromFormat('Y-m-d', $etapa[0]['data_inicio'])->format('Y');
                        $anoDataFinalEtapa = Carbon::createFromFormat('Y-m-d', $etapa[0]['data_fim'])->format('Y');

                        if (($anoDataInicialEtapa == $anoLetivo) || ($anoDataFinalEtapa == $anoLetivo)) {
                            $resources[$professor_turma['ref_cod_turma']] = $professor_turma['nm_turma'] . " (" . $professor_turma['nm_escola'] . ")";
                        }
                    }

                }
            } else {
                $obj_usuario = new clsPmieducarUsuario($userId,);
                $tipo_usuario = $obj_usuario->detalhe()['ref_cod_tipo_usuario'];

                $obj_tipo_usuario = new clsPmieducarTipoUsuario($tipo_usuario);
                $nivel = $obj_tipo_usuario->detalhe()['nivel'];

                $obj_turma = new clsPmieducarTurma();
                $obj_turma->setOrderby('nm_turma ASC');

                if ($nivel == 1 || $nivel == 2) {
                    $turmas[] = $obj_turma->lista(
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    );
                } else if ($nivel == 4) {
                    $obj_escola_usuario = new clsPmieducarEscolaUsuario();
                    $escolas_usuario = $obj_escola_usuario->lista($userId);

                    foreach ($escolas_usuario as $key => $escola_usuario) {
                        $turmas[] = $obj_turma->lista(
                            null,
                            null,
                            null,
                            null,
                            $escola_usuario['ref_cod_escola'],
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            null,
                            false,
                            null,
                            null,
                            null,
                            null,
                            null,
                            false
                        );
                    }
                }

                $obj_escola = new clsPmieducarEscola();

                $objTurmaModulo      = new clsPmieducarTurmaModulo();
                $objTurmaModulo->setOrderBy('data_fim DESC');
                $objTurmaModulo->setLimite(1);

                foreach ($turmas as $key => $turma) {
                    foreach ($turma as $key => $turma_item) {
                        $etapa = $objTurmaModulo->lista($turma_item['cod_turma']);

                        if ($etapa[0]) {
                            $anoDataInicialEtapa = Carbon::createFromFormat('Y-m-d', $etapa[0]['data_inicio'])->format('Y');
                            $anoDataFinalEtapa = Carbon::createFromFormat('Y-m-d', $etapa[0]['data_fim'])->format('Y');

                            if (($anoDataInicialEtapa == $anoLetivo) || ($anoDataFinalEtapa == $anoLetivo)) {
                                $nm_escola = $obj_escola->lista($turma_item['ref_ref_cod_escola'])[0]['nome'];
                                $resources[$turma_item['cod_turma']] = $turma_item['nm_turma'] . " (" . $nm_escola . ")";
                            }
                        }
                    }
                }
            }
        }

        if (count($resources) == 0)
            return $this->insertOption(null, 'Sem opções', $resources);
        else
            return $this->insertOption(null, 'Selecione a turma', $resources);
    }

    protected function defaultOptions()
    {
        return [
            'id' => null,
            'options' => ['required' => true, 'label' => 'Turma'],
            'resources' => []
        ];
    }

    public function todasTurmas($options = [])
    {
        parent::select($options);
    }
}
