<?php

class PlanejamentoAulaConteudoAeeController extends ApiCoreController
{
    public function getPAC()
    {
        $ref_cod_matricula = $this->getRequest()->ref_cod_matricula;

        if (is_numeric($ref_cod_matricula)) {
            // $obj = new clsModulesFrequencia($frequencia);
            // $freq = $obj->detalhe()['detalhes'];

            $obj = new clsModulesPlanejamentoAulaAee();
            $id = $obj->lista(
                null,
                null,
                null,
                null,
                null,               
                null,
                $this->ref_cod_matricula,
                null,               
                null,
                null,
                null, 
                null,
                null                 
            )[0]['id'];

            if (is_numeric($id)) {
                $obj = new clsModulesPlanejamentoAulaConteudo();
                $conteudos = $obj->lista2_aee($id);

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
            $obj = new clsModulesPlanejamentoAula();
            $id = $obj->lista(
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
                null,
                Portabilis_Date_Utils::brToPgSQL($data)
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
