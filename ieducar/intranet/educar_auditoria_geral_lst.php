<?php

use iEducar\Modules\AuditoriaGeral\Model\Operacoes;
use iEducar\Modules\AuditoriaGeral\Model\JsonToHtmlTable;

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

    public function Gerar()
    {
        $this->titulo = 'Auditoria geral';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null: $val;
        }

        $this->campoTexto('usuario', 'Matrícula usuário', $this->usuario, 50, 50);

        $options = [
            'label' => 'Rotinas',
            'required'   => false
        ];
        $helperOptions = [
            'objectName' => 'rotinas_auditoria',
            'hiddenInputOptions' => [
                'options' => ['value' => $this->rotinas_auditoria]
            ]
        ];
        $this->inputsHelper()->simpleSearchRotinasAuditoria(null, $options, $helperOptions);

        $operacoes = Operacoes::getDescriptiveValues();
        $operacoes = array_replace([null => 'Todas'], $operacoes);

        $this->campoTexto('codigo', 'Código do registro', $this->codigo, 10, 50);
        $this->campoLista('operacao', 'Operação', $operacoes, $this->operacao, null, null, null, null, null, false);
        $this->inputsHelper()->dynamic(['dataInicial','dataFinal']);
        $this->campoHora('hora_inicial', 'Hora Inicial', $this->hora_inicial, false);
        $this->campoHora('hora_final', 'Hora Final', $this->hora_final, false);

        $obj_usuario = new clsPmieducarUsuario($this->pessoa_logada);
        $detalhe = $obj_usuario->detalhe();

        // Paginador
        $this->limite = 10;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $this->addCabecalhos([ 'Matrícula', 'Rotina', 'Operação', 'Valor antigo', 'Valor novo', 'Data']);

        // FIXME #parameters
        $auditoria = new clsModulesAuditoriaGeral(null, null);
        $auditoria->setOrderby('data_hora DESC');
        $auditoria->setLimite($this->limite, $this->offset);
        $auditoriaLst = $auditoria->lista(
            $this->rotinas_auditoria,
            $this->usuario,
            Portabilis_Date_Utils::brToPgSQL($this->data_inicial),
            Portabilis_Date_Utils::brToPgSQL($this->data_final),
            $this->hora_inicial,
            $this->hora_final,
            $this->operacao,
            $this->codigo
        );
        $total = $auditoria->_total;

        foreach ($auditoriaLst as $a) {
            $valorAntigo = JsonToHtmlTable::transformJsonToHtmlTable($a['valor_antigo']);
            $valorNovo = JsonToHtmlTable::transformJsonToHtmlTable($a['valor_novo']);

            $usuario = new clsFuncionario($a['usuario_id']);
            $usuario = $usuario->detalhe();

            $operacao = $operacoes[$a['operacao']];

            $dataAuditoria = Portabilis_Date_Utils::pgSQLToBr($a['data_hora']);

            $this->addLinhas([
                $this->retornaLinkDaAuditoria($a['id'], $usuario['matricula']),
                $this->retornaLinkDaAuditoria($a['id'], ucwords($a['rotina'])),
                $this->retornaLinkDaAuditoria($a['id'], $operacao),
                $valorAntigo,
                $valorNovo,
                $this->retornaLinkDaAuditoria($a['id'], $dataAuditoria)
            ]);
        }

        $this->addPaginador2('educar_auditoria_geral_lst.php', $total, $_GET, $this->nome, $this->limite);

        $this->largura = '100%';

        $this->breadcrumb('Auditoria geral',['educar_configuracoes_index.php' => 'Configurações']);
    }

    public function retornaLinkDaAuditoria($idAuditoria, $campo)
    {
        return "<a href='educar_auditoria_geral_det.php?id={$idAuditoria}'>{$campo}</a>";
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
