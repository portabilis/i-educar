<?php


return new class extends clsCadastro
{
    /**
     * Referencia pega da session para o idpes do usuario atual
     *
     * @var int
     */
    public $pessoa_logada;

    public $cod_cliente_tipo;
    public $ref_cod_biblioteca;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_tipo;
    public $descricao;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;
    public $ref_cod_escola;

    public $ref_cod_exemplar_tipo;
    public $dias_emprestimo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_cliente_tipo=$_GET['cod_cliente_tipo'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        if (is_numeric($this->cod_cliente_tipo)) {
            $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo);
            $registro  = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }

                $obj_biblioteca = new clsPmieducarBiblioteca($this->ref_cod_biblioteca);
                $obj_det = $obj_biblioteca->detalhe();
                $this->ref_cod_instituicao = $obj_det['ref_cod_instituicao'];
                $this->ref_cod_escola = $obj_det['ref_cod_escola'];

                if ($obj_permissoes->permissao_excluir(596, $this->pessoa_logada, 11)) {
                    $this->fexcluir = true;
                }
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_cliente_tipo_det.php?cod_cliente_tipo={$registro['cod_cliente_tipo']}" : 'educar_cliente_tipo_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' tipo de cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_cliente_tipo', $this->cod_cliente_tipo);

        if ($this->cod_cliente_tipo) {
            $instituicao_desabilitado = true;
            $escola_desabilitado = true;
            $biblioteca_desabilitado = true;
        }

        // foreign keys
        $get_escola     = 1;
        $escola_obrigatorio = false;
        $get_biblioteca = 1;
        $instituicao_obrigatorio = true;
        $biblioteca_obrigatorio = true;
        include('include/pmieducar/educar_campo_lista.php');

        // text
        $this->campoTexto('nm_tipo', 'Tipo Cliente', $this->nm_tipo, 30, 255, true);
        $this->campoMemo('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 60, 5, false);

        //-----------------------INICIO EXEMPLAR TIPO------------------------//

        $opcoes = [ '' => 'Selecione' ];
        $script .= "var editar_ = 0;\n";
        if ($_GET['cod_cliente_tipo']) {
            $script .= "editar_ = {$_GET['cod_cliente_tipo']};\n";
        }

        echo "<script>{$script}</script>";

        // se o caso é EDITAR
        if ($this->ref_cod_biblioteca) {
            $objTemp = new clsPmieducarExemplarTipo();
            $objTemp->setOrderby('nm_tipo ASC');
            $lista = $objTemp->lista(null, $this->ref_cod_biblioteca, null, null, null, null, null, null, null, null, 1);
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_exemplar_tipo']}"] = "{$registro['nm_tipo']}";
                }
            }
        }

        $this->campoRotulo('div_exemplares', 'Tipo Exemplar', '<div id=\'exemplares\'></div>');
        $this->acao_enviar = 'Valida();';
        //-----------------------FIM EXEMPLAR TIPO------------------------//
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $exemplar_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $exemplar_tipo;
            }
        }

        $obj = new clsPmieducarClienteTipo(null, $this->ref_cod_biblioteca, null, $this->pessoa_logada, $this->nm_tipo, $this->descricao, null, null, 1);
        $this->cod_cliente_tipo = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $obj->cod_cliente_tipo = $this->cod_cliente_tipo;

            //-----------------------CADASTRA EXEMPLAR TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $exemplar_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($cadastrou, $exemplar_tipo, $dias_emprestimo);
                    $cadastrou2  = $obj->cadastra();
                    if (!$cadastrou2) {
                        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

                        return false;
                    }
                }
            }
            //-----------------------FIM CADASTRA EXEMPLAR TIPO------------------------//

            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Cadastro n&atilde;o realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $array_tipos = [];
        foreach ($_POST as $key => $exemplar_tipo) {
            if (substr($key, 0, 5) == 'tipo_') {
                $array_tipos[substr($key, 5)] = $exemplar_tipo;
            }
        }

        $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, $this->ref_cod_biblioteca, $this->pessoa_logada, null, $this->nm_tipo, $this->descricao, null, null, 1);
        $editou = $obj->edita();
        if ($editou) {
            //-----------------------EDITA EXEMPLAR TIPO------------------------//
            if ($array_tipos) {
                foreach ($array_tipos as $exemplar_tipo => $dias_emprestimo) {
                    $obj = new clsPmieducarClienteTipoExemplarTipo($this->cod_cliente_tipo, $exemplar_tipo, $dias_emprestimo);

                    if ($obj->existe() == false) {
                        $result = $obj->cadastra();
                    } else {
                        $result = $obj->edita();
                    }

                    if (! $result) {
                        $this->mensagem = 'Aparentemente ocorreu um erro ao gravar os dias de emprestimo.<br>';

                        return false;
                    }
                }
            }

            //-----------------------FIM EDITA EXEMPLAR TIPO------------------------//

            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(596, $this->pessoa_logada, 11, 'educar_cliente_tipo_lst.php');

        $obj = new clsPmieducarClienteTipo($this->cod_cliente_tipo, null, $this->pessoa_logada, null, null, null, null, null, 0);
        $excluiu = $obj->excluir();
        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_cliente_tipo_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';

        return false;
    }
}


?>
<script>

var scriptValida = "";
var retorno = 1;
var divExemplares = document.getElementById( "tr_div_exemplares" );
setVisibility ('tr_div_exemplares', false);

function getExemplarTipo()
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var campoClienteTipo = document.getElementById('cod_cliente_tipo').value;
    var xml1 = new ajax(getExemplarTipo_XML);
    strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca+"&cod_tipo_cliente="+campoClienteTipo;
//  strURL = "educar_exemplar_tipo_xml.php?bib="+campoBiblioteca;
    xml1.envia(strURL);
}

function getExemplarTipo_XML(xml)
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var exemplares = document.getElementById('exemplares');
    var nm_tipo_exemplar = document.createElement( "input" );
    var span = document.createElement( "span" );
    var dias_tipo_exemplar = document.createElement( "input" );
    var br = document.createElement( "br" );
    var tipos = false;

    exemplares.innerHTML = "";
    scriptValida = "";

    var tipo_exemplar = xml.getElementsByTagName( "exemplar_tipo" );

    var aux = exemplares.innerHTML;

    if(tipo_exemplar.length)
        setVisibility ('tr_div_exemplares', true);

    for (var j = 0; j < tipo_exemplar.length; j++)
    {
        //if (tipo_exemplar[j][2] == campoBiblioteca)
        //{
        tipos = true;
        exemplares.appendChild(nm_tipo_exemplar);
        exemplares.appendChild(span);
        exemplares.appendChild(dias_tipo_exemplar);
        exemplares.appendChild(br);
        span.innerHTML = "Dias de Empréstimo";
        span.setAttribute( "class", "dias" );
        nm_tipo_exemplar.setAttribute( "id", "teste"+j );
        nm_tipo_exemplar.setAttribute( 'type', 'text' );
        nm_tipo_exemplar.setAttribute( 'disabled', 'true' );
        nm_tipo_exemplar.setAttribute( 'class', 'obrigatorio' );
        nm_tipo_exemplar.setAttribute( 'style', 'margin: 2px;' );
        nm_tipo_exemplar.setAttribute( 'value', tipo_exemplar[j].firstChild.nodeValue );
        dias_tipo_exemplar.setAttribute( "id", "tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo") );
        dias_tipo_exemplar.setAttribute( 'type', 'text' );
        dias_tipo_exemplar.setAttribute( 'size', '3' );
        dias_tipo_exemplar.setAttribute( 'autocomplete', 'off' );
        dias_tipo_exemplar.setAttribute( 'style', 'margin: 2px;' );
        dias_tipo_exemplar.setAttribute( 'maxlength', '3' );
        if(tipo_exemplar[j].getAttribute("dias_emprestimo"))
            dias_tipo_exemplar.setAttribute( 'value', tipo_exemplar[j].getAttribute("dias_emprestimo"));
        else
            dias_tipo_exemplar.setAttribute( 'value', '');

        dias_tipo_exemplar.setAttribute( 'class', 'obrigatorio' );

        exemplares.innerHTML += aux;

        scriptValida += "if (!(/[^ ]/.test( document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').value )) || !((/^[0-9]+$/).test( document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').value )))\n";
        scriptValida += "{\n";
        scriptValida += "retorno = 0;\n";
        scriptValida += "mudaClassName( 'formdestaque', 'formlttd' );\n";
        scriptValida += "document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').className = \"formdestaque\";\n";
        scriptValida += "alert( 'Preencha o campo \""+tipo_exemplar[j].firstChild.nodeValue +"\" corretamente!' );\n";
        scriptValida += "document.getElementById('tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")+"').focus();\n";
        //scriptValida +=   "return retorno;\n";
        scriptValida += "}\n\n";
        document.getElementById("tipo_"+tipo_exemplar[j].getAttribute("cod_exemplar_tipo")).name = dias_tipo_exemplar.id;
        //}
    }

    if(!tipos)
    {
        setVisibility ('tr_div_exemplares', false);

    }

    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Tipo Cliente");
        $this->processoAp = '596';
    }
};

function Valida()
{
    eval(scriptValida);
    if (retorno == 0)
    {
        retorno = 1;
        return false;
    }
    acao();
}

if(document.getElementById('ref_cod_biblioteca').type == 'hidden')
{
    getExemplarTipo();

}
else
{
    document.getElementById('ref_cod_biblioteca').onchange = function()
    {
        getExemplarTipo();
    }

}

if(editar_)
{
    getExemplarTipo();
}

</script>
<style>
.dias
{
    padding: 6px;
}
</style>
