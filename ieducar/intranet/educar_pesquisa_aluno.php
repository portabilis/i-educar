<?php

return new class extends clsListagem
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

        $this->campoOculto(nome: 'ref_cod_escola', valor: $this->ref_cod_escola);

        $this->titulo = 'Aluno - Listagem';

        $this->addCabecalhos([
            'Aluno',
        ]);

        $this->campoNumero(nome: 'cod_aluno', campo: 'Código Aluno', valor: $this->cod_aluno, tamanhovisivel: 8, tamanhomaximo: 20);
        $this->campoTexto(nome: 'nm_aluno', campo: 'Nome Aluno', valor: $this->nm_aluno, tamanhovisivel: 30, tamanhomaximo: 255);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"] * $this->limite - $this->limite : 0;

        $obj_aluno = new clsPmieducarAluno();
        $obj_aluno->setOrderby('nome_aluno ASC');
        $obj_aluno->setLimite(intLimiteQtd: $this->limite, intLimiteOffset: $this->offset);

        $lista = $obj_aluno->lista(
            int_cod_aluno: $this->cod_aluno,
            int_ativo: 1,
            str_nome_aluno: $this->nm_aluno,
            int_ref_cod_escola: $this->ref_cod_escola
        );

        $total = $obj_aluno->_total;

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $registro['nome_aluno'] = str_replace(search: '\'', replace: '', subject: $registro['nome_aluno']);
                $script = " onclick=\"addVal1('ref_cod_aluno','{$registro['cod_aluno']}'); addVal1('nm_aluno','{$registro['nome_aluno']}'); addVal1('nm_aluno_','{$registro['nome_aluno']}');fecha();\"";

                $display = $registro['nome_aluno'];

                if (!empty($registro['nome_social'])) {
                    $display = $registro['nome_social'] . ' - Nome de registro: ' . $registro['nome_aluno'];
                }

                $this->addLinhas([
                    "<a href=\"javascript:void(0);\" {$script}>{$display}</a>",
                ]);
            }
        }
        $this->addPaginador2(strUrl: 'educar_pesquisa_aluno.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
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
