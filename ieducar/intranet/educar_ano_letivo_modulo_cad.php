<?php

use App\Exceptions\AcademicYearServiceException;
use App\Models\LegacyAcademicYearStage;
use App\Models\LegacySchool;
use App\Models\LegacySchoolAcademicYear;
use App\Models\LegacyStageType;
use App\Services\AcademicYearService;

return new class extends clsCadastro
{
    public $pessoa_logada;

    public $ref_cod_instituicao;

    public $ref_ano;

    public $ref_ref_cod_escola;

    public $sequencial;

    public $ref_cod_modulo;

    public $data_inicio;

    public $data_fim;

    public $ano_letivo_modulo;

    public $modulos = [];

    public $etapas = [];

    public $copiar_alocacoes_e_vinculos_professores;

    public $copiar_alocacoes_demais_servidores;

    public $copiar_turmas;

    private $isAdmin;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $this->ref_cod_modulo = $_GET['ref_cod_modulo'];
        $this->ref_ref_cod_escola = $_GET['ref_cod_escola'];
        $this->ref_ano = $_GET['ano'];

        $obj_permissoes = new clsPermissoes;

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        if (is_numeric(value: $this->ref_ano) && is_numeric(value: $this->ref_ref_cod_escola)) {
            $schoolAcademicYear = LegacySchoolAcademicYear::query()->where(
                column: [
                    'ref_cod_escola' => $this->ref_ref_cod_escola,
                    'ano' => $this->ref_ano,
                ]
            )->first();

            if ($schoolAcademicYear) {
                if ($obj_permissoes->permissao_excluir(int_processo_ap: 561, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 7)) {
                    $this->fexcluir = true;
                }

                $this->copiar_alocacoes_e_vinculos_professores = $schoolAcademicYear->copia_dados_professor;
                $this->copiar_alocacoes_demais_servidores = $schoolAcademicYear->copia_dados_demais_servidores;
                $this->copiar_turmas = $schoolAcademicYear->copia_turmas;

                $retorno = 'Editar';

                $this->etapas = LegacyAcademicYearStage::query()->where('ref_ano', $this->ref_ano)->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->orderBy('sequencial')->get();
                $this->ref_cod_modulo = $this->etapas->first()?->ref_cod_modulo;
            }
        }

        $this->url_cancelar = $_GET['referrer']
            ? $_GET['referrer'] . '?cod_escola=' . $this->ref_ref_cod_escola
            : 'educar_escola_lst.php';

        $this->breadcrumb(currentPage: 'Etapas do ano letivo', breadcrumbs: [
            url(path: 'intranet/educar_index.php') => 'Escola',
        ]);

        $this->nome_url_cancelar = 'Cancelar';

        return $retorno;
    }

    public function Gerar()
    {
        if ($_POST) {
            foreach ($_POST as $campo => $val) {
                $this->$campo = $this->$campo ? $this->$campo : $val;
            }
        }

        // Primary keys
        $this->campoOculto(nome: 'ref_ano', valor: $this->ref_ano);
        $this->campoOculto(nome: 'ref_ref_cod_escola', valor: $this->ref_ref_cod_escola);

        $this->ref_cod_instituicao = LegacySchool::query()->where('cod_escola', $this->ref_ref_cod_escola)->value('ref_cod_instituicao');

        $registros = LegacyAcademicYearStage::query()->where('ref_ano', $this->ref_ano - 1)->where('ref_ref_cod_escola', $this->ref_ref_cod_escola)->orderBy('sequencial')->get();

        $cont = 0;

        if ($registros->isNotEmpty()) {
            $cor = '';
            $tabela = '<table border=0 style=\'\' cellpadding=2 width=\'100%\'>';
            $tabela .= "<tr bgcolor=$cor><td colspan='2'>Etapas do ano anterior (".($this->ref_ano - 1).')</td></tr><tr><td>';
            $tabela .= '<table cellpadding="2" cellspacing="2" border="0" align="left" width=\'300px\'>';
            $tabela .= '<tr bgcolor=\'#ccdce6\'><th width=\'100px\'>Etapa<a name=\'ano_letivo\'/></th><th width=\'200px\'>Período</th></tr>';

            $existeBissexto = false;

            foreach ($registros as $campo) {
                $cor = '#f5f9fd';
                $cont++;
                $tabela .= "<tr bgcolor='$cor'><td align='center'>{$cont}</td><td align='center'>".dataFromPgToBr(data_original: $campo['data_inicio']).' à '.dataFromPgToBr(data_original: $campo['data_fim']).'</td></tr>';

                $ano = date_parse_from_format(format: 'Y-m-d', datetime: $campo['data_inicio']);
                $ano = $ano['year'];

                $novaDataInicio = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_inicio']);
                $novaDataFim = str_replace(search: $ano, replace: $this->ref_ano, subject: $campo['data_fim']);

                if (
                    Portabilis_Date_Utils::checkDateBissexto(data: $novaDataInicio)
                    || Portabilis_Date_Utils::checkDateBissexto(data: $novaDataFim)
                ) {
                    $existeBissexto = true;
                }
            }

            if ($existeBissexto) {
                $tabela .= "<tr bgcolor='#FCF8E3' style='color: #8A6D3B; font-weight:normal;'>
                    <td align='center'><b>Observação:</b></td>
                    <td align='center'>A data 29/02/$this->ref_ano não poderá ser migrada pois $this->ref_ano não é um ano bissexto, portanto será substituída por 28/02/$this->ref_ano.</td>
                    </tr>";
            }

            $tabela .= '</table>';
            $tabela .= "<tr><td colspan='2'><b> Adicione as etapas abaixo para {$this->ref_ano} semelhante ao exemplo do ano anterior: </b></td></tr><tr><td>";
            $tabela .= '</table>';
        }

        $ref_ano_ = $this->ref_ano;

        $this->campoTexto(
            nome: 'ref_ano_',
            campo: 'Ano',
            valor: $ref_ano_,
            tamanhovisivel: 4,
            tamanhomaximo: 4,
            evento: '',
            disabled: true
        );

        $opcoesCampoModulo = [];

        $lista = LegacyStageType::query()
            ->where('ativo', 1)
            ->where('ref_cod_instituicao', $this->ref_cod_instituicao)
            ->orderBy('nm_tipo')
            ->get()
            ->toArray();

        if (is_array(value: $lista) && count(value: $lista)) {
            $this->modulos = $lista;

            foreach ($lista as $registro) {
                $opcoesCampoModulo[$registro['cod_modulo']] = sprintf('%s - %d etapa(s)', $registro['nm_tipo'], $registro['num_etapas']);
            }
        }

        $this->campoLista(
            nome: 'ref_cod_modulo',
            campo: 'Etapa',
            valor: $opcoesCampoModulo,
            default: \Request::get('ref_cod_modulo', $this->ref_cod_modulo),
            acao: null,
            duplo: null,
            descricao: null,
            complemento: null,
            desabilitado: null,
        );

        if ($tabela) {
            $this->campoQuebra();
            $this->campoRotulo(nome: 'modulosAnoAnterior', campo: '-', valor: $tabela);
        }

        $this->campoQuebra();

        if (is_numeric(value: $this->ref_ano) && is_numeric(value: $this->ref_ref_cod_escola) && !$_POST) {
            $qtd_registros = 0;

            if (Request::has('data_inicio')) {
                foreach (Request::get('data_inicio') as $key => $campo) {
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('data_inicio')[$key] ?? null;
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('data_fim')[$key] ?? null;
                    $this->ano_letivo_modulo[$qtd_registros][] = \Request::get('dias_letivos')[$key] ?? null;
                    $qtd_registros++;
                }
            } else {
                foreach ($this->etapas as $campo) {
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_inicio']);
                    $this->ano_letivo_modulo[$qtd_registros][] = dataFromPgToBr(data_original: $campo['data_fim']);
                    $this->ano_letivo_modulo[$qtd_registros][] = $campo['dias_letivos'];
                    $qtd_registros++;
                }
            }

            $this->campoTabelaInicio(
                nome: 'modulos_ano_letivo',
                titulo: 'Etapas do ano letivo',
                arr_campos: ['Data inicial', 'Data final', 'Dias Letivos'],
                arr_valores: $this->ano_letivo_modulo
            );

            $this->campoData(nome: 'data_inicio', campo: 'Hora', valor: $this->data_inicio, obrigatorio: true);
            $this->campoData(nome: 'data_fim', campo: 'Hora', valor: $this->data_fim, obrigatorio: true);
            $this->campoNumero(nome: 'dias_letivos', campo: 'Dias Letivos', valor: $this->dias_letivos, tamanhovisivel: 6, tamanhomaximo: 3, obrigatorio: false);

            $this->campoTabelaFim();

            $this->campoQuebra();

            $this->campoRotulo(
                nome: 'titulo-alocacoes-vinculos',
                campo: 'Alocações e vínculos',
                separador: null
            );

            $this->campoRotulo(
                nome: 'informativo1-alocacoes-vinculos',
                campo: '
                    Ao definir um novo ano letivo, o i-Educar copia automaticamente as turmas do ano anterior. <br>
                    Gostaria de copiar também as alocações e vínculos?
                ',
                separador: null
            );

            $checkedProfessores = ($this->copiar_alocacoes_e_vinculos_professores) ? 'checked' : '';
            $checkedDemaisServidores = ($this->copiar_alocacoes_demais_servidores) ? 'checked' : '';
            $checkedTurmas = ($this->copiar_turmas || $this->tipoacao == 'Novo') ? 'checked' : '';

            $isAdmin = $this->getIsAdmin();

            if ($isAdmin) {
                $this->campoRotulo(
                    nome: 'copiar_turmas_',
                    campo: '
                        <input type="checkbox" '.$checkedTurmas.' id="copiar_turmas" name="copiar_turmas">
                        <label for="copiar_turmas">Copiar turmas do ano anterior</label>
                    ',
                    separador: null
                );
            }

            $disabledProfessores = '';
            $disabledServidores = '';

            if ($this->tipoacao == 'Novo') {
                // Se não é admin, "Copiar turmas" é sempre true, então os checkboxes devem estar habilitados
                if ($isAdmin && !$checkedTurmas) {
                    $disabledProfessores = 'disabled';
                    $disabledServidores = 'disabled';
                }
            }

            $this->campoRotulo(
                nome: 'copiar_alocacoes_e_vinculos_professores_',
                campo: '
                    <input type="checkbox" '.$checkedProfessores.' '.$disabledProfessores.' id="copiar_alocacoes_e_vinculos_professores" name="copiar_alocacoes_e_vinculos_professores">
                    <label for="copiar_alocacoes_e_vinculos_professores">Copiar alocações e vínculos dos professores</label>
                ',
                separador: null
            );

            $this->campoRotulo(
                nome: 'copiar_alocacoes_demais_servidores_',
                campo: '
                    <input type="checkbox" '.$checkedDemaisServidores.' '.$disabledServidores.' id="copiar_alocacoes_demais_servidores" name="copiar_alocacoes_demais_servidores">
                    <label for="copiar_alocacoes_demais_servidores">Copiar alocações dos demais servidores</label>
                ',
                separador: null
            );

            $this->campoRotulo(
                nome: 'informativo2-alocacoes-vinculos',
                campo: 'As alocações e vínculos podem depois ser editadas e excluídas, caso necessário',
                separador: null
            );

            $this->campoRotulo(
                nome: 'atencao',
                campo: 'Atenção ao utilizar o recurso de cópia:',
                descricao: '
                    <ul>
                        <li>As alocações e vínculos copiados poderão ser editados ou excluídos posteriormente, se necessário;</li>
                        <li>Todos os campos de data dos novos registros serão copiados em branco e deverão ser ajustados manualmente após a criação.</li>
                    </ul>',
                separador: null
            );
        }

        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: [
            '/vendor/legacy/Portabilis/Assets/Javascripts/Validator.js',
            '/intranet/scripts/etapas.js',
        ]);

        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/EscolaAnosLetivos.css'];

        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this,
            files: ['/vendor/legacy/Cadastro/Assets/Stylesheets/AnoLetivoModulo.css']);

        if ($isAdmin) {
            $this->campoRotulo(
                nome: 'script_alocacoes',
                campo: '
                    <script>
                        (function ($) {
                            $(document).ready(function () {
                                const copiarTurmasCheckbox = $("#copiar_turmas");
                                const copiarProfessoresCheckbox = $("#copiar_alocacoes_e_vinculos_professores");
                                const copiarServidoresCheckbox = $("#copiar_alocacoes_demais_servidores");
                                
                                function toggleAlocacoesFields() {
                                    const isTurmasChecked = copiarTurmasCheckbox.is(":checked");
                                    
                                    // Apenas o checkbox de professores depende de "Copiar turmas"
                                    copiarProfessoresCheckbox.prop("disabled", !isTurmasChecked);
                                    
                                    // O checkbox de servidores é independente
                                    copiarServidoresCheckbox.prop("disabled", false);
                                    
                                    if (!isTurmasChecked) {
                                        copiarProfessoresCheckbox.prop("checked", false);
                                        // Não desmarca o checkbox de servidores
                                    }
                                }
                                
                                if (copiarTurmasCheckbox.length) {
                                    copiarTurmasCheckbox.on("change", toggleAlocacoesFields);
                                    toggleAlocacoesFields();
                                }
                            });
                        })(jQuery);
                    </script>
                ',
                separador: null
            );
        }
    }

    public function Novo()
    {
        $obj_permissoes = new clsPermissoes;

        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        try {
            app(AcademicYearService::class)->validateAcademicYearDates(
                startDates: $this->data_inicio,
                endDates: $this->data_fim,
                year: $this->ref_ano,
                schoolId: $this->ref_ref_cod_escola
            );
        } catch (AcademicYearServiceException $e) {
            $_POST = [];
            $this->Inicializar();
            $this->mensagem = $e->getMessage();

            return false;
        }

        $this->copiar_alocacoes_e_vinculos_professores = !is_null(value: $this->copiar_alocacoes_e_vinculos_professores);
        $this->copiar_alocacoes_demais_servidores = !is_null(value: $this->copiar_alocacoes_demais_servidores);

        $isAdmin = $this->getIsAdmin();

        $this->copiar_turmas = $isAdmin ? !is_null(value: $this->copiar_turmas) : true;

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            try {
                app(AcademicYearService::class)->createAcademicYearForSchool(
                    schoolId: $this->ref_ref_cod_escola,
                    year: $this->ref_ano,
                    startDates: $this->data_inicio,
                    endDates: $this->data_fim,
                    schoolDays: $this->dias_letivos,
                    moduleId: $this->ref_cod_modulo,
                    copySchoolClasses: $this->copiar_turmas,
                    copyTeacherData: $this->copiar_alocacoes_e_vinculos_professores,
                    copyEmployeeData: $this->copiar_alocacoes_demais_servidores,
                    userId: $this->pessoa_logada
                );

                $this->mensagem = 'Cadastro efetuado com sucesso.<br />';
                $this->simpleRedirect(url: 'educar_escola_det.php?cod_escola=' . $this->ref_ref_cod_escola . '#ano_letivo');
            } catch (AcademicYearServiceException $e) {
                $_POST = [];
                $this->Inicializar();
                $this->mensagem = $e->getMessage();

                return false;
            }
        }

        $this->mensagem = 'Cadastro não realizado.<br />';

        return false;
    }

    public function Editar()
    {
        $obj_permissoes = new clsPermissoes;
        $obj_permissoes->permissao_cadastra(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        try {
            app(AcademicYearService::class)->validateAcademicYearDates(
                startDates: $this->data_inicio,
                endDates: $this->data_fim,
                year: $this->ref_ano,
                schoolId: $this->ref_ref_cod_escola
            );
        } catch (AcademicYearServiceException $e) {
            $_POST = [];
            $this->Inicializar();
            $this->mensagem = $e->getMessage();

            return false;
        }

        if ($this->ref_cod_modulo && $this->data_inicio && $this->data_fim) {
            try {
                $academicYearService = app(AcademicYearService::class);
                $academicYearService->validateAcademicYearModules(
                    year: $this->ref_ano,
                    schoolId: $this->ref_ref_cod_escola,
                    stagesCount: count($this->data_inicio)
                );

                $academicYearService->updateAcademicYearStages(
                    schoolId: $this->ref_ref_cod_escola,
                    year: $this->ref_ano,
                    startDates: $this->data_inicio,
                    endDates: $this->data_fim,
                    schoolDays: $this->dias_letivos,
                    moduleId: $this->ref_cod_modulo
                );

                $this->mensagem = 'Edição efetuada com sucesso.<br />';
                $this->simpleRedirect(url: 'educar_escola_lst.php');
            } catch (AcademicYearServiceException $e) {
                $_POST = [];
                $this->Inicializar();
                $this->mensagem = $e->getMessage();

                return false;
            }
        }

        echo '<script>alert(\'É necessário adicionar pelo menos uma etapa!\')</script>';
        $this->mensagem = 'Edição não realizada.<br />';

        return false;
    }

    public function Excluir()
    {
        $obj_permissoes = new clsPermissoes;

        $obj_permissoes->permissao_excluir(
            int_processo_ap: 561,
            int_idpes_usuario: $this->pessoa_logada,
            int_soma_nivel_acesso: 7,
            str_pagina_redirecionar: 'educar_escola_lst.php'
        );

        try {
            app(AcademicYearService::class)->deleteAcademicYear(
                schoolId: $this->ref_ref_cod_escola,
                year: $this->ref_ano,
                userId: $this->pessoa_logada
            );

            if ($excluiu) {
                $this->mensagem = 'Exclusão efetuada com sucesso.<br />';
                $this->simpleRedirect(url: 'educar_escola_lst.php');

                return true;
            }

            $this->mensagem = 'Exclusão não realizada.<br />';

            return false;
        } catch (AcademicYearServiceException $e) {
            $this->mensagem = 'Erro na exclusão: ' . $e->getMessage();

            return false;
        }
    }

    private function getIsAdmin(): bool
    {
        if ($this->isAdmin === null) {
            $this->isAdmin = Auth::user() ? Auth::user()->isAdmin() : false;
        }

        return $this->isAdmin;
    }

    public function gerarJsonDosModulos()
    {
        $retorno = [];

        foreach ($this->modulos as $modulo) {
            $retorno[$modulo['cod_modulo']] = [
                'label' => $modulo['nm_tipo'],
                'etapas' => (int) $modulo['num_etapas'],
            ];
        }

        return json_encode(value: $retorno);
    }

    public function makeExtra()
    {
        return str_replace(
            search: '#modulos',
            replace: $this->gerarJsonDosModulos(),
            subject: file_get_contents(filename: __DIR__ . '/scripts/extra/educar-ano-letivo-modulo-cad.js')
        );
    }

    public function Formular()
    {
        $this->title = 'Ano Letivo Etapa';
        $this->processoAp = 561;
    }
};
