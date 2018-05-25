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
require_once ("include/clsBase.inc.php");
require_once ("include/clsCadastro.inc.php");
require_once ("include/clsBanco.inc.php");
require_once( "include/pmieducar/geral.inc.php");
require_once 'lib/Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Turno da matrícula" );
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

    var $cod_matricula;
    var $turno_id;
    var $ref_cod_aluno;
    var $nm_aluno;

    function Inicializar()
    {
        $retorno = "Novo";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_matricula = $_GET["cod_matricula"];
        $this->ref_cod_aluno = $_GET["ref_cod_aluno"];
        $cancela = $_GET["cancela"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?ref_cod_aluno={$this->cod_matricula}" );

        $obj_matricula  = new clsPmieducarMatricula($this->cod_matricula);

        $det_matricula  = $obj_matricula->detalhe();

        if ($det_matricula) {
            $retorno = 'Editar';
        }

        $this->turno_id = $det_matricula['turno_id'];

        $this->url_cancelar = "educar_matricula_det.php?cod_matricula={$this->cod_matricula}";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "Início",
             "educar_index.php"                  => "Escola",
             ""                                  => "Turno da matrícula"
        ));

        $this->enviaLocalizacao($localizacao->montar());

        $this->nome_url_cancelar = "Cancelar";

        return $retorno;
    }

    function Gerar()
    {
        $this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
        $this->campoOculto( "cod_matricula", $this->cod_matricula );

        $obj_aluno = new clsPmieducarAluno();
        $lst_aluno = $obj_aluno->lista( $this->ref_cod_aluno,null,null,null,null,null,null,null,null,null,1 );
        if ( is_array($lst_aluno) )
        {
            $det_aluno = array_shift($lst_aluno);
            $this->nm_aluno = $det_aluno["nome_aluno"];
            $this->campoTexto( "nm_aluno", "Aluno", $this->nm_aluno, 30, 255, false,false,false,"","","","",true );
        }

        $this->inputsHelper()->turmaTurno(array('value' => $this->turno_id, 'required' => false), array('attrName' => 'turno_id'));
    }

    function Editar()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->cod_matricula}" );

        $obj_matricula = new clsPmieducarMatricula($this->cod_matricula);

        $atualizaTurno = $obj_matricula->atualizaTurno($this->cod_matricula, $this->turno_id);

        if ($atualizaTurno) {
            $this->mensagem .= "Turno atualizado com sucesso.<br>";
            header( "Location: educar_matricula_det.php?cod_matricula={$this->cod_matricula}" );
            return true;
        }

        $this->mensagem = "Turno não atualizado.<br>";
        return false;

    }

   function Excluir()
   {
     @session_start();
       $this->pessoa_logada = $_SESSION['id_pessoa'];
     @session_write_close();

     $obj_permissoes = new clsPermissoes();
     $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_matricula_det.php?cod_matricula={$this->cod_matricula}" );
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
