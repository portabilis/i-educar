<?php


return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    /**
     * Quantidade de registros a ser apresentada em cada pagina
     *
     * @var int
     */
    public $limite;

    /**
     * Inicio dos registros a serem exibidos (limit)
     *
     * @var int
     */
    public $offset;

    public function Gerar()
    {
        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $obj_usuario_det = $obj_usuario->detalhe();
        $this->ref_cod_instituicao = $obj_usuario_det['ref_cod_instituicao'];

        $obj_permissoes = new clsPermissoes();

        $nivelUsuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivelUsuario == 4) {
            $this->campoOculto('ref_cod_instituicao', $this->ref_cod_instituicao);

            $obj_instituicao = new clsPmieducarInstituicao();
            $lst_instituicao = $obj_instituicao->lista($this->ref_cod_instituicao);

            if (is_array($lst_instituicao)) {
                $det_instituicao      = array_shift($lst_instituicao);
                $this->nm_instituicao = $det_instituicao['nm_instituicao'];
                $this->campoRotulo('nm_instituicao', 'Institução', $this->nm_instituicao);
            }
        }

        $this->largura = '100%';

        $this->breadcrumb('Documentação padrão', [
            url('intranet/educar_index.php') => 'Escola',
        ]);

        $this->inputsHelper()->dynamic(['instituicao']);

        $opcoes_relatorio = [];
        $opcoes_relatorio[''] = 'Selecione';
        $this->campoLista('relatorio', 'Relatório', $opcoes_relatorio);

    }

    public function Formular()
    {
        $this->title = "i-Educar - Documentação padrão";
        $this->processoAp = '578';
    }
};

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
