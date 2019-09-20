<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);


require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Exemplar" );
        $this->processoAp = "606";
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

    var $cod_exemplar;
    var $ref_cod_fonte;
    var $ref_cod_motivo_baixa;
    var $ref_cod_acervo;
    var $ref_cod_situacao;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $permite_emprestimo;
    var $preco;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $data_aquisicao;

    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_biblioteca;

    var $tombo;
    var $qtd_livros;

    function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_exemplar=$_GET["cod_exemplar"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        if( is_numeric( $this->cod_exemplar ) )
        {

            $obj = new clsPmieducarExemplar( $this->cod_exemplar );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $obj_obra = new clsPmieducarAcervo($this->ref_cod_acervo);
                $det_obra = $obj_obra->detalhe();

                $obj_biblioteca = new clsPmieducarBiblioteca($det_obra["ref_cod_biblioteca"]);
                $obj_det = $obj_biblioteca->detalhe();

                $this->ref_cod_instituicao = $obj_det["ref_cod_instituicao"];
                $this->ref_cod_escola = $obj_det["ref_cod_escola"];
                $this->ref_cod_biblioteca = $obj_det["cod_biblioteca"];


                $this->data_aquisicao = dataFromPgToBr( $this->data_aquisicao );

                if( $obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11 ) )
                {
                    $this->fexcluir = true;
                }

                $retorno = "Editar";
            }
        }

        $this->url_cancelar = ($retorno == "Editar") ? "educar_exemplar_det.php?cod_exemplar={$registro["cod_exemplar"]}" : "educar_exemplar_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' exemplar', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        $this->campoOculto( "cod_exemplar", $this->cod_exemplar );

    $selectInputs = ['instituicao', 'escola', 'biblioteca'];
    $this->inputsHelper()->dynamic($selectInputs);

    $this->inputsHelper()->dynamic('bibliotecaSituacao', ['label' => 'Situação']);
    $this->inputsHelper()->dynamic('bibliotecaFonte');

        $opcoes = array( "" => "Selecione", "2" => "Sim", "1" => "N&atilde;o" );
        $this->campoLista( "permite_emprestimo", "Permite empréstimo", $opcoes, $this->permite_emprestimo );

        $this->preco = is_numeric($this->preco) ? number_format($this->preco, 2, ",", ".") : "";
        $this->campoMonetario( "preco", "Preço", $this->preco, 10, 20, false );

    $this->inputsHelper()->dynamic('bibliotecaPesquisaObra', array('required' => true));

        // data
        if(!$this->data_aquisicao)
            $this->data_aquisicao = date("d/m/Y");

        $this->campoData( "data_aquisicao", "Data de entrada", $this->data_aquisicao, false );

    $this->campoNumero("tombo", "Tombo", $this->tombo, 10, 10, false, 'somente números. Deixe em branco para gerar o sequencial automaticamente.');

        if (!is_numeric($this->cod_exemplar))
            $this->campoNumero("qtd_livros", "Quantidade de exemplares", 1, 5, 5, true, 'somente números. Altere esse campo caso deseje cadastrar mais cópias deste exemplar automaticamente.<br/> Os códigos tombos inseridos serão sequenciais.');
    }

    function Novo()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        $this->preco = str_replace(".","",$this->preco);
        $this->preco = str_replace(",",".",$this->preco);
        $this->data_aquisicao = dataToBanco($this->data_aquisicao);


        for ($i = 0; $i < $this->qtd_livros; $i++)
        {
            $obj_temp = new clsPmieducarExemplar();
            $tombo_valido = $obj_temp->retorna_tombo_valido($this->ref_cod_biblioteca,null,$this->tombo);
            if (!$tombo_valido){
                $this->mensagem = "Esse Tombo já está registrado";
                return false;
            }

            $obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao, $this->getTombo(), $this->getSequencial());
            $cadastrou = $obj->cadastra();
            if (!$cadastrou)
            {
                $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

                return false;
            }
        }
        $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
        $this->simpleRedirect('educar_exemplar_lst.php');
    }


    function Editar()
    {



        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );

        $this->preco = str_replace(".","",$this->preco);
        $this->preco = str_replace(",",".",$this->preco);

        $obj_temp = new clsPmieducarExemplar();
            $tombo_valido = $obj_temp->retorna_tombo_valido($this->ref_cod_biblioteca,$this->cod_exemplar,$this->tombo);
            if (!$tombo_valido){
                $this->mensagem = "Esse Tombo já está registrado";
                return false;
            }
        $this->data_aquisicao = dataToBanco($this->data_aquisicao);

        $obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->data_aquisicao, $this->getTombo());
        $editou = $obj->edita();
        if( $editou )
        {
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_exemplar_lst.php');
        }

        $this->mensagem = "Edi&ccedil;&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 606, $this->pessoa_logada, 11,  "educar_exemplar_lst.php" );
        $this->data_aquisicao = dataToBanco($this->data_aquisicao);

        $obj = new clsPmieducarExemplar($this->cod_exemplar, $this->ref_cod_fonte, $this->ref_cod_motivo_baixa, $this->ref_cod_acervo, $this->ref_cod_situacao, $this->pessoa_logada, $this->pessoa_logada, $this->permite_emprestimo, $this->preco, $this->data_cadastro, $this->data_exclusao, 0, $this->data_aquisicao);
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_exemplar_lst.php');
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";

        return false;
    }

  protected function getTombo(){
        if (! $this->tombo) {
            $exemplar = new clsPmieducarExemplar();
            $tombo    = $exemplar->retorna_tombo_maximo($this->ref_cod_biblioteca, $this->cod_exemplar) + 1;
    }
    else {
      // após obter tombo reseta para na proxima chamada de getTombo buscar o proximo no banco
      $tombo       = $this->tombo;
      $this->tombo = null;
    }

    return $tombo;
  }

  protected function getSequencial(){
    $exemplar = new clsPmieducarExemplar();
    $sequencial = $exemplar->getProximoSequencialObra($this->ref_cod_acervo);
    return $sequencial;
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
