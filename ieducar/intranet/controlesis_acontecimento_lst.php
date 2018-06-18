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
require_once('include/pmicontrolesis/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Acontecimento");
        $this->processoAp = '605';
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

    public $cod_acontecimento;
    public $ref_cod_tipo_acontecimento;
    public $ref_cod_funcionario_cad;
    public $ref_cod_funcionario_exc;
    public $titulo2;
    public $descricao;
    public $dt_inicio;
    public $dt_fim;
    public $hr_inicio;
    public $hr_fim;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo2 = 'Acontecimento - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $this->addCabecalhos([
            'Tipo Acontecimento',
            'Titulo',
            'Descric&atilde;o',
            'Data Inicio',
            'Data Fim',
            'Hora Inicio',
            'Hora Fim'
        ]);

        // Filtros de Foreign Keys
        $opcoes = [ '' => 'Selecione' ];
        if (class_exists('clsPmicontrolesisTipoAcontecimento')) {
            $objTemp = new clsPmicontrolesisTipoAcontecimento();
            $lista = $objTemp->lista();
            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes["{$registro['cod_tipo_acontecimento']}"] = "{$registro['nm_tipo']}";
                }
            }
        } else {
            echo "<!--\nErro\nClasse clsPmicontrolesisTipoAcontecimento nao encontrada\n-->";
            $opcoes = [ '' => 'Erro na geracao' ];
        }
        $this->campoLista('ref_cod_tipo_acontecimento', 'Tipo Acontecimento', $opcoes, $this->ref_cod_tipo_acontecimento);

        // outros Filtros

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_acontecimento = new clsPmicontrolesisAcontecimento(null, null, null, null, null, null, null, null, null, null, null, null, 1);
        $obj_acontecimento->setOrderby('dt_inicio DESC');
        $obj_acontecimento->setLimite($this->limite, $this->offset);

        $lista = $obj_acontecimento->lista(
            null,
            $this->ref_cod_tipo_acontecimento,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1
        );

        $total = $obj_acontecimento->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // muda os campos data
                if ($registro['dt_inicio']) {
                    $registro['dt_inicio'] =  date('d/m/Y', strtotime(substr($registro['dt_inicio'], 0, 19)));
                }
                if ($registro['dt_fim']) {
                    $registro['dt_fim'] = date('d/m/Y', strtotime(substr($registro['dt_fim'], 0, 19)));
                }
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br'] = date('d/m/Y H:i', $registro['data_cadastro_time']);

                $registro['data_exclusao_time'] = strtotime(substr($registro['data_exclusao'], 0, 16));
                $registro['data_exclusao_br'] = date('d/m/Y H:i', $registro['data_exclusao_time']);

                // pega detalhes de foreign_keys
                if (class_exists('clsPmicontrolesisTipoAcontecimento')) {
                    $obj_ref_cod_tipo_acontecimento = new clsPmicontrolesisTipoAcontecimento($registro['ref_cod_tipo_acontecimento']);
                    $det_ref_cod_tipo_acontecimento = $obj_ref_cod_tipo_acontecimento->detalhe();
                    $registro['ref_cod_tipo_acontecimento'] = $det_ref_cod_tipo_acontecimento['nm_tipo'];
                } else {
                    $registro['ref_cod_tipo_acontecimento'] = 'Erro na geracao';
                    echo "<!--\nErro\nClasse nao existente: clsPmicontrolesisTipoAcontecimento\n-->";
                }

                $registro['hr_fim'] = substr($registro['hr_fim'], 0, 5);
                $registro['hr_inicio'] = substr($registro['hr_inicio'], 0, 5);
                $registro['descricao'] = truncate($registro['descricao'], 100);
                $this->addLinhas([

                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['ref_cod_tipo_acontecimento']}</a>",

                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['titulo']}</a>",
                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['descricao']}</a>",
                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['dt_inicio']}</a>",
                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['dt_fim']}</a>",
                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['hr_inicio']}</a>",
                    "<a href=\"controlesis_acontecimento_det.php?cod_acontecimento={$registro['cod_acontecimento']}\">{$registro['hr_fim']}</a>"
                ]);
            }
        }
        $this->addPaginador2('controlesis_acontecimento_lst.php', $total, $_GET, $this->nome, $this->limite);
        $this->acao = 'go("controlesis_acontecimento_cad.php")';
        $this->nome_acao = 'Novo';
        $this->largura = '100%';
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
