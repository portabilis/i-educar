<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
*                                                                        *
*   @author Prefeitura Municipal de Itajaí                               *
*   @updated 29/03/2007                                                  *
*   Pacote: i-PLB Software Público Livre e Brasileiro                    *
*                                                                        *
*   Copyright (C) 2006  PMI - Prefeitura Municipal de Itajaí             *
*                       ctima@itajai.sc.gov.br                           *
*                                                                        *
*   Este  programa  é  software livre, você pode redistribuí-lo e/ou     *
*   modificá-lo sob os termos da Licença Pública Geral GNU, conforme     *
*   publicada pela Free  Software  Foundation,  tanto  a versão 2 da     *
*   Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.    *
*                                                                        *
*   Este programa  é distribuído na expectativa de ser útil, mas SEM     *
*   QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-     *
*   ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-     *
*   sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.     *
*                                                                        *
*   Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU     *
*   junto  com  este  programa. Se não, escreva para a Free Software     *
*   Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA     *
*   02111-1307, USA.                                                     *
*                                                                        *
* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
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
        $this->addEstilo("localizacaoSistema");
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

        $det_matricula = $obj_matricula->detalhe();

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->ref_cod_matricula}";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""                                  => "Registro do falecimento do aluno"
        ));
        $this->enviaLocalizacao($localizacao->montar());

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
