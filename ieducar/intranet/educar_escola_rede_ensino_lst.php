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
        $this->SetTitulo("{$this->_instituicao} i-Educar - Escola Rede Ensino");
        $this->processoAp = '647';
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

    public $cod_escola_rede_ensino;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_rede;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Escola Rede Ensino - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Rede Ensino'
        ];

        $obj_permissoes = new clsPermissoes();
        $nivel_usuario = $obj_permissoes->nivel_acesso($this->pessoa_logada);
        if ($nivel_usuario == 1) {
            $lista_busca[] = 'Institui&ccedil;&atilde;o';
        }

        $this->addCabecalhos($lista_busca);

        // Filtros de Foreign Keys
        include('include/pmieducar/educar_campo_lista.php');

        $this->campoTexto('nm_rede', 'Rede Ensino', $this->nm_rede, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_escola_rede_ensino = new clsPmieducarEscolaRedeEnsino();
        $obj_escola_rede_ensino->setOrderby('nm_rede ASC');
        $obj_escola_rede_ensino->setLimite($this->limite, $this->offset);

        $lista = $obj_escola_rede_ensino->lista(
            null,
            null,
            null,
            $this->nm_rede,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        $total = $obj_escola_rede_ensino->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // pega detalhes de foreign_keys
                if (class_exists('clsPmieducarInstituicao')) {
                    $obj_ref_cod_instituicao = new clsPmieducarInstituicao($registro['ref_cod_instituicao']);
                    $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();
                    $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
                } else {
                    $registro['ref_cod_instituicao'] = 'Erro na geracao';
                    echo "<!--\nErro\nClasse nao existente: clsPmieducarInstituicao\n-->";
                }

                $lista_busca = [
                    "<a href=\"educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro['cod_escola_rede_ensino']}\">{$registro['nm_rede']}</a>"
                ];

                if ($nivel_usuario == 1) {
                    $lista_busca[] = "<a href=\"educar_escola_rede_ensino_det.php?cod_escola_rede_ensino={$registro['cod_escola_rede_ensino']}\">{$registro['ref_cod_instituicao']}</a>";
                }
                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_escola_rede_ensino_lst.php', $total, $_GET, $this->nome, $this->limite);

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(647, $this->pessoa_logada, 3)) {
            $this->acao = 'go("educar_escola_rede_ensino_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
             $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
             'educar_index.php'                  => 'Escola',
             ''        => 'Listagem de redes de ensino'
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
