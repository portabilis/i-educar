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
require_once( "include/pmieducar/geral.inc.php" );

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Documentação padrão" );
        $this->processoAp = "578";
        $this->addEstilo('localizacaoSistema');
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

    var $cod_instituicao;

    function Inicializar()
    {
        $retorno = "Documentação padrão";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_index.php"                  => "Escola",
             ""                                  => "Documentação padrão"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->cod_instituicao=$_GET["cod_instituicao"];

        return $retorno;
    }

    function Gerar()
    {
        Portabilis_View_Helper_Application::loadJavascript($this, array('/modules/Cadastro/Assets/Javascripts/DocumentacaoPadrao.js'));

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_escola = $obj_usuario_det["ref_cod_escola"];

        $this->campoOculto( "cod_instituicao", $this->cod_instituicao );
        $this->campoOculto( "pessoa_logada", $this->pessoa_logada );
        $this->campoOculto( "ref_cod_escola", $this->ref_cod_escola );


        $this->campoTexto( "titulo_documento", "Título", $this->titulo_documento, 30, 50, false );

        $this->campoArquivo('documento','Documentação padrão',$this->documento,40,Portabilis_String_Utils::toLatin1("<span id='aviso_formato'>São aceitos apenas arquivos no formato PDF com até 2MB.</span>", array('escape' => false)));

        $this->array_botao[] = 'Salvar';
        $this->array_botao_url_script[] = "go('educar_instituicao_lst.php')";

        $this->array_botao[] = 'Voltar';
        $this->array_botao_url_script[] = "go('educar_instituicao_lst.php')";
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