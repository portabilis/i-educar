<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EducacensoRepository
{
    /**
     * @param $sql
     * @param array $params
     * @return array
     */
    protected function fetchPreparedQuery($sql, $params = [])
    {
        return DB::select(DB::raw($sql), $params);
    }

    /**
     * @param $school
     * @param $year
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
            COALESCE(ep.cep, ee.cep) AS cep,
            municipio.cod_ibge AS "codigoIbgeMunicipio",
            distrito.cod_ibge AS "codigoIbgeDistrito",
            COALESCE(l.idtlog || l.nome, ee.idtlog || ee.logradouro) AS logradouro,
            COALESCE(ep.numero, ee.numero) AS numero,
            COALESCE(ep.complemento, ee.complemento) AS complemento,
            COALESCE(bairro.nome, ee.bairro) AS bairro,
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
                    WHERE j.idpes = fone_pessoa.idpes),
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
            e.conveniada_com_poder_publico AS "conveniadaPoderPublico",
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
            distrito.iddis AS "idDistrito",
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
            LEFT JOIN cadastro.endereco_externo ee ON (ee.idpes = p.idpes)
            LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
            LEFT JOIN public.bairro ON (bairro.idbai = COALESCE(ep.idbai, (SELECT b.idbai
                                                                       FROM public.bairro b
                                                                           INNER JOIN cadastro.endereco_externo ee
                                                                               ON (UPPER(ee.bairro) = UPPER(b.nome))
                                                                       WHERE ee.idpes = e.ref_idpes
                                                                       LIMIT 1)))
            LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
            LEFT JOIN public.uf ON (uf.sigla_uf = COALESCE(municipio.sigla_uf, ee.sigla_uf))
            LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)

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
     * @return array
     */
    public function getDataForRecord10($school)
    {
        $sql = '
            SELECT
                escola.cod_escola AS "codEscola",
                escola.local_funcionamento AS "localFuncionamento",
                escola.condicao AS "condicao",
                escola.agua_consumida AS "aguaConsumida",
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
                (ARRAY[2] <@ escola.esgoto_sanitario)::int AS "esgotoFossa",
                (ARRAY[3] <@ escola.esgoto_sanitario)::int AS "esgotoInexistente",
                (ARRAY[1] <@ escola.destinacao_lixo)::int AS "lixoColetaPeriodica",
                (ARRAY[2] <@ escola.destinacao_lixo)::int AS "lixoQueima",
                (ARRAY[3] <@ escola.destinacao_lixo)::int AS "lixoJogaOutraArea",
                (ARRAY[4] <@ escola.destinacao_lixo)::int AS "lixoRecicla",
                (ARRAY[5] <@ escola.destinacao_lixo)::int AS "lixoEnterra",
                (ARRAY[6] <@ escola.destinacao_lixo)::int AS "lixoOutros",
                escola.tratamento_lixo AS "tratamentoLixo",
                escola.dependencia_sala_diretoria AS "dependenciaSalaDiretoria",
                escola.dependencia_sala_professores AS "dependenciaSalaProfessores",
                escola.dependencia_sala_secretaria AS "dependnciaSalaSecretaria",
                escola.dependencia_laboratorio_informatica AS "dependenciaLaboratorioInformatica",
                escola.dependencia_laboratorio_ciencias AS "dependenciaLaboratorioCiencias",
                escola.dependencia_sala_aee AS "dependenciaSalaAee",
                escola.dependencia_quadra_coberta AS "dependenciaQuadraCoberta",
                escola.dependencia_quadra_descoberta AS "dependenciaQuadraDescoberta",
                escola.dependencia_cozinha AS "dependenciaCozinha",
                escola.dependencia_biblioteca AS "dependenciaBiblioteca",
                escola.dependencia_sala_leitura AS "dependenciaSalaLeitura",
                escola.dependencia_parque_infantil AS "dependenciaParqueInfantil",
                escola.dependencia_bercario AS "dependenciaBercario",
                escola.dependencia_banheiro_fora AS "dependenciaBanheiroFora",
                escola.dependencia_banheiro_dentro AS "dependenciaBanheiroDentro",
                escola.dependencia_banheiro_infantil AS "dependenciaBanheiroInfantil",
                escola.dependencia_banheiro_deficiente AS "dependenciaBanheiroDeficiente",
                escola.dependencia_banheiro_chuveiro AS "dependenciaBanheiroChuveiro",
                escola.dependencia_refeitorio AS "dependenciaRefeitorio",
                escola.dependencia_dispensa AS "dependenciaDispensa",
                escola.dependencia_aumoxarifado AS "dependenciaAumoxarifado",
                escola.dependencia_auditorio AS "dependenciaAuditorio",
                escola.dependencia_patio_coberto AS "dependenciaPatioCoberto",
                escola.dependencia_patio_descoberto AS "dependenciaPatioDescoberto",
                escola.dependencia_alojamento_aluno AS "dependenciaAlojamentoAluno",
                escola.dependencia_alojamento_professor AS "dependenciaAlojamentoProfessor",
                escola.dependencia_area_verde AS "dependenciaAreaVerde",
                escola.dependencia_lavanderia AS "dependenciaLavanderia",
                escola.dependencia_nenhuma_relacionada AS "dependenciaNenhumaRelacionada",
                escola.numero_salas_utilizadas_dentro_predio AS "numeroSalasUtilizadasDentroPredio",
                escola.numero_salas_utilizadas_fora_predio AS "numeroSalasUtilizadasForaPredio",
                escola.televisoes AS "televisoes",
                escola.videocassetes AS "videocassetes",
                escola.dvds AS "dvds",
                escola.antenas_parabolicas AS "antenasParabolicas",
                escola.copiadoras AS "copiadoras",
                escola.retroprojetores AS "retroprojetores",
                escola.impressoras AS "impressoras",
                escola.aparelhos_de_som AS "aparelhosDeSom",
                escola.projetores_digitais AS "projetoresDigitais",
                escola.faxs AS "faxs",
                escola.maquinas_fotograficas AS "maquinasFotograficas",
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
                escola.alimentacao_escolar_alunos as "alimentacaoEscolarAlunos",
                escola.orgaos_colegiados as "orgaosColegiados",
                escola.exame_selecao_ingresso as "exameSelecaoIngresso",
                escola.reserva_vagas_cotas as "reservaVagasCotas"
            FROM pmieducar.escola
            INNER JOIN cadastro.juridica ON TRUE
                AND juridica.idpes = escola.ref_idpes
            WHERE TRUE
                AND escola.cod_escola = :school
        ';

        return $this->fetchPreparedQuery($sql, [
            'school' => $school
        ]);
    }
}
