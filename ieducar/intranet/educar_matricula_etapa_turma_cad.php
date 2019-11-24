<?php

require_once "include/clsBase.inc.php";
require_once "include/clsCadastro.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/App/Model/Educacenso.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Etapa do aluno");
        $this->processoAp = "578";
    }
}

class indice extends clsCadastro
{
    public $cod_matricula;
    public $ref_cod_aluno;
    public $etapas_educacenso;

    public function Formular()
    {
        $this->nome_url_cancelar = "Voltar";
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";
        $this->montaLocalizacao();
    }

    public function Inicializar()
    {


        $this->cod_matricula = $_GET["ref_cod_matricula"];
        $this->ref_cod_aluno = $_GET["ref_cod_aluno"];

        $this->validaPermissao();
        $this->validaParametros();
        return 'Editar';
    }

    public function Gerar()
    {
        $this->campoOculto("cod_matricula", $this->cod_matricula);
        $this->campoOculto("ref_cod_aluno", $this->ref_cod_aluno);

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista($this->ref_cod_aluno, null, null, null, null, null, null, null, null, null, 1);
        if (is_array($lst_aluno)) {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno["nome_aluno"];
            $this->campoRotulo("nm_aluno", "Aluno", $this->nm_aluno);
        }
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            $this->cod_matricula, null, null,
            null, null, null, null, null, 1, null, null, null,
            null, null, null, null, null, null, null, null, false,
            null, null, null, false, false, false, null, null,
            false, null, false, false, false, null, true
        );

        $todasEtapasEducacenso = loadJson('educacenso_json/etapas_ensino.json');

        foreach ($enturmacoes as $enturmacao) {
            $etapasEducacenso = $this->array_filter_key($todasEtapasEducacenso,
                function ($value) use ($enturmacao)
                {
                    return in_array($value, App_Model_Educacenso::etapasDaTurma($enturmacao['etapa_ensino']));
                }
            );
            $etapasEducacenso = array(0 => 'Nenhuma') + $etapasEducacenso;

            $this->campoLista("etapas_educacenso[{$enturmacao['ref_cod_turma']}-{$enturmacao['sequencial']}]", "Etapa do aluno na turma {$enturmacao['nm_turma']}:", $etapasEducacenso, $enturmacao['etapa_educacenso'], '', false, '', '', false, false);
        }
    }

    public function Editar()
    {


        $this->validaPermissao();
        $this->validaParametros();

        foreach ($this->etapas_educacenso as $codTurmaESequencial => $etapaEducacenso) {
            // Necessário pois chave é Turma + Matrícula + Sequencial
            $codTurmaESequencial = explode('-', $codTurmaESequencial);
            $codTurma = $codTurmaESequencial[0];
            $sequencial = $codTurmaESequencial[1];
            $obj = new clsPmieducarMatriculaTurma($this->cod_matricula, $codTurma, $this->pessoa_logada);
            $obj->sequencial = $sequencial;
            $obj->etapa_educacenso = $etapaEducacenso;
            $obj->edita();
        }

        $this->mensagem .= "Etapas atualizadas com sucesso.<br>";
        $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->cod_matricula}");
    }

    private function montaLocalizacao()
    {
        $this->breadcrumb('Etapa do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7, "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if (!$det_matricula) {
            $this->simpleRedirect('educar_matricula_lst.php?ref_cod_aluno=' . $this->ref_cod_aluno);
        }

    }

    /**
     * Filtering a array by its keys using a callback.
     *
     * @param $array array The array to filter
     * @param $callback Callback The filter callback, that will get the key as first argument.
     *
     * @return array The remaining key => value combinations from $array.
     */
    public function array_filter_key(array $array, $callback)
    {
        $matchedKeys = array_filter(array_keys($array), $callback);
        return array_intersect_key($array, array_flip($matchedKeys));
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
