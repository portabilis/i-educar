<?php
/**
 *
 * Copyright (C) 2006 PMI - Prefeitura Municipal de Itajaí
 *            ctima@itajai.sc.gov.br
 *
 * Este  programa  é  software livre, você pode redistribuí-lo e/ou
 * modificá-lo sob os termos da Licença Pública Geral GNU, conforme
 * publicada pela Free  Software  Foundation,  tanto  a versão 2 da
 * Licença   como  (a  seu  critério)  qualquer  versão  mais  nova.
 *
 * Este programa  é distribuído na expectativa de ser útil, mas SEM
 * QUALQUER GARANTIA. Sem mesmo a garantia implícita de COMERCIALI-
 * ZAÇÃO  ou  de ADEQUAÇÃO A QUALQUER PROPÓSITO EM PARTICULAR. Con-
 * sulte  a  Licença  Pública  Geral  GNU para obter mais detalhes.
 *
 * Você  deve  ter  recebido uma cópia da Licença Pública Geral GNU
 * junto  com  este  programa. Se não, escreva para a Free Software
 * Foundation,  Inc.,  59  Temple  Place,  Suite  330,  Boston,  MA
 * 02111-1307, USA.
 *
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/View/Helper/Application.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Distribui&ccedil;&atilde;o de uniforme" );
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

    var $cod_distribuicao_uniforme;
    var $ref_cod_aluno;
    var $ano;
    var $agasalho_qtd;
    var $camiseta_curta_qtd;
    var $camiseta_longa_qtd;
    var $meias_qtd;
    var $bermudas_tectels_qtd;
    var $bermudas_coton_qtd;
    var $tenis_qtd;
    var $data;
    var $agasalho_tm;
    var $camiseta_curta_tm;
    var $camiseta_longa_tm;
    var $meias_tm;
    var $bermudas_tectels_tm;
    var $bermudas_coton_tm;
    var $tenis_tm;
    var $ref_cod_escola;

    function Inicializar()
    {
        $retorno = "Novo";
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->cod_distribuicao_uniforme=$_GET["cod_distribuicao_uniforme"];
        $this->ref_cod_aluno=$_GET["ref_cod_aluno"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

        if( is_numeric( $this->ref_cod_aluno ) && is_numeric( $this->cod_distribuicao_uniforme ) )
        {
            $obj = new clsPmieducarDistribuicaoUniforme( $this->cod_distribuicao_uniforme );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;

                $this->data = Portabilis_Date_Utils::pgSqlToBr($this->data);

                $this->kit_completo = dbBool($this->kit_completo);

                if( $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7 ) )
                {
                    $this->fexcluir = true;
                }
                $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_distribuicao_uniforme_det.php?ref_cod_aluno={$registro["ref_cod_aluno"]}&cod_distribuicao_uniforme={$registro["cod_distribuicao_uniforme"]}" : "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}";
        $this->nome_url_cancelar = "Cancelar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_index.php"                  => "Escola",
         ""                                  => "Distribuições de uniforme escolar"
    ));
    $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    function Gerar()
    {
        if( $_POST )
            foreach( $_POST AS $campo => $val )
                $this->$campo = ( !$this->$campo ) ?  $val : $this->$campo ;

        // primary keys
        $this->campoOculto( "ref_cod_aluno", $this->ref_cod_aluno );
        $this->campoOculto( "cod_distribuicao_uniforme", $this->cod_distribuicao_uniforme );

        $this->campoNumero( "ano", "Ano", $this->ano, 4, 4, true );
        $this->inputsHelper()->date('data', array( 'label' => "Data da distribuição", 'value' => $this->data, 'placeholder' => ''));

        $opcoes = array("" => "Selecione");
        $objTemp = new clsPmieducarEscola();

        $lista = $objTemp->lista(null, null, null, $det_matricula['ref_cod_instituicao']);

        foreach ($lista as $escola){
            $opcoes["{$escola['cod_escola']}"] = "{$escola['nome']}";
        }
        $this->campoLista("ref_cod_escola", "Escola", $opcoes, $this->ref_cod_escola, '', false, '(Responsável pela distribuição do uniforme)', '', false, true);

        $this->inputsHelper()->checkbox('kit_completo', array( 'label' => "Kit completo", 'value' => $this->kit_completo));
        // $this->campoNumero( "agasalho_qtd", "Quantidade de agasalhos (jaqueta e calça)", $this->agasalho_qtd, 2, 2, false );
        $options = array('required' => false, 'label' => 'Quantidade de agasalhos (jaqueta e calça)', 'value' => $this->agasalho_qtd, 'max_length' => 2, 'size' => 2, 'inline'  => true);
        $this->inputsHelper()->integer('agasalho_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->agasalho_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('agasalho_tm', $options);
        // $this->campoNumero( "camiseta_curta_qtd", "Quantidade de camisetas (manga curta)", $this->camiseta_curta_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Quantidade de camisetas (manga curta)', 'value' => $this->camiseta_curta_qtd, 'max_length' => 2, 'size' => 2, 'inline'    => true);
        $this->inputsHelper()->integer('camiseta_curta_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->camiseta_curta_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('camiseta_curta_tm', $options);
        // $this->campoNumero( "camiseta_longa_qtd", "Quantidade de camisetas (manga longa)", $this->camiseta_longa_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Quantidade de camisetas (manga longa)', 'value' => $this->camiseta_longa_qtd, 'max_length' => 2, 'size' => 2, 'inline'    => true);
        $this->inputsHelper()->integer('camiseta_longa_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->camiseta_longa_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('camiseta_longa_tm', $options);
        // $this->campoNumero( "meias_qtd", "Quantidade de meias", $this->meias_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Quantidade de meias', 'value' => $this->meias_qtd, 'max_length' => 2, 'size' => 2, 'inline'   => true);
        $this->inputsHelper()->integer('meias_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->meias_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('meias_tm', $options);
        // $this->campoNumero( "bermudas_tectels_qtd", "Bermudas tectels (masculino)", $this->bermudas_tectels_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Bermudas tectels (masculino)', 'value' => $this->bermudas_tectels_qtd, 'max_length' => 2, 'size' => 2, 'inline'   => true);
        $this->inputsHelper()->integer('bermudas_tectels_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->bermudas_tectels_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('bermudas_tectels_tm', $options);
        // $this->campoNumero( "bermudas_coton_qtd", "Bermudas coton (feminino)", $this->bermudas_coton_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Bermudas coton (feminino)', 'value' => $this->bermudas_coton_qtd, 'max_length' => 2, 'size' => 2, 'inline' => true);
        $this->inputsHelper()->integer('bermudas_coton_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->bermudas_coton_tm, 'max_length' => 10, 'size' => 10);
        $this->inputsHelper()->text('bermudas_coton_tm', $options);
        // $this->campoNumero( "tamanho", "Tênis", $this->tenis_qtd, 2, 2, false);
        $options = array('required' => false, 'label' => 'Tênis', 'value' => $this->tenis_qtd, 'max_length' => 2, 'size' => 2, 'inline' => true);
        $this->inputsHelper()->integer('tenis_qtd', $options);
        $options = array('required' => false, 'label' => ' Tamanho', 'value' => $this->tenis_tm, 'max_length'  => 10, 'size' => 10);
        $this->inputsHelper()->text('tenis_tm', $options);

    }

    function Novo()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista($this->ref_cod_aluno, $this->ano);

        if($lista_tmp){
            $this->mensagem = "Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>";
            return false;
        }

        $obj = new clsPmieducarDistribuicaoUniforme( null, $this->ref_cod_aluno, $this->ano, !is_null($this->kit_completo), $this->agasalho_qtd,
                                                                                                $this->camiseta_curta_qtd, $this->camiseta_longa_qtd, $this->meias_qtd, $this->bermudas_tectels_qtd,
                                                                                                $this->bermudas_coton_qtd, $this->tenis_qtd, $this->data,
                                                                                                $this->agasalho_tm, $this->camiseta_curta_tm, $this->camiseta_longa_tm, $this->meias_tm,
                                                                                                $this->bermudas_tectels_tm, $this->bermudas_coton_tm, $this->tenis_tm, $this->ref_cod_escola);
        $this->cod_distribuicao_uniforme = $cadastrou = $obj->cadastra();
        if( $cadastrou )
        {
      $distribuicao = new clsPmieducarDistribuicaoUniforme($this->cod_distribuicao_uniforme);
      $distribuicao = $distribuicao->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("distribuicao_uniforme", $this->pessoa_logada, $this->cod_distribuicao_uniforme);
      $auditoria->inclusao($distribuicao);

                $this->mensagem .= "Cadastro efetuado com sucesso.<br>";
                header( "Location: educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
                die();
                return true;
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
        return false;
    }

    function Editar()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $this->data = Portabilis_Date_Utils::brToPgSQL($this->data);

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 578, $this->pessoa_logada, 7,  "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );

        $obj_tmp = $obj = new clsPmieducarDistribuicaoUniforme();
        $lista_tmp = $obj_tmp->lista($this->ref_cod_aluno, $this->ano);

        if($lista_tmp){
            foreach ($lista_tmp as $reg) {
                if ($reg['cod_distribuicao_uniforme'] != $this->cod_distribuicao_uniforme){
                    $this->mensagem = "Já existe uma distribuição cadastrada para este ano, por favor, verifique.<br>";
                    return false;
                }
            }
        }

        $obj = new clsPmieducarDistribuicaoUniforme( $this->cod_distribuicao_uniforme, $this->ref_cod_aluno, $this->ano, !is_null($this->kit_completo),
                                                                                                    $this->agasalho_qtd, $this->camiseta_curta_qtd, $this->camiseta_longa_qtd, $this->meias_qtd,
                                                                                                    $this->bermudas_tectels_qtd, $this->bermudas_coton_qtd, $this->tenis_qtd, $this->data,
                                                                                                    $this->agasalho_tm, $this->camiseta_curta_tm, $this->camiseta_longa_tm, $this->meias_tm,
                                                                                                    $this->bermudas_tectels_tm, $this->bermudas_coton_tm, $this->tenis_tm, $this->ref_cod_escola);
    $detalheAntigo = $obj->detalhe();
        $editou = $obj->edita();
        if( $editou )
        {
      $auditoria = new clsModulesAuditoriaGeral("distribuicao_uniforme", $this->pessoa_logada, $this->cod_distribuicao_uniforme);
      $auditoria->alteracao($detalheAntigo, $obj->detalhe());

            $this->mensagem .= "Ed&ccedil;&atilde;o efetuada com sucesso.<br>";
            header( "Location: educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
            die();
            return true;
        }

        $this->mensagem = "Ed&ccedil;&atilde;o n&atilde;o realizada.<br>";
        return false;
    }

    function Excluir()
    {
        @session_start();
         $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 578, $this->pessoa_logada, 7,  "educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );


        $obj = new clsPmieducarDistribuicaoUniforme( $this->cod_distribuicao_uniforme);
    $detalhe = $obj->detalhe();
        $excluiu = $obj->excluir();
        if( $excluiu )
        {
      $auditoria = new clsModulesAuditoriaGeral("distribuicao_uniforme", $this->pessoa_logada, $this->cod_distribuicao_uniforme);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            header( "Location: educar_distribuicao_uniforme_lst.php?ref_cod_aluno={$this->ref_cod_aluno}" );
            die();
            return true;
        }

        $this->mensagem = "Exclus&atilde;o n&atilde;o realizada.<br>";
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

<script type="text/javascript">
    function bloqueiaCamposQuantidade(){
        $j('#agasalho_qtd').val('').attr('disabled', 'disabled');
        $j('#camiseta_curta_qtd').val('').attr('disabled', 'disabled');
        $j('#camiseta_longa_qtd').val('').attr('disabled', 'disabled');
        $j('#meias_qtd').val('').attr('disabled', 'disabled');
        $j('#bermudas_tectels_qtd').val('').attr('disabled', 'disabled');
        $j('#bermudas_coton_qtd').val('').attr('disabled', 'disabled');
        $j('#tenis_qtd').val('').attr('disabled', 'disabled');
        return true;
    }

    function liberaCamposQuantidade(){
        $j('#agasalho_qtd').removeAttr('disabled');
        $j('#camiseta_curta_qtd').removeAttr('disabled');
        $j('#camiseta_longa_qtd').removeAttr('disabled');
        $j('#meias_qtd').removeAttr('disabled');
        $j('#bermudas_tectels_qtd').removeAttr('disabled');
        $j('#bermudas_coton_qtd').removeAttr('disabled');
        $j('#tenis_qtd').removeAttr('disabled');
    }

    $j(document).ready(function(){
        if($j('#kit_completo').is(':checked'))
            bloqueiaCamposQuantidade();

        $j('#kit_completo').on('change', function(){
            if($j('#kit_completo').is(':checked'))
                bloqueiaCamposQuantidade();
            else
                liberaCamposQuantidade();
        });
    })
</script>
