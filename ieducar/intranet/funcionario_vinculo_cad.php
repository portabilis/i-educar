<?php

return new class extends clsCadastro
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
            $this->db->Consulta(consulta: "SELECT nm_vinculo, abreviatura FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo = $this->cod_vinculo");

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

        $this->breadcrumb(currentPage: "{$nomeMenu} vínculo");

        return $retorno;
    }

    public function Gerar()
    {
        $this->campoOculto(nome: 'cod_vinculo', valor: $this->cod_vinculo);
        $this->campoTexto(nome: 'nm_vinculo', campo: 'Nome', valor: $this->nm_vinculo, tamanhovisivel: 30, tamanhomaximo: 250, obrigatorio: true);
        $this->campoTexto(nome: 'abreviatura', campo: 'Abreviatura', valor: $this->abreviatura, tamanhovisivel: 5, tamanhomaximo: 4, obrigatorio: true);
    }

    public function Novo()
    {
        $db = new clsBanco();
        if ($this->duplicado(nmVinculo: $this->nm_vinculo, abreviatura: $this->abreviatura)) {
            $this->mensagem = 'Já existe um registro com este nome ou abreviatura.';

            return false;
        }
        $nm_vinculo = $db->escapeString(string: $this->nm_vinculo);
        $abreviatura = $db->escapeString(string: $this->abreviatura);

        $this->db->Consulta(consulta: "INSERT INTO portal.funcionario_vinculo ( nm_vinculo, abreviatura ) VALUES ( '$nm_vinculo', '$abreviatura' )");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Editar()
    {
        $db = new clsBanco();
        if ($this->duplicado(nmVinculo: $this->nm_vinculo, abreviatura: $this->abreviatura, id: $this->cod_vinculo)) {
            $this->mensagem = 'Já existe um registro com este nome ou abreviatura.';

            return false;
        }
        $nm_vinculo = $db->escapeString(string: $this->nm_vinculo);
        $abreviatura = $db->escapeString(string: $this->abreviatura);

        $this->db->Consulta(consulta: "UPDATE portal.funcionario_vinculo SET nm_vinculo = '{$nm_vinculo}', abreviatura = '{$abreviatura}' WHERE cod_funcionario_vinculo = $this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    public function Excluir()
    {
        $count = (int) $this->db->CampoUnico(consulta: "SELECT COUNT(*) FROM pmieducar.servidor_alocacao WHERE ref_cod_funcionario_vinculo = $this->cod_vinculo;");
        $count += (int) $this->db->CampoUnico(consulta: "SELECT COUNT(*) FROM portal.funcionario WHERE ref_cod_funcionario_vinculo = $this->cod_vinculo;");

        if ($count > 0) {
            $this->mensagem = 'Não é possível remover. Já existem funcionários cadastrados e alocados com este vínculo.';

            return false;
        }

        $this->db->Consulta(consulta: "DELETE FROM portal.funcionario_vinculo WHERE cod_funcionario_vinculo=$this->cod_vinculo");
        echo '<script>document.location=\'funcionario_vinculo_lst.php\';</script>';

        return true;
    }

    protected function duplicado($nmVinculo, $abreviatura, $id = null)
    {
        $db = new clsBanco();
        $nm_Vinculo = $db->escapeString(string: $nmVinculo);
        $abrevia = $db->escapeString(string: $abreviatura);
        $sql = "SELECT COUNT(*) FROM portal.funcionario_vinculo WHERE TRUE AND nm_vinculo ILIKE '{$nm_Vinculo}' OR abreviatura ILIKE '{$abrevia}'";

        if (!is_null(value: $id)) {
            $sql .= " AND cod_funcionario_vinculo <> {$id}";
        }

        $count = (int) $this->db->CampoUnico(consulta: $sql);

        return $count > 0;
    }

    public function Formular()
    {
        $this->title = 'Vínculo Funcionários!';
        $this->processoAp = '190';
    }
};
