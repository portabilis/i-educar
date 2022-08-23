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

    public $data_inicial;
    public $data_final;

    public $etapa;
    public $fase_etapa;

    public function Gerar()
    {
        $this->titulo = 'Ficha AEE - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Data',
            'Aluno',
            'Turma',
            'Escola',
            'Professor'
        ];

        $this->addCabecalhos($lista_busca);

        if (!isset($_GET['busca'])) {
            $this->ano = date('Y');
        }

        $this->inputsHelper()->dynamic(['ano'], ['required' => false]);
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie', 'turma'], ['required' => false]);
        $this->inputsHelper()->turmaTurno(['required' => false, 'label' => 'Turno']);
        $this->inputsHelper()->dynamic('componenteCurricular', ['required' => false]);

        $this->campoQuebra();
        $this->campoRotulo('filtros_periodo', '<b>Filtros por período</b>');

        $this->inputsHelper()->dynamic(['dataInicial'], ['required' => false, 'value' => $this->data_inicial]);
        $this->inputsHelper()->dynamic(['dataFinal'], ['required' => false, 'value' => $this->data_final]);

        $this->campoQuebra();
        $this->campoRotulo('filtros_etapa', '<b>Filtros por etapa</b>');

        $this->inputsHelper()->dynamic(['faseEtapa'], ['required' => false, 'label' => 'Etapa']);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_plano = new clsModulesFichaAee();
        $obj_plano->setOrderby('data_inicial DESC');
        $obj_plano->setLimite($this->limite, $this->offset);

        if ($this->data_inicial && Portabilis_Date_Utils::validaData($this->data_inicial) || !$this->data_inicial) {
            $this->data_inicial = dataToBanco($this->data_inicial);
        } else {
            $temp_data_inicial = new DateTime('now');
            $this->data_inicial = dataToBanco($temp_data_inicial->format('d/m/Y'));
        }

        if ($this->data_final && Portabilis_Date_Utils::validaData($this->data_final) || !$this->data_final) {
            $this->data_final = dataToBanco($this->data_final);
        } else {
            $temp_data_final = new DateTime('now');
            $this->data_final = dataToBanco($temp_data_final->format('d/m/Y'));
        }

        $obj_servidor = new clsPmieducarServidor(
            $this->pessoa_logada,
            null,
            null,
            null,
            null,
            null,
            1,      //  Ativo
            1,      //  Fixado na instituição de ID 1
        );
        $eh_professor = $obj_servidor->isProfessor();

        $lista = $obj_plano->lista(
            $this->data,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_turma,
            $this->ref_cod_matricula, 
            $eh_professor ? $this->pessoa_logada : null         // Passe o ID do servidor caso ele seja um professor            
        );

        $total = $obj_plano->_total;
        // monta a lista
        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                // $obj = new clsModulesPlanejamentoAulaComponenteCurricular();
                // $componentesCurriculares = $obj->lista($registro['id']);

                // $obj = new clsPmieducarSerie();
                // $tipo_presenca = $obj->tipoPresencaRegraAvaliacao($registro['cod_serie']);

                $data_inicial_formatada = dataToBrasil($registro['data']);

                $lista_busca = [
                    "<a href=\"educar_professores_ficha_aee_det.php?id={$registro['id']}\">{$data_inicial_formatada}</a>",
                    "<a href=\"educar_professores_ficha_aee_det.php?id={$registro['id']}\">{$registro['aluno']}</a>",
                    "<a href=\"educar_professores_ficha_aee_det.php?id={$registro['id']}\">{$registro['turma']}</a>",                   
                    "<a href=\"educar_professores_ficha_aee_det.php?id={$registro['id']}\">{$registro['escola']}</a>",
                ];

                // if (isset($componentesCurriculares) && is_array($componentesCurriculares) && !empty($tipo_presenca) && $tipo_presenca == 2) {
                //     $abreviatura = '';
                //     foreach ($componentesCurriculares as $componenteCurricular) {
                //         $abreviatura .= $componenteCurricular['abreviatura'].'<br>';
                //     }
                //     $lista_busca[] = "<a href=\"educar_professores_frequencia_det.php?id={$registro['id']}\">{$abreviatura}</a>";
                // } else {
                //     $lista_busca[] = "<a href=\"educar_professores_frequencia_det.php?id={$registro['id']}\">—</a>";
                // }

                $lista_busca[] = "<a href=\"educar_professores_ficha_aee_det.php?id={$registro['id']}\">{$registro['professor']}</a>";

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_professores_ficha_aee_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_professores_ficha_aee_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de Ficha AEE', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }

    public function Formular()
    {
        $this->title = 'Ficha AEE - Listagem';
        $this->processoAp = '58';
    }
};
