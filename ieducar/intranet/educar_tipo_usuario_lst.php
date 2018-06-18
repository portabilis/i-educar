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
require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmieducar/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} i-Educar - Tipo Usuario");
        $this->processoAp = '554';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsListagem
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

    public $cod_tipo_usuario;
    public $ref_funcionario_cad;
    public $ref_funcionario_exc;
    public $nm_tipo;
    public $descricao;
    public $nivel;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Tipo Usuario - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'C&oacute;digo Tipo Usu&aacute;rio',
            'Tipo Usu&aacute;rio',
            'Descri&ccedil;&atilde;o',
            'N&iacute;vel',
        ]);

        //niveis
        $array_nivel = [ '-1' =>'Selecione', '8' => 'Biblioteca', '4' => 'Escola', '2' => 'Institucional', '1' => 'Poli-institucional' ];

        if (!isset($this->nivel)) {
            $this->nivel = -1;
        }

        // outros Filtros
        $this->campoTexto('nm_tipo', 'Nome Tipo', $this->nm_tipo, 30, 255, false);
        $this->campoTexto('descricao', 'Descri&ccedil;&atilde;o', $this->descricao, 30, 255, false);
        $this->campoLista('nivel', 'N&iacute;vel', $array_nivel, $this->nivel, '', false, '', '', false, false);

        $this->nivel = $this->nivel == -1 ? '' : $this->nivel;

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_tipo_usuario = new clsPmieducarTipoUsuario();
        $obj_tipo_usuario->setOrderby('nm_tipo ASC');
        $obj_tipo_usuario->setLimite($this->limite, $this->offset);

        $lista = $obj_tipo_usuario->lista(
            null,
            null,
            null,
            $this->nm_tipo,
            $this->descricao,
            $this->nivel,
            null,
            null,
            1
        );

        $total = $obj_tipo_usuario->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {

                // pega detalhes de foreign_keys

                $this->addLinhas([
                    "<a href=\"educar_tipo_usuario_det.php?cod_tipo_usuario={$registro['cod_tipo_usuario']}\">{$registro['cod_tipo_usuario']}</a>",
                    "<a href=\"educar_tipo_usuario_det.php?cod_tipo_usuario={$registro['cod_tipo_usuario']}\">{$registro['nm_tipo']}</a>",
                    "<a href=\"educar_tipo_usuario_det.php?cod_tipo_usuario={$registro['cod_tipo_usuario']}\">{$registro['descricao']}</a>",
                    "<a href=\"educar_tipo_usuario_det.php?cod_tipo_usuario={$registro['cod_tipo_usuario']}\">{$array_nivel[$registro['nivel']]}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_tipo_usuario_lst.php', $total, $_GET, $this->nome, $this->limite);

        //** Verificacao de permissao para cadastro
        $obj_permissao = new clsPermissoes();

        if ($obj_permissao->permissao_cadastra(554, $this->pessoa_logada, 7, null, true)) {
            $this->acao = 'go("educar_tipo_usuario_cad.php")';
            $this->nome_acao = 'Novo';
        }
        //**

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
             $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
             'educar_configuracoes_index.php'    => 'Configurações',
             ''                                  => 'Listagem de tipo de usu&aacute;rio'
        ]);
        $this->enviaLocalizacao($localizacao->montar());
    }
}
// cria uma extensao da classe base
$pagina = new clsIndexBase();
// cria o conteudo
$miolo = new indice();
// adiciona o conteudo na clsBase
$pagina->addForm($miolo);
// gera o html
$pagina->MakeAll();
