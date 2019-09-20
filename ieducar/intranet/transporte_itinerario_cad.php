<?php
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Module
 * @since     07/2013
 * @version   $Id$
 */

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/View/Helper/Application.php';

require_once 'include/modules/clsModulesRotaTransporteEscolar.inc.php';
require_once 'include/modules/clsModulesItinerarioTransporteEscolar.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Itinerário" );
        $this->processoAp = "21238";
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

    var $cod_rota;
    var $descricao;

// INCLUI NOVO
    var $pontos;
    var $ref_cod_ponto_transporte_escolar;
    var $hora;
    var $tipo;
    var $ref_cod_veiculo;

//------INCLUI DISCIPLINA------//
    var $historico_disciplinas;
    var $nm_disciplina;
    var $nota;
    var $faltas;
    var $excluir_disciplina;
    var $ultimo_sequencial;

    var $aceleracao;

    function Inicializar()
    {
        $retorno = "Editar";


        $this->cod_rota=$_GET["cod_rota"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 21238, $this->pessoa_logada, 7,  "transporte_rota_det.php?cod_rota={$this->cod_rota}" );
        $volta = false;
        if( is_numeric( $this->cod_rota ))
        {
            $obj = new clsModulesRotaTransporteEscolar( $this->cod_rota );
            $registro  = $obj->detalhe();
            if( $registro )
                $this->descricao = $registro['descricao'];
            else
                $volta = true;
        }else
            $volta = true;


        if ($volta){
            $this->simpleRedirect('transporte_rota_lst.php');
        }
        $this->url_cancelar = "transporte_rota_det.php?cod_rota={$this->cod_rota}";
        $this->nome_url_cancelar = "Cancelar";

    $localizacao = new LocalizacaoSistema();
    $localizacao->entradaCaminhos( array(
         $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
         "educar_transporte_escolar_index.php" => "Transporte escolar",
         "" => "Editar itinerário"
    ));
    $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    function Gerar()
    {

        if( $_POST )
            foreach( $_POST AS $campo => $val )
                $this->$campo = ( !$this->$campo ) ?  $val : $this->$campo ;

        $this->campoRotulo("cod_rota","Código da rota" ,$this->cod_rota);
        $this->campoRotulo("descricao","Rota", $this->descricao );



    //---------------------INCLUI DISCIPLINAS---------------------//
        $this->campoQuebra();

        if( is_numeric( $this->cod_rota) && !$_POST)
        {
            $obj = new clsModulesItinerarioTransporteEscolar();
            $obj->setOrderby(" seq ASC");
            $registros = $obj->lista(null, $this->cod_rota);
            $qtd_pontos = 0;
            if( $registros )
            {
                foreach ( $registros AS $campo )
                {
                    //$this->pontos[$qtd_pontos][] = $campo["cod_itinerario_transporte_escolar"];
                    $this->pontos[$qtd_pontos][] = $campo["ref_cod_ponto_transporte_escolar"].' - '.$campo["descricao"];
                    //$this->pontos[$qtd_pontos][] = $campo["descricao"];
                    $this->pontos[$qtd_pontos][] = $campo["hora"];
                    $this->pontos[$qtd_pontos][] = $campo["tipo"];
                    $this->pontos[$qtd_pontos][] = $campo["ref_cod_veiculo"].' - '.$campo["nome_onibus"];
                    //$this->pontos[$qtd_pontos][] = $campo["seq"];
                    $qtd_pontos++;
                }
            }
        }

        $this->campoTabelaInicio("pontos","Itinerário",array("Ponto (Requer pré-cadastro)<br/> <spam style=\" font-weight: normal; font-size: 10px;\">Digite o código ou nome do ponto e selecione o desejado</spam>","Hora","Tipo","Veículo (Requer pré-cadastro)<br/> <spam style=\" font-weight: normal; font-size: 10px;\">Digite o código, nome ou placa do veículo e selecione o desejado</spam>" ),$this->pontos);

        $this->campoTexto( "ref_cod_ponto_transporte_escolar", "Ponto (Requer pré-cadastro)", $this->ref_cod_ponto_transporte_escolar, 50, 255, false, true, false, '', '', '', 'onfocus' );

        $this->campoHora( "hora", "Hora", $this->hora);
        $this->campoLista( "tipo", "Tipo", array( '' => "Selecione", 'I' => 'Ida', 'V' => 'Volta'),$this->tipo );
        $this->campoTexto( "ref_cod_veiculo", "Veículo", $this->ref_cod_veiculo, 50, 255, false, false, false, '', '', '', 'onfocus' );
        $this->campoTabelaFim();

        $this->campoQuebra();
    //---------------------FIM INCLUI DISCIPLINAS---------------------//

    // carrega estilo para feedback messages, para exibir msg validação frequencia.

       $style = "/modules/Portabilis/Assets/Stylesheets/Frontend.css";
       Portabilis_View_Helper_Application::loadStylesheet($this, $style);

        Portabilis_View_Helper_Application::loadJavascript(
            $this,
            array('/modules/Portabilis/Assets/Javascripts/Utils.js',
                        '/modules/Portabilis/Assets/Javascripts/Frontend/Inputs/SimpleSearch.js',
                        '/modules/Portabilis/Assets/Javascripts/Validator.js')
        );
        $this->addBotao('Excluir todos',"transporte_itinerario_del.php?cod_rota={$this->cod_rota}");

    }

    function Novo()
    {
        return true;
    }

    function Editar()
    {



        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 21238, $this->pessoa_logada, 7,  "transporte_rota_det.php?cod_rota={$this->cod_rota}" );

        if ($this->ref_cod_ponto_transporte_escolar)
        {

            $obj  = new clsModulesItinerarioTransporteEscolar();
            $codRotaInt = (int)$this->cod_rota;
            $itinerario = $obj->lista(null, $codRotaInt);

            $excluiu = $obj->excluirTodos( $this->cod_rota );

            if ( $excluiu )
            {

            foreach ($itinerario as $key => $campo) {
                $auditoria = new clsModulesAuditoriaGeral("itinerario_transporte_escolar", $this->pessoa_logada, $campo['cod_itinerario_transporte_escolar']);
                $auditoria->exclusao($campo);
            }

                $sequencial = 1;
                foreach ( $this->ref_cod_ponto_transporte_escolar AS $key => $ponto )
                {

                    $obj = new clsModulesItinerarioTransporteEscolar(NULL, $this->cod_rota, $sequencial, $this->retornaCodigo($ponto), $this->retornaCodigo($this->ref_cod_veiculo[$key]),
                    $this->hora[$key], $this->tipo[$key]);
                    $cadastrou1 = $obj->cadastra();
                    if( !$cadastrou1 )
                    {
                        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";
                        return false;
                    }
                    $sequencial++;

                    $itinerario = new clsModulesItinerarioTransporteEscolar($cadastrou1);
                    $itinerario = $itinerario->detalhe();

                    $auditoria = new clsModulesAuditoriaGeral("itinerario_transporte_escolar", $this->pessoa_logada, $cadastrou1);
                    $auditoria->inclusao($itinerario);

                }
            }
            $this->mensagem .= "Edi&ccedil;&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect("transporte_rota_det.php?cod_rota={$this->cod_rota}");
        }

    }

    function Excluir()
    {
         return true;
    }

    protected function retornaCodigo($palavra){

        return substr($palavra, 0, strpos($palavra, " -"));
    }

    protected function fixupFrequencia($frequencia) {
        if (strpos($frequencia, ',')) {
            $frequencia = str_replace('.', '',  $frequencia);
            $frequencia = str_replace(',', '.', $frequencia);
        }

        return $frequencia;
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

    // autocomplete disciplina fields

  var handleSelect = function(event, ui){
        $j(event.target).val(ui.item.label);
        return false;
    };

    var search = function(request, response) {
        var searchPath = '/module/Api/Ponto?oper=get&resource=ponto-search';
        var params     = { query : request.term };

        $j.get(searchPath, params, function(dataResponse) {
            simpleSearch.handleSearch(dataResponse, response);
        });
    };

    var searchV = function(request, response) {
        var searchPath = '/module/Api/Veiculo?oper=get&resource=veiculo-search';
        var params     = { query : request.term };

        $j.get(searchPath, params, function(dataResponse) {
            simpleSearch.handleSearch(dataResponse, response);
        });
    };

    function setAutoComplete() {
        $j.each($j('input[id^="ref_cod_ponto_transporte_escolar"]'), function(index, field) {

            $j(field).autocomplete({
                source    : search,
                select    : handleSelect,
                minLength : 1,
                autoFocus : true
            });

        });
        $j.each($j('input[id^="ref_cod_veiculo"]'), function(index, field) {

            $j(field).autocomplete({
                source    : searchV,
                select    : handleSelect,
                minLength : 1,
                autoFocus : true
            });

        });
    }

    setAutoComplete();

    document.onclick = function(event) {
        var targetElement = event.target;
        if ( targetElement.value == " Cancelar " ) {

            var cod_rota = $j('#cod_rota').val();
            location.href="transporte_rota_det.php?cod_rota="+cod_rota;
        } else if(targetElement.value == "Excluir todos"){
            var cod_rota = $j('#cod_rota').val();
            if(confirm('Este procedimento irá excluir todos os pontos do itinerário. Tem certeza que deseja continuar?')){
                location.href="transporte_itinerario_del.php?cod_rota="+cod_rota;
            }
        }
    };

    var submitForm = function(event) {
        // Esse formUtils.submit() chama o Editar();
        // Mais à frente bolar uma validação aqui
    /*  var $frequenciaField = $j('#frequencia');
        var frequencia       = $frequenciaField.val();

        if (frequencia.indexOf(',') > -1)
            frequencia = frequencia.replace('.', '').replace(',', '.');

      if (validatesIfNumericValueIsInRange(frequencia, $frequenciaField, 0, 100))*/
        formUtils.submit();
    }


    // bind events

    var $addPontosButton = $j('#btn_add_tab_add_1');

    $addPontosButton.click(function(){
        setAutoComplete();
    });


    // submit button

    var $submitButton = $j('#btn_enviar');

    $submitButton.removeAttr('onclick');
    $submitButton.click(submitForm);

</script>
