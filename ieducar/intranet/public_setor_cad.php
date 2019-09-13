<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsCadastro.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/public/geral.inc.php';
require_once 'include/public/clsPublicSetorBai.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' Setor');
        $this->processoAp = 759;
    }
}

class indice extends clsCadastro
{
    public $idsetorbai;
    public $nome;

    public function Inicializar()
    {
        $retorno = 'Novo';
        $this->idsetorbai = $_GET['idsetorbai'];

        if (is_numeric($this->idsetorbai)) {
            $obj_setor_bai = new clsPublicSetorBai($this->idsetorbai);
            $det_setor_bai = $obj_setor_bai->detalhe();

            if ($det_setor_bai) {
                $registro = $det_setor_bai;
            }

            if ($registro) {
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $retorno = 'Editar';
            }
        }

        $this->url_cancelar = $retorno == 'Editar'
            ? 'public_setor_det.php?idsetorbai=' . $registro['idsetorbai']
            : 'public_setor_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb("{$nomeMenu} setor", [
            url('intranet/educar_enderecamento_index.php') => 'Endereçamento',
        ]);

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('idsetorbai', $this->idsetorbai);

        $this->campoTexto('nome', 'Nome', $this->nome, 30, 255, true);
    }

    public function Novo()
    {
        $obj = new clsPublicSetorBai(null, $this->nome);

        $cadastrou = $obj->cadastra();
        if ($cadastrou) {
            $enderecamento = new clsPublicSetorBai($cadastrou);
            $enderecamento = $enderecamento->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Setor', $this->pessoa_logada, $cadastrou);
            $auditoria->inclusao($enderecamento);

            $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('public_setor_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $enderecamentoDetalhe = new clsPublicSetorBai($this->idsetorbai);
        $enderecamentoDetalhe->cadastrou = $this->idsetorbai;
        $enderecamentoDetalheAntes = $enderecamentoDetalhe->detalhe();

        $obj = new clsPublicSetorBai($this->idsetorbai, $this->nome);

        $editou = $obj->edita();
        if ($editou) {
            $enderecamentoDetalheDepois = $enderecamentoDetalhe->detalhe();
            $auditoria = new clsModulesAuditoriaGeral('Endereçamento de Setor', $this->pessoa_logada, $this->idsetorbai);
            $auditoria->alteracao($enderecamentoDetalheAntes, $enderecamentoDetalheDepois);

            $this->mensagem = 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('public_setor_lst.php');
        }

        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj = new clsPublicSetorBai($this->idsetorbai);
        $excluiu = $obj->excluir();

        if ($excluiu) {
            $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
            $this->simpleRedirect('public_setor_lst.php');
        }

        $this->mensagem = 'Exclusão não realizada.<br>';

        return false;
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();

$pagina->addForm($miolo);
$pagina->MakeAll();
