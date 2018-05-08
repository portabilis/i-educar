<?php

require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php" );
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Etapa do aluno" );
        $this->processoAp = "578";
        $this->addEstilo("localizacaoSistema");
    }
}

class indice extends clsCadastro
{
    var $cod_matricula;
    var $ref_cod_aluno;
    var $etapas_educacenso;

    function Formular()
    {
        $this->nome_url_cancelar = "Voltar";
        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";
        $this->montaLocalizacao();
    }

    function Inicializar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_matricula=$_GET["ref_cod_matricula"];
        $this->ref_cod_aluno=$_GET["ref_cod_aluno"];

        $this->validaPermissao();
        $this->validaParametros();
        return 'Editar';
    }

    function Gerar()
    {
        $this->campoOculto( "cod_matricula", $this->cod_matricula );
        $this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
        if ( is_array($lst_aluno) )
        {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno["nome_aluno"];
            $this->campoRotulo( "nm_aluno", "Aluno", $this->nm_aluno);
        }
        $enturmacoes = new clsPmieducarMatriculaTurma();
        $enturmacoes = $enturmacoes->lista(
            $this->cod_matricula, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL,
            NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, FALSE,
            NULL, NULL, NULL, FALSE, FALSE, FALSE, NULL, NULL,
            FALSE, NULL, FALSE, FALSE, FALSE, NULL, TRUE
        );

        $etapasEducacenso = array(0 => 'Nenhuma') + loadJson('educacenso_json/etapas_ensino.json');

        foreach ($enturmacoes as $enturmacao) {
            $this->campoLista("etapas_educacenso[{$enturmacao['ref_cod_turma']}-{$enturmacao['sequencial']}]", "Etapa turma: {$enturmacao['nm_turma']}", $etapasEducacenso, $enturmacao['etapa_educacenso'],'', FALSE, '', '', FALSE, FALSE);
        }
    }

    function Editar()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->validaPermissao();
        $this->validaParametros();

        foreach ($this->etapas_educacenso as $codTurmaESequencial => $etapaEducacenso) {
            // Necessário pois chave é Turma + Matrícula + Sequencial
            $codTurmaESequencial = explode('-',$codTurmaESequencial);
            $codTurma = $codTurmaESequencial[0];
            $sequencial = $codTurmaESequencial[1];
            $obj = new clsPmieducarMatriculaTurma($this->cod_matricula, $codTurma, $this->pessoa_logada);
            $obj->sequencial = $sequencial;
            $obj->etapa_educacenso = $etapaEducacenso;
            $obj->edita();
        }

        $this->mensagem .= "Etapas atualizadas com sucesso.<br>";
        return TRUE;
    }

    private function montaLocalizacao()
    {
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "Início",
             "educar_index.php"                  => "Escola",
             ""        => "Etapa do aluno"
        ));
        $this->enviaLocalizacao($localizacao->montar());
    }

    private function validaPermissao()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
    }

    private function validaParametros()
    {
        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);
        $det_matricula = $obj_matricula->detalhe();

        if(!$det_matricula)
            header("location: educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}");
    }
}

// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm( $miolo );
// gera o html
$pagina->MakeAll();
?>
