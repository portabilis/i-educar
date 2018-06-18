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
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/pmicontrolesis/geral.inc.php');

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Tipo Acontecimento");
        $this->processoAp = '604';
    }
}

class indice extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_tipo_acontecimento;
    public $ref_cod_funcionario_cad;
    public $ref_cod_funcionario_exc;
    public $nm_tipo;
    public $caminho;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Tipo Acontecimento - Detalhe';

        $this->cod_tipo_acontecimento=$_GET['cod_tipo_acontecimento'];

        $tmp_obj = new clsPmicontrolesisTipoAcontecimento($this->cod_tipo_acontecimento);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            header('location: controlesis_tipo_acontecimento_lst.php');
            die();
        }

        if ($registro['cod_tipo_acontecimento']) {
            $this->addDetalhe([ 'Tipo Acontecimento', "{$registro['cod_tipo_acontecimento']}"]);
        }
        if ($registro['ref_cod_funcionario_cad']) {
            $this->addDetalhe([ 'Funcionario Cad', "{$registro['ref_cod_funcionario_cad']}"]);
        }
        if ($registro['ref_cod_funcionario_exc']) {
            $this->addDetalhe([ 'Funcionario Exc', "{$registro['ref_cod_funcionario_exc']}"]);
        }
        if ($registro['nm_tipo']) {
            $this->addDetalhe([ 'Nome Tipo', "{$registro['nm_tipo']}"]);
        }
        if ($registro['caminho']) {
            $this->addDetalhe([ 'Caminho', "{$registro['caminho']}"]);
        }

        $this->url_novo = 'controlesis_tipo_acontecimento_cad.php';
        $this->url_editar = "controlesis_tipo_acontecimento_cad.php?cod_tipo_acontecimento={$registro['cod_tipo_acontecimento']}";
        $this->url_cancelar = 'controlesis_tipo_acontecimento_lst.php';
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
