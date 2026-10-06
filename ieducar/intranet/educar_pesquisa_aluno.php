<?php

use App\Models\LegacyStudent;

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

        $alunos = LegacyStudent::query()
            ->active()
            ->joinPerson()
            ->joinIndividual()
            ->when(is_numeric($this->cod_aluno), fn ($q) => $q->whereStudent($this->cod_aluno))
            ->when(is_string($this->nm_aluno), fn ($q) => $q->whereStudentNameLike($this->nm_aluno))
            ->when(is_numeric($this->ref_cod_escola), fn ($q) => $q->whereLastRegistrationSchool($this->ref_cod_escola))
            ->orderBy('pessoa.nome')
            ->paginate(
                perPage: $this->limite,
                columns: [
                    'cod_aluno',
                    'pessoa.nome as nome_aluno',
                    'fisica.nome_social',
                ],
                pageName: 'pagina_' . $this->nome
            );

        foreach ($alunos as $registro) {
            $nomeAluno = str_replace(search: '\'', replace: '', subject: $registro['nome_aluno']);
            $script = " onclick=\"addVal1('ref_cod_aluno','{$registro['cod_aluno']}'); addVal1('nm_aluno','{$nomeAluno}'); addVal1('nm_aluno_','{$nomeAluno}');fecha();\"";

            $display = $nomeAluno;

            if (!empty($registro['nome_social'])) {
                $display = $registro['nome_social'] . ' - Nome de registro: ' . $nomeAluno;
            }

            $this->addLinhas([
                "<a href=\"javascript:void(0);\" {$script}>{$display}</a>",
            ]);
        }

        $this->addPaginador2(strUrl: 'educar_pesquisa_aluno.php', intTotalRegistros: $alunos->total(), mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: $this->limite);
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
