<?php

return new class extends clsDetalhe {
    /**
     * Titulo no topo da pagina
     *
     * @var int
     */
    public $titulo;

    public $cod_disciplina_topico;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $nm_topico;
    public $desc_topico;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;

    public function Gerar()
    {
        $this->titulo = 'Disciplina Tópico - Detalhe';

        $this->cod_disciplina_topico=$_GET['cod_disciplina_topico'];

        $tmp_obj = new clsPmieducarDisciplinaTopico($this->cod_disciplina_topico);
        $registro = $tmp_obj->detalhe();

        if (! $registro) {
            $this->simpleRedirect('educar_disciplina_topico_lst.php');
        }

        if ($registro['nm_topico']) {
            $this->addDetalhe([ 'Nome Tópico', "{$registro['nm_topico']}"]);
        }
        if ($registro['desc_topico']) {
            $this->addDetalhe([ 'Descrição Tópico', "{$registro['desc_topico']}"]);
        }

        $objPermissao = new clsPermissoes();
        if ($objPermissao->permissao_cadastra(565, $this->pessoa_logada, 7)) {
            $this->url_novo = 'educar_disciplina_topico_cad.php';
            $this->url_editar = "educar_disciplina_topico_cad.php?cod_disciplina_topico={$registro['cod_disciplina_topico']}";
        }
        $this->url_cancelar = 'educar_disciplina_topico_lst.php';
        $this->largura = '100%';
    }

    public function Formular()
    {
        $this->title = 'Disciplina Tópico';
        $this->processoAp = '565';
    }
};
