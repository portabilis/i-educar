<?php

use App\Models\LegacyDiscipline;
use App\Models\LegacyGrade;
use App\Process;
use App\Services\CheckPostedDataService;
use App\Services\iDiarioService;
use App\Services\SchoolLevelsService;
use Illuminate\Support\Arr;

return new class extends clsCadastro {
    public $id;
    public $bncc;
    public $atividades;
    public $observacao;
    public $frequencia;

    public function Inicializar () {
        $this->titulo = 'Conteúdo Ministrado - Cadastro';

        $retorno = 'Novo';

        $this->id = $_GET['id'];

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7, 'educar_professores_conteudo_ministrado_lst.php');

        if (is_numeric($this->id)) {
            $tmp_obj = new clsModulesComponenteMinistrado(
                $this->id
            );
            $registro = $tmp_obj->detalhe();

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro['detalhes'] as $campo => $val) {
                    $this->$campo = $val;
                }
                $this->bncc = $registro['bncc']['ids'];

                $this->fexcluir = $obj_permissoes->permissao_excluir(58, $this->pessoa_logada, 7);
                $retorno = 'Editar';

                $this->titulo = 'Frequência - Edição';
            }
        }

        $this->nome_url_cancelar = 'Cancelar';
        $this->url_cancelar = ($retorno == 'Editar')
            ? sprintf('educar_professores_conteudo_ministrado_det.php?id=%d', $this->id)
            : 'educar_professores_conteudo_ministrado_lst.php';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb($nomeMenu . ' Conteúdo ministrado', [
            url('intranet/educar_conteudo_ministrado_index.php') => 'Professores',
        ]);

        return $retorno;
    }

    public function Gerar () {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = ($this->$campo) ? $this->$campo : $val;
            }
        }

        $this->campoOculto('cod_serie', 9);
        $this->campoOculto('cod_componente_curricular', 5);

        if (is_numeric($this->frequencia)) {
            $desabilitado = true;
        }

        $this->campoOculto('id', $this->id);
        $this->inputsHelper()->dynamic(['frequencia'], ['frequencia' => $this->frequencia, 'disabled' => $desabilitado]);

        $this->campoMemo('atividades', 'Atividades', $this->atividades, 100, 5, true);

        $helperOptions = [
            'objectName' => 'bncc',
        ];

        $todos_bncc = $this->getBNCC($this->frequencia)['bncc'];

        $options = [
            'label' => 'BNCC',
            'required' => false,
            'size' => 50,
            'options' => [
                'values' => $this->bncc,
                'all_values' => $todos_bncc
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);
        
        $this->campoMemo('observacao', 'Observação', $this->observacao, 100, 5, false);
    }

    public function Novo() {
        $obj = new clsModulesComponenteMinistrado(
            null,
            $this->frequencia,
            $this->atividades,
            $this->observacao,
            $this->bncc
        );

        $cadastrou = $obj->cadastra();

        if (!$cadastrou) {   
            $this->mensagem = 'Cadastro não realizado.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_cad.php');
        } else {
            $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_lst.php');
        }

        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar() {
        $obj = new clsModulesComponenteMinistrado(
            $this->id,
            null,
            $this->atividades,
            $this->observacao,
            $this->bncc
        );

        $editou = $obj->edita();

        if ($editou) {
            $this->mensagem .= 'Edi&ccedil;&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_lst.php');
        }

        $this->mensagem = 'Edi&ccedil;&atilde;o n&atilde;o realizada.<br>';

        return false;
    }

    public function Excluir () {
        $obj = new clsModulesComponenteMinistrado(
            $this->id,
        );

        $excluiu = $obj->excluir($this->id);

        if ($excluiu) {
            $this->mensagem .= 'Exclus&atilde;o efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_professores_conteudo_ministrado_lst.php');
        }

        $this->mensagem = 'Exclus&atilde;o n&atilde;o realizada.<br>';
        
        return false;
    }

    private function getBNCC($frequencia = null)
    {
        if (is_numeric($frequencia)) {
            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->lista($frequencia)) {
                foreach ($bncc_temp as $bncc_item) {
                    $id = $bncc_item['id'];
                    $codigo = $bncc_item['codigo'];
                    $habilidade = $bncc_item['habilidade'];

                    $bncc[$id] = $codigo . ' - ' . $habilidade;
                }
            }

            return ['bncc' => $bncc];
        }

        return [];
    }

    public function loadAssets () {
        $scripts = [
            '/modules/Cadastro/Assets/Javascripts/BNCC.js',
        ];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
    }

    public function __construct () {
        parent::__construct();
        $this->loadAssets();
    }

    public function Formular () {
        $this->title = 'Conteúdo ministrado - Cadastro';
        $this->processoAp = '58';
    }
};
