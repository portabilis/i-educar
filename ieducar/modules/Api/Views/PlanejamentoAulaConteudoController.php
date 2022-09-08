<?php

class PlanejamentoAulaConteudoController extends ApiCoreController
{
    public function getPAC()
    {
        $frequencia = $this->getRequest()->frequencia;

        if (is_numeric($frequencia)) {
            $obj = new clsModulesFrequencia($frequencia);
            $freq = $obj->detalhe()['detalhes'];

            $obj = new clsModulesPlanejamentoAula();
            $id = $obj->lista(
                null,
                null,
                null,
                null,
                null,
                $freq['ref_cod_turma'],
                $freq['ref_cod_componente_curricular'],
                null,
                null,
                null,
                $freq['fase_etapa'],
                null,
                $freq['data']
            )[0]['id'];

            if (is_numeric($id)) {
                $obj = new clsModulesPlanejamentoAulaConteudo();
                $conteudos = $obj->lista2($id);

                foreach ($conteudos as $key => $conteudo) {
                    $lista[$conteudo['id']] = [$conteudo['conteudo'], $conteudo['usando']];
                }
                $conteudos = $lista;


                $lista = [];
                // $obj = new clsModulesPlanejamentoAulaBNCCEspecificacao();
                // $especificacoes = $obj->lista($id);

                // foreach ($especificacoes as $key => $especificacao) {
                //     $lista[$especificacao['id']] = $especificacao['especificacao'];
                // }
                $especificacoes = $lista;

                return ['pac' => [$especificacoes, $conteudos]];
            }

            return [];
        }

        return [];
    }

    public function getPacByFreq()
    {
        $ref_cod_turma = $this->getRequest()->campoTurma;
        $fase_etapa = $this->getRequest()->campoFaseEtapa;
        $data = $this->getRequest()->campoData;
        $tipoPresenca = $this->getRequest()->tipoPresenca;
        $campoComponenteCurricular = $this->getRequest()->campoComponenteCurricular;


        if (is_numeric($ref_cod_turma) && is_numeric($fase_etapa) && !empty($data) && ($tipoPresenca == 1 || ($tipoPresenca == 2 && !empty($campoComponenteCurricular)))) {
            $obj_servidor = new clsPmieducarServidor(
                $this->pessoa_logada,
                null,
                null,
                null,
                null,
                null,
                1,      //  Ativo
                1,      //  Fixado na instituição de ID 1
            );
            $is_professor = $obj_servidor->isProfessor();

            $obj = new clsModulesPlanejamentoAula();
            $planejamentos = $obj->lista(
                null,
                null,
                null,
                null,
                null,
                $ref_cod_turma,
                !empty($campoComponenteCurricular) ? $campoComponenteCurricular : null,
                null,
                null,
                null,
                $fase_etapa,
                $is_professor ? $this->pessoa_logada : null,
                Portabilis_Date_Utils::brToPgSQL($data)
            );

            if (isset($planejamentos) && is_array($planejamentos) && !empty($planejamentos)) {
                $planejamentos_ids = [];

                foreach ($planejamentos as $planejamento) {
                    if (!in_array($planejamento['id'], $planejamentos_ids)) {
                        array_push($planejamentos_ids, $planejamento['id']);
                    }
                }

                if (!empty($planejamentos_ids)) {
                    $lista = [];
                    $obj = new clsModulesPlanejamentoAulaConteudo();
                    $conteudos = $obj->listaByPlanejamentos($planejamentos_ids);

                    foreach ($conteudos as $key => $conteudo) {
                        $lista[$conteudo['id']] = [$conteudo['conteudo'], $conteudo['usando']];
                    }

                    return ['pac' => [[], $lista]];

                }
            }
        }

        return [];
    }

    // api
    protected function getTurma()
    {
        if (!$this->canGet()) {
            return void;
        }

        $id = $this->getRequest()->id;

        $turma = new clsPmieducarTurma();
        $turma->cod_turma = $id;
        $turma = $turma->detalhe();

        foreach ($turma as $k => $v) {
            if (is_numeric($k)) {
                unset($turma[$k]);
            }
        }

    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'pac')) {
            $this->appendResponse($this->getPAC());
        }

        if ($this->isRequestFor('get', 'pacByFreq')) {
            $this->appendResponse($this->getPacByFreq());
        }

        if ($this->isRequestFor('get', 'turma')) {
            $this->appendResponse($this->getTurma());
        }
    }
}
