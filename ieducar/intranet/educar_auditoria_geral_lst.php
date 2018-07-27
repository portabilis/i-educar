<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'include/modules/clsModulesAuditoriaGeral.inc.php';
require_once 'Portabilis/Date/Utils.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Auditoria geral");
        $this->processoAp = '9998851';
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

    public $rotina;

    public function Gerar()
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        session_write_close();

        $this->titulo = 'Auditoria geral';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->campoTexto('usuario', 'Matrícula usuário', $this->usuario, 35, 50);
        $this->campoTexto('rotina', 'Rotina', $this->rotina, 35, 50);
        $this->inputsHelper()->dynamic(['dataInicial','dataFinal']);

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $detalhe = $obj_usuario->detalhe();

        // Paginador
        $this->limite = 10;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $this->addCabecalhos([ 'Matrícula', 'Rotina', 'Operação', 'Valor antigo', 'Valor novo', 'Data']);

        $auditoria = new clsModulesAuditoriaGeral();
        $auditoria->setOrderby('data_hora DESC');
        $auditoria->setLimite($this->limite, $this->offset);
        $auditoriaLst = $auditoria->lista(
            $this->rotina,
            $this->usuario,
            Portabilis_Date_Utils::brToPgSQL($this->data_inicial),
            Portabilis_Date_Utils::brToPgSQL($this->data_final)
        );
        $total = $auditoria->_total;

        foreach ($auditoriaLst as $a) {
            $valorAntigo = $this->transformaJsonEmTabela($a['valor_antigo']);
            $valorNovo = $this->transformaJsonEmTabela($a['valor_novo']);

            $usuario = new clsFuncionario($a['usuario_id']);
            $usuario = $usuario->detalhe();

            $operacao = $this->getNomeOperacao($a['operacao']);

            $dataAuditoria = Portabilis_Date_Utils::pgSQLToBr($a['data_hora']);

            $this->addLinhas([
                $usuario['matricula'],
                ucwords($a['rotina']),
                $operacao,
                $valorAntigo,
                $valorNovo,
                $dataAuditoria
            ]);
        }

        $this->addPaginador2('educar_auditoria_geral_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'Início',
            'educar_configuracoes_index.php' => 'Configurações',
            '' => 'Auditoria geral'
        ]);
        $this->enviaLocalizacao($localizacao->montar());
    }

    public function transformaJsonEmTabela($json)
    {
        $dataJson = json_decode($json);
        $tabela = '<table class=\'tablelistagem auditoria-tab\' width=\'100%\' border=\'0\' cellpadding=\'4\' cellspacing=\'1\'>
                        <tr>
                            <td class=\'formdktd\' valign=\'top\' align=\'left\' style=\'font-weight:bold;\'>Campo</td>
                            <td class=\'formdktd\' valign=\'top\' align=\'left\' style=\'font-weight:bold;\'>Valor</td>
                        <tr>';

        foreach ($dataJson as $key => $value) {
            if ($this->isDate($value)) {
                $value = date('d/m/Y', strtotime($value));
            }
            $tabela .= '<tr>';
            $tabela .= "<td class='formlttd'>$key</td>";
            $tabela .= "<td class='formlttd'>$value</td>";
            $tabela .= '</tr>';
        }

        $tabela .= '</table>';

        return $tabela;
    }

    public function isDate($value)
    {
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
            return true;
        }

        return false;
    }

    public function getNomeOperacao($operacap)
    {
        switch ($operacap) {
            case 1:
                $operacao = 'Novo';
                break;
            case 2:
                $operacao = 'Edição';
                break;
            case 3:
                $operacao = 'Exclusão';
                break;
        }

        return $operacao;
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
