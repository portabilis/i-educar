<?php

use App\Models\EmployeeGraduation;
use App\Models\LegacySchoolingDegree;
use App\Services\EmployeeGraduationService;
use iEducar\Modules\Educacenso\Model\Escolaridade;
use iEducar\Modules\ValueObjects\EmployeeGraduationValueObject;
use iEducar\Support\View\SelectOptions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

return new class extends clsCadastro {
    public $pessoa_logada;
    public $cod_servidor;
    public $ref_cod_instituicao;
    public $ref_idesco;
    public $ref_cod_funcao = [];
    public $carga_horaria;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $ref_cod_instituicao_original;
    public $curso_formacao_continuada;
    public $multi_seriado;
    public $tipo_ensino_medio_cursado;
    public $matricula = [];
    public $cod_servidor_funcao = [];

    public $total_horas_alocadas;

    public $cod_docente_inep;

    // Determina se o servidor é um docente para buscar código Educacenso/Inep.
    public $docente = false;

    public $employee_course_id;
    public $employee_completion_year;
    public $employee_college_id;
    public $employee_discipline_id;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->cod_servidor = $this->getQueryString('cod_servidor');
        $this->ref_cod_instituicao = $this->getQueryString('ref_cod_instituicao');
        $this->ref_cod_instituicao_original = $this->getQueryString('ref_cod_instituicao');

        if ($_POST['ref_cod_instituicao_original']) {
            $this->ref_cod_instituicao_original = $_POST['ref_cod_instituicao_original'];
        }

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            635,
            $this->pessoa_logada,
            7,
            'educar_servidor_lst.php'
        );
        if (is_numeric($this->cod_servidor) && is_numeric($this->ref_cod_instituicao)) {
            $obj = new clsPmieducarServidor(
                $this->cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                $this->ref_cod_instituicao
            );

            $registro = $obj->detalhe();

            if (empty($registro)) {
                return $this->simpleRedirect(url('intranet/educar_servidor_lst.php'));
            }

            if ($registro) {
                // passa todos os valores obtidos no registro para atributos do objeto
                foreach ($registro as $campo => $val) {
                    $this->$campo = $val;
                }

                $this->multi_seriado = dbBool($this->multi_seriado);

                $obj_permissoes = new clsPermissoes();
                if ($obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7)) {
                    $this->fexcluir = true;
                }

                $db = new clsBanco();

                // Carga horária alocada no ultimo ano de alocação
                $sql = sprintf(
                    '
                    SELECT
                        SUM(extract(hours from carga_horaria::interval))
                    FROM
                        pmieducar.servidor_alocacao
                    WHERE
                        ref_cod_servidor = %d AND
                        ativo = 1
                        AND ano = %d
                        AND (data_saida > now() or data_saida is null)',
                    $this->cod_servidor,
                    $this->ano ?: date('Y')
                );

                $db->Consulta($sql);
                while ($db->ProximoRegistro()) {
                    $cargaHoraria = $db->Tupla();
                    $cargaHoraria = $cargaHoraria['sum'];
                }

                $cargaHoraria = str_pad($cargaHoraria, 2, 0, STR_PAD_LEFT);

                $this->total_horas_alocadas = $cargaHoraria;

                // Funções
                $obj_funcoes = new clsPmieducarServidorFuncao();
                $lst_funcoes = $obj_funcoes->lista($this->ref_cod_instituicao, $this->cod_servidor);

                if ($lst_funcoes) {
                    foreach ($lst_funcoes as $funcao) {
                        $obj_funcao = new clsPmieducarFuncao($funcao['ref_cod_funcao']);
                        $det_funcao = $obj_funcao->detalhe();

                        $this->ref_cod_funcao[] = [$funcao['ref_cod_funcao'] . '-' . $det_funcao['professor'], null, null, $funcao['matricula'], $funcao['cod_servidor_funcao']];

                        if (false == $this->docente && (bool) $det_funcao['professor']) {
                            $this->docente = true;
                        }
                    }
                }

                $obj_servidor_disciplina = new clsPmieducarServidorDisciplina();
                $lst_servidor_disciplina = $obj_servidor_disciplina->lista(null, $this->ref_cod_instituicao, $this->cod_servidor);

                Session::forget("servant:{$this->cod_servidor}");

                if ($lst_servidor_disciplina) {
                    foreach ($lst_servidor_disciplina as $disciplina) {
                        $funcoes[$disciplina['ref_cod_funcao']][$disciplina['ref_cod_curso']][] = $disciplina['ref_cod_disciplina'];
                    }

                    // Armazena na sessão para permitir a alteração via modal
                    Session::put("servant:{$this->cod_servidor}", $funcoes);
                }

                if (is_string($this->pos_graduacao)) {
                    $this->pos_graduacao = explode(',', str_replace(['{', '}'], '', $this->pos_graduacao));
                }

                if (is_string($this->curso_formacao_continuada)) {
                    $this->curso_formacao_continuada = explode(',', str_replace(['{', '}'], '', $this->curso_formacao_continuada));
                }

                $retorno = 'Editar';
            }
        }

        // remove dados que podem estar na session de outras consultas
        Session::forget("cursos_por_funcao");

        $this->url_cancelar = ($retorno == 'Editar') ?
            "educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}" :
            'educar_servidor_lst.php';

        $this->nome_url_cancelar = 'Cancelar';

        $nomeMenu = $retorno == 'Editar' ? $retorno : 'Cadastrar';

        $this->breadcrumb('Funções do servidor', [
            url('intranet/educar_servidores_index.php') => 'Servidores',
        ]);

        return $retorno;
    }

    /**
     * Gerar formulário
     */
    public function Gerar()
    {
        // Foreign keys
        $obrigatorio = true;
        $get_instituicao = true;
        include 'include/pmieducar/educar_campo_lista.php';

        $obrigarCamposCenso = $this->validarCamposObrigatoriosCenso();
        $this->campoOculto('obrigar_campos_censo', (int) $obrigarCamposCenso);

        /**
         * Selecionar funcionário,
         * Escolher a pessoa (não o usuário)
         */
        $opcoes = ['' => 'Para procurar, clique na lupa ao lado.'];
        if ($this->cod_servidor) {
            $servidor = new clsFuncionario($this->cod_servidor);
            $detalhe = $servidor->detalhe();
            //$detalhe = $detalhe['idpes']->detalhe();

            $this->campoRotulo('nm_servidor', 'Pessoa', $servidor->nome);
            $this->campoOculto('cod_servidor', $this->cod_servidor);
            $this->campoOculto(
                'ref_cod_instituicao_original',
                $this->ref_cod_instituicao_original
            );
        } else {
            $parametros = new clsParametrosPesquisas();
            $parametros->setSubmit(0);
            $parametros->adicionaCampoSelect(
                'cod_servidor',
                'idpes',
                'nome'
            );

            // Configurações do campo de pesquisa
            $this->campoListaPesq(
                'cod_servidor',
                'Pessoa',
                $opcoes,
                $this->cod_servidor,
                'pesquisa_pessoa_lst.php',
                '',
                false,
                '',
                '',
                null,
                null,
                '',
                false,
                $parametros->serializaCampos(),
                true
            );
        }

        $this->inputsHelper()->integer(
            'cod_docente_inep',
            [
                'label' => 'Código INEP',
                'required' => false,
                'label_hint' => 'Somente números',
                'max_length' => 12,
                'placeholder' => 'INEP'
            ]
        );

        $helperOptions = ['objectName' => 'deficiencias'];
        $options = [
            'label' => 'Deficiências',
            'size' => 50,
            'required' => false,
            'options' => ['value' => null]
        ];

        $this->inputsHelper()->multipleSearchDeficiencias(
            '',
            $options,
            $helperOptions
        );

        $opcoes = ['' => 'Selecione'];

        if (is_numeric($this->ref_cod_instituicao)) {
            $objTemp = new clsPmieducarFuncao();
            $objTemp->setOrderby('nm_funcao ASC');
            $lista = $objTemp->lista(null, null, null, null, null, null, null, null, null, null, 1, $this->ref_cod_instituicao);

            if (is_array($lista) && count($lista)) {
                foreach ($lista as $registro) {
                    $opcoes[$registro['cod_funcao'] . '-' . $registro['professor']] = $registro['nm_funcao'];
                }
            }
        }

        $this->campoTabelaInicio(
            'funcao',
            'Funções Servidor',
            [
                'Função',
                'Componentes Curriculares',
                'Cursos',
                'Matrícula'],
            ($this->ref_cod_funcao)
        );

        $funcao = 'popless(this)';

        $this->campoLista('ref_cod_funcao', 'Função', $opcoes, $this->ref_cod_funcao, 'funcaoChange(this)', '', '', '');

        $this->campoRotulo(
            'disciplina',
            'Componentes Curriculares',
            "<img src='imagens/lupaT.png' border='0' style='cursor:pointer;' alt='Buscar Componente Curricular' title='Buscar Componente Curricular' onclick=\"$funcao\">"
        );

        $funcao = 'popCurso(this)';

        $this->campoRotulo(
            'curso',
            'Curso',
            "<img src='imagens/lupaT.png' border='0' style='cursor:pointer;' alt='Buscar Cursos' title='Buscar Cursos' onclick=\"$funcao\">"
        );

        $this->campoTexto('matricula', 'Matricula', $this->matricula);

        $this->campoOculto('cod_servidor_funcao', null);

        $this->campoTabelaFim();

        $horas = '00:00';
        if ($this->total_horas_alocadas) {
            $horas = $this->total_horas_alocadas . ':00';
        }

        if (mb_strtoupper($this->tipoacao) == 'EDITAR') {
            $this->campoTextoInv(
                'total_horas_alocadas_',
                'Total de Horas Alocadadas',
                $horas,
                6,
                20
            );

            $hora = explode(':', $this->total_horas_alocadas);
            $this->total_horas_alocadas = $hora[0] + ($hora[1] / 60);
            $this->campoOculto('total_horas_alocadas', $this->total_horas_alocadas);
            $this->acao_enviar = 'acao2()';
        }

        if ($this->carga_horaria) {
            $horas = (int) $this->carga_horaria;
            $minutos = round(($this->carga_horaria - (int) $this->carga_horaria) * 60);
            $hora_formatada = sprintf('%02d:%02d', $horas, $minutos);
        }

        $this->campoHora(
            'carga_horaria',
            'Carga Horária Semanal',
            $hora_formatada,
            true,
            ' * Número de horas deve ser maior que horas alocadas',
            '',
            false
        );

        $this->inputsHelper()->checkbox('multi_seriado', ['label' => 'Multisseriado', 'value' => $this->multi_seriado]);

        // Dados do docente no Inep/Educacenso.
        if ($this->docente) {
            $docenteMapper = new Educacenso_Model_DocenteDataMapper();

            $docenteInep = null;
            try {
                $docenteInep = $docenteMapper->find(['docente' => $this->cod_servidor]);
            } catch (Exception $e) {
            }
        }

        $opcoes = ['' => 'Selecione'];

        $objTemp = new clsCadastroEscolaridade();
        $lista = $objTemp->lista();

        if (is_array($lista) && count($lista)) {
            foreach ($lista as $registro) {
                $opcoes[$registro['idesco']] = $registro['descricao'];
            }
        }

        $obj_permissoes = new clsPermissoes();
        if ($obj_permissoes->permissao_cadastra(632, $this->pessoa_logada, 4)) {
            $script = 'javascript:showExpansivelIframe(1024, 480, \'educar_escolaridade_cad_pop.php\');';
            $script = "<img id='img_deficiencia' style='display: \'\'' src='imagens/banco_imagens/escreve.gif' style='cursor:hand; cursor:pointer;' border='0' onclick=\"{$script}\">";
        } else {
            $script = null;
        }

        $this->campoLista('ref_idesco', 'Escolaridade', $opcoes, $this->ref_idesco, '', false, '', $script, false, $obrigarCamposCenso);

        $options = [
            'label' => 'Tipo de ensino médio cursado',
            'resources' => SelectOptions::tiposEnsinoMedioCursados(),
            'value' => $this->tipo_ensino_medio_cursado,
            'required' => false
        ];

        $this->inputsHelper()->select('tipo_ensino_medio_cursado', $options);

        $helperOptions = ['objectName' => 'pos_graduacao'];
        $options = [
            'label' => 'Pós-Graduações concluídas',
            'required' => false,
            'options' => [
                'values' => $this->pos_graduacao,
                'all_values' => [
                    1 => 'Especialização',
                    2 => 'Mestrado',
                    3 => 'Doutorado',
                    4 => 'Não tem pós-graduação concluída'
                ]
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $helperOptions = ['objectName' => 'curso_formacao_continuada'];
        $options = [
            'label' => 'Outros cursos de formação continuada (Mínimo de 80 horas)',
            'required' => $obrigarCamposCenso,
            'options' => [
                'values' => $this->curso_formacao_continuada,
                'all_values' => [
                    1 => 'Creche (0 a 3 anos)',
                    2 => 'Pré-escola (4 e 5 anos)',
                    3 => 'Anos iniciais do ensino fundamental',
                    4 => 'Anos finais do ensino fundamental',
                    5 => 'Ensino médio',
                    6 => 'Educação de jovens e adultos',
                    7 => 'Educação especial',
                    8 => 'Educação indígena',
                    9 => 'Educação do campo',
                    10 => 'Educação ambiental',
                    11 => 'Educação em direitos humanos',
                    12 => 'Gênero e diversidade sexual',
                    13 => 'Direitos de criança e adolescente',
                    14 => 'Educação para as relações étnico-raciais e História e cultura Afro-Brasileira e Africana',
                    17 => 'Gestão Escolar',
                    15 => 'Outros',
                    16 => 'Nenhum'
                ]
            ]
        ];
        $this->inputsHelper()->multipleSearchCustom('', $options, $helperOptions);

        $resources = [
            null => 'Selecione',
            1 => 'Concluído',
            2 => 'Em andamento'
        ];

        $this->addGraduationsTable();

        $scripts = ['/modules/Cadastro/Assets/Javascripts/Servidor.js'];

        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);

        $styles = [
            '/modules/Cadastro/Assets/Stylesheets/Servidor.css',
            '/modules/Portabilis/Assets/Stylesheets/Frontend/Resource.css'
        ];

        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);

        $script = <<<'JS'
(function () {
    $j('.ref_cod_funcao select').each(function () {
        const $this = $j(this);
        const value = $this.val();

        if (value != '') {
            $this.data('valor-original', value);
        }
    });
})();
JS;

        Portabilis_View_Helper_Application::embedJavascript($this, $script);
    }

    public function Novo()
    {
        $this->cod_servidor = (int) $this->cod_servidor;
        $this->ref_cod_instituicao = (int) $this->ref_cod_instituicao;

        $timesep = explode(':', $this->carga_horaria);
        $hour = (int) $timesep[0] + ((int) ($timesep[1] / 60));
        $min = abs(((int) ($timesep[1] / 60)) - ($timesep[1] / 60)) . '<br>';

        $this->carga_horaria = $hour + $min;
        $this->carga_horaria = $hour + $min;

        $this->pos_graduacao = '{' . implode(',', array_filter($this->pos_graduacao)) . '}';

        $this->curso_formacao_continuada = '{' . implode(',', $this->curso_formacao_continuada) . '}';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

        $obj = new clsPmieducarServidor($this->cod_servidor, null, null, null, null, null, null, $this->ref_cod_instituicao);

        if ($obj->detalhe()) {
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $obj = new clsPmieducarServidor($this->cod_servidor, null, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao);
            $obj = $this->addCamposCenso($obj);
            $obj->multi_seriado = !is_null($this->multi_seriado);

            $editou = $obj->edita();

            if ($editou) {
                $servidorDepois = $obj->detalhe();

                $this->cadastraFuncoes();
                $this->createOrUpdateInep();
                $this->createOrUpdateDeficiencias();

                $this->storeGraduations($this->cod_servidor);

                include 'educar_limpa_sessao_curso_disciplina_servidor.php';

                $this->mensagem .= 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
            }
        } else {
            $this->ref_cod_instituicao = (int) $this->ref_cod_instituicao;
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

            $obj_2 = new clsPmieducarServidor($this->cod_servidor, null, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao);
            $obj_2 = $this->addCamposCenso($obj_2);
            $obj_2->multi_seriado = !is_null($this->multi_seriado);
            $obj_2->cod_servidor = $this->cod_servidor;

            $cadastrou = $obj_2->cadastra();

            if ($cadastrou) {
                $this->cadastraFuncoes();
                $this->createOrUpdateInep();
                $this->createOrUpdateDeficiencias();

                $this->storeGraduations($this->cod_servidor);

                include 'educar_limpa_sessao_curso_disciplina_servidor.php';

                $this->mensagem = 'Cadastro efetuado com sucesso.<br>';
                $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
            }
        }
        $this->mensagem = 'Cadastro não realizado.<br>';

        return false;
    }

    public function Editar()
    {
        $timesep = explode(':', $this->carga_horaria);
        $hour = $timesep[0] + ((int) ($timesep[1] / 60));
        $min = abs(((int) ($timesep[1] / 60)) - ($timesep[1] / 60)) . '<br>';
        $this->carga_horaria = $hour + $min;
        $this->carga_horaria = $hour + $min;

        $this->pos_graduacao = '{' . implode(',', array_filter($this->pos_graduacao)) . '}';

        $this->curso_formacao_continuada = '{' . implode(',', $this->curso_formacao_continuada) . '}';

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

        if ($this->ref_cod_instituicao == $this->ref_cod_instituicao_original) {
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

            $obj = new clsPmieducarServidor($this->cod_servidor, null, $this->ref_idesco, $this->carga_horaria, null, null, 1, $this->ref_cod_instituicao);
            $obj = $this->addCamposCenso($obj);
            $obj->multi_seriado = !is_null($this->multi_seriado);
            $editou = $obj->edita();

            if ($editou) {
                $this->cadastraFuncoes();
                $this->createOrUpdateInep();
                $this->createOrUpdateDeficiencias();

                $this->storeGraduations($this->cod_servidor);

                include 'educar_limpa_sessao_curso_disciplina_servidor.php';

                $this->mensagem = 'Edição efetuada com sucesso.<br>';
                $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
            }
        } else {
            $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);
            $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(
                null,
                null,
                null,
                null,
                null,
                null,
                $this->cod_servidor,
                null,
                null,
                null,
                null,
                null,
                null,
                1,
                $this->ref_cod_instituicao
            );

            if ($obj_quadro_horario->detalhe()) {
                $this->mensagem = 'Edição não realizada. O servidor está vinculado a um quadro de horários.<br>';

                return false;
            } else {
                $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    $this->cod_servidor,
                    null,
                    null,
                    null,
                    null,
                    null,
                    1,
                    null,
                    $this->ref_cod_instituicao
                );

                if ($obj_quadro_horario->detalhe()) {
                    $this->mensagem = 'Edição não realizada. O servidor está vinculado a um quadro de horários.<br>';

                    return false;
                } else {
                    $this->carga_horaria = str_replace(',', '.', $this->carga_horaria);

                    $obj = new clsPmieducarServidor(
                        $this->cod_servidor,
                        null,
                        $this->ref_idesco,
                        $this->carga_horaria,
                        null,
                        null,
                        0,
                        $this->ref_cod_instituicao_original
                    );
                    $obj = $this->addCamposCenso($obj);
                    $obj->multi_seriado = !is_null($this->multi_seriado);
                    $editou = $obj->edita();

                    if ($editou) {
                        $obj = new clsPmieducarServidor(
                            $this->cod_servidor,
                            null,
                            $this->ref_idesco,
                            $this->carga_horaria,
                            null,
                            null,
                            1,
                            $this->ref_cod_instituicao
                        );

                        if ($obj->existe()) {
                            $cadastrou = $obj->edita();
                        } else {
                            $cadastrou = $obj->cadastra();
                        }

                        if ($cadastrou) {
                            $this->cadastraFuncoes();
                            $this->createOrUpdateInep();
                            $this->createOrUpdateDeficiencias();

                            $this->storeGraduations($this->cod_servidor);

                            include 'educar_limpa_sessao_curso_disciplina_servidor.php';

                            $this->mensagem = 'Edição efetuada com sucesso.<br>';
                            $this->simpleRedirect("educar_servidor_det.php?cod_servidor={$this->cod_servidor}&ref_cod_instituicao={$this->ref_cod_instituicao}");
                        }
                    }
                }
            }
        }
        $this->mensagem = 'Edição não realizada.<br>';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_excluir(635, $this->pessoa_logada, 7, 'educar_servidor_lst.php');

        $obj_quadro_horario = new clsPmieducarQuadroHorarioHorarios(
            null,
            null,
            null,
            null,
            null,
            null,
            $this->cod_servidor,
            null,
            null,
            null,
            null,
            null,
            null,
            1,
            $this->ref_cod_instituicao
        );

        if ($obj_quadro_horario->detalhe()) {
            $this->mensagem = 'Exclusão não realizada. O servidor está vinculado a um quadro de horários.<br>';
            return false;
        }

        DB::beginTransaction();
        $obj = new clsPmieducarServidor(
            $this->cod_servidor,
            null,
            $this->ref_idesco,
            $this->carga_horaria,
            null,
            null,
            0,
            $this->ref_cod_instituicao_original
        );

        $excluiu = $obj->excluir();

        if ($excluiu === false) {
            DB::rollBack();
            $this->mensagem = 'Exclusão não realizada.<br>';
            return false;
        }

        $this->excluiDisciplinas(null);
        $this->excluiFuncoes();
        $this->excluiFaltaAtraso();
        DB::commit();

        $this->mensagem = 'Exclusão efetuada com sucesso.<br>';
        $this->simpleRedirect('educar_servidor_lst.php');
    }

    public function addCamposCenso($obj)
    {
        $obj->tipo_ensino_medio_cursado = $this->tipo_ensino_medio_cursado;
        $obj->pos_graduacao = $this->pos_graduacao;
        $obj->curso_formacao_continuada = $this->curso_formacao_continuada;

        return $obj;
    }

    public function cadastraFuncoes()
    {
        $funcoes = Session::get("servant:{$this->cod_servidor}", []);
        $existe_funcao_professor = false;

        $listFuncoesCadastradas = [];

        if ($this->ref_cod_funcao) {
            foreach ($this->ref_cod_funcao as $k => $funcao) {
                list($funcao, $professor) = explode('-', $funcao);

                if ((bool) $professor) {
                    $existe_funcao_professor = true;
                }

                $cod_servidor_funcao = $this->cod_servidor_funcao[$k];
                $obj_servidor_funcao = new clsPmieducarServidorFuncao(null, null, null, null, $cod_servidor_funcao);

                if ($obj_servidor_funcao->existe()) {
                    $this->atualizaFuncao($obj_servidor_funcao, $funcao, $this->matricula[$k]);
                } else {
                    $cod_servidor_funcao = $this->cadastraFuncao($funcao, $this->matricula[$k]);

                    $funcoes[$cod_servidor_funcao] = $funcoes['new_' . $k];
                    unset($funcoes['new_' . $k]);
                }

                if (empty($cod_servidor_funcao)) {
                    $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $this->cod_servidor, $funcao);
                    $cod_servidor_funcao = $obj_servidor_funcao->detalhe()['cod_servidor_funcao'];
                }

                array_push($listFuncoesCadastradas, $cod_servidor_funcao);
            }
        }

        if (!$existe_funcao_professor) {
            $this->excluiDisciplinas(array_keys($funcoes));
            $this->excluiCursos();
        }

        $cursos_servidor = [];

        if ($existe_funcao_professor) {
            $this->excluiDisciplinas(array_keys($funcoes));

            foreach ($funcoes as $funcao => $cursos) {
                foreach ($cursos as $curso => $disciplinas) {
                    $cursos_servidor[] = $curso;

                    foreach ($disciplinas as $disciplina) {
                        $obj_servidor_disciplina = new clsPmieducarServidorDisciplina(
                            $disciplina,
                            $this->ref_cod_instituicao,
                            $this->cod_servidor,
                            $curso,
                            $funcao
                        );

                        if (!$obj_servidor_disciplina->existe()) {
                            $obj_servidor_disciplina->cadastra();
                        }
                    }
                }

                $cursos_servidor = array_unique($cursos_servidor);
            }

            if ($cursos_servidor) {
                $this->excluiCursos();

                foreach ($cursos_servidor as $curso) {
                    $obj_curso_servidor = new clsPmieducarServidorCursoMinistra($curso, $this->ref_cod_instituicao, $this->cod_servidor);

                    if (!$obj_curso_servidor->existe()) {
                        $det_curso_servidor = $obj_curso_servidor->cadastra();
                    }
                }
            }

            $funcoesRemovidas = $funcoes;

            foreach ($listFuncoesCadastradas as $funcao) {
                unset($funcoesRemovidas[$funcao]);
            }

            if (count($funcoesRemovidas) > 0) {
                $this->excluiDisciplinas(array_keys($funcoesRemovidas));
            }

            $this->excluiFuncoesRemovidas($listFuncoesCadastradas);
        }
    }

    public function excluiFuncoes()
    {
        $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $this->cod_servidor);
        $obj_servidor_funcao->excluirTodos();
    }

    public function excluiFaltaAtraso()
    {
        (new clsPmieducarFaltaAtraso())->excluiTodosPorServidor($this->cod_servidor);
    }

    public function excluiFuncoesRemovidas($funcoes)
    {
        $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $this->cod_servidor);
        $obj_servidor_funcao->excluirFuncoesRemovidas($funcoes);
    }

    public function atualizaFuncao($obj_servidor_funcao, $funcao, $matricula)
    {
        $obj_servidor_funcao->ref_cod_funcao = $funcao;
        $obj_servidor_funcao->matricula = $matricula;

        $obj_servidor_funcao->edita();
    }

    public function cadastraFuncao($funcao, $matricula)
    {
        $obj_servidor_funcao = new clsPmieducarServidorFuncao($this->ref_cod_instituicao, $this->cod_servidor, $funcao, $matricula);

        return $obj_servidor_funcao->cadastra();
    }

    public function excluiDisciplinas($funcao)
    {
        $obj_servidor_disciplina = new clsPmieducarServidorDisciplina(null, $this->ref_cod_instituicao, $this->cod_servidor);
        $obj_servidor_disciplina->excluirTodos($funcao);
    }

    public function excluiCursos()
    {
        $obj_servidor_curso = new clsPmieducarServidorCursoMinistra(null, $this->ref_cod_instituicao, $this->cod_servidor);
        $obj_servidor_curso->excluirTodos();
    }

    protected function createOrUpdateDeficiencias()
    {
        $servidorId = $this->cod_servidor;

        $sql = 'delete from cadastro.fisica_deficiencia where ref_idpes = $1';
        Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$servidorId]], false);

        foreach ($this->getRequest()->deficiencias as $id) {
            if (!empty($id)) {
                $deficiencia = new clsCadastroFisicaDeficiencia($servidorId, $id);
                $deficiencia->cadastra();
            }
        }
    }

    protected function createOrUpdateInep()
    {
        Portabilis_Utils_Database::fetchPreparedQuery('DELETE FROM modules.educacenso_cod_docente WHERE cod_servidor = $1', ['params' => [$this->cod_servidor]], false);
        if ($this->cod_docente_inep) {
            $sql = 'INSERT INTO modules.educacenso_cod_docente (cod_servidor,cod_docente_inep, fonte, created_at)
                                                  VALUES ($1, $2,\'U\', \'NOW()\')';
            Portabilis_Utils_Database::fetchPreparedQuery($sql, ['params' => [$this->cod_servidor, $this->cod_docente_inep]]);
        }
    }

    protected function addGraduationsTable()
    {
        $graduations = $this->fillEmployeeGraduations($this->cod_servidor);

        $rows = $this->getGraduateTableRows($graduations);

        $this->campoTabelaInicio(
            'graduations',
            'Curso(s) superior(es) concluído(s)',
            [
                'Curso',
                'Ano de conclusão',
                'Instituição de Educação Superior',
                'Área de conhecimento/Disciplina de formação',
            ],
            $rows
        );

        $this->inputsHelper()->simpleSearchCursoSuperior(null, ['required' => false], ['objectName' => 'employee_course']);
        $this->campoTexto('employee_completion_year', null, null, null, 4);
        $this->inputsHelper()->simpleSearchIes(null, ['required' => false], ['objectName' => 'employee_college']);
        $options = [
            'resources' => SelectOptions::employeeGraduationDisciplines(),
            'required' => false
        ];
        $this->inputsHelper()->select('employee_discipline_id', $options);

        $this->campoTabelaFim();
    }

    /**
     * @param $employeeId
     *
     * @return array|mixed
     */
    protected function fillEmployeeGraduations($employeeId)
    {
        $graduations = [];
        if (old('course_id')) {
            foreach (old('course_id') as $key => $value) {
                $oldInputGraduation = new EmployeeGraduation();
                $oldInputGraduation->course = old('employee_course')[$key];
                $oldInputGraduation->course_id = old('employee_course_id')[$key];
                $oldInputGraduation->completion_year = old('employee_completion_year')[$key];
                $oldInputGraduation->college = old('employee_college')[$key];
                $oldInputGraduation->college_id = old('employee_college_id')[$key];
                $oldInputGraduation->discipline_id = old('employee_discipline_id')[$key];
                $graduations[] = $oldInputGraduation;
            }

            return $graduations;
        }

        /** @var EmployeeGraduationService $employeeGraduationService */
        $employeeGraduationService = app(EmployeeGraduationService::class);
        $graduations = $employeeGraduationService->getEmployeeGraduations($employeeId);

        foreach ($graduations as $graduation) {
            $graduation->course = $this->getCourseName($graduation->course_id);
            $graduation->college = $this->getCollegeName($graduation->college_id);
        }

        return $graduations;
    }

    protected function getGraduateTableRows($graduations)
    {
        $rows = [];

        foreach ($graduations as $graduation) {
            $rows[] = [
                $graduation->course,
                $graduation->completion_year,
                $graduation->college,
                $graduation->discipline_id,
                $graduation->course_id,
                $graduation->college_id,
            ];
        }

        return $rows;
    }

    protected function storeGraduations($employeeId)
    {
        /** @var EmployeeGraduationService $employeeGraduationService */
        $employeeGraduationService = app(EmployeeGraduationService::class);

        $employeeGraduationService->deleteAll($employeeId);

        if (empty($this->ref_idesco)) {
            return true;
        }

        if (LegacySchoolingDegree::find($this->ref_idesco)->escolaridade != Escolaridade::EDUCACAO_SUPERIOR) {
            return true;
        }

        foreach ($this->employee_course_id as $key => $courseId) {
            if (empty($courseId)) {
                continue;
            }

            $valueObject = new EmployeeGraduationValueObject();
            $valueObject->employeeId = $employeeId;
            $valueObject->courseId = $this->employee_course_id[$key];
            $valueObject->completionYear = $this->employee_completion_year[$key];
            $valueObject->collegeId = $this->employee_college_id[$key];
            $valueObject->disciplineId = $this->employee_discipline_id[$key] ?: null;
            $employeeGraduationService->storeGraduation($valueObject);
        }
    }

    protected function getCourseName($courseId)
    {
        $academicLevels = [
            1 => 'Tecnológico',
            2 => 'Licenciatura',
            3 => 'Bacharelado',
        ];

        $course = DB::table('modules.educacenso_curso_superior')->where('id', $courseId)->get(['nome', 'curso_id', 'grau_academico'])->first();

        return $course->curso_id . ' - ' . $course->nome . ' / ' . ($academicLevels[$course->grau_academico] ?? '');
    }

    protected function getCollegeName($collegeId)
    {
        $college = DB::table('modules.educacenso_ies')->where('id', $collegeId)->get(['nome', 'ies_id'])->first();

        return $college->ies_id . ' - ' . $college->nome;
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-servidor-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Servidores - Servidor';
        $this->processoAp = 635;
    }
};
