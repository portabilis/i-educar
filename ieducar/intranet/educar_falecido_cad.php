<?php

require_once "include/clsBase.inc.php";
require_once "include/clsCadastro.inc.php";
require_once "include/clsBanco.inc.php";
require_once "include/pmieducar/geral.inc.php";
require_once "lib/Portabilis/Date/Utils.php";
require_once 'modules/Avaliacao/Model/NotaAlunoDataMapper.php';
require_once 'modules/Avaliacao/Model/NotaComponenteMediaDataMapper.php';
require_once 'lib/App/Model/MatriculaSituacao.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Transfer&ecirc;ncia Solicita&ccedil;&atilde;o" );
        $this->processoAp = "578";
    }
}

class indice extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    var $pessoa_logada;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->ref_cod_matricula=$_GET["ref_cod_matricula"];
        $this->ref_cod_aluno=$_GET["ref_cod_aluno"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

        $obj_matricula = new clsPmieducarMatricula( $this->cod_matricula,null,null,null,$this->pessoa_logada,null,null,6 );

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $this->breadcrumb('Registro do falecimento do aluno', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
        $this->campoOculto( "ref_cod_matricula", $this->ref_cod_matricula );

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
        if ( is_array($lst_aluno) )
        {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno["nome_aluno"];
            $this->campoTexto( "nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false,false,false,"","","","",true );
        }

        $this->inputsHelper()->date('data_cancel', array('label' => 'Data do falecimento', 'placeholder' => 'dd/mm/yyyy', 'value' => date('d/m/Y')));

        $this->campoMemo( "observacao", "Observa&ccedil;&atilde;o", $this->observacao, 60, 5, false );
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}" );

        $tamanhoObs = strlen($this->observacao);
        if($tamanhoObs > 300){
            $this->mensagem = "O campo observação deve conter no máximo 300 caracteres.<br>";
            return FALSE;
        }

        $obj_matricula = new clsPmieducarMatricula( $this->ref_cod_matricula,null,null,null,$this->pessoa_logada,null,null,6 );
        $obj_matricula->data_cancel = Portabilis_Date_Utils::brToPgSQL($this->data_cancel);

        $det_matricula = $obj_matricula->detalhe();

        if(is_null($det_matricula['data_matricula'])){

            if(substr($det_matricula['data_cadastro'], 0, 10) > $obj_matricula->data_cancel) {
                $this->mensagem = "Data de falecimento não pode ser inferior a data da matrícula.<br>";
                return false;
            }
        }else {
            if(substr($det_matricula['data_matricula'], 0, 10) > $obj_matricula->data_cancel){
                $this->mensagem = "Data de falecimento não pode ser inferior a data da matrícula.<br>";
                return false;
            }
        }

        if($obj_matricula->edita()) {
            if($obj_matricula->cadastraObservacaoFalecido($this->observacao)) {
                $enturmacoes = new clsPmieducarMatriculaTurma();
                $enturmacoes = $enturmacoes->lista($this->ref_cod_matricula, null, null, null, null, null, null, null, 1 );

                foreach ($enturmacoes as $enturmacao) {
                  $enturmacao = new clsPmieducarMatriculaTurma( $this->ref_cod_matricula, $enturmacao['ref_cod_turma'], $this->pessoa_logada, null, null, null, 0, null, $enturmacao['sequencial']);

                  if(! $enturmacao->edita())
                  {
                    $this->mensagem = "N&atilde;o foi poss&iacute;vel desativar as enturma&ccedil;&otilde;es da matr&iacute;cula.";
                    return false;
                  }else
                    $enturmacao->marcaAlunoFalecido($this->data_cancel);

                }

                $notaAluno = (new Avaliacao_Model_NotaAlunoDataMapper())
                                    ->findAll(['id'], ['matricula_id' => $obj_matricula->cod_matricula])[0] ?? null;

                if (!empty($notaAluno)) {
                    (new Avaliacao_Model_NotaComponenteMediaDataMapper())
                        ->updateSituation($notaAluno->get('id'), App_Model_MatriculaSituacao::FALECIDO);
                }

                $this->mensagem .= "Alteração realizado com sucesso.<br>";
                $this->simpleRedirect("educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}");
            }

            $this->mensagem = "A alteração não pode ser salva.<br>";

            return false;
        }
        $this->mensagem = "A alteração não pode ser realizado.<br>";
        return false;
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
