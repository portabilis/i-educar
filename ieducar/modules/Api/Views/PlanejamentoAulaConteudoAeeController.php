<?php

class PlanejamentoAulaConteudoAeeController extends ApiCoreController
{
    public function getPAC()
    {
        $frequencia = $this->getRequest()->frequencia;

        if (is_numeric($frequencia)) {
            $obj = new clsModulesFrequencia($frequencia);
            $freq = $obj->detalhe()['detalhes'];

            $obj = new clsModulesPlanejamentoAulaAee();
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
                $obj = new clsModulesPlanejamentoAulaConteudoAee();
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
        $ref_cod_matricula = $this->getRequest()->campoMatricula;
         $fase_etapa = $this->getRequest()->campoFaseEtapa;
         $campoComponenteCurricular = $this->getRequest()->campoComponenteCurricular;

        if (is_numeric($ref_cod_matricula)) {
            $obj = new clsModulesPlanejamentoAulaAee();
            $id = $obj->lista(
                null,
                null,
                null,
                null,
                null,
                $ref_cod_turma,
                $ref_cod_matricula,
                !empty($campoComponenteCurricular) ? $campoComponenteCurricular : null,
                null,
                null,
                null,
                $fase_etapa,
                null
            )[0]['id'];

            if (is_numeric($id)) {
                $obj = new clsModulesPlanejamentoAulaConteudoAee();
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