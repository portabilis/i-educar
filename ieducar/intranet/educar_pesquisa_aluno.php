<?php

return new class extends clsListagem {
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

    public $ref_cod_aluno;
    public $nm_aluno;
    public $cod_aluno;

    public $ref_cod_escola;

    public function Gerar()
    {
        $this->nm_aluno = $_GET['nm_aluno'];
        $this->cod_aluno = $_GET['cod_aluno'];

        $this->ref_cod_escola = $_GET['ref_cod_escola'];

        if (!$this->ref_cod_escola) {
            $this->ref_cod_escola = $_POST['ref_cod_escola'];
        }

        $this->campoOculto('ref_cod_escola', $this->ref_cod_escola);

        $this->titulo = 'Aluno - Listagem';

        $this->addCabecalhos([
            'Aluno'
        ]);

        $this->campoNumero('cod_aluno', 'Código Aluno', $this->cod_aluno, 8, 20, false);
        $this->campoTexto('nm_aluno', 'Nome Aluno', $this->nm_aluno, 30, 255, false);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_aluno = new clsPmieducarAluno();
        $obj_aluno->setOrderby('nome_aluno ASC');
        $obj_aluno->setLimite($this->limite, $this->offset);

        $lista = $obj_aluno->lista(
            $this->cod_aluno,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            null,
            $this->nm_aluno,
            null,
            null,
            null,
            null,
            null,
            $this->ref_cod_escola
        );

        $total = $obj_aluno->_total;

        // monta a lista
        if (is_array($lista) && count($lista)) {
//          echo "<pre>";print_r($lista);die;
            foreach ($lista as $registro) {
                $registro['nome_aluno'] = str_replace('\'', '', $registro['nome_aluno']);
                $script = " onclick=\"addVal1('ref_cod_aluno','{$registro['cod_aluno']}'); addVal1('nm_aluno','{$registro['nome_aluno']}'); addVal1('nm_aluno_','{$registro['nome_aluno']}');fecha();\"";

                $display = $registro['nome_aluno'];

                if (!empty($registro['nome_social'])) {
                    $display = $registro['nome_social'] . ' - Nome de registro: ' . $registro['nome_aluno'];
                }

                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$display}</a>"
                ]);
            }
        }
        $this->addPaginador2('educar_pesquisa_aluno.php', $total, $_GET, $this->nome, $this->limite);
        $this->largura = '100%';
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-pesquisa-aluno.js');
    }

    public function Formular()
    {
        $this->title = 'Aluno';
        $this->processoAp = '0';
        $this->renderMenu = false;
        $this->renderMenuSuspenso = false;
    }
};
