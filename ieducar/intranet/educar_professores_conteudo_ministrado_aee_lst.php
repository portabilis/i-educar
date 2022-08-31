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
        $this->titulo = 'Atendimento AEE - Listagem';

        foreach ($_GET as $var => $val) { // passa todos os valores obtidos no GET para atributos do objeto
            $this->$var = ($val === '') ? null: $val;
        }

        $lista_busca = [
            'Data',
            'Hora Início',
            'Hora Fim',
            'Aluno'
        ];

        $this->addCabecalhos($lista_busca);

        if (!isset($_GET['busca'])) {
            $this->ano = date('Y');
        }

        $this->inputsHelper()->dynamic(['ano'], ['required' => false]);
        $this->inputsHelper()->dynamic(['instituicao', 'escola', 'curso', 'serie', 'turma', 'matricula']);
        //$this->inputsHelper()->turmaTurno(['required' => false, 'label' => 'Turno']);
  
        $this->campoQuebra();
        $this->campoRotulo('filtros_periodo', '<b>Filtros por período</b>');

        $this->inputsHelper()->dynamic(['dataInicial'], ['required' => false, 'value' => $this->data_inicial]);
        $this->inputsHelper()->dynamic(['dataFinal'], ['required' => false, 'value' => $this->data_final]);
     
        //$this->campoQuebra();
        //$this->campoRotulo('filtros_etapa', '<b>Filtros por etapa</b>');

        //$this->inputsHelper()->dynamic(['faseEtapa'], ['required' => false, 'label' => 'Etapa']);

        // Paginador
        $this->limite = 20;
        $this->offset = ($_GET["pagina_{$this->nome}"]) ? $_GET["pagina_{$this->nome}"]*$this->limite-$this->limite: 0;

        $obj_turma = new clsModulesComponenteMinistradoAee();
        $obj_turma->setOrderby('data DESC');
        $obj_turma->setLimite($this->limite, $this->offset);

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

        $lista = $obj_turma->lista(
            $this->ano,
            $this->ref_cod_instituicao,
            $this->ref_cod_escola,
            $this->ref_cod_curso,
            $this->ref_cod_turma,
            $this->ref_cod_matricula,
            $this->data,
            $this->data_fim,
            $this->fase_etapa,
            $eh_professor ? $this->pessoa_logada : null         // Passe o ID do servidor caso ele seja um professor
        );

        $total = $obj_turma->_total;
        // monta a lista
        if (is_array($lista) && count($lista)) {
            $ref_cod_escola = '';
            $nm_escola = '';
            foreach ($lista as $registro) {
                $data_formatada = dataToBrasil($registro['data']); 

                $lista_busca = [
                    "<a href=\"educar_professores_conteudo_ministrado_aee_det.php?id={$registro['id']}\">{$data_formatada}</a>",
                    "<a href=\"educar_professores_conteudo_ministrado_aee_det.php?id={$registro['id']}\">{$registro['hora_inicio']}</a>",
                    "<a href=\"educar_professores_conteudo_ministrado_aee_det.php?id={$registro['id']}\">{$registro['hora_fim']}</a>",
                    "<a href=\"educar_professores_conteudo_ministrado_aee_det.php?id={$registro['id']}\">{$registro['aluno']}</a>"
                ];

                $this->addLinhas($lista_busca);
            }
        }

        $this->addPaginador2('educar_professores_conteudo_ministrado_aee_lst.php', $total, $_GET, $this->nome, $this->limite);
        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(58, $this->pessoa_logada, 7)) {
            $this->acao = 'go("educar_professores_conteudo_ministrado_aee_cad.php")';
            $this->nome_acao = 'Novo';
        }
        $this->largura = '100%';

        $this->breadcrumb('Listagem de Atendimentos - AEE', [
            url('intranet/educar_professores_index.php') => 'Professores',
        ]);
    }

    public function Formular()
    { 
        $this->title = 'Atendimento  AEE - Listagem';
        $this->processoAp = '58';
    }
};
