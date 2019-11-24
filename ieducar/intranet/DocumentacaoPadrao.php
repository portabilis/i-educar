<?php

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

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    var $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    var $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    var $offset;

    function Gerar()
    {
        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_instituicao = $obj_usuario_det["ref_cod_instituicao"];

        $obj_permissoes = new clsPermissoes();

        $nivelUsuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivelUsuario == 4){
            $this->campoOculto( "ref_cod_instituicao", $this->ref_cod_instituicao );

            $obj_instituicao = new clsPmieducarInstituicao();
            $lst_instituicao = $obj_instituicao->lista($this->ref_cod_instituicao);

            if (is_array($lst_instituicao)) {
                $det_instituicao      = array_shift($lst_instituicao);
                $this->nm_instituicao = $det_instituicao['nm_instituicao'];
                $this->campoRotulo('nm_instituicao', 'Institução', $this->nm_instituicao);
            }
        }

        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos( array(
             $_SERVER['SERVER_NAME']."/intranet" => "In&iacute;cio",
             "educar_escola_index.php"                  => "Escola",
             ""                                  => "Documentação padrão"
        ));
        $this->enviaLocalizacao($localizacao->montar());

        $this->inputsHelper()->dynamic(array('instituicao'));

        $opcoes_relatorio = array();
        $opcoes_relatorio[""] = "Selecione";
        $this->campoLista("relatorio", "Relatório", $opcoes_relatorio);
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

<style type="text/css">
    select#relatorio{
        min-width: 180px;
    }
</style>
<script>

var instituicaoId = document.getElementById('ref_cod_instituicao').value;
if (instituicaoId != '') {
    var selectRelatorio = document.getElementById('relatorio');
     selectRelatorio.length = 1;
     getDocumento(instituicaoId);
}

document.getElementById('btn_enviar').style.display = 'none';

document.getElementById('ref_cod_instituicao').onchange = function()
{
  var selectRelatorio = document.getElementById('relatorio');
  if (this.selectedIndex!==0) {
     selectRelatorio.length = 1;
     selectRelatorio.disabled = true;
     selectRelatorio.options[0].text = 'Carregando Relatorios';
     var instituicaoId = document.getElementById('ref_cod_instituicao').value;
     getDocumento(instituicaoId);
  }else{
    selectRelatorio.length = 1;
    selectRelatorio.options[0].text = 'Selecione';
  }
}

document.getElementById('relatorio').onchange = function()
{
 if (this.selectedIndex!==0) {
    window.open(linkUrlPrivada(this.value),'_blank');
 }
}

function getDocumento(instituicaoId) {
  var searchPath = '../module/Api/InstituicaoDocumentacao?oper=get&resource=getDocuments';
  var params = {instituicao_id : instituicaoId}
  var id     = '';
  var titulo = '';
  var url    = '';

  $j.get(searchPath, params, function(data){

    var documentos = data.documentos;

    for (var i = 0; i < documentos.length; i++) {
        var selectRelatorio = document.getElementById("relatorio");
        var option = document.createElement("option");
        selectRelatorio.options[0].text = 'Selecione um relatório';
        selectRelatorio.disabled = false;
        option.text = documentos[i].titulo_documento;
        option.value = documentos[i].url_documento;
        selectRelatorio.add(option);
    }
  });
}
</script>
