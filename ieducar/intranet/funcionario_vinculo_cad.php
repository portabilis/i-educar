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
    }
}

class indice extends clsCadastro
{
    public $nm_vinculo;
    public $cod_vinculo;
    public $abreviatura;

    protected $db;

    public function __construct()
    {
        parent::__construct();

        $this->db = new clsBanco();
    }

    public function Inicializar()
    {
        $retorno = 'Novo';

        if ($_GET['cod_funcionario_vinculo']) {
            $this->cod_vinculo = $_GET['cod_funcionario_vinculo'];
            $this->db->Consulta("SELECT nm_vinculo, abreviatura FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo = $this->cod_vinculo");

            if ($this->db->ProximoRegistro()) {
                $tupla = $this->db->Tupla();
                $this->nm_vinculo = $tupla[0];
                $this->abreviatura = $tupla[1];
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
            '' => "{$nomeMenu} v&iacute;nculo"
        ]);

        $this->enviaLocalizacao($localizacao->montar());

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto('cod_vinculo', $this->cod_vinculo);
        $this->campoTexto('nm_vinculo', 'Nome', $this->nm_vinculo, 30, 250, true);
        $this->campoTexto('abreviatura', 'Abreviatura', $this->abreviatura, 5, 4, true);
    }

    public function Novo()
    {
        $db = new clsBanco();
        if ($this->duplicado($this->nm_vinculo, $this->abreviatura)) {
            $this->mensagem = 'Já existe um registro com este nome ou abreviatura.';

            return false;
        }
            $nm_vinculo = $db->escapeString($this->nm_vinculo);
            $abreviatura = $db->escapeString($this->abreviatura);

        $this->db->Consulta("INSERT INTO portal.funcionario_vinculo ( nm_vinculo, abreviatura ) VALUES ( '$nm_vinculo', '$abreviatura' )");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Editar()
    {
        $db = new clsBanco();
        if ($this->duplicado($this->nm_vinculo, $this->abreviatura, $this->cod_vinculo)) {
            $this->mensagem = 'Já existe um registro com este nome ou abreviatura.';

            return false;
        }
        $nm_vinculo = $db->escapeString($this->nm_vinculo);
        $abreviatura = $db->escapeString($this->abreviatura);

        $this->db->Consulta("UPDATE portal.funcionario_vinculo SET nm_vinculo = '{$nm_vinculo}', abreviatura = '{$abreviatura}' WHERE cod_funcionario_vinculo = $this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Excluir()
    {
        $count = (int)$this->db->CampoUnico("SELECT COUNT(*) FROM pmieducar.servidor_alocacao WHERE ref_cod_funcionario_vinculo = $this->cod_vinculo;");
        $count += (int)$this->db->CampoUnico("SELECT COUNT(*) FROM portal.funcionario WHERE ref_cod_funcionario_vinculo = $this->cod_vinculo;");

        if ($count > 0) {
            $this->mensagem = 'Não é possível remover. Já existem funcionários cadastrados e alocados com este vínculo.';

            return false;
        }

        $this->db->Consulta("DELETE FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo=$this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    protected function duplicado($nmVinculo, $abreviatura, $id = null)
    {
        $db = new clsBanco();
        $nm_Vinculo = $db->escapeString($nmVinculo);
        $abrevia = $db->escapeString($abreviatura);
        $sql = "SELECT COUNT(*) FROM portal.funcionario_vinculo WHERE TRUE AND nm_vinculo ILIKE '{$nm_Vinculo}' OR abreviatura ILIKE '{$abrevia}'";

        if (!is_null($id)) {
            $sql .= " AND cod_funcionario_vinculo <> {$id}";
        }

        $count = (int)$this->db->CampoUnico($sql);

        return $count > 0;
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();
