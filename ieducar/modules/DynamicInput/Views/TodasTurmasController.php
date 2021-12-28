<?php

use App\Services\SchoolGradeDisciplineService;

class TodasTurmasController extends ApiCoreController
{
    public function debug_to_console($data) {
        $output = $data;
        if (is_array($output))
            $output = implode(',', $output);
    
        echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
    }

    protected function canGetTodasTurmas()
    {
        return $this->validatesPresenceOf('ano');
    }

    private function agrupaTodasTurmas($turmas)
    {
        $options = [];

        $obj_escola = new clsPmieducarEscola();

        foreach ($turmas as $key => $turma) {
            foreach ($turma as $key => $turma_item) {
                $nm_escola = $obj_escola->lista($turma_item['ref_ref_cod_escola'])[0]['nome'];
                $options[$turma_item['cod_turma']] = $turma_item['nm_turma'] . " (" . $nm_escola . ")";
            }
        }

        return $options;
    }

    protected function getTodasTurmas()
    {
        if ($this->canGetTodasTurmas()) {
            $userId = \Illuminate\Support\Facades\Auth::id();
            $ano = $this->getRequest()->ano;

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
                $professor_turmas = $obj_professor->lista($userId);

                foreach ($professor_turmas as $key => $professor_turma) {
                    $resources[$professor_turma['ref_cod_turma']] = $professor_turma['nm_turma'] . " (" . $professor_turma['nm_escola'] . ")";
                }
            } else {
                $obj_usuario = new clsPmieducarUsuario($userId);
                $tipo_usuario = $obj_usuario->detalhe()['ref_cod_tipo_usuario'];

                $obj_tipo_usuario = new clsPmieducarTipoUsuario($tipo_usuario);
                $nivel = $obj_tipo_usuario->detalhe()['nivel'];
                
                $obj_turma = new clsPmieducarTurma();

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
                        $ano,
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
                            $escola_usuario,
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
                            $ano
                        );
                    }   
                }
            }

            $options = [];
            // $options = $this->agrupaTodasTurmas($turmas);

            $obj_escola = new clsPmieducarEscola();
            // echo("<script>console.log('PHP: " . $ano . "');</script>");
            foreach ($turmas as $key => $turma) {
                foreach ($turma as $key => $turma_item) {
                    $options[$turma_item['cod_turma']] = "adawd";
                }
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        $this->appendResponse($this->getTodasTurmas());
    }
}
