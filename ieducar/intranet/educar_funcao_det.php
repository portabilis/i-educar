<?php

use App\Models\LegacyRole;

return new class extends clsDetalhe
{
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_funcao;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $nm_funcao;

    public $abreviatura;

    public $professor;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Função - Detalhe';

        $this->cod_funcao = $_GET['cod_funcao'];

        $registro = LegacyRole::find(request()->integer('cod_funcao'))?->getAttributes();
        if (!$registro) {
            $this->simpleRedirect('educar_funcao_lst.php');
        }

        if ($registro['cod_funcao']) {
            $this->addDetalhe(['Funcão', "{$registro['cod_funcao']}"]);
        }
        if ($registro['nm_funcao']) {
            $this->addDetalhe(['Nome Funcão', "{$registro['nm_funcao']}"]);
        }
        if ($registro['abreviatura']) {
            $this->addDetalhe(['Abreviatura', "{$registro['abreviatura']}"]);
        }

        $opcoes = ['1' => 'Sim',
            '0' => 'Não',
        ];

        if (is_numeric($registro['professor'])) {
            $this->addDetalhe(['Professor', "{$opcoes[$registro['professor']]}"]);
        }

        $obj_permissoes = new clsPermissoes;
        if ($obj_permissoes->permissao_cadastra(634, $this->pessoa_logada, 3)) {
            $this->url_novo = 'educar_funcao_cad.php';
            $this->url_editar = "educar_funcao_cad.php?cod_funcao={$registro['cod_funcao']}";
        }

        $this->url_cancelar = 'educar_funcao_lst.php';
        $this->largura = '100%';

        $this->breadcrumb('Detalhe da função', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Servidores -  Funções do servidor';
        $this->processoAp = '634';
    }
};
