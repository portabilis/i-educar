<?php

use App\Models\LegacyDisciplineExemption;
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

        $this->titulo = 'Dispensa Componente Curricular - Listagem';

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

        if (is_array($lst_matricula)) {
            $det_matricula             = array_shift($lst_matricula);
            $this->ref_cod_instituicao = $det_matricula['ref_cod_instituicao'];
            $this->ref_cod_escola      = $det_matricula['ref_ref_cod_escola'];
            $this->ref_cod_serie       = $det_matricula['ref_ref_cod_serie'];

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

            if (is_array($lst_matricula_turma)) {
                $det                  = array_shift($lst_matricula_turma);
                $this->ref_cod_turma  = $det['ref_cod_turma'];
                $this->ref_sequencial = $det['sequencial'];
            }
        }

        $this->campoOculto('ref_cod_turma', $this->ref_cod_turma);

        $this->addCabecalhos([
      'Disciplina',
      'Tipo Dispensa',
      'Data Dispensa'
    ]);

        // Filtros de Foreign Keys
        $opcoes = ['' => 'Selecione'];
        $objTemp = new clsPmieducarTipoDispensa();

        if ($this->ref_cod_instituicao) {
            $lista = $objTemp->lista(
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
                $this->ref_cod_instituicao
            );
        } else {
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, 1);
        }

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                if ($registro['tipo'] != ExemptionType::DISPENSA_BUSCA_ATIVA) {
                    $opcoes[$registro['cod_tipo_dispensa']] = $registro['nm_tipo'];
                }
            }
        }

        $this->campoLista(
            'ref_cod_tipo_dispensa',
            'Motivo',
            $opcoes,
            $this->ref_cod_tipo_dispensa,
            '',
            false,
            '',
            '',
            false,
            false
        );

        $this->campoOculto('ref_cod_matricula', $this->ref_cod_matricula);

        // outros Filtros
        $opcoes = ['' => 'Selecione'];

        // Escola série disciplina
        $componentes = App_Model_IedFinder::getComponentesTurma(
            $this->ref_cod_serie,
            $this->ref_cod_escola,
            $this->ref_cod_turma
        );

        foreach ($componentes as $componente) {
            $opcoes[$componente->id] = $componente->nome;
        }

        $this->campoLista(
            'ref_cod_disciplina',
            'Disciplina',
            $opcoes,
            $this->ref_cod_disciplina,
            '',
            false,
            '',
            '',
            false,
            false
        );

        // Paginador
        $this->limite = 20;
        $this->offset = $_GET['pagina_' . $this->nome] ?
      $_GET['pagina_' . $this->nome] * $this->limite - $this->limite : 0;

        $legagyDisciplineExemption = LegacyDisciplineExemption::query()
            ->select('dispensa_disciplina.*')
            ->join('pmieducar.tipo_dispensa', 'ref_cod_tipo_dispensa', '=', 'cod_tipo_dispensa')
            ->where('ref_cod_matricula', $this->ref_cod_matricula)
            ->where('dispensa_disciplina.ativo', 1)
            ->whereNotIn('tipo', [ExemptionType::DISPENSA_BUSCA_ATIVA])
            ->orderBy('dispensa_disciplina.data_cadastro')
            ->limit($this->limite)
            ->offset($this->offset);

        if($this->ref_cod_tipo_dispensa){
            $legagyDisciplineExemption->where('ref_cod_tipo_dispensa', $this->ref_cod_tipo_dispensa);
        }

        if($this->ref_cod_disciplina){
            $legagyDisciplineExemption->where('ref_cod_disciplina', $this->ref_cod_disciplina);
        }

        $disciplineExemptions = $legagyDisciplineExemption->get()->toArray();

        $total = count($disciplineExemptions);

        // Mapper de componente curricular
        $componenteMapper = new ComponenteCurricular_Model_ComponenteDataMapper();

        // monta a lista
        if ($total > 0) {
            foreach ($disciplineExemptions as $registro) {

                // muda os campos data
                $registro['data_cadastro_time'] = strtotime(substr($registro['data_cadastro'], 0, 16));
                $registro['data_cadastro_br']   = date('d/m/Y', $registro['data_cadastro_time']);

                // Tipo da dispensa
                $obj_ref_cod_tipo_dispensa = new clsPmieducarTipoDispensa($registro['ref_cod_tipo_dispensa']);
                $det_ref_cod_tipo_dispensa = $obj_ref_cod_tipo_dispensa->detalhe();
                $registro['nome_tipo_dispensa'] = $det_ref_cod_tipo_dispensa['nm_tipo'];

                // Componente curricular
                $componente = $componenteMapper->find($registro['ref_cod_disciplina']);

                // Dados para a url
                $url     = 'educar_dispensa_disciplina_det.php';
                $options = [
                    'query' => [
                        'ref_cod_matricula'  => $registro['ref_cod_matricula'],
                        'ref_cod_serie'      => $registro['ref_cod_serie'],
                        'ref_cod_escola'     => $registro['ref_cod_escola'],
                        'ref_cod_disciplina' => $registro['ref_cod_disciplina'],
                        'ref_cod_tipo_dispensa' => $registro['ref_cod_tipo_dispensa']
                    ]
                ];
                $this->addLinhas([
                    $urlHelper->l($componente->nome, $url, $options),
                    $urlHelper->l($registro['nome_tipo_dispensa'], $url, $options),
                    $urlHelper->l($registro['data_cadastro_br'], $url, $options)
                ]);
            }
        }

        $this->addPaginador2(
            'educar_dispensa_disciplina_lst.php',
            $total,
            $_GET,
            $this->nome,
            $this->limite
        );

        $obj_permissoes = new clsPermissoes();

        if ($obj_permissoes->permissao_cadastra(578, $this->pessoa_logada, 7) && $det_matricula['aprovado'] == App_Model_MatriculaSituacao::EM_ANDAMENTO) {
            $this->array_botao_url[] = 'educar_dispensa_disciplina_cad.php?ref_cod_matricula=' . $this->ref_cod_matricula;
            $this->array_botao[] = [
                'name' => 'Novo',
                'css-extra' => 'btn-green'
            ];
        }

        $this->array_botao_url[] = 'educar_matricula_det.php?cod_matricula=' . $this->ref_cod_matricula;
        $this->array_botao[]     = 'Voltar';

        $this->largura = '100%';

        $this->breadcrumb('Dispensa de componentes curriculares', [
        url('intranet/educar_index.php') => 'Escola',
    ]);
    }

    public function Formular()
    {
        $this->title = 'i-Educar - Dispensa Componente Curricular';
        $this->processoAp = 578;
    }
};
