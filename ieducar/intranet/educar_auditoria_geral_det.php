<?php

require_once 'include/clsBase.inc.php';
require_once 'include/clsDetalhe.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'Portabilis/Date/Utils.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Auditoria geral');
        $this->processoAp = 9998851;
        $this->addEstilo('localizacaoSistema');
    }
}
class indice extends clsDetalhe
{
    public $titulo;

    public $id;

    public function Gerar()
    {
        $this->titulo = 'Auditoria geral - Detalhe';
        $this->addBanner(
            'imagens/nvp_top_intranet.jpg',
            'imagens/nvp_vert_intranet.jpg',
            'Intranet'
        );

        $this->id = $_GET['id'];

        $objAuditoriaGeral = new clsModulesAuditoriaGeral();
        $objAuditoriaGeral->id = $this->id;
        $registro = array_shift($objAuditoriaGeral->lista());
        $this->redirectIf(!$registro, 'educar_auditoria_geral_lst.php');

        $usuario = new clsFuncionario($registro['usuario_id']);
        $usuario = $usuario->detalhe();

        foreach ($registro as $key => $value) {
            $this->$key = $value;
        }

        $this->addDetalhe([
            'ID da auditoria',
            $registro["id"]
        ]);

        $this->addDetalhe([
            'Código do registro',
            $registro["codigo"]
        ]);

        $operacoes = [
            1 => 'Novo',
            2 => 'Edição',
            3 => 'Exclusão'
        ];
        $this->addDetalhe([
            'Operação',
            $operacoes[$registro["operacao"]]
        ]);

        $this->addDetalhe([
            'Rotina',
            $registro['rotina']
        ]);

        $this->addDetalhe([
            'Data Hora',
            Portabilis_Date_Utils::pgSQLToBr($registro['data_hora'])
        ]);

        $this->addDetalhe([
            'Valor Antigo',
            $this->transformaJsonEmTabela($registro['valor_antigo'])
        ]);

        $this->addDetalhe([
            'Valor Novo',
            $this->transformaJsonEmTabela($registro['valor_novo'])
        ]);

        $this->addDetalhe([
            '<b>Dados do usuário</b>'
        ]);

        $this->addDetalhe([
            'Código',
            $registro['usuario_id']
        ]);

        $this->addDetalhe([
            'Matrícula',
            $usuario['matricula']
        ]);

        $pessoa = new clsPessoaFisica($registro['usuario_id']);
        $pessoa = $pessoa->detalhe();

        $this->addDetalhe([
            'Nome',
            $pessoa['nome']
        ]);

        $this->url_cancelar = "educar_auditoria_geral_lst.php";
        $this->largura = "100%";

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME']."/intranet" => "Início",
            "educar_configuracoes_index.php" => "Configurações",
            "" => "Auditoria Geral"
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
            if (Portabilis_Date_Utils::isDateValid($value)) {
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

}

// Instancia objeto de página
$pagina = new clsIndexBase();

// Instancia objeto de conteúdo
$miolo = new indice();

// Atribui o conteúdo à  página
$pagina->addForm($miolo);

// Gera o código HTML
$pagina->MakeAll();
