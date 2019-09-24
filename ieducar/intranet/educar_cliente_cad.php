<?php
/**
 *
 * @author  Prefeitura Municipal de Itajaí
 * @version SVN: $Id$
 *
 * Pacote: i-PLB Software Público Livre e Brasileiro
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
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    function Formular()
    {
        $this->SetTitulo( "{$this->_instituicao} i-Educar - Cliente" );
        $this->processoAp = "603";
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

    var $cod_cliente;
    var $ref_cod_instituicao;
    var $ref_cod_escola;
    var $ref_cod_biblioteca;
    var $ref_cod_biblioteca_atual;
    var $ref_cod_cliente_tipo;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $ref_idpes;
    var $login_;
    var $senha_;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $del_cod_cliente;
    var $del_cod_cliente_tipo;
  var $observacoes;


  function Inicializar()
    {
        $retorno = "Novo";


        $this->cod_cliente   = $_GET["cod_cliente"];
        $this->ref_cod_biblioteca = $_GET["ref_cod_biblioteca"];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra( 603, $this->pessoa_logada, 11,  "educar_cliente_lst.php" );
        if( is_numeric( $this->cod_cliente ) && is_numeric($this->ref_cod_biblioteca) )
        {
            $obj = new clsPmieducarCliente( $this->cod_cliente );
            $registro  = $obj->detalhe();
            if( $registro )
            {
                foreach( $registro AS $campo => $val )  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                $this->data_cadastro = dataFromPgToBr( $this->data_cadastro );
                $this->data_exclusao = dataFromPgToBr( $this->data_exclusao );

                $this->login_ = $this->login;
                $this->senha_ = $this->senha;

        $observacoes =  $this->observacoes;

        $obj_permissoes = new clsPermissoes();
                if( $obj_permissoes->permissao_excluir( 603, $this->pessoa_logada, 11 ) )
                {
                    $this->fexcluir = true;
                }

                    $retorno = "Editar";
            }
        }
        $this->url_cancelar = ($retorno == "Editar") ? "educar_cliente_det.php?cod_cliente={$registro["cod_cliente"]}&ref_cod_biblioteca={$this->ref_cod_biblioteca}" : "educar_cliente_lst.php";
        $this->nome_url_cancelar = "Cancelar";

        $nomeMenu = $retorno == "Editar" ? $retorno : "Cadastrar";

        $this->breadcrumb($nomeMenu . ' cliente', [
            url('intranet/educar_biblioteca_index.php') => 'Biblioteca',
        ]);

        return $retorno;
    }

    function Gerar()
    {
        // primary keys
        $this->campoOculto( "cod_cliente", $this->cod_cliente );
        $this->campoOculto("requisita_senha", "0");
        $opcoes = array( "" => "Pesquise a pessoa clicando na lupa ao lado" );
        if( $this->ref_idpes )
        {
            $objTemp = new clsPessoaFisica( $this->ref_idpes );
            $detalhe = $objTemp->detalhe();
            $opcoes["{$detalhe["idpes"]}"] = $detalhe["nome"];
        }

    // Caso o cliente não exista, exibe um campo de pesquisa, senão, mostra um rótulo
    if (!$this->cod_cliente) {
      $parametros = new clsParametrosPesquisas();
      $parametros->setSubmit(0);
      $parametros->adicionaCampoSelect('ref_idpes', 'idpes', 'nome');
      $parametros->setPessoa('F');
      $parametros->setPessoaCPF('N');
      $parametros->setCodSistema(null);
      $parametros->setPessoaNovo('S');
      $parametros->setPessoaTela('frame');

      $dados = array(
        'nome' => 'Cliente',
        'campo' => '',
        'valor' => array(null => 'Para procurar, clique na lupa ao lado.'),
        'default' => null,
        'acao' => "",
        'descricao' => "",
        'caminho' => 'pesquisa_pessoa_lst.php',
        'descricao2' => "",
        'flag' => null,
        'pag_cadastro' => null,
        'disabled' => "",
        'div' => false,
        'serializedcampos' => $parametros->serializaCampos(),
        'duplo' => false,
        'obrigatorio' => true
      );
      $this->setOptionsListaPesquisa("ref_idpes", $dados);

        }
    else {
      $this->campoTexto("codigo","Código",$this->cod_cliente,9,9,null,null,null,null,null,null,null,true);
      $this->campoOculto('ref_idpes', $this->ref_idpes);
      $this->campoRotulo('nm_cliente', 'Cliente', $detalhe['nome']);
    }


        // text
        $this->campoNumero( "login", "Login", $this->login_, 9, 9, false );
        $this->campoSenha( "senha", "Senha", $this->senha_, false );

        if($this->cod_cliente && $this->ref_cod_biblioteca)
        {
            $db = new clsBanco();

      // Cria campo oculto com o ID da biblioteca atual ao qual usuário está cadastrado
            $this->ref_cod_biblioteca_atual = $this->ref_cod_biblioteca;
            $this->campoOculto("ref_cod_biblioteca_atual", $this->ref_cod_biblioteca_atual);

            //$this->ref_cod_biblioteca   = $db->CampoUnico("SELECT cod_biblioteca  FROM pmieducar.biblioteca, pmieducar.cliente_tipo_cliente ctc, pmieducar.cliente_tipo ct WHERE ref_cod_cliente = '$this->cod_cliente' AND ref_cod_cliente_tipo = cod_cliente_tipo AND ct.ref_cod_biblioteca = cod_biblioteca AND ctc.ref_cod_biblioteca = {$this->ref_cod_biblioteca}");

      // obtem o codigo do tipo de cliente, apartir da tabela cliente_tipo_cliente
            $this->ref_cod_cliente_tipo = $db->CampoUnico("SELECT ref_cod_cliente_tipo FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = '$this->cod_cliente'");
        }

    $this->inputsHelper()->dynamic(array('instituicao', 'escola', 'biblioteca', 'bibliotecaTipoCliente'));

    $obs_options = array(
      'required'    => false,
      'label'       => 'Observações:',
      'cols'        => 35,
      'placeholder' => '',
      'max_length'  => 255,
      'value'       => $this->observacoes
    );
    $this->inputsHelper()->textArea( 'observacoes', $obs_options);
    }



  /**
   * Sobrescrita do método clsCadastro::Novo.
   *
   * Insere novo registro nas tabelas pmieducar.cliente e pmieducar.cliente_tipo_cliente.
   */
  public function Novo() {
    $senha = md5($this->senha . 'asnk@#*&(23');

    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11,  'educar_cliente_lst.php');

    $obj = new clsPmieducarCliente();
    $lista = $obj->lista(NULL, NULL, NULL, $this->ref_idpes, NULL, NULL, NULL, NULL, NULL, NULL, 1);
    if (!$lista) {
      $obj_cliente = new clsPmieducarCliente();
      $lst_cliente = $obj_cliente->lista(NULL, NULL, NULL, NULL, $this->login);

      if ($lst_cliente && $this->login != '') {
        $this->mensagem = "Este login já está sendo utilizado por outra pessoa!<br>";
      }
      else {
        $obj = new clsPmieducarCliente($this->cod_cliente, NULL, $this->pessoa_logada,
                  $this->ref_idpes, $this->login, $senha, $this->data_cadastro, $this->data_exclusao, 1, $this->observacoes);

        $this->cod_cliente = $cadastrou = $obj->cadastra();
        if ($cadastrou) {
          $obj->cod_cliente = $this->cod_cliente;
          $cliente = $obj->detalhe();
          $auditoria = new clsModulesAuditoriaGeral("cliente", $this->pessoa_logada, $this->cod_cliente);
          $auditoria->inclusao($cliente);

          $this->cod_cliente = $cadastrou;
          $obj_cliente_tipo = new clsPmieducarClienteTipoCliente($this->ref_cod_cliente_tipo,
            $this->cod_cliente, NULL, NULL, $this->pessoa_logada, $this->pessoa_logada, 1);

          if ($obj_cliente_tipo->existeCliente()) {
            if ($obj_cliente_tipo->trocaTipo()) {
              $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
              $this->simpleRedirect('educar_definir_cliente_tipo_lst.php');
            }
          }
          else {
            $obj_cliente_tipo = new clsPmieducarClienteTipoCliente($this->ref_cod_cliente_tipo,
              $this->cod_cliente, NULL, NULL, $this->pessoa_logada, NULL, 1, $this->ref_cod_biblioteca);

            if ($obj_cliente_tipo->cadastra()) {
              $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
              $this->simpleRedirect('educar_cliente_lst.php');
            }
          }
        }

        $this->mensagem = "Cadastro n&atilde;o realizado.<br>";

        return FALSE;
      }
    }
    else {
      $obj = new clsPmieducarCliente();
      $registro = $obj->lista(NULL, NULL, NULL, $this->ref_idpes, NULL, NULL, NULL, NULL, NULL, NULL, 1);
      if ($registro) {
        $this->cod_cliente = $registro[0]['cod_cliente'];
      }

      $this->ativo = 1;

      $sql = "SELECT COUNT(0) FROM pmieducar.cliente_tipo_cliente WHERE ref_cod_cliente = {$this->cod_cliente}
        AND ref_cod_biblioteca = {$this->ref_cod_biblioteca} AND ativo = 1";

      $db = new clsBanco();
      $possui_biblio = $db->CampoUnico($sql);
      if ($possui_biblio == 0) {
        $obj_cliente_tipo_cliente = new clsPmieducarClienteTipoCliente($this->ref_cod_cliente_tipo,
          $this->cod_cliente, NULL, NULL, $this->pessoa_logada, NULL, NULL, $this->ref_cod_biblioteca);

        if (!$obj_cliente_tipo_cliente->cadastra()) {
          $this->mensagem = "Não cadastrou";

          return FALSE;
                }
        else {
            $this->simpleRedirect('educar_cliente_lst.php');
                }
      }
            else {
        //$this->Editar();
        $this->mensagem = "O cliente já está cadastrado!<br>";
      }
    }
  }



  /**
   * Sobrescrita do método clsCadastro::Editar.
   *
   * Verifica:
   * - Se usuário tem permissão de edição
   * - Se usuário existe na biblioteca atual
   *   - Se existir, troca pela biblioteca escolhida na interface
   *   - Senão, cadastra como cliente da biblioteca
   */
  public function Editar() {
    $senha = md5($this->senha . 'asnk@#*&(23');
    $obj_permissoes = new clsPermissoes();
    $obj_permissoes->permissao_cadastra(603, $this->pessoa_logada, 11, 'educar_cliente_lst.php');

    $obj = new clsPmieducarCliente($this->cod_cliente, $this->pessoa_logada, $this->pessoa_logada,
      $this->ref_idpes, $this->login, $senha, $this->data_cadastro, $this->data_exclusao, $this->ativo, $this->observacoes);

    $detalheAntigo = $obj->detalhe();
    $editou = $obj->edita();

    if ($editou) {
      $detalheAtual = $obj->detalhe();
      $auditoria = new clsModulesAuditoriaGeral("cliente", $this->pessoa_logada, $this->cod_cliente);
      $auditoria->alteracao($detalheAntigo, $detalheAtual);
      // Cria objeto clsPemieducarClienteTipoCliente configurando atributos usados nas queries
      $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
        $this->ref_cod_cliente_tipo, $this->cod_cliente, NULL, NULL,
        $this->pessoa_logada, $this->pessoa_logada, 1, $this->ref_cod_biblioteca);

      // clsPmieducarClienteTipoCliente::trocaTipoBiblioteca recebe o valor antigo para usar
      // na cláusula WHERE
      if ($obj_cliente_tipo->existeClienteBiblioteca($_POST['ref_cod_biblioteca_atual'])) {
        if ($obj_cliente_tipo->trocaTipoBiblioteca($_POST['ref_cod_biblioteca_atual'])) {
          $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
          $this->simpleRedirect('educar_cliente_lst.php');
        }
      }
      else {
        $obj_cliente_tipo = new clsPmieducarClienteTipoCliente(
          $this->ref_cod_cliente_tipo, $this->cod_cliente, NULL, NULL,
          $this->pessoa_logada, NULL, 1, $this->ref_cod_biblioteca);

        if ($obj_cliente_tipo->cadastra()) {
          $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
          $this->simpleRedirect('educar_cliente_lst.php');
        }
      }
    }

    $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';
        die();
    }



    function Excluir()
    {


        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir( 603, $this->pessoa_logada, 11,  "educar_cliente_lst.php" );

        $obj = new clsPmieducarCliente( $this->cod_cliente, $this->pessoa_logada, null, $this->ref_idpes, null, null, null, null, 0 );
        $detalhe = $obj->detalhe();
    $excluiu = $obj->excluir();
        if( $excluiu )
        {

      $auditoria = new clsModulesAuditoriaGeral("cliente", $this->pessoa_logada, $this->cod_cliente);
      $auditoria->exclusao($detalhe);
            $this->mensagem .= "Exclus&atilde;o efetuada com sucesso.<br>";
            $this->simpleRedirect('educar_cliente_lst.php');
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
<script>
document.getElementById('ref_cod_biblioteca').onchange = function()
{
    ajaxBiblioteca();
};

if(document.getElementById('ref_cod_biblioteca').value != '')
{
    ajaxBiblioteca();
}

function ajaxBiblioteca()
{
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;
    var xml_biblioteca = new ajax( requisitaSenha );
    xml_biblioteca.envia( "educar_biblioteca_xml.php?bib="+campoBiblioteca );
}

setVisibility('tr_login_', false);
setVisibility('tr_senha_', false);

function requisitaSenha(xml)
{
    var DOM_array = xml.getElementsByTagName( "biblioteca" );
    var campoBiblioteca = document.getElementById('ref_cod_biblioteca').value;

    if (campoBiblioteca == '')
    {
        setVisibility('tr_login_', false);
        setVisibility('tr_senha_', false);
    }
    else
    {
        for( var i = 0; i < DOM_array.length; i++ )
        {
            if (DOM_array[i].getAttribute("requisita_senha") == 0)
            {
                setVisibility('tr_login_', false);
                setVisibility('tr_senha_', false);
                document.getElementById('login_').setAttribute('class', 'geral');
                document.getElementById('senha_').setAttribute('class', 'geral');
                document.getElementById('requisita_senha').value = '0';
            }
            else if (DOM_array[i].getAttribute("requisita_senha") == 1)
            {
                setVisibility('tr_login_', true);
                setVisibility('tr_senha_', true);
                document.getElementById('login_').setAttribute('class', 'obrigatorio');
                document.getElementById('senha_').setAttribute('class', 'obrigatorio');
                document.getElementById('requisita_senha').value = '1';
            }
        }
    }
}
</script>
