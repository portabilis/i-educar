<?php

use App\Models\LegacyStudent;
use App\Models\UniformDistribution;

return new class extends clsListagem
{
    public $pessoa_logada;

    public $titulo;

    public $limite;

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

        $this->campoOculto(nome: 'ref_cod_aluno', valor: $this->ref_cod_aluno);

        if (!$this->ref_cod_aluno) {
            $this->simpleRedirect(url: 'educar_aluno_lst.php');
        }

        $this->addCabecalhos(coluna: ['Ano', 'Kit completo', 'Tipo', 'Data da Distribuição']);

        $obj_permissao = new clsPermissoes();
        $obj_permissao->nivel_acesso(int_idpes_usuario: $this->pessoa_logada);

        $student = LegacyStudent::find(id: $this->ref_cod_aluno);
        $nm_aluno = mb_strtoupper(string: $student->name);

        if ($nm_aluno) {
            $this->campoRotulo(nome: 'nm_aluno', campo: 'Aluno', valor: "{$nm_aluno}");
        }

        $this->campoNumero(nome: 'ano', campo: 'Ano', valor: $this->ano, tamanhovisivel: 4, tamanhomaximo: 4, obrigatorio: false);

        $query = UniformDistribution::orderBy('year', 'ASC');

        if (request(key: 'ref_cod_aluno')) {
            $query->where('student_id', request(key: 'ref_cod_aluno'));
        }
        if (request(key: 'ano')) {
            $query->where('year', request(key: 'ano'));
        }

        $result = $query->paginate(20, pageName: 'pagina_formulario');

        $lista = $result->items();
        $total = $result->total();

        // monta a lista
        if (is_array(value: $lista) && count(value: $lista)) {
            foreach ($lista as $uniformDistribution) {
                $complete_kit = $uniformDistribution->complete_kit ? 'SIM' : 'NÃO';
                $lista_busca = [
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->year}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$complete_kit}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->type}</a>",
                    "<a href=\"educar_distribuicao_uniforme_det.php?ref_cod_aluno={$uniformDistribution->student_id}&cod_distribuicao_uniforme={$uniformDistribution->id}\">{$uniformDistribution->distribution_date?->format('d/m/Y')}</a>",
                ];

                $this->addLinhas(linha: $lista_busca);
            }
        }
        $this->addPaginador2(strUrl: 'educar_distribuicao_uniforme_lst.php', intTotalRegistros: $total, mixVariaveisMantidas: $_GET, nome: $this->nome, intResultadosPorPagina: 20);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(int_processo_ap: 578, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
            $this->acao = 'go("educar_distribuicao_uniforme_cad.php?ref_cod_aluno='.request(key: 'ref_cod_aluno').'")';
            $this->nome_acao = 'Novo';
        }
        $this->array_botao[] = 'Voltar';
        $this->array_botao_url[] = 'educar_aluno_det.php?cod_aluno='.request(key: 'ref_cod_aluno');

        $this->largura = '100%';

        $this->breadcrumb(currentPage: 'Distribuições de uniforme escolar', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Distribuição de uniforme';
        $this->processoAp = '578';
    }
};
