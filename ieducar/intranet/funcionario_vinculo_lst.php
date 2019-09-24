<?php

require_once('include/clsBase.inc.php');
require_once('include/clsListagem.inc.php');
require_once('include/clsBanco.inc.php');

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Vínculo Funcionários!");
        $this->processoAp = '190';
    }
}

class indice extends clsListagem
{
    public function Gerar()
    {
        $this->titulo = 'Vínculos';

        $nome_ = @$_GET['nome_'];

        $this->addCabecalhos(['Nome']);

        $this->campoTexto('nome_', 'Nome', $nome_, '50', '255', true);

        $db = new clsBanco();
        $sql  = 'SELECT cod_funcionario_vinculo, nm_vinculo FROM portal.funcionario_vinculo';
        $where = '';
        $where_and = '';

        if (!empty($nome_)) {
            $where .= $where_and." nm_vinculo LIKE '%$nome_%' ";
            $where_and = ' AND';
        }

        if ($where) {
            $where = " WHERE $where";
        }

        $sql .= $where.' ORDER BY nm_vinculo';

        $db->Consulta("SELECT count(*) FROM portal.funcionario_vinculo $where");
        $db->ProximoRegistro();

        list($total) = $db->Tupla();

        // Paginador
        $limite = 10;
        $iniciolimit = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$limite-$limite: 0;
        $sql .= " LIMIT $iniciolimit,$limite";
        $db->Consulta($sql);

        while ($db->ProximoRegistro()) {
            list($cod_func_vinculo, $nome) = $db->Tupla();
            $this->addLinhas([ "<img src='imagens/noticia.jpg' border=0> <a href='funcionario_vinculo_det.php?cod_func=$cod_func_vinculo'>$nome</a>"]);
        }

        $this->largura = '100%';

        // Paginador
        $this->addPaginador2('funcionario_vinculo_lst.php', $total, $_GET, $this->nome, $limite);
        $this->acao = 'go("funcionario_vinculo_cad.php")';
        $this->nome_acao = 'Novo';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            '' => 'Listagem de v&iacute;nculos'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
