<?php

use App\Models\LegacyIndividual;

require_once 'include/clsBase.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'CoreExt/View/Helper/UrlHelper.php';

class clsIndexBase extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Servidor');
        $this->processoAp = 635;
    }
}

class indice extends clsListagem
{
    public $limite;
    public $offset;
    public $cod_servidor;
    public $ref_idesco;
    public $ref_cod_funcao;
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nome;
    public $matricula_servidor;
    public $ref_cod_escola;
    public $ref_cod_instituicao;
    public $servidor_sem_alocacao;
    public $ano_letivo;

    public function Gerar()
    {
        $this->titulo = 'Servidor - Listagem';

        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        $this->addCabecalhos([
            'Nome do Servidor',
            'Matrícula',
            'CPF',
            'Instituição'
        ]);

        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'anoLetivo']);

        if ($this->cod_servidor) {
            $objTemp = new clsFuncionario($this->cod_servidor);
            $detalhe = $objTemp->detalhe();

            $opcoes[$detalhe['idpes']] = $detalhe['nome'];
            $opcoes[$detalhe['ref_cod_pessoa_fj']] = $detalhe['matricula_servidor'];
        }

        $parametros = new clsParametrosPesquisas();
        $parametros->setSubmit(0);
        $this->campoTexto('nome', 'Nome do servidor', $this->nome, 50, 255, false);
        $this->campoTexto('matricula_servidor', 'Matrícula', $this->matricula_servidor, 50, 255, false);
        $this->inputsHelper()->dynamic('escolaridade', ['required' => false]);
        $this->campoCheck('servidor_sem_alocacao', 'Incluir servidores sem alocação', isset($_GET['servidor_sem_alocacao']));

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET['pagina_' . $this->nome])
            ? $_GET['pagina_' . $this->nome] * $this->limite - $this->limite
            : 0;

        if (!$this->ref_idesco && $_GET['idesco']) {
            $this->ref_idesco = $_GET['idesco'];
        }

        $obj_servidor = new clsPmieducarServidor();
        $obj_servidor->setOrderby('carga_horaria ASC');
        $obj_servidor->setLimite($this->limite, $this->offset);

        $lista = $obj_servidor->lista(
            $this->cod_servidor,
            null,
            $this->ref_idesco,
            $this->carga_horaria,
            null,
            null,
            null,
            null,
            1,
            null,
            null,
            null,
            null,
            $this->nome,
            null,
            null,
            true,
            true,
            true,
            null,
            null,
            $this->ref_cod_escola,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            isset($_GET['servidor_sem_alocacao']),
            $this->ano_letivo,
            $this->matricula_servidor
        );
        $total = $obj_servidor->_total;

        // UrlHelper
        $url = CoreExt_View_Helper_UrlHelper::getInstance();

        // Monta a lista
        if (is_array($lista) && count($lista)) {
            $obj_ref_cod_instituicao = new clsPmieducarInstituicao($lista[0]['ref_cod_instituicao']);
            $det_ref_cod_instituicao = $obj_ref_cod_instituicao->detalhe();

            $ids = array_map(function ($individual) {
                return $individual['cod_servidor'];
            }, $lista);

            $cpfs = LegacyIndividual::query()->whereKey($ids)->pluck('cpf', 'idpes')->toArray();

            foreach ($lista as $registro) {
                $registro['ref_cod_instituicao'] = $det_ref_cod_instituicao['nm_instituicao'];
                $registro['cpf'] = int2CPF($cpfs[$registro['cod_servidor']]);

                $path = 'educar_servidor_det.php';
                $options = [
                    'query' => [
                        'cod_servidor' => $registro['cod_servidor'],
                        'ref_cod_instituicao' => $det_ref_cod_instituicao['cod_instituicao'],
                    ]
                ];

                $this->addLinhas([
                    $url->l($registro['nome'], $path, $options),
                    $url->l($registro['matricula_servidor'], $path, $options),
                    $url->l($registro['cpf'], $path, $options),
                    $url->l($registro['ref_cod_instituicao'], $path, $options),
                ]);
            }
        }

        $this->addPaginador2('educar_servidor_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_servidor_cad.php")';
            $this->nome_acao = 'Novo';
        }

        $this->largura = '100%';

        $this->breadcrumb('Servidores', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }
}

$pagina = new clsIndexBase();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
