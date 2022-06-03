<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class EducacensoRepository
{
    /**
     * @param int $year
     * @param int $school
     *
     * @return Builder
     */
    public function getBuilderForRecord20($year, $school)
    {
        return DB::table('public.educacenso_record20')
            ->where('anoTurma', $year)
            ->where('codEscola', $school);
    }

    /**
     * @param int $school
     *
     * @return Builder
     */
    public function getBuilderForRecord40($school)
    {
        return DB::table('public.educacenso_record40')
            ->where('codEscola', $school);
    }

    /**
     * @param int $year
     * @param int $school
     *
     * @return Builder
     */
    public function getBuilderForRecord50($year, $school)
    {
        return DB::table('public.educacenso_record50')
            ->where('anoTurma', $year)
            ->where('codEscola', $school);
    }

    /**
     * @param int $year
     * @param int $school
     *
     * @return Builder
     */
    public function getBuilderForRecord60($year, $school)
    {
        return DB::table('public.educacenso_record60')
            ->where('anoTurma', $year)
            ->where('codEscola', $school);
    }

    /**
     * @param       $sql
     * @param array $params
     *
     * @return array
     */
    protected function fetchPreparedQuery($sql, $params = [])
    {
        return DB::select(DB::raw($sql), $params);
    }

    /**
     * @param $school
     * @param $year
     *
     * @return array
     */
    public function getDataForRecord00($school, $year)
    {
        $sql = <<<'SQL'
            SELECT
            '00' AS registro,
            ece.cod_escola_inep AS "codigoInep",
            e.situacao_funcionamento AS "situacaoFuncionamento",
            (SELECT min(ano_letivo_modulo.data_inicio)
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "inicioAnoLetivo",
            (SELECT max(ano_letivo_modulo.data_fim)
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "fimAnoLetivo",
            j.fantasia AS nome,
            ep.cep AS cep,
            municipio.cod_ibge AS "codigoIbgeMunicipio",
            districts.ibge_code AS "codigoIbgeDistrito",
            l.nome AS logradouro,
            ep.numero AS numero,
            ep.complemento AS complemento,
            bairro.nome AS bairro,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.ddd)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes),
              (SELECT min(ddd_telefone)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS ddd,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.fone)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 1),
              (SELECT min(telefone)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS telefone,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.fone)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 2),
              (SELECT min(fax)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS "telefoneOutro",
            (SELECT COALESCE(p.email,(SELECT email FROM pmieducar.escola_complemento WHERE ref_cod_escola = e.cod_escola))) AS email,
            i.orgao_regional AS "orgaoRegional",
            e.zona_localizacao AS "zonaLocalizacao",
            e.localizacao_diferenciada AS "localizacaoDiferenciada",
            e.dependencia_administrativa AS "dependenciaAdministrativa",
            (ARRAY[1] <@ e.orgao_vinculado_escola)::INT AS "orgaoOutro",
            (ARRAY[2] <@ e.orgao_vinculado_escola)::INT AS "orgaoEducacao",
            (ARRAY[3] <@ e.orgao_vinculado_escola)::INT AS "orgaoSeguranca",
            (ARRAY[4] <@ e.orgao_vinculado_escola)::INT AS "orgaoSaude",
            (ARRAY[1] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraEmpresa",
            (ARRAY[2] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraSindicato",
            (ARRAY[3] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraOng",
            (ARRAY[4] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraInstituicoes",
            (ARRAY[5] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraSistemaS",
            (ARRAY[6] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraOscip",
            e.categoria_escola_privada AS "categoriaEscolaPrivada",
            e.poder_publico_parceria_convenio AS "poderPublicoConveniado",
            e.formas_contratacao_adm_publica_e_outras_instituicoes AS "formasContratacaoPoderPublico",
            e.qtd_matriculas_atividade_complementar AS "qtdMatAtividadesComplentar",
            e.qtd_atendimento_educacional_especializado AS "qtdMatAee",
            e.qtd_ensino_regular_creche_par AS "qtdMatCrecheParcial",
            e.qtd_ensino_regular_creche_int AS "qtdMatCrecheIntegral",
            e.qtd_ensino_regular_pre_escola_par AS "qtdMatPreEscolaParcial",
            e.qtd_ensino_regular_pre_escola_int AS "qtdMatPreEscolaIntegral",
            e.qtd_ensino_regular_ensino_fund_anos_iniciais_par AS "qtdMatFundamentalIniciaisParcial",
            e.qtd_ensino_regular_ensino_fund_anos_iniciais_int AS "qtdMatFundamentalIniciaisIntegral",
            e.qtd_ensino_regular_ensino_fund_anos_finais_par AS "qtdMatFundamentalFinaisParcial",
            e.qtd_ensino_regular_ensino_fund_anos_finais_int AS "qtdMatFundamentalFinaisIntegral",
            e.qtd_ensino_regular_ensino_med_anos_iniciais_par AS "qtdMatEnsinoMedioParcial",
            e.qtd_ensino_regular_ensino_med_anos_iniciais_int AS "qtdMatEnsinoMedioIntegral",
            e.qtd_edu_especial_classe_especial_par AS "qdtMatClasseEspecialParcial",
            e.qtd_edu_especial_classe_especial_int AS "qdtMatClasseEspecialIntegral",
            e.qtd_edu_eja_ensino_fund AS "qdtMatEjaFundamental",
            e.qtd_edu_eja_ensino_med AS "qtdMatEjaEnsinoMedio",
            e.qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_par AS "qtdMatEdProfIntegradaEjaFundamentalParcial",
            e.qtd_edu_prof_quali_prof_inte_edu_eja_no_ensino_fund_int AS  "qtdMatEdProfIntegradaEjaFundamentalIntegral",
            e.qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_par AS "qtdMatEdProfIntegradaEjaNivelMedioParcial",
            e.qtd_edu_prof_quali_prof_tec_inte_edu_eja_nivel_med_int AS "qtdMatEdProfIntegradaEjaNivelMedioIntegral",
            e.qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_par AS "qtdMatEdProfConcomitanteEjaNivelMedioParcial",
            e.qtd_edu_prof_quali_prof_tec_conc_edu_eja_nivel_med_int AS "qtdMatEdProfConcomitanteEjaNivelMedioIntegral",
            e.qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_par AS "qtdMatEdProfIntercomentarEjaNivelMedioParcial",
            e.qtd_edu_prof_quali_prof_tec_conc_inter_edu_eja_nivel_med_int AS "qtdMatEdProfIntercomentarEjaNivelMedioIntegral",
            e.qtd_edu_prof_quali_prof_tec_inte_ensino_med_par AS "qtdMatEdProfIntegradaEnsinoMedioParcial",
            e.qtd_edu_prof_quali_prof_tecinte_ensino_med_int AS "qtdMatEdProfIntegradaEnsinoMedioIntegral",
            e.qtd_edu_prof_quali_prof_tec_conc_ensino_med_par AS "qtdMatEdProfConcomitenteEnsinoMedioParcial",
            e.qtd_edu_prof_quali_prof_tec_conc_ensino_med_int AS "qtdMatEdProfConcomitenteEnsinoMedioIntegral",
            e.qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_par AS "qtdMatEdProfIntercomplementarEnsinoMedioParcial",
            e.qtd_edu_prof_quali_prof_tec_conc_inter_ensino_med_int AS "qtdMatEdProfIntercomplementarEnsinoMedioIntegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_par AS "qtdMatEdProfTecnicaIntegradaEnsinoMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_inte_ensino_med_int AS "qtdMatEdProfTecnicaIntegradaEnsinoMedioIntegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_par AS "qtdMatEdProfTecnicaConcomitanteEnsinoMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_ensino_med_int AS "qtdMatEdProfTecnicaConcomitanteEnsinoMedioIntegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_par AS "qtdMatEdProfTecnicaIntercomplementarEnsinoMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_ensino_med_int AS "qtdMatEdProfTecnicaIntercomplementarEnsinoMedioItegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_subsequente_ensino_med AS "qtdMatEdProfTecnicaSubsequenteEnsinoMedio",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_par AS "qtdMatEdProfTecnicaIntegradaEjaNivelMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_inte_edu_eja_nivel_med_int AS "qtdMatEdProfTecnicaIntegradaEjaNivelMedioIntegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_par AS "qtdMatEdProfTecnicaConcomitanteEjaNivelMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_edu_eja_nivel_med_int AS "qtdMatEdProfTecnicaConcomitanteEjaNivelMedioIntegral",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_par AS "qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioParcial",
            e.qtd_edu_prof_edu_prof_tec_nivel_med_conc_inter_edu_eja_med_int AS "qtdMatEdProfTecnicaIntercomplementarEjaNivelMedioIntegral",
            e.cnpj_mantenedora_principal AS "cnpjMantenedoraPrincipal",
            j.cnpj AS "cnpjEscolaPrivada",
            e.regulamentacao AS "regulamentacao",
            CASE WHEN e.esfera_administrativa = 1 THEN 1 ELSE 0 END AS "esferaFederal",
            CASE WHEN e.esfera_administrativa = 2 THEN 1 ELSE 0 END AS "esferaEstadual",
            CASE WHEN e.esfera_administrativa = 3 THEN 1 ELSE 0 END AS "esferaMunicipal",
            e.unidade_vinculada_outra_instituicao AS "unidadeVinculada",
            e.inep_escola_sede AS "inepEscolaSede",
            e.codigo_ies AS "codigoIes",

            e.mantenedora_escola_privada[1] AS "mantenedoraEscolaPrivada",
            e.orgao_vinculado_escola AS "orgaoVinculado",
            e.esfera_administrativa AS "esferaAdministrativa",
            e.cod_escola AS "idEscola",
            municipio.idmun AS "idMunicipio",
            districts.id AS "idDistrito",
            i.cod_instituicao AS "idInstituicao",
            uf.sigla_uf AS "siglaUf",
            (SELECT EXTRACT(YEAR FROM min(ano_letivo_modulo.data_inicio))
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "anoInicioAnoLetivo",
            (SELECT EXTRACT(YEAR FROM max(ano_letivo_modulo.data_fim))
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "anoFimAnoLetivo"

            FROM pmieducar.escola e
            JOIN pmieducar.instituicao i ON i.cod_instituicao = e.ref_cod_instituicao
            INNER JOIN cadastro.pessoa p ON (e.ref_idpes = p.idpes)
            INNER JOIN cadastro.juridica j ON (j.idpes = p.idpes)
            LEFT JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
            LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
            LEFT JOIN public.bairro ON (bairro.idbai = ep.idbai)
            LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
            LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
            LEFT JOIN public.districts ON (districts.id = e.iddis)

            LEFT JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
            LEFT JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
            LEFT JOIN public.logradouro l ON (l.idlog = cl.idlog)
            WHERE e.cod_escola = :school
SQL;

        return $this->fetchPreparedQuery($sql, [
            'school' => $school,
            'year' => $year,
        ]);
    }

    /**
     * @param $school
     *
     * @return array
     */
    public function getDataForRecord10($school)
    {
        $sql = <<<'SQL'
            SELECT
                escola.cod_escola AS "codEscola",
                educacenso_cod_escola.cod_escola_inep AS "codigoInep",
                escola.local_funcionamento AS "localFuncionamento",
                escola.condicao AS "condicao",
                escola.agua_potavel_consumo AS "aguaPotavelConsumo",
                (ARRAY[1] <@ escola.abastecimento_agua)::int AS "aguaRedePublica",
                (ARRAY[2] <@ escola.abastecimento_agua)::int AS "aguaPocoArtesiano",
                (ARRAY[3] <@ escola.abastecimento_agua)::int AS "aguaCacimbaCisternaPoco",
                (ARRAY[4] <@ escola.abastecimento_agua)::int AS "aguaFonteRio",
                (ARRAY[5] <@ escola.abastecimento_agua)::int AS "aguaInexistente",
                (ARRAY[1] <@ escola.abastecimento_energia)::int AS "energiaRedePublica",
                (ARRAY[2] <@ escola.abastecimento_energia)::int AS "energiaGerador",
                (ARRAY[3] <@ escola.abastecimento_energia)::int AS "energiaOutros",
                (ARRAY[4] <@ escola.abastecimento_energia)::int AS "energiaInexistente",
                (ARRAY[1] <@ escola.esgoto_sanitario)::int AS "esgotoRedePublica",
                (ARRAY[2] <@ escola.esgoto_sanitario)::int AS "esgotoFossaComum",
                (ARRAY[3] <@ escola.esgoto_sanitario)::int AS "esgotoInexistente",
                (ARRAY[4] <@ escola.esgoto_sanitario)::int AS "esgotoFossaRudimentar",
                (ARRAY[1] <@ escola.destinacao_lixo)::int AS "lixoColetaPeriodica",
                (ARRAY[2] <@ escola.destinacao_lixo)::int AS "lixoQueima",
                (ARRAY[3] <@ escola.destinacao_lixo)::int AS "lixoJogaOutraArea",
                (ARRAY[5] <@ escola.destinacao_lixo)::int AS "lixoDestinacaoPoderPublico",
                (ARRAY[7] <@ escola.destinacao_lixo)::int AS "lixoEnterra",
                escola.tratamento_lixo AS "tratamentoLixo",
                escola.dependencia_nenhuma_relacionada AS "dependenciaNenhumaRelacionada",
                escola.numero_salas_utilizadas_dentro_predio AS "numeroSalasUtilizadasDentroPredio",
                escola.numero_salas_utilizadas_fora_predio AS "numeroSalasUtilizadasForaPredio",
                escola.numero_salas_climatizadas AS "numeroSalasClimatizadas",
                escola.numero_salas_acessibilidade AS "numeroSalasAcessibilidade",
                escola.televisoes AS "televisoes",
                escola.videocassetes AS "videocassetes",
                escola.dvds AS "dvds",
                escola.antenas_parabolicas AS "antenasParabolicas",
                escola.lousas_digitais AS "lousasDigitais",
                escola.copiadoras AS "copiadoras",
                escola.retroprojetores AS "retroprojetores",
                escola.impressoras AS "impressoras",
                escola.aparelhos_de_som AS "aparelhosDeSom",
                escola.projetores_digitais AS "projetoresDigitais",
                escola.faxs AS "faxs",
                escola.maquinas_fotograficas AS "maquinasFotograficas",
                escola.quantidade_computadores_alunos_mesa AS "quantidadeComputadoresAlunosMesa",
                escola.quantidade_computadores_alunos_portateis AS "quantidadeComputadoresAlunosPortateis",
                escola.quantidade_computadores_alunos_tablets AS "quantidadeComputadoresAlunosTablets",
                escola.computadores AS "computadores",
                escola.computadores_administrativo AS "computadoresAdministrativo",
                escola.computadores_alunos AS "computadoresAlunos",
                escola.impressoras_multifuncionais AS "impressorasMultifuncionais",
                escola.total_funcionario AS "totalFuncionario",
                escola.atendimento_aee AS "atendimentoAee",
                escola.atividade_complementar AS "atividadeComplementar",
                escola.localizacao_diferenciada AS "localizacaoDiferenciada",
                escola.materiais_didaticos_especificos AS "materiaisDidaticosEspecificos",
                escola.lingua_ministrada AS "linguaMinistrada",
                escola.codigo_lingua_indigena AS "codigoLinguaIndigena",
                escola.educacao_indigena AS "educacaoIndigena",
                juridica.fantasia AS "nomeEscola",
                escola.predio_compartilhado_outra_escola as "predioCompartilhadoOutraEscola",
                escola.codigo_inep_escola_compartilhada as "codigoInepEscolaCompartilhada",
                escola.codigo_inep_escola_compartilhada2 as "codigoInepEscolaCompartilhada2",
                escola.codigo_inep_escola_compartilhada3 as "codigoInepEscolaCompartilhada3",
                escola.codigo_inep_escola_compartilhada4 as "codigoInepEscolaCompartilhada4",
                escola.codigo_inep_escola_compartilhada5 as "codigoInepEscolaCompartilhada5",
                escola.codigo_inep_escola_compartilhada6 as "codigoInepEscolaCompartilhada6",
                escola.possui_dependencias as "possuiDependencias",
                escola.salas_gerais as "salasGerais",
                escola.salas_funcionais as "salasFuncionais",
                escola.banheiros as "banheiros",
                escola.laboratorios as "laboratorios",
                escola.salas_atividades as "salasAtividades",
                escola.dormitorios as "dormitorios",
                escola.areas_externas as "areasExternas",
                escola.recursos_acessibilidade as "recursosAcessibilidade",
                escola.uso_internet as "usoInternet",
                escola.acesso_internet as "acessoInternet",
                escola.equipamentos_acesso_internet as "equipamentosAcessoInternet",
                escola.equipamentos as "equipamentos",
                escola.rede_local as "redeLocal",
                escola.qtd_secretario_escolar as "qtdSecretarioEscolar",
                escola.qtd_auxiliar_administrativo as "qtdAuxiliarAdministrativo",
                escola.qtd_apoio_pedagogico as "qtdApoioPedagogico",
                escola.qtd_coordenador_turno as "qtdCoordenadorTurno",
                escola.qtd_tecnicos as "qtdTecnicos",
                escola.qtd_bibliotecarios as "qtdBibliotecarios",
                escola.qtd_segurancas as "qtdSegurancas",
                escola.qtd_auxiliar_servicos_gerais as "qtdAuxiliarServicosGerais",
                escola.qtd_nutricionistas as "qtdNutricionistas",
                escola.qtd_profissionais_preparacao as "qtdProfissionaisPreparacao",
                escola.qtd_bombeiro as "qtdBombeiro",
                escola.qtd_psicologo as "qtdPsicologo",
                escola.qtd_fonoaudiologo as "qtdFonoaudiologo",
                escola.nao_ha_funcionarios_para_funcoes as "semFuncionariosParaFuncoes",
                escola.alimentacao_escolar_alunos as "alimentacaoEscolarAlunos",
                escola.orgaos_colegiados as "orgaosColegiados",
                escola.exame_selecao_ingresso as "exameSelecaoIngresso",
                escola.reserva_vagas_cotas as "reservaVagasCotas",
                escola.instrumentos_pedagogicos as "instrumentosPedagogicos",
                escola.compartilha_espacos_atividades_integracao AS "compartilhaEspacosAtividadesIntegracao",
                escola.usa_espacos_equipamentos_atividades_regulares AS "usaEspacosEquipamentosAtividadesRegulares",
                pessoa.url AS "url",
                escola.projeto_politico_pedagogico AS "projetoPoliticoPedagogico",
                escola.qtd_vice_diretor AS "qtdViceDiretor",
                escola.qtd_orientador_comunitario AS "qtdOrientadorComunitario"
            FROM pmieducar.escola
            INNER JOIN cadastro.juridica ON juridica.idpes = escola.ref_idpes
            INNER JOIN cadastro.pessoa ON pessoa.idpes = escola.ref_idpes
            LEFT JOIN modules.educacenso_cod_escola ON (escola.cod_escola = educacenso_cod_escola.cod_escola)
            WHERE TRUE
                AND escola.cod_escola = :school
SQL;

        return $this->fetchPreparedQuery($sql, [
            'school' => $school
        ]);
    }

    /**
     * @param $school
     *
     * @return array
     */
    public function getDataForRecord40($school)
    {
        return $this->getBuilderForRecord40($school)
            ->get()
            ->toArray();
    }

    /**
     * @param $school
     * @param $year
     *
     * @return array
     */
    public function getDataForRecord20($school, $year)
    {
        return $this->getBuilderForRecord20($year, $school)
            ->get()
            ->toArray();
    }

    /**
     * @param $classroomId
     * @param $disciplineIds
     *
     * @return array
     */
    public function getDisciplinesWithoutTeacher($classroomId, $disciplineIds)
    {
        $disciplineIds = implode(', ', $disciplineIds);
        $sql = "
            SELECT componente_curricular.nome
            from modules.componente_curricular
            WHERE componente_curricular.id IN ({$disciplineIds})
            AND not exists (
                SELECT 1
                FROM modules.professor_turma_disciplina
                JOIN modules.professor_turma
                ON professor_turma.id = professor_turma_disciplina.professor_turma_id
                WHERE professor_turma.turma_id = :classroomId
                AND professor_turma_disciplina.componente_curricular_id = componente_curricular.id
            )
        ";

        return $this->fetchPreparedQuery($sql, [
            'classroomId' => $classroomId,
        ]);
    }

    public function getDataForRecord50($year, $school)
    {
        return $this->getBuilderForRecord50($year, $school)
            ->get()
            ->toArray();
    }

    public function getDataForRecord60($school, $year)
    {
        return $this->getBuilderForRecord60($year, $school)
            ->get()
            ->toArray();
    }

    public function getCommonDataForRecord30($arrayPersonId, $schoolId)
    {
        if (empty($arrayPersonId)) {
            return [];
        }

        $stringPersonId = join(',', $arrayPersonId);
        $sql = <<<SQL
            SELECT
                '30' AS registro,
                dadosescola.cod_escola_inep AS "inepEscola",
                dadosescola.cod_escola AS "codigoEscola",
                fisica.idpes AS "codigoPessoa",
                fisica.cpf AS cpf,
                pessoa.nome AS "nomePessoa",
                fisica.data_nasc AS "dataNascimento",
                (pessoa_mae.nome IS NOT NULL OR pessoa_pai.nome IS NOT NULL)::INTEGER AS "filiacao",
                pessoa_mae.nome AS "filiacao1",
                pessoa_pai.nome AS "filiacao2",
                CASE WHEN fisica.sexo = 'M' THEN 1 ELSE 2 END AS "sexo",
                raca.raca_educacenso AS "raca",
                fisica.nacionalidade AS "nacionalidade",
                CASE WHEN fisica.nacionalidade = 3 THEN pais.cod_ibge ELSE 76 END AS "paisNacionalidade",
                municipio_nascimento.cod_ibge AS "municipioNascimento",
                CASE WHEN
                    true = (
                        SELECT true
                        FROM cadastro.fisica_deficiencia
                        JOIN cadastro.deficiencia ON deficiencia.cod_deficiencia = fisica_deficiencia.ref_cod_deficiencia
                        WHERE fisica_deficiencia.ref_idpes = fisica.idpes
                        AND deficiencia.deficiencia_educacenso != 999
                        LIMIT 1
                    ) THEN 1
                    ELSE 0 END
                AS "deficiencia",
                1 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaCegueira",
                2 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaBaixaVisao",
                3 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaSurdez",
                4 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaAuditiva",
                5 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaSurdoCegueira",
                6 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaFisica",
                7 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaIntelectual",
                CASE WHEN array_length(deficiencias.array_deficiencias, 1) > 1 THEN 1 ELSE 0 END "deficienciaMultipla",
                13 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaAltasHabilidades",
                25 = ANY (deficiencias.array_deficiencias)::INTEGER AS "deficienciaAutismo",
                fisica.pais_residencia AS "paisResidencia",
                endereco_pessoa.cep AS "cep",
                municipio.cod_ibge AS "municipioResidencia",
                fisica.zona_localizacao_censo AS "localizacaoResidencia",
                fisica.localizacao_diferenciada AS "localizacaoDiferenciada",
                dadosescola.nomeescola AS "nomeEscola",
                   CASE WHEN fisica.nacionalidade = 1 THEN 'Brasileira'
                     WHEN fisica.nacionalidade = 2 THEN 'Naturalizado brasileiro'
                     ELSE 'Estrangeira' END AS "nomeNacionalidade",
                deficiencias.array_deficiencias AS "arrayDeficiencias"
                 FROM cadastro.fisica
                 JOIN cadastro.pessoa ON pessoa.idpes = fisica.idpes
            LEFT JOIN cadastro.fisica_raca ON fisica_raca.ref_idpes = fisica.idpes
            LEFT JOIN cadastro.raca ON (raca.cod_raca = fisica_raca.ref_cod_raca)
            LEFT JOIN cadastro.pessoa as pessoa_mae
            ON fisica.idpes_mae = pessoa_mae.idpes
            LEFT JOIN cadastro.pessoa as pessoa_pai
            ON fisica.idpes_pai = pessoa_pai.idpes
            LEFT JOIN public.municipio municipio_nascimento ON municipio_nascimento.idmun = fisica.idmun_nascimento
            LEFT JOIN cadastro.endereco_pessoa ON endereco_pessoa.idpes = pessoa.idpes
            LEFT JOIN public.logradouro ON logradouro.idlog = endereco_pessoa.idlog
            LEFT JOIN public.municipio ON municipio.idmun = logradouro.idmun
            LEFT JOIN public.pais ON pais.idpais = CASE WHEN fisica.nacionalidade = 3 THEN fisica.idpais_estrangeiro ELSE 76 END
            LEFT JOIN LATERAL (
                 SELECT educacenso_cod_escola.cod_escola_inep,
                        educacenso_cod_escola.cod_escola,
                        relatorio.get_nome_escola(educacenso_cod_escola.cod_escola) AS nomeescola
                 FROM modules.educacenso_cod_escola
                 WHERE educacenso_cod_escola.cod_escola = :school
                 ) dadosescola ON true
            LEFT JOIN LATERAL (
                 SELECT fisica_deficiencia.ref_idpes,
                        ARRAY_AGG(deficiencia.deficiencia_educacenso) as array_deficiencias
                 FROM cadastro.fisica_deficiencia
                 JOIN cadastro.deficiencia ON deficiencia.cod_deficiencia = fisica_deficiencia.ref_cod_deficiencia
                 WHERE fisica_deficiencia.ref_idpes = fisica.idpes
                   AND deficiencia.deficiencia_educacenso IN (1,2,3,4,5,6,7,25,13)
                 GROUP BY 1
                 ) deficiencias ON true

            WHERE fisica.idpes IN ({$stringPersonId})

SQL;

        return $this->fetchPreparedQuery($sql, ['school' => $schoolId]);
    }

    public function getEmployeeDataForRecord30($arrayEmployeeId)
    {
        if (empty($arrayEmployeeId)) {
            return [];
        }

        $stringPersonId = join(',', $arrayEmployeeId);
        $sql = <<<SQL
            SELECT DISTINCT
                servidor.ref_cod_instituicao AS "codigoInstituicao",
                servidor.cod_servidor AS "codigoPessoa",
                escolaridade.escolaridade AS "escolaridade",
                servidor.tipo_ensino_medio_cursado AS "tipoEnsinoMedioCursado",
                tbl_formacoes.course_id AS "formacaoCurso",
                tbl_formacoes.completion_year AS "formacaoAnoConclusao",
                tbl_formacoes.college_id AS "formacaoInstituicao",
                tbl_formacoes.discipline_id AS "formacaoComponenteCurricular",
                tbl_posgraduacoes.pos_graduate::text AS "posGraduacoes",
                coalesce(cardinality(servidor.curso_formacao_continuada),0) as "countFormacaoContinuada",
                (ARRAY[1] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaCreche",
                (ARRAY[2] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaPreEscola",
                (ARRAY[3] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaAnosIniciaisFundamental",
                (ARRAY[4] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaAnosFinaisFundamental",
                (ARRAY[5] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEnsinoMedio",
                (ARRAY[6] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoJovensAdultos",
                (ARRAY[7] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoEspecial",
                (ARRAY[8] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoIndigena",
                (ARRAY[9] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoCampo",
                (ARRAY[10] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoAmbiental",
                (ARRAY[11] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoDireitosHumanos",
                (ARRAY[12] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaGeneroDiversidadeSexual",
                (ARRAY[13] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaDireitosCriancaAdolescente",
                (ARRAY[14] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoRelacoesEticoRaciais",
                (ARRAY[17] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoGestaoEscolar",
                (ARRAY[15] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoOutros",
                (ARRAY[16] <@ servidor.curso_formacao_continuada)::INT "formacaoContinuadaEducacaoNenhum",
                pessoa.email AS "email",
                educacenso_cod_docente.cod_docente_inep AS "inepServidor"

            FROM pmieducar.servidor
                 JOIN cadastro.pessoa ON pessoa.idpes = servidor.cod_servidor
            LEFT JOIN cadastro.escolaridade ON escolaridade.idesco = servidor.ref_idesco
            LEFT JOIN modules.educacenso_cod_docente ON educacenso_cod_docente.cod_servidor = servidor.cod_servidor,
            LATERAL (
                SELECT ARRAY_REMOVE(ARRAY_AGG(educacenso_curso_superior.curso_id), NULL) course_id,
                       ARRAY_REMOVE(ARRAY_AGG(completion_year), NULL) completion_year,
                       ARRAY_REMOVE(ARRAY_AGG(educacenso_ies.ies_id), NULL) college_id,
                       ARRAY_REMOVE(ARRAY_AGG(discipline_id), NULL) discipline_id
                 FROM employee_graduations
                 JOIN modules.educacenso_curso_superior ON educacenso_curso_superior.id = employee_graduations.course_id
                 JOIN modules.educacenso_ies ON educacenso_ies.id = employee_graduations.college_id
                WHERE employee_graduations.employee_id = servidor.cod_servidor
            ) AS tbl_formacoes,
            LATERAL (
                SELECT
                    array_agg(
                        row_to_json(employee_posgraduate)
                    ) AS "pos_graduate"
                FROM employee_posgraduate
                WHERE employee_posgraduate.employee_id = servidor.cod_servidor
            ) AS tbl_posgraduacoes
            WHERE servidor.cod_servidor IN ({$stringPersonId})
SQL;

        return $this->fetchPreparedQuery($sql);
    }

    public function getStudentDataForRecord30($arrayStudentId)
    {
        if (empty($arrayStudentId)) {
            return [];
        }

        $stringStudentId = join(',', $arrayStudentId);
        $sql = <<<SQL
            SELECT DISTINCT
                aluno.ref_idpes AS "codigoPessoa",
                educacenso_cod_aluno.cod_aluno_inep AS "inepAluno",
                aluno.recursos_prova_inep AS "recursosProvaInep",
                (ARRAY[1] <@ aluno.recursos_prova_inep)::INT "recursoLedor",
                (ARRAY[2] <@ aluno.recursos_prova_inep)::INT "recursoTranscricao",
                (ARRAY[3] <@ aluno.recursos_prova_inep)::INT "recursoGuia",
                (ARRAY[4] <@ aluno.recursos_prova_inep)::INT "recursoTradutor",
                (ARRAY[5] <@ aluno.recursos_prova_inep)::INT "recursoLeituraLabial",
                (ARRAY[10] <@ aluno.recursos_prova_inep)::INT "recursoProvaAmpliada",
                (ARRAY[8] <@ aluno.recursos_prova_inep)::INT "recursoProvaSuperampliada",
                (ARRAY[11] <@ aluno.recursos_prova_inep)::INT "recursoAudio",
                (ARRAY[12] <@ aluno.recursos_prova_inep)::INT "recursoLinguaPortuguesaSegundaLingua",
                (ARRAY[13] <@ aluno.recursos_prova_inep)::INT "recursoVideoLibras",
                (ARRAY[9] <@ aluno.recursos_prova_inep)::INT "recursoBraile",
                (ARRAY[14] <@ aluno.recursos_prova_inep)::INT "recursoNenhum",
                fisica.nis_pis_pasep AS "nis",
                documento.certidao_nascimento AS "certidaoNascimento"
            FROM pmieducar.aluno
                 JOIN cadastro.fisica ON fisica.idpes = aluno.ref_idpes
            LEFT JOIN cadastro.documento ON documento.idpes = fisica.idpes
            LEFT JOIN modules.educacenso_cod_aluno ON educacenso_cod_aluno.cod_aluno = aluno.cod_aluno
            WHERE aluno.cod_aluno IN ({$stringStudentId})
SQL;

        return $this->fetchPreparedQuery($sql);
    }
}
