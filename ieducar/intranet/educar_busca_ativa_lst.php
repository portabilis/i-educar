<?php

use App\Models\LegacyDisciplineExemption;
use iEducar\Modules\School\Model\ActiveSearch;
use iEducar\Modules\School\Model\ExemptionType;

return new class extends clsListagem {
    public $pessoa_logada;
    public $titulo;
    public $limite;
    public $offset;
    public $ref_cod_matricula;
    public $ref_cod_serie;
    public $ref_cod_escola;
    public $ref_cod_disciplina;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_cod_tipo_dispensa;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $observacao;
    public $ref_sequencial;
    public $ref_cod_instituicao;
    public $ref_cod_turma;

    public function Gerar()
    {
        // Helper para url
        $urlHelper = CoreExt_View_Helper_UrlHelper::getInstance();

        $this->titulo = 'Busca Ativa - Listagem';

        // passa todos os valores obtidos no GET para atributos do objeto
        foreach ($_GET as $var => $val) {
            $this->$var = ($val === '') ? null : $val;
        }

        if (!$_GET['ref_cod_matricula']) {
            $this->simpleRedirect('educar_matricula_lst.php');
        }

        $this->ref_cod_matricula = $_GET['ref_cod_matricula'];

        $obj_matricula = new clsPmieducarMatricula();
        $lst_matricula = $obj_matricula->lista($this->ref_cod_matricula);
        $det_matricula = current($lst_matricula);

        $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
        $this->ref_cod_escola = $det_matricula['ref_ref_cod_escola'];
        $this->ref_cod_serie = $det_matricula['ref_ref_cod_serie'];

        $obj_matricula_turma = new clsPmieducarMatriculaTurma();
        $lst_matricula_turma = $obj_matricula_turma->lista(
            $this->ref_cod_matricula,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_serie,
            null,
            $this->ref_cod_escola
        );
        $det_matricula_turma = current($lst_matricula_turma);
        $this->ref_cod_turma = $det_matricula_turma['ref_cod_turma'];
        $this->ref_sequencial = $det_matricula_turma['sequencial'];

        $this->addCabecalhos([
            'Data de início',
            'Data de finalização',
            'Resultado da busca ativa'
        ]);

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
            $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $legagyDisciplineExemption = LegacyDisciplineExemption::query()
            ->selectRaw("ref_cod_tipo_dispensa,
                        TO_CHAR(dispensa_disciplina.data_cadastro, 'YYYY-mm-dd')::date as data_cadastro,
                        TO_CHAR(dispensa_disciplina.data_fim, 'YYYY-mm-dd')::date as data_fim,
                        resultado_busca_ativa,
                        array_to_string(array_agg(DISTINCT dispensa_etapa.etapa), ','::text) etapas
                        ")
            ->distinct()
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
            ->join('pmieducar.dispensa_etapa', 'dispensa_etapa.ref_cod_dispensa', '=', 'cod_dispensa')
            ->where('tipo', ExemptionType::DISPENSA_BUSCA_ATIVA)
            ->orderByRaw('2')
            ->groupByRaw('1,2,3,4')
            ->limit($this->limite)
            ->offset($this->offset);

        if ($this->ref_cod_tipo_dispensa) {
            $legagyDisciplineExemption->where('ref_cod_tipo_dispensa', $this->ref_cod_tipo_dispensa);
        }
        $activeSearchExemptions = $legagyDisciplineExemption->get()->toArray();

        $total = count($activeSearchExemptions);

        // monta a lista
        if (count($activeSearchExemptions) > 0) {
            foreach ($activeSearchExemptions as $activeSearchExemption) {

                // muda os campos data
                $row = [];

                $startDate = new DateTime($activeSearchExemption['data_cadastro']);
                $endDate = empty($activeSearchExemption['data_fim']) ? null : (new DateTime($activeSearchExemption['data_fim']));

                $row['data_cadastro_br'] = $startDate->format('d/m/Y');
                $row['data_fim_br'] = $endDate ? $endDate->format('d/m/Y') : '-';

                // Dados para a url
                $url = 'educar_busca_ativa_cad.php';
                $options = ['query' => [
                    'ref_cod_matricula' => $this->ref_cod_matricula,
                    'ref_cod_tipo_dispensa' => $activeSearchExemption['ref_cod_tipo_dispensa'],
                    'data_cadastro' => $startDate->getTimestamp(),
                    'etapa' => $activeSearchExemption['etapas']
                ]];

                $row['active_search_result'] = ActiveSearch::getDescription($activeSearchExemption['resultado_busca_ativa']);

                $this->addLinhas([
                    $urlHelper->l($row['data_cadastro_br'], $url, $options),
                    $urlHelper->l($row['data_fim_br'], $url, $options),
                    $urlHelper->l($row['active_search_result'], $url, $options)
                ]);
            }
        }

        $this->addPaginador2(
            'educar_busca_ativa_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7) && $det_matricula['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->array_botao_url[] = 'educar_busca_ativa_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[] = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb('Busca Ativa', [
            url('intranet/educar_index.php') => 'Escola',
        ]);
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Busca Ativa';
        $this->processoAp = 578;
    }
};
