<?php

require_once('include/clsBase.inc.php');
require_once('include/clsCadastro.inc.php');
require_once('include/clsBanco.inc.php');

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Vínculo Funcionários!");
        $this->processoAp = '190';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsCadastro
{
    public $nm_vinculo;
    public $cod_vinculo;

    public function Inicializar()
    {
        $retorno = 'Novo';

        if ($_GET['cod_funcionario_vinculo']) {
            $this->cod_vinculo = $_GET['cod_funcionario_vinculo'];
            $db =new clsBanco();
            $db->Consulta("SELECT nm_vinculo FROM funcionario_vinculo WHERE cod_funcionario_vinculo = $this->cod_vinculo");

            if ($db->ProximoRegistro()) {
                $tupla = $db->Tupla();
                $this->nm_vinculo = $tupla[0];
                $retorno = 'Editar';
                $this->fexcluir = true;
            }
        }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = 'funcionario_vinculo_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';
        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            ''=> "{$nomeMenu} v&iacute;nculo"
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_vinculo', $this->cod_vinculo);
        $this->campoTexto('nm_vinculo', 'Nome', $this->nm_vinculo, 30, 250, true);
    }

    public function Novo()
    {
        $db = new clsBanco();
        $db->Consulta("INSERT INTO funcionario_vinculo ( nm_vinculo ) VALUES ( '$this->nm_vinculo' )");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Editar()
    {
        $db = new clsBanco();
        $db->Consulta("UPDATE funcionario_vinculo SET nm_vinculo = '$this->nm_vinculo' WHERE cod_funcionario_vinculo=$this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Excluir()
    {
        $db = new clsBanco();
        $db->Consulta("DELETE FROM funcionario_vinculo WHERE cod_funcionario_vinculo=$this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
