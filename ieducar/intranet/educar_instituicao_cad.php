<?php

use App\Menu;
use App\Models\State;

return new class extends clsCadastro
{
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

    public $obrigar_cpf;

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

        $obj_permissoes->permissao_cadastra(int_processo_ap: 559, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3, str_pagina_redirecionar: 'educar_instituicao_lst.php');

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

                $this->fexcluir = $obj_permissoes->permissao_excluir(int_processo_ap: 559, int_idpes_usuario: $this->pessoa_logada, int_soma_nivel_acesso: 3);
                $retorno = 'Editar';
            }
        }
        $this->url_cancelar = ($retorno == 'Editar') ? "educar_instituicao_det.php?cod_instituicao={$registro['cod_instituicao']}" : 'educar_instituicao_lst.php';
        $this->nome_url_cancelar = 'Cancelar';

        $this->breadcrumb(currentPage: 'Instituição', breadcrumbs: ['educar_index.php' => 'Escola']);

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
        $this->obrigar_cpf = dbBool($this->obrigar_cpf);
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
        $this->campoOculto(nome: 'cod_instituicao', valor: $this->cod_instituicao);

        // text
        $this->campoTexto(nome: 'nm_instituicao', campo: 'Nome da Instituição', valor: $this->nm_instituicao, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoCep(nome: 'cep', campo: 'CEP', valor: int2CEP($this->cep), obrigatorio: true);
        $this->campoTexto(nome: 'logradouro', campo: 'Logradouro', valor: $this->logradouro, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoNumero(nome: 'numero', campo: 'Número', valor: $this->numero, tamanhovisivel: 6, tamanhomaximo: 6);
        $this->campoTexto(nome: 'complemento', campo: 'Complemento', valor: $this->complemento, tamanhovisivel: 30, tamanhomaximo: 50);
        $this->campoTexto(nome: 'bairro', campo: 'Bairro', valor: $this->bairro, tamanhovisivel: 30, tamanhomaximo: 40, obrigatorio: true);
        $this->campoTexto(nome: 'cidade', campo: 'Cidade', valor: $this->cidade, tamanhovisivel: 30, tamanhomaximo: 60, obrigatorio: true);

        // foreign keys
        $opcoes = ['' => 'Selecione'] + State::getListKeyAbbreviation()->toArray();

        $this->campoLista(nome: 'ref_sigla_uf', campo: 'UF', valor: $opcoes, default: $this->ref_sigla_uf);

        $this->campoTexto(nome: 'nm_responsavel', campo: 'Nome do Responsável', valor: $this->nm_responsavel, tamanhovisivel: 30, tamanhomaximo: 255, obrigatorio: true);
        $this->campoNumero(nome: 'ddd_telefone', campo: 'DDD Telefone', valor: $this->ddd_telefone, tamanhovisivel: 2, tamanhomaximo: 2);
        $this->campoNumero(nome: 'telefone', campo: 'Telefone', valor: $this->telefone, tamanhovisivel: 11, tamanhomaximo: 11);

        $options = [
            'label' => 'Coordenador(a) de transporte',
            'size' => 50,
            'value' => $this->coordenador_transporte,
            'required' => false,
        ];

        $this->inputsHelper()->simpleSearchPessoa(attrName: 'coordenador_transporte', inputOptions: $options);

        if (!empty($this->ref_sigla_uf)) {
            $opcoes = [null => 'Selecione'];
            $orgaoRegional = new Educacenso_Model_OrgaoRegionalDataMapper();
            $orgaosRegionais = $orgaoRegional->findAll(
                columns: ['sigla_uf', 'codigo'],
                where: ['sigla_uf' => $this->ref_sigla_uf],
                orderBy: ['codigo' => 'asc'],
                addColumnIdIfNotSet: false
            );
            foreach ($orgaosRegionais as $orgaoRegional) {
                $opcoes[strtoupper($orgaoRegional->codigo)] = strtoupper($orgaoRegional->codigo);
            }
        } else {
            $opcoes = [null => 'Informe uma UF'];
        }

        $options = ['label' => 'Código do órgão regional de ensino', 'resources' => $opcoes, 'value' => $this->orgao_regional, 'required' => false, 'size' => 70];
        $this->inputsHelper()->select(attrName: 'orgao_regional', inputOptions: $options);

        $this->campoRotulo(nome: 'gerais', campo: '<b>Gerais</b>');
        $this->campoCheck(nome: 'obrigar_documento_pessoa', campo: 'Exigir documento (RG, CPF ou Certidão de nascimento / casamento) no cadastro pessoa / aluno', valor: $this->obrigar_documento_pessoa);

        $this->campoCheck(nome: 'obrigar_cpf', campo: 'Exigir CPF no cadastro pessoa / aluno', valor: $this->obrigar_cpf);

        $this->campoRotulo(nome: 'datas', campo: '<b>Datas</b>');
        $dataBaseDeslocamento = 'A ordenação/apresentação de alunos transferidos nos relatórios (ex.: Relação de alunos por turma) será baseada neste campo quando preenchido.';
        $this->inputsHelper()->date(
            attrName: 'data_base_transferencia',
            inputOptions: [
                'label' => 'Data máxima para deslocamento',
                'required' => false,
                'hint' => $dataBaseDeslocamento,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_base_transferencia),
            ]
        );
        $dataBaseRemanejamento = 'A ordenação/apresentação de alunos remanejados nas turmas, nos relatórios (ex.: Relação de alunos por turma), será baseada neste campo quando preenchido.';
        $this->inputsHelper()->date(
            attrName: 'data_base_remanejamento',
            inputOptions: [
                'label' => 'Data máxima para troca de sala',
                'required' => false,
                'hint' => $dataBaseRemanejamento,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_base_remanejamento),
            ]
        );
        $dataBase = 'Caso o campo seja preenchido, o sistema irá controlar distorção de idade/série e limitar inscrições por idade no Pré-matrícula com base na data informada.';
        $this->inputsHelper()->dateDiaMes(
            attrName: 'data_base',
            inputOptions: [
                'label' => 'Data base para matrícula (dia/mês)',
                'size' => 5,
                'max_length' => 5,
                'placeholder' => 'dd/mm',
                'required' => false,
                'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_base_matricula),
                'hint' => $dataBase,
            ]
        );
        $dataExpiracaoReservaVaga = 'Caso o campo seja preenchido, o sistema irá indeferir automaticamente as reservas em situação de espera após a data informada.';
        $this->inputsHelper()->date(
            attrName: 'data_expiracao_reserva_vaga',
            inputOptions: [
                'label' => 'Data para indeferimento automático da reserva de vaga',
                'required' => false,
                'hint' => $dataExpiracaoReservaVaga,
                'placeholder' => 'dd/mm/yyyy',
                'value' => Portabilis_Date_Utils::pgSQLToBr($this->data_expiracao_reserva_vaga),
            ]
        );
        $dataFechamento = 'Caso o campo seja preenchido, o sistema irá bloquear a matrícula de novos alunos nas turmas após a data informada.';
        $this->inputsHelper()->dateDiaMes(
            attrName: 'data_fechamento',
            inputOptions: [
                'label' => 'Data de fechamento das turmas para matrícula',
                'size' => 5,
                'max_length' => 5,
                'placeholder' => 'dd/mm',
                'required' => false,
                'value' => Portabilis_Date_Utils::pgSQLToBr_ddmm($this->data_fechamento),
                'hint' => $dataFechamento,
            ]
        );
        $dataEducacenso = 'Este campo deve ser preenchido com a data máxima das matrículas que devem ser enviadas para o Censo.';
        $this->inputsHelper()->date(
            attrName: 'data_educacenso',
            inputOptions: [
                'label' => 'Data de referência do Educacenso',
                'required' => false,
                'hint' => $dataEducacenso,
                'placeholder' => 'dd/mm/yyyy',
                'value' => $this->data_educacenso,
            ]
        );

        $this->campoRotulo(nome: 'historicos', campo: '<b>Históricos</b>');
        $this->campoCheck(nome: 'gerar_historico_transferencia', campo: 'Gerar histórico de transferência ao transferir matrícula?', valor: $this->gerar_historico_transferencia);
        $this->campoCheck(nome: 'controlar_posicao_historicos', campo: 'Permitir controlar posicionamento dos históricos em seu respectivo documento', valor: $this->controlar_posicao_historicos);
        $this->campoCheck(nome: 'restringir_historico_escolar', campo: 'Restringir modificações de históricos escolares?', valor: $this->restringir_historico_escolar, desc: null, dica: 'Com esta opção selecionada, somente será possível cadastrar/editar históricos escolares de alunos que pertençam a mesma escola do funcionário.');
        $this->campoCheck(nome: 'permitir_carga_horaria', campo: 'Não permitir definir C.H. por componente no histórico escolar', valor: $this->permitir_carga_horaria, desc: null, dica: 'Caso a opção estiver habilitada, não será possivel adicionar carga horária na tabela de disciplinas do histórico do aluno.');

        $this->campoRotulo(nome: 'reserva_vaga', campo: '<b>Reserva de vaga</b>');
        $this->multiplas_reserva_vaga = isset($this->cod_instituicao) ? dbBool($this->multiplas_reserva_vaga) : true;
        $this->campoCheck(nome: 'multiplas_reserva_vaga', campo: 'Permitir múltiplas reservas de vagas para o mesmo candidato em escolas diferentes', valor: $this->multiplas_reserva_vaga);
        $this->campoCheck(nome: 'reserva_integral_somente_com_renda', campo: 'Permitir reserva de vaga para o turno integral somente quando a renda for informada', valor: $this->reserva_integral_somente_com_renda);
        $this->campoCheck(nome: 'exigir_dados_socioeconomicos', campo: 'Exigir dados socioeconômicos na reserva de vaga para turno integral', valor: $this->exigir_dados_socioeconomicos);

        $this->campoRotulo(nome: 'relatorios', campo: '<b>Relatórios</b>');
        $this->campoCheck(nome: 'permissao_filtro_abandono_transferencia', campo: 'Não permitir a apresentação de alunos com matrícula em abandono ou transferida na emissão do relatório de frequência', valor: $this->permissao_filtro_abandono_transferencia);
        $this->campoCheck(nome: 'altera_atestado_para_declaracao', campo: 'Alterar nome do título do menu e relatórios de Atestado para Declaração', valor: $this->altera_atestado_para_declaracao);
        $this->campoCheck(nome: 'exibir_apenas_professores_alocados', campo: 'Exibir apenas professores alocados nos filtros de emissão do Diário de classe', valor: $this->exibir_apenas_professores_alocados);
        $this->campoCheck(
            nome: 'ordenar_alunos_sequencial_enturmacao',
            campo: 'Apresentar alunos em relatórios de acordo com a ordenação definida de forma automática/manual na turma',
            valor: $this->ordenar_alunos_sequencial_enturmacao,
            desc: null
        );

        $this->campoRotulo(nome: 'processos_escolares', campo: '<b>Processos escolares</b>');
        $this->campoCheck(nome: 'exigir_vinculo_turma_professor', campo: 'Exigir vínculo com turma para lançamento de notas do professor?', valor: $this->exigir_vinculo_turma_professor);

        $this->campoCheck(nome: 'matricula_apenas_bairro_escola', campo: 'Permitir matrícula de alunos apenas do bairro da escola?', valor: $this->matricula_apenas_bairro_escola);

        $this->campoCheck(nome: 'controlar_espaco_utilizacao_aluno', campo: 'Controlar espaço utilizado pelo aluno?', valor: $this->controlar_espaco_utilizacao_aluno);
        $this->campoMonetario(
            nome: 'percentagem_maxima_ocupacao_salas',
            campo: 'Percentagem máxima de ocupação da sala',
            valor: Portabilis_Currency_Utils::moedaUsToBr($this->percentagem_maxima_ocupacao_salas),
            tamanhovisivel: 6,
            tamanhomaximo: 6
        );
        $this->campoNumero(nome: 'quantidade_alunos_metro_quadrado', campo: 'Quantidade máxima de alunos permitidos por metro quadrado', valor: $this->quantidade_alunos_metro_quadrado, tamanhovisivel: 6, tamanhomaximo: 6);

        $this->campoCheck(nome: 'restringir_multiplas_enturmacoes', campo: 'Não permitir múltiplas enturmações para o aluno no mesmo curso e série/ano', valor: $this->restringir_multiplas_enturmacoes);

        $this->permitir_carga_horaria = isset($this->cod_instituicao) ? dbBool($this->permitir_carga_horaria) : true;

        $this->campoCheck(
            nome: 'componente_curricular_turma',
            campo: 'Permitir definir componentes curriculares diferenciados nas turmas',
            valor: $this->componente_curricular_turma
        );

        $this->campoCheck(
            nome: 'reprova_dependencia_ano_concluinte',
            campo: 'Não permitir dependência em séries/anos concluintes',
            valor: $this->reprova_dependencia_ano_concluinte,
            desc: null,
            dica: 'Caso marcado, o aluno que reprovar em algum componente em ano concluinte será automaticamente reprovado.'
        );

        $this->campoCheck(nome: 'bloqueia_matricula_serie_nao_seguinte', campo: 'Não permitir matrículas que não respeitem a sequência de enturmação', valor: $this->bloqueia_matricula_serie_nao_seguinte);

        $this->campoCheck(nome: 'obrigar_campos_censo', campo: 'Obrigar e validar o preenchimento dos campos exigidos pelo Censo escolar', valor: $this->obrigar_campos_censo);

        $this->campoCheck(
            nome: 'exigir_lancamentos_anteriores',
            campo: 'Exigir o lançamento de notas em etapas que o aluno não estava enturmado',
            valor: $this->exigir_lancamentos_anteriores
        );

        $this->campoCheck(
            nome: 'bloquear_vinculo_professor_sem_alocacao_escola',
            campo: 'Bloquear vínculos de professores em turmas pertencentes às escolas em que eles não estão alocados',
            valor: $this->bloquear_vinculo_professor_sem_alocacao_escola,
            desc: null,
            dica: 'Caso marcado, os vínculos de professores em turmas pertencentes às escolas em que eles não estão alocados será bloqueado.'
        );

        $this->campoCheck(
            nome: 'permitir_matricula_fora_periodo_letivo',
            campo: 'Permitir matricular alunos fora do período letivo',
            valor: $this->permitir_matricula_fora_periodo_letivo,
            desc: null
        );

        $this->campoCheck(
            nome: 'obrigar_telefone_pessoa',
            campo: 'Obrigar o preenchimento de um telefone no cadastro de pessoa física',
            valor: $this->obrigar_telefone_pessoa,
            desc: null
        );

        $scripts = ['/vendor/legacy/Cadastro/Assets/Javascripts/Instituicao.js'];
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: $scripts);
        $styles = ['/vendor/legacy/Cadastro/Assets/Stylesheets/Instituicao.css'];
        Portabilis_View_Helper_Application::loadStylesheet(viewInstance: $this, files: $styles);
    }

    public function Novo()
    {
        $this->simpleRedirect('educar_instituicao_lst.php');
    }

    public function Editar()
    {
        $obj = new clsPmieducarInstituicao(
            cod_instituicao: $this->cod_instituicao,
            ref_usuario_exc: $this->ref_usuario_exc,
            ref_usuario_cad: $this->pessoa_logada,
            ref_idtlog: $this->ref_idtlog,
            ref_sigla_uf: $this->ref_sigla_uf,
            cep: str_replace(search: '-', replace: '', subject: $this->cep),
            cidade: $this->cidade,
            bairro: $this->bairro,
            logradouro: $this->logradouro,
            numero: $this->numero,
            complemento: $this->complemento,
            nm_responsavel: $this->nm_responsavel,
            ddd_telefone: $this->ddd_telefone,
            telefone: $this->telefone,
            data_cadastro: $this->data_cadastro,
            data_exclusao: $this->data_exclusao,
            ativo: 1,
            nm_instituicao: $this->nm_instituicao,
            quantidade_alunos_metro_quadrado: $this->quantidade_alunos_metro_quadrado,
            exigir_dados_socioeconomicos: $this->exigir_dados_socioeconomicos,
            altera_atestado_para_declaracao: $this->altera_atestado_para_declaracao,
            obrigar_campos_censo: $this->obrigar_campos_censo,
            obrigar_documento_pessoa: $this->obrigar_documento_pessoa,
            obrigar_cpf: $this->obrigar_cpf,
            exigir_lancamentos_anteriores: $this->exigir_lancamentos_anteriores,
            exibir_apenas_professores_alocados: $this->exibir_apenas_professores_alocados,
            bloquear_vinculo_professor_sem_alocacao_escola: $this->bloquear_vinculo_professor_sem_alocacao_escola,
            permitir_matricula_fora_periodo_letivo: $this->permitir_matricula_fora_periodo_letivo,
            ordenar_alunos_sequencial_enturmacao: $this->ordenar_alunos_sequencial_enturmacao,
            obrigar_telefone_pessoa: $this->obrigar_telefone_pessoa
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
        $obj->obrigar_cpf = !is_null($this->obrigar_cpf);
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
