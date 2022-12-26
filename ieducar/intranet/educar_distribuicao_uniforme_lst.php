<?php

use App\Models\LegacyStudent;
use App\Models\UniformDistribution;

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

    public $cod_distribuicao_uniforme;
    public $ref_cod_aluno;
    public $ano;
    public $kit_completo;

    public function Gerar()
    {
        $this->titulo = 'Distribuição de uniforme - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = $val;
        }

        $this->campoOculto('ref_cod_aluno', $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect('educar_aluno_lst.php');
        }

        $this->addCabecalhos([ 'Ano', 'Kit completo', 'Tipo', 'Data da Distribuição' ]);

        $obj_permissao = new clsPermissoes();
        $obj_permissao->nivel_acesso($this->pessoa_logada);

        $student = LegacyStudent::find($this->ref_cod_aluno);
        $nm_aluno = mb_strtoupper($student->name);

        if ($nm_aluno) {
            $this->campoRotulo('nm_aluno', 'Aluno', "{$nm_aluno}");
        }

        $this->campoNumero('ano', 'Ano', $this->ano, 4, 4, false);

        $query = UniformDistribution::orderBy('year', 'ASC');

        if (request('ref_cod_aluno')) {
            $query->where('student_id', request('ref_cod_aluno'));
        }
        if (request('ano')) {
            $query->where('year', request('ano'));
        }

        $result = $query->paginate(20, pageName: 'pagina_formulario');

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $uniformDistribution) {
                $complete_kit = $uniformDistribution->complete_kit ? 'SIM' : 'NÃO';
                $lista_busca = [
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->year}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$complete_kit}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->type}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->distribution_date?->format('d/m/Y')}</a>"
                ];

                $this->addLinhas($lista_busca);
            }
        }
        $this->addPaginador2('educar_distribuicao_uniforme_lst.php', $total, $_GET, $this->nome, 20);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7)) {
            $this->acao = "go(\"educar_distribuicao_uniforme_cad.php?ref_cod_aluno=".request('ref_cod_aluno')."\")";
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = "educar_aluno_det.php?cod_aluno=".request('ref_cod_aluno');

        $this->largura = '100%';

        $this->breadcrumb('Distribuições de uniforme escolar', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribuição de uniforme';
        $this->processoAp = '578';
    }
};
