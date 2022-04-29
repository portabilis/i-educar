<?php

use App\Menu;
use App\Models\State;

return new class extends clsCadastro {
    public $cod_instituicao;
    public $ref_usuario_exc;
    public $ref_usuario_cad;
    public $ref_idtlog;
    public $ref_sigla_uf;
    public $cep;
    public $cidade;
    public $bairro;
    public $logradouro;
    public $numero;
    public $complemento;
    public $nm_responsavel;
    public $ddd_telefone;
    public $telefone;
    public $data_cadastro;
    public $data_exclusao;
    public $ativo;
    public $nm_instituicao;
    public $data_base_transferencia;
    public $data_base_remanejamento;
    public $exigir_vinculo_turma_professor;
    public $exigir_dados_socioeconomicos;
    public $controlar_espaco_utilizacao_aluno;
    public $percentagem_maxima_ocupacao_salas;
    public $quantidade_alunos_metro_quadrado;
    public $gerar_historico_transferencia;
    public $controlar_posicao_historicos;
    public $matricula_apenas_bairro_escola;
    public $restringir_historico_escolar;
    public $restringir_multiplas_enturmacoes;
    public $permissao_filtro_abandono_transferencia;
    public $multiplas_reserva_vaga;
    public $permitir_carga_horaria;
    public $reserva_integral_somente_com_renda;
    public $data_base_matricula;
    public $data_expiracao_reserva_vaga;
    public $data_fechamento;
    public $componente_curricular_turma;
    public $reprova_dependencia_ano_concluinte;
    public $bloqueia_matricula_serie_nao_seguinte;
    public $data_educacenso;
    public $altera_atestado_para_declaracao;
    public $obrigar_campos_censo;
    public $obrigar_documento_pessoa;
    public $orgao_regional;
    public $exigir_lancamentos_anteriores;
    public $exibir_apenas_professores_alocados;
    public $bloquear_vinculo_professor_sem_alocacao_escola;
    public $permitir_matricula_fora_periodo_letivo;
    public $ordenar_alunos_sequencial_enturmacao;
    public $obrigar_telefone_pessoa;

    public function Inicializar()
    {
        $retorno = 'Novo';

        $obj_permissoes = new clsPermissoes();

        $obj_permissoes->permissao_cadastra(559, $this->pessoa_logada, 3, 'educar_instituicao_lst.php');

        $this->cod_instituicao = $this->getQueryString('cod_instituicao');

        if (is_numeric($this->cod_instituicao)) {
            $obj = new clsPmieducarInstituicao($this->cod_instituicao);
            $registro = $obj->detalhe();
            if ($registro) {
                foreach ($registro as $campo => $val) {  // passa todos os valores obtidos no registro para atributos do objeto
                    $this->$campo = $val;
                }
                $this->data_cadastro = dataFromPgToBr($this->data_cadastro);
                $this->data_exclusao = dataFromPgToBr($this->data_exclusao);

                $this->fexcluir = $obj_permissoes->permissao_excluir(559, $this->pessoa_logada, 3);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_instituicao_det.php?cod_instituicao={$registro['cod_instituicao']}" : 'educar_instituicao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb('Instituição', ['educar_index.php' => 'Escola']);

        $this->gerar_historico_transferencia = dbBool($this->gerar_historico_transferencia);
        $this->controlar_posicao_historicos = dbBool($this->controlar_posicao_historicos);
        $this->matricula_apenas_bairro_escola = dbBool($this->matricula_apenas_bairro_escola);
        $this->restringir_historico_escolar = dbBool($this->restringir_historico_escolar);
        $this->restringir_multiplas_enturmacoes = dbBool($this->restringir_multiplas_enturmacoes);
        $this->permissao_filtro_abandono_transferencia = dbBool($this->permissao_filtro_abandono_transferencia);
        $this->multiplas_reserva_vaga = dbBool($this->multiplas_reserva_vaga);
        $this->permitir_carga_horaria = dbBool($this->permitir_carga_horaria);
        $this->componente_curricular_turma = dbBool($this->componente_curricular_turma);
        $this->reprova_dependencia_ano_concluinte = dbBool($this->reprova_dependencia_ano_concluinte);
        $this->reserva_integral_somente_com_renda = dbBool($this->reserva_integral_somente_com_renda);
        $this->bloqueia_matricula_serie_nao_seguinte = dbBool($this->bloqueia_matricula_serie_nao_seguinte);
        $this->exigir_dados_socioeconomicos = dbBool($this->exigir_dados_socioeconomicos);
        $this->altera_atestado_para_declaracao = dbBool($this->altera_atestado_para_declaracao);
        $this->obrigar_campos_censo = dbBool($this->obrigar_campos_censo);
        $this->obrigar_documento_pessoa = dbBool($this->obrigar_documento_pessoa);
        $this->exigir_lancamentos_anteriores = dbBool($this->exigir_lancamentos_anteriores);
        $this->exibir_apenas_professores_alocados = dbBool($this->exibir_apenas_professores_alocados);
        $this->bloquear_vinculo_professor_sem_alocacao_escola = dbBool($this->bloquear_vinculo_professor_sem_alocacao_escola);
        $this->permitir_matricula_fora_periodo_letivo = dbBool($this->permitir_matricula_fora_periodo_letivo);
        $this->ordenar_alunos_sequencial_enturmacao = dbBool($this->ordenar_alunos_sequencial_enturmacao);
        $this->obrigar_telefone_pessoa = dbBool($this->obrigar_telefone_pessoa);

        return $retorno;
    }

    public function Gerar()
    {
        // primary keys
        $this->campoOculto('cod_instituicao', $this->cod_instituicao);

        // text
        $this->campoTexto('nm_instituicao', 'Nome da Instituição', $this->nm_instituicao, 30, 255, true);
        $this->campoCep('cep', 'CEP', int2CEP($this->cep), true, '-', false, false);
        $this->campoTexto('logradouro', 'Logradouro', $this->logradouro, 30, 255, true);
        $this->campoNumero('numero', 'Número', $this->numero, 6, 6);
        $this->campoTexto('complemento', 'Complemento', $this->complemento, 30, 50, false);
        $this->campoTexto('bairro', 'Bairro', $this->bairro, 30, 40, true);
        $this->campoTexto('cidade', 'Cidade', $this->cidade, 30, 60, true);

        // foreign keys
        $opcoes = ['' => 'Selecione'] + State::getListKeyAbbreviation()->toArray();

        $this->campoLista('ref_sigla_uf', 'UF', $opcoes, $this->ref_sigla_uf, '', false, '', '', false, true);

        $this->campoTexto('nm_responsavel', 'Nome do Responsável', $this->nm_responsavel, 30, 255, true);
        $this->campoNumero('ddd_telefone', 'DDD Telefone', $this->ddd_telefone, 2, 2);
        $this->campoNumero('telefone', 'Telefone', $this->telefone, 11, 11);

        $options = [
            'label' => 'Coordenador(a) de transporte',
            'size' => 50,
            'value' => $this->coordenador_transporte,
            'required' => false
        ];

        $this->inputsHelper()->simpleSearchPessoa('coordenador_transporte', $options);

        if (!empty($this->ref_sigla_uf)) {
            $opcoes = [null => 'Selecione'];
            $orgaoRegional = new Educacenso_Model_OrgaoRegionalDataMapper();
            $orgaosRegionais = $orgaoRegional->findAll(
                ['sigla_uf', 'codigo'],
                ['sigla_uf' => $this->ref_sigla_uf],
                ['codigo' => 'asc'],
                false
            );
            foreach ($orgaosRegionais as $orgaoRegional) {
                $opcoes[$orgaoRegional->codigo] = $orgaoRegional->codigo;
            }
        } else {
            $opcoes = [null => 'Informe uma UF'];
        }

        $options = ['label' => 'Código do órgão regional de ensino', 'resources' => $opcoes, 'value' => $this->orgao_regional, 'required' => false, 'size' => 70,];
        $this->inputsHelper()->select('orgao_regional', $options);

        $this->campoRotulo('gerais', '<b>Gerais</b>');
        $this->campoCheck('obrigar_documento_pessoa', 'Exigir documento (RG, CPF ou Certidão de nascimento / casamento) no cadastro pessoa / aluno', $this->obrigar_documento_pessoa);

        $this->campoRotulo('datas', '<b>Datas</b>');
        $dataBaseDeslocamento = 'A ordenação/apresentação de alunos transferidos nos relatórios (ex.: Relação de alunos por turma) será baseada neste campo quando preenchido.';
        $this->inputsHelper()->date(
            'data_base_transferencia',
            [
                'label' => 'Data máxima para deslocamento',
                'required' => false,
                'hint' => $dataBaseDeslocamento,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_base_transferencia)
            ]
        );
        $dataBaseRemanejamento = 'A ordenação/apresentação de alunos remanejados nas turmas, nos relatórios (ex.: Relação de alunos por turma), será baseada neste campo quando preenchido.';
        $this->inputsHelper()->date(
            'data_base_remanejamento',
            [
                'label' => 'Data máxima para troca de sala',
                'required' => false,
                'hint' => $dataBaseRemanejamento,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_base_remanejamento)
            ]
        );
        $dataBase = 'Caso o campo seja preenchido, o sistema irá controlar distorção de idade/série e limitar inscrições por idade no Pré-matrícula com base na data informada.';
        $this->inputsHelper()->dateDiaMes(
            'data_base',
            [
                'label' => 'Data base para matrícula (dia/mês)',
                'size' => 5,
                'max_length' => 5,
                'placeholder' => 'dd/mm',
                'required' => false,
                'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_base_matricula),
                'hint' => $dataBase
            ]
        );
        $dataExpiracaoReservaVaga = 'Caso o campo seja preenchido, o sistema irá indeferir automaticamente as reservas em situação de espera após a data informada.';
        $this->inputsHelper()->date(
            'data_expiracao_reserva_vaga',
            [
                'label' => 'Data para indeferimento automático da reserva de vaga',
                'required' => false,
                'hint' => $dataExpiracaoReservaVaga,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_expiracao_reserva_vaga)
            ]
        );
        $dataFechamento = 'Caso o campo seja preenchido, o sistema irá bloquear a matrícula de novos alunos nas turmas após a data informada.';
        $this->inputsHelper()->dateDiaMes(
            'data_fechamento',
            [
                'label' => 'Data de fechamento das turmas para matrícula',
                'size' => 5,
                'max_length' => 5,
                'placeholder' => 'dd/mm',
                'required' => false,
                'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_fechamento),
                'hint' => $dataFechamento
            ]
        );
        $dataEducacenso = 'Este campo deve ser preenchido com a data máxima das matrículas que devem ser enviadas para o Censo.';
        $this->inputsHelper()->date(
            'data_educacenso',
            [
                'label' => 'Data de referência do Educacenso',
                'required' => false,
                'hint' => $dataEducacenso,
                'placeholder' => 'dd/mm/yyyy',
                'value' => $this->data_educacenso
            ]
        );

        $this->campoRotulo('historicos', '<b>Históricos</b>');
        $this->campoCheck('gerar_historico_transferencia', 'Gerar histórico de transferência ao transferir matrícula?', $this->gerar_historico_transferencia);
        $this->campoCheck('controlar_posicao_historicos', 'Permitir controlar posicionamento dos históricos em seu respectivo documento', $this->controlar_posicao_historicos);
        $this->campoCheck('restringir_historico_escolar', 'Restringir modificações de históricos escolares?', $this->restringir_historico_escolar, null, false, false, false, 'Com esta opção selecionada, somente será possível cadastrar/editar históricos escolares de alunos que pertençam a mesma escola do funcionário.');
        $this->campoCheck('permitir_carga_horaria', 'Não permitir definir C.H. por componente no histórico escolar', $this->permitir_carga_horaria, null, false, false, false, 'Caso a opção estiver habilitada, não será possivel adicionar carga horária na tabela de disciplinas do histórico do aluno.');

        $this->campoRotulo('reserva_vaga', '<b>Reserva de vaga</b>');
        $this->multiplas_reserva_vaga = isset($this->cod_instituicao) ? dbBool($this->multiplas_reserva_vaga) : true;
        $this->campoCheck('multiplas_reserva_vaga', 'Permitir múltiplas reservas de vagas para o mesmo candidato em escolas diferentes', $this->multiplas_reserva_vaga);
        $this->campoCheck('reserva_integral_somente_com_renda', 'Permitir reserva de vaga para o turno integral somente quando a renda for informada', $this->reserva_integral_somente_com_renda);
        $this->campoCheck('exigir_dados_socioeconomicos', 'Exigir dados socioeconômicos na reserva de vaga para turno integral', $this->exigir_dados_socioeconomicos);

        $this->campoRotulo('relatorios', '<b>Relatórios</b>');
        $this->campoCheck('permissao_filtro_abandono_transferencia', 'Não permitir a apresentação de alunos com matrícula em abandono ou transferida na emissão do relatório de frequência', $this->permissao_filtro_abandono_transferencia);
        $this->campoCheck('altera_atestado_para_declaracao', 'Alterar nome do título do menu e relatórios de Atestado para Declaração', $this->altera_atestado_para_declaracao);
        $this->campoCheck('exibir_apenas_professores_alocados', 'Exibir apenas professores alocados nos filtros de emissão do Diário de classe', $this->exibir_apenas_professores_alocados);
        $this->campoCheck(
            'ordenar_alunos_sequencial_enturmacao',
            'Apresentar alunos em relatórios de acordo com a ordenação definida de forma automática/manual na turma',
            $this->ordenar_alunos_sequencial_enturmacao,
            null,
            false,
            false,
            false
        );

        $this->campoRotulo('processos_escolares', '<b>Processos escolares</b>');
        $this->campoCheck('exigir_vinculo_turma_professor', 'Exigir vínculo com turma para lançamento de notas do professor?', $this->exigir_vinculo_turma_professor);

        $this->campoCheck('matricula_apenas_bairro_escola', 'Permitir matrícula de alunos apenas do bairro da escola?', $this->matricula_apenas_bairro_escola);

        $this->campoCheck('controlar_espaco_utilizacao_aluno', 'Controlar espaço utilizado pelo aluno?', $this->controlar_espaco_utilizacao_aluno);
        $this->campoMonetario(
            'percentagem_maxima_ocupacao_salas',
            'Percentagem máxima de ocupação da sala',
            Portabilis_Currency_Utils::moedaUsToBr($this->percentagem_maxima_ocupacao_salas),
            6,
            6,
            false
        );
        $this->campoNumero('quantidade_alunos_metro_quadrado', 'Quantidade máxima de alunos permitidos por metro quadrado', $this->quantidade_alunos_metro_quadrado, 6, 6);

        $this->campoCheck('restringir_multiplas_enturmacoes', 'Não permitir múltiplas enturmações para o aluno no mesmo curso e série/ano', $this->restringir_multiplas_enturmacoes);

        $this->permitir_carga_horaria = isset($this->cod_instituicao) ? dbBool($this->permitir_carga_horaria) : true;

        $this->campoCheck(
            'componente_curricular_turma',
            'Permitir definir componentes curriculares diferenciados nas turmas',
            $this->componente_curricular_turma
        );

        $this->campoCheck(
            'reprova_dependencia_ano_concluinte',
            'Não permitir dependência em séries/anos concluintes',
            $this->reprova_dependencia_ano_concluinte,
            null,
            false,
            false,
            false,
            'Caso marcado, o aluno que reprovar em algum componente em ano concluinte será automaticamente reprovado.'
        );

        $this->campoCheck('bloqueia_matricula_serie_nao_seguinte', 'Não permitir matrículas que não respeitem a sequência de enturmação', $this->bloqueia_matricula_serie_nao_seguinte);

        $this->campoCheck('obrigar_campos_censo', 'Obrigar e validar o preenchimento dos campos exigidos pelo Censo escolar', $this->obrigar_campos_censo);

        $this->campoCheck(
            'exigir_lancamentos_anteriores',
            'Exigir o lançamento de notas em etapas que o aluno não estava enturmado',
            $this->exigir_lancamentos_anteriores
        );

        $this->campoCheck(
            'bloquear_vinculo_professor_sem_alocacao_escola',
            'Bloquear vínculos de professores em turmas pertencentes às escolas em que eles não estão alocados',
            $this->bloquear_vinculo_professor_sem_alocacao_escola,
            null,
            false,
            false,
            false,
            'Caso marcado, os vínculos de professores em turmas pertencentes às escolas em que eles não estão alocados será bloqueado.'
        );

        $this->campoCheck(
            'permitir_matricula_fora_periodo_letivo',
            'Permitir matricular alunos fora do período letivo',
            $this->permitir_matricula_fora_periodo_letivo,
            null,
            false,
            false,
            false
        );

        $this->campoCheck(
            'obrigar_telefone_pessoa',
            'Obrigar o preenchimento de um telefone no cadastro de pessoa física',
            $this->obrigar_telefone_pessoa,
            null,
            false,
            false,
            false
        );

        $scripts = ['/modules/Cadastro/Assets/Javascripts/Instituicao.js'];
        Portabilis_View_Helper_Application::loadJavascript($this, $scripts);
        $styles = ['/modules/Cadastro/Assets/Stylesheets/Instituicao.css'];
        Portabilis_View_Helper_Application::loadStylesheet($this, $styles);
    }

    public function Novo()
    {
        $this->simpleRedirect('educar_instituicao_lst.php');
    }

    public function Editar()
    {
        $obj = new clsPmieducarInstituicao(
            $this->cod_instituicao,
            $this->ref_usuario_exc,
            $this->pessoa_logada,
            $this->ref_idtlog,
            $this->ref_sigla_uf,
            str_replace('-', '', $this->cep),
            $this->cidade,
            $this->bairro,
            $this->logradouro,
            $this->numero,
            $this->complemento,
            $this->nm_responsavel,
            $this->ddd_telefone,
            $this->telefone,
            $this->data_cadastro,
            $this->data_exclusao,
            1,
            $this->nm_instituicao,
            null,
            null,
            $this->quantidade_alunos_metro_quadrado,
            $this->exigir_dados_socioeconomicos,
            $this->altera_atestado_para_declaracao,
            $this->obrigar_campos_censo,
            $this->obrigar_documento_pessoa,
            $this->exigir_lancamentos_anteriores,
            $this->exibir_apenas_professores_alocados,
            $this->bloquear_vinculo_professor_sem_alocacao_escola,
            $this->permitir_matricula_fora_periodo_letivo,
            $this->ordenar_alunos_sequencial_enturmacao,
            $this->obrigar_telefone_pessoa
        );
        $obj->data_base_remanejamento = Portabilis_Date_Utils::brToPgSQL($this->data_base_remanejamento);
        $obj->data_base_transferencia = Portabilis_Date_Utils::brToPgSQL($this->data_base_transferencia);
        $obj->data_expiracao_reserva_vaga = Portabilis_Date_Utils::brToPgSQL($this->data_expiracao_reserva_vaga);
        $obj->exigir_vinculo_turma_professor = is_null($this->exigir_vinculo_turma_professor) ? 0 : 1;
        $obj->gerar_historico_transferencia = !is_null($this->gerar_historico_transferencia);
        $obj->controlar_posicao_historicos = !is_null($this->controlar_posicao_historicos);
        $obj->matricula_apenas_bairro_escola = !is_null($this->matricula_apenas_bairro_escola);
        $obj->restringir_historico_escolar = !is_null($this->restringir_historico_escolar);
        $obj->restringir_multiplas_enturmacoes = !is_null($this->restringir_multiplas_enturmacoes);
        $obj->permissao_filtro_abandono_transferencia = !is_null($this->permissao_filtro_abandono_transferencia);
        $obj->multiplas_reserva_vaga = !is_null($this->multiplas_reserva_vaga);
        $obj->permitir_carga_horaria = !is_null($this->permitir_carga_horaria);
        $obj->componente_curricular_turma = !is_null($this->componente_curricular_turma);
        $obj->reprova_dependencia_ano_concluinte = !is_null($this->reprova_dependencia_ano_concluinte);
        $obj->bloqueia_matricula_serie_nao_seguinte = !is_null($this->bloqueia_matricula_serie_nao_seguinte);
        $obj->reserva_integral_somente_com_renda = !is_null($this->reserva_integral_somente_com_renda);
        $obj->coordenador_transporte = $this->pessoa_coordenador_transporte;
        $obj->controlar_espaco_utilizacao_aluno = is_null($this->controlar_espaco_utilizacao_aluno) ? 0 : 1;
        $obj->altera_atestado_para_declaracao = is_null($this->altera_atestado_para_declaracao) ? 0 : 1;
        $obj->percentagem_maxima_ocupacao_salas = Portabilis_Currency_Utils::moedaBrToUs($this->percentagem_maxima_ocupacao_salas);
        $obj->data_base_matricula = Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_base);
        $obj->data_fechamento = Portabilis_Date_Utils::brToPgSQL_ddmm($this->data_fechamento);
        $obj->data_educacenso = $this->data_educacenso;
        $obj->exigir_dados_socioeconomicos = is_null($this->exigir_dados_socioeconomicos) ? false : true;
        $obj->obrigar_campos_censo = !is_null($this->obrigar_campos_censo);
        $obj->obrigar_documento_pessoa = !is_null($this->obrigar_documento_pessoa);
        $obj->orgao_regional = $this->orgao_regional;
        $obj->exigir_lancamentos_anteriores = !is_null($this->exigir_lancamentos_anteriores);
        $obj->exibir_apenas_professores_alocados = !is_null($this->exibir_apenas_professores_alocados);
        $obj->bloquear_vinculo_professor_sem_alocacao_escola = !is_null($this->bloquear_vinculo_professor_sem_alocacao_escola);
        $obj->permitir_matricula_fora_periodo_letivo = !is_null($this->permitir_matricula_fora_periodo_letivo);
        $obj->ordenar_alunos_sequencial_enturmacao = !is_null($this->ordenar_alunos_sequencial_enturmacao);
        $obj->obrigar_telefone_pessoa = !is_null($this->obrigar_telefone_pessoa);

        $editou = $obj->edita();
        if ($editou) {
            if (is_null($this->altera_atestado_para_declaracao)) {
                Menu::changeMenusToAttestation();
            } else {
                Menu::changeMenusToDeclaration();
            }

            $this->mensagem .= 'Edição efetuada com sucesso.<br>';
            $this->simpleRedirect('educar_instituicao_lst.php');
        } else {
            $this->mensagem = 'Edição não realizada.<br>';
        }

        return false;
    }

    public function Excluir()
    {
        $this->simpleRedirect('educar_instituicao_lst.php');
    }

    public function makeExtra()
    {
        return file_get_contents(__DIR__ . '/scripts/extra/educar-instituicao-cad.js');
    }

    public function Formular()
    {
        $this->title = 'Instituição';
        $this->processoAp = '559';
    }
};
