<?php

use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaAluno;
use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaProfessor;
use iEducar\Modules\Educacenso\Deficiencia\MapeamentoDeficienciasAluno;
use iEducar\Modules\Educacenso\Deficiencia\ValueDeficienciaMultipla;
use iEducar\Modules\Educacenso\ValueTurmaMaisEducacao;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/pmieducar/geral.inc.php';
require_once 'lib/Portabilis/Utils/Database.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once 'Portabilis/Business/Professor.php';
require_once 'App/Model/IedFinder.php';
require_once 'ComponenteCurricular/Model/CodigoEducacenso.php';
require_once 'lib/App/Model/Educacenso.php';
require_once __DIR__ . '/../../../lib/App/Model/Servidor.php';

/**
 * Class EducacensoExportController
 * @deprecated Essa versão da API pública será descontinuada
 */
class EducacensoExportController extends ApiCoreController
{

    var $pessoa_logada;

    var $ref_cod_escola;
    var $ref_cod_escola_;
    var $ref_cod_serie;
    var $ref_cod_serie_;
    var $ref_usuario_exc;
    var $ref_usuario_cad;
    var $hora_inicial;
    var $hora_final;
    var $data_cadastro;
    var $data_exclusao;
    var $ativo;
    var $hora_inicio_intervalo;
    var $hora_fim_intervalo;
    var $hora_fim_intervalo_;

    var $ano;
    var $ref_cod_instituicao;
    var $msg = "";
    var $error = false;

    var $turma_presencial_ou_semi;

    const TECNOLOGO = 1;
    const LICENCIATURA = 2;
    const BACHARELADO = 3;


    protected function educacensoExport()
    {

        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $conteudo = $this->exportaDadosCensoPorEscola($escola,
            $ano,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim));

        if ($this->error) {
            return array(
                "error" => true,
                "mensagem" => $this->msg
            );
        }

        return array('conteudo' => $conteudo);
    }

    protected function educacensoExportFase2()
    {

        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $conteudo = $this->exportaDadosCensoPorEscolaFase2($escola,
            $ano,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim));

        if ($this->error) {
            return array(
                "error" => true,
                "mensagem" => $this->msg
            );
        }

        return array('conteudo' => $conteudo);
    }

    protected function exportaDadosCensoPorEscola($escolaId, $ano, $data_ini, $data_fim)
    {

        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(846, $this->pessoa_logada, 7,
            'educar_index.php');
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $export = $this->exportaDadosRegistro00($escolaId, $ano);
        $export .= $this->exportaDadosRegistro10($escolaId, $ano);
        foreach ($this->getTurmas($escolaId, $ano) as $turmaId => $turmaNome) {
            $export .= $this->exportaDadosRegistro20($escolaId, $turmaId, $data_ini, $data_fim);
        }
        foreach ($this->getServidores($escolaId, $ano, $data_ini, $data_fim) as $servidor) {

            $registro30 = $this->exportaDadosRegistro30($servidor['id'], $escolaId);
            $registro40 = $this->exportaDadosRegistro40($servidor['id'], $escolaId);
            $registro50 = $this->exportaDadosRegistro50($servidor['id'], $escolaId);
            $registro51 = $this->exportaDadosRegistro51($servidor['id'], $escolaId, $data_ini, $data_fim, $ano);
            if (!empty($registro30) && !empty($registro40) && !empty($registro50)) {
                $export .= $registro30 . $registro40 . $registro50 . $registro51;
            }
        }

        foreach ($this->getAlunos($escolaId, $ano, $data_ini, $data_fim) as $alunoId) {
            $registro60 = $this->exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
            $registro70 = $this->exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
            $registro80 = $this->exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
            if (!empty($registro60) && !empty($registro70) && !empty($registro80)) {
                $export .= $registro60 . $registro70 . $registro80;
            }
        }
        $export .= $this->exportaDadosRegistro99();
        return $export;
    }

    protected function exportaDadosCensoPorEscolaFase2($escolaId, $ano, $data_ini, $data_fim)
    {
        @session_start();
        $this->pessoa_logada = $_SESSION['id_pessoa'];
        @session_write_close();

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(846, $this->pessoa_logada, 7,
            'educar_index.php');
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);

        $export = $this->exportaDadosRegistro89($escolaId);

        foreach ($this->getTurmas($escolaId, $ano) as $turmaId => $turmaNome) {
            foreach ($this->getMatriculasTurma($escolaId, $ano, $data_ini, $data_fim, $turmaId) as $matricula) {
                $export .= $this->exportaDadosRegistro90($escolaId, $turmaId, $matricula['id']);
            }
        }
        foreach ($this->getTurmas($escolaId, $ano) as $turmaId => $turmaNome) {
            foreach ($this->getMatriculasTurmaAposData($escolaId, $ano, $data_ini, $data_fim, $turmaId) as $matricula) {
                $export .= $this->exportaDadosRegistro91($escolaId, $turmaId, $matricula['id']);
            }
        }

        return $export;
    }

    protected function getTurmas($escolaId, $ano)
    {
        return App_Model_IedFinder::getTurmasEducacenso($escolaId, $ano);
    }

    protected function getServidores($escolaId, $ano, $data_ini, $data_fim)
    {
        $sql = 'SELECT DISTINCT cod_servidor AS id
              FROM pmieducar.servidor
             INNER JOIN modules.professor_turma ON(servidor.cod_servidor = professor_turma.servidor_id)
             INNER JOIN pmieducar.turma ON(professor_turma.turma_id = turma.cod_turma)
             WHERE turma.ref_ref_cod_escola = $1
               AND servidor.ativo = 1
               AND professor_turma.ano = $2
               AND turma.ativo = 1
               AND NOT EXISTS (SELECT 1 FROM
                    pmieducar.servidor_alocacao
                    WHERE servidor.cod_servidor = servidor_alocacao.ref_cod_servidor
                    AND turma.ref_ref_cod_escola = servidor_alocacao.ref_cod_escola
                    AND turma.ano = servidor_alocacao.ano
                    AND servidor_alocacao.data_admissao > DATE($4)
                )
               AND (SELECT 1
                      FROM pmieducar.matricula_turma mt
                     INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
                      WHERE mt.ref_cod_turma = turma.cod_turma
                      AND (mt.ativo = 1 OR mt.data_exclusao > DATE($4))
                      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
                      LIMIT 1) IS NOT NULL';

        return Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim)));
    }

    protected function getAlunos($escolaId, $ano, $data_ini, $data_fim)
    {
        $sql =
            'SELECT
      DISTINCT(a.cod_aluno) AS id

      FROM  pmieducar.aluno a
      INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
      INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
      INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
      INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)

      WHERE e.cod_escola = $1
      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
      AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
      AND m.ano = $2
    ';
        return Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim)));
    }

    protected function getMatriculasTurma($escolaId, $ano, $data_ini, $data_fim, $turmaId)
    {
        $sql =
            'SELECT DISTINCT m.cod_matricula AS id
        FROM  pmieducar.aluno a
       INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
       INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
       INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
       INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
       INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
       INNER JOIN pmieducar.instituicao i ON (i.cod_instituicao = e.ref_cod_instituicao)
       INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
       WHERE e.cod_escola = $1
         AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
         AND m.aprovado IN (1, 2, 3, 4, 6, 15)
         AND m.ano = $2
         AND mt.ref_cod_turma = $5
         AND m.ativo = 1
    ';
        return Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim, $turmaId)));
    }

    protected function getMatriculasTurmaAposData($escolaId, $ano, $data_ini, $data_fim, $turmaId)
    {
        $sql =
            'SELECT
      DISTINCT(m.cod_matricula) AS id

      FROM  pmieducar.aluno a
      INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
      INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
      INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
      INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
      INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
      INNER JOIN pmieducar.instituicao i ON (i.cod_instituicao = e.ref_cod_instituicao)
      WHERE e.cod_escola = $1
        AND m.aprovado IN (1, 2, 3, 4, 6, 15)
        AND m.ano = $2
        AND mt.ref_cod_turma = $3
        AND mt.data_enturmacao > i.data_educacenso
        AND i.data_educacenso IS NOT NULL
        AND m.ativo = 1
    ';
        return Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($escolaId, $ano, $turmaId)));
    }

    protected function exportaDadosRegistro00($escolaId, $ano)
    {
        $sql =
            ' SELECT
        \'00\' AS r00s1,
        ece.cod_escola_inep AS r00s2,

      gestor_f.cpf AS r00s3,
      gestor_p.nome AS r00s4,
      e.cargo_gestor AS r00s5,
      e.email_gestor AS r00s6,

      e.situacao_funcionamento AS r00s7,

        (SELECT min(ano_letivo_modulo.data_inicio)
          FROM pmieducar.ano_letivo_modulo
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS r00s8,

        (SELECT max(ano_letivo_modulo.data_fim)
          FROM pmieducar.ano_letivo_modulo
          WHERE ano_letivo_modulo.ref_ano = $2 AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS r00s9,

        p.nome AS r00s10,
        e.latitude AS r00s11,
        e.longitude AS r00s12,
        COALESCE(ep.cep, ee.cep) AS r00s13,
        COALESCE(l.idtlog || l.nome, ee.idtlog || ee.logradouro) AS r00s14,
        COALESCE(ep.numero, ee.numero) AS r00s15,
        COALESCE(ep.complemento, ee.complemento) AS r00s16,
        COALESCE(bairro.nome, ee.bairro) AS r00s17,
        uf.cod_ibge AS r00s18,
        municipio.cod_ibge AS r00s19,
        distrito.cod_ibge AS r00s20,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.ddd)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(ddd_telefone)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS r00s21,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes),
          (SELECT min(telefone)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS r00s22,


        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 3),
          (SELECT min(fax)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS r00s24,

        (SELECT COALESCE(
          (SELECT min(fone_pessoa.fone)
                FROM cadastro.fone_pessoa
                WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 4),
          (SELECT min(fax)
            FROM pmieducar.escola_complemento
            WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS r00s25,

        (SELECT COALESCE(p.email,(SELECT email FROM pmieducar.escola_complemento WHERE ref_cod_escola = e.cod_escola))) AS r00s26,

        i.orgao_regional AS r00s27,
        e.dependencia_administrativa AS r00s28,
        e.zona_localizacao AS r00s29,
        e.categoria_escola_privada AS r00s30,
        e.conveniada_com_poder_publico r00s31,
        (ARRAY[1] <@ e.mantenedora_escola_privada)::INT AS r00s32,
        (ARRAY[2] <@ e.mantenedora_escola_privada)::INT AS r00s33,
        (ARRAY[3] <@ e.mantenedora_escola_privada)::INT AS r00s34,
        (ARRAY[4] <@ e.mantenedora_escola_privada)::INT AS r00s35,
        (ARRAY[5] <@ e.mantenedora_escola_privada)::INT AS r00s36,
        e.cnpj_mantenedora_principal AS r00s37,
        j.cnpj AS r00s38,
        e.regulamentacao AS r00s39,
        0 AS r00s40,
        e.situacao_funcionamento

        FROM pmieducar.escola e
        JOIN pmieducar.instituicao i ON i.cod_instituicao = e.ref_cod_instituicao
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        INNER JOIN cadastro.pessoa p ON (e.ref_idpes = p.idpes)
        INNER JOIN cadastro.juridica j ON (j.idpes = p.idpes)
        INNER JOIN cadastro.pessoa gestor_p ON (gestor_p.idpes = e.ref_idpes_gestor)
        INNER JOIN cadastro.fisica gestor_f ON (gestor_f.idpes = gestor_p.idpes)
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
        WHERE e.cod_escola = $1
    ';
        // Transforma todos resultados em variáveis
        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('return_only' => 'first-row', 'params' => array($escolaId, $ano))));
        if ($r00s1) {
            $d = '|';
            $return = '';

            $r00s2 = substr($r00s2, 0, 8);
            $r00s3 = $this->cpfToCenso($r00s3);
            $r00s4 = $this->convertStringToCenso($r00s4);
            $r00s6 = strtoupper($r00s6);

            $r00s8 = Portabilis_Date_Utils::pgSQLToBr($r00s8);
            $r00s9 = Portabilis_Date_Utils::pgSQLToBr($r00s9);

            $r00s10 = $this->convertStringToCenso($r00s10);
            $r00s14 = $this->convertStringToCenso($r00s14);
            $r00s15 = $this->convertStringToCenso($r00s15);
            $r00s16 = $this->convertStringToCenso($r00s16);
            $r00s17 = $this->convertStringToCenso($r00s17);
            $r00s26 = strtoupper($r00s26);
            $r00s27 = ($r00s27 ? str_pad($r00s27, 5, "0", STR_PAD_LEFT) : null);

            $r00s37 = $this->cnpjToCenso($r00s37);
            $r00s38 = $this->cnpjToCenso($r00s38);

            if ($r00s28 != 4) {
                $r00s30 = $r00s31 = $r00s32 = $r00s33 = $r00s34 = $r00s35 = $r00s36 = $r00s37 = $r00s38 = null;
            }

            for ($i = 1; $i <= 42; $i++) {
                $return .= ${'r00s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);

            return $return . "\n";
        } else {
            $this->msg .= "Dados para formular o registro 00 da escola {$escolaId} não encontrados. Verifique se a escola possuí endereço normalizado, código do INEP e dados do gestor cadastrados.<br/>";
            $this->error = true;
        }
    }

    protected function exportaDadosRegistro10($escolaId, $ano)
    {
        $sql =
            'SELECT
      \'10\' AS r10s1,
      ece.cod_escola_inep AS r10s2,

      e.local_funcionamento,
      e.condicao AS r10s12,
      e.codigo_inep_escola_compartilhada AS r10s14,
      e.codigo_inep_escola_compartilhada2 AS r10s15,
      e.codigo_inep_escola_compartilhada3 AS r10s16,
      e.codigo_inep_escola_compartilhada4 AS r10s17,
      e.codigo_inep_escola_compartilhada5 AS r10s18,
      e.codigo_inep_escola_compartilhada6 AS r10s19,
      e.agua_consumida AS r10s20,
      (ARRAY[1] <@ e.abastecimento_agua)::INT AS r10s21,
      (ARRAY[2] <@ e.abastecimento_agua)::INT AS r10s22,
      (ARRAY[3] <@ e.abastecimento_agua)::INT AS r10s23,
      (ARRAY[4] <@ e.abastecimento_agua)::INT AS r10s24,
      (ARRAY[5] <@ e.abastecimento_agua)::INT AS r10s25,
      (ARRAY[1] <@ e.abastecimento_energia)::INT AS r10s26,
      (ARRAY[2] <@ e.abastecimento_energia)::INT AS r10s27,
      (ARRAY[3] <@ e.abastecimento_energia)::INT AS r10s28,
      (ARRAY[4] <@ e.abastecimento_energia)::INT AS r10s29,
      (ARRAY[1] <@ e.esgoto_sanitario)::INT AS r10s30,
      (ARRAY[2] <@ e.esgoto_sanitario)::INT AS r10s31,
      (ARRAY[3] <@ e.esgoto_sanitario)::INT AS r10s32,
      (ARRAY[1] <@ e.destinacao_lixo)::INT AS r10s33,
      (ARRAY[2] <@ e.destinacao_lixo)::INT AS r10s34,
      (ARRAY[3] <@ e.destinacao_lixo)::INT AS r10s35,
      (ARRAY[4] <@ e.destinacao_lixo)::INT AS r10s36,
      (ARRAY[5] <@ e.destinacao_lixo)::INT AS r10s37,
      (ARRAY[6] <@ e.destinacao_lixo)::INT AS r10s38,
      e.dependencia_sala_diretoria AS r10s39,
      e.dependencia_sala_professores AS r10s40,
      e.dependencia_sala_secretaria AS r10s41,
      e.dependencia_laboratorio_informatica AS r10s42,
      e.dependencia_laboratorio_ciencias AS r10s43,
      e.dependencia_sala_aee AS r10s44,
      e.dependencia_quadra_coberta AS r10s45,
      e.dependencia_quadra_descoberta AS r10s46,
      e.dependencia_cozinha AS r10s47,
      e.dependencia_biblioteca AS r10s48,
      e.dependencia_sala_leitura AS r10s49,
      e.dependencia_parque_infantil AS r10s50,
      e.dependencia_bercario AS r10s51,
      e.dependencia_banheiro_fora AS r10s52,
      e.dependencia_banheiro_dentro AS r10s53,
      e.dependencia_banheiro_infantil AS r10s54,
      e.dependencia_banheiro_deficiente AS r10s55,
      e.dependencia_vias_deficiente AS r10s56,
      e.dependencia_banheiro_chuveiro AS r10s57,
      e.dependencia_refeitorio AS r10s58,
      e.dependencia_dispensa AS r10s59,
      e.dependencia_aumoxarifado AS r10s60,
      e.dependencia_auditorio AS r10s61,
      e.dependencia_patio_coberto AS r10s62,
      e.dependencia_patio_descoberto AS r10s63,
      e.dependencia_alojamento_aluno AS r10s64,
      e.dependencia_alojamento_professor AS r10s65,
      e.dependencia_area_verde AS r10s66,
      e.dependencia_lavanderia AS r10s67,
      e.dependencia_nenhuma_relacionada AS r10s68,
      e.dependencia_numero_salas_existente AS r10s69,
      e.dependencia_numero_salas_utilizadas AS r10s70,

      e.televisoes AS r10s71,
      e.videocassetes AS r10s72,
      e.dvds AS r10s73,
      e.antenas_parabolicas AS r10s74,
      e.copiadoras AS r10s75,
      e.retroprojetores AS r10s76,
      e.impressoras AS r10s77,
      e.aparelhos_de_som AS r10s78,
      e.projetores_digitais  AS r10s79,
      e.faxs AS r10s80,
      e.maquinas_fotograficas AS r10s81,
      e.computadores AS r10s82,
      e.impressoras_multifuncionais AS r10s83,
      e.computadores_administrativo AS r10s84,
      e.computadores_alunos AS r10s85,
      e.acesso_internet AS r10s86,

      total_funcionario AS r10s88,
      1 AS r10s89,
      atendimento_aee AS r10s90,
      atividade_complementar AS r10s91,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 1
        LIMIT 1
      ) AS r10s92,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 2
        LIMIT 1
      ) AS r10s93,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 3
        LIMIT 1
      ) AS r10s94,

      (SELECT 1
         FROM pmieducar.curso
        INNER JOIN pmieducar.escola_curso ON (escola_curso.ref_cod_curso = curso.cod_curso)
        WHERE escola_curso.ref_cod_escola = e.cod_escola
          AND curso.modalidade_curso = 4
        LIMIT 1
      ) AS r10s95,

      fundamental_ciclo AS r10s96,
      localizacao_diferenciada AS r10s97,
      CASE WHEN materiais_didaticos_especificos = 1 THEN 1
           ELSE 0
       END AS r10s98,
      CASE WHEN materiais_didaticos_especificos = 2 THEN 1
           ELSE 0
       END AS r10s99,
      CASE WHEN materiais_didaticos_especificos = 3 THEN 1
           ELSE 0
       END AS r10s100,
      educacao_indigena AS r10s101,
      CASE WHEN lingua_ministrada = 1 THEN 1 ELSE 0 END AS r10s102,
      CASE WHEN lingua_ministrada = 2 THEN 1 ELSE 0 END AS r10s103,
      codigo_lingua_indigena AS r10s104,
      espaco_brasil_aprendizado AS r10s105,
      abre_final_semana AS r10s106,
      proposta_pedagogica AS r10s107,

      (SELECT 1
         FROM pmieducar.turma
        WHERE ref_ref_cod_escola = $1
          AND etapa_educacenso IN (4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,41,56)
          AND ano = $2
        LIMIT 1
      ) AS etapa_ensino_fundamental

      FROM pmieducar.escola e
      INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
      INNER JOIN cadastro.pessoa p ON (p.idpes = e.ref_idpes_gestor)
      INNER JOIN cadastro.fisica f ON (f.idpes = p.idpes)
      WHERE e.cod_escola = $1
    ';

        $exclusivamente = 2;

        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('return_only' => 'first-row', 'params' => array($escolaId, $ano))));
        if ($r10s1) {
            $d = '|';
            $return = '';

            for ($i = 3; $i <= 11; $i++) {
                if ($local_funcionamento == $i) {
                    ${'r10s' . $i} = 1;
                } else {
                    ${'r10s' . $i} = 0;
                }
            }

            $existeEscolaCompartilhada = false;
            for ($i = 14; $i <= 19; $i++) {
                if (${'r10s' . $i} != null && !$existeEscolaCompartilhada) {
                    $existeEscolaCompartilhada = true;
                }
            }

            if ($existeEscolaCompartilhada) {
                $r10s13 = 1;
            } else {
                $r10s13 = 0;
            }

            if ($r10s3 == 0) {
                $r10s13 = null;
            }

            if ($r10s3 <> 1 && $r10s8 <> 1) {
                $r10s12 = null;
            }

            if ($r10s3 == 1) {
                if (is_null($r10s12)) {
                    $this->msg .= "Dados para formular o registro 10 campo 12 da escola {$escolaId} com problemas. Obrigatório quando o campo 3 for igual a 1 <br/>";
                    $this->error = true;
                }
            }

            if ($r10s25 == 1) {
                $r10s21 = $r10s22 = $r10s23 = $r10s24 = 0;
            }

            if ($r10s29 == 1) {
                $r10s26 = $r10s27 = $r10s28 = 0;
            }

            if ($r10s32 == 1) {
                $r10s30 = $r10s31 = 0;
            }

            if (!$r10s82) {
                $r10s86 = null;
            }

            $r10s87 = $r10s86 == 1 ? 1 : null;

            $r10s96 = $etapa_ensino_fundamental ? $r10s96 : null;

            if ($r10s90 == $exclusivamente || $r10s91 == $exclusivamente) {
                $r10s92 = $r10s93 = $r10s94 = $r10s95 = '';
            } else {
                $r10s92 = ($r10s92 ? $r10s92 : 0);
                $r10s93 = ($r10s93 ? $r10s93 : 0);
                $r10s94 = ($r10s94 ? $r10s94 : 0);
                $r10s95 = ($r10s95 ? $r10s95 : 0);
            }

            $r10s98 = 1;
            $r10s99 = $r10s100 = 0;

            if (!$r10s101) {
                $r10s102 = $r10s103 = $r10s104 = null;
            }

            for ($i = 1; $i <= 107; $i++) {
                if ($i >= 71 && $i <= 85) {
                    $return .= (${'r10s' . $i} == 0 ? '' : ${'r10s' . $i}) . $d;
                } else {
                    $return .= ${'r10s' . $i} . $d;
                }
            }
            $return = substr_replace($return, "", -1);

            return $return . "\n";
        } else {
            $this->msg .= "Dados para formular o registro 10 da escola {$escolaId} não encontrados. Verifique se a escola possuí código do INEP cadastrado. <br/>";
            $this->error = true;
        }
    }

    protected function exportaDadosRegistro20($escolaId, $turmaId, $data_ini, $data_fim)
    {
        $sql =
            ' SELECT
        \'20\' AS r20s1,
        ece.cod_escola_inep AS r20s2,
        t.cod_turma AS r20s4,
        t.nm_turma AS r20s5,
        1 AS r20s6,
        substring(t.hora_inicial::VARCHAR,1,2) AS r20s7,
        substring(t.hora_inicial::VARCHAR,4,2) AS r20s8,
        substring(t.hora_final::VARCHAR,1,2) AS r20s9,
        substring(t.hora_final::VARCHAR,4,2) AS r20s10,
        (ARRAY[1] <@ t.dias_semana)::INT AS r20s11,
        (ARRAY[2] <@ t.dias_semana)::INT AS r20s12,
        (ARRAY[3] <@ t.dias_semana)::INT AS r20s13,
        (ARRAY[4] <@ t.dias_semana)::INT AS r20s14,
        (ARRAY[5] <@ t.dias_semana)::INT AS r20s15,
        (ARRAY[6] <@ t.dias_semana)::INT AS r20s16,
        (ARRAY[7] <@ t.dias_semana)::INT AS r20s17,
        t.tipo_atendimento AS r20s18,
        t.turma_mais_educacao AS r20s19,
        t.atividades_complementares[1] AS r20s20,
        t.atividades_complementares[2] AS r20s21,
        t.atividades_complementares[3] AS r20s22,
        t.atividades_complementares[4] AS r20s23,
        t.atividades_complementares[5] AS r20s24,
        t.atividades_complementares[6] AS r20s25,
        (ARRAY[1] <@ t.atividades_aee)::INT AS r20s26,
        (ARRAY[2] <@ t.atividades_aee)::INT AS r20s27,
        (ARRAY[3] <@ t.atividades_aee)::INT AS r20s28,
        (ARRAY[4] <@ t.atividades_aee)::INT AS r20s29,
        (ARRAY[5] <@ t.atividades_aee)::INT AS r20s30,
        (ARRAY[6] <@ t.atividades_aee)::INT AS r20s31,
        (ARRAY[7] <@ t.atividades_aee)::INT AS r20s32,
        (ARRAY[8] <@ t.atividades_aee)::INT AS r20s33,
        (ARRAY[9] <@ t.atividades_aee)::INT AS r20s34,
        (ARRAY[10] <@ t.atividades_aee)::INT AS r20s35,
        (ARRAY[11] <@ t.atividades_aee)::INT AS r20s36,
        c.modalidade_curso AS r20s37,
        t.etapa_educacenso AS r20s38,
        t.cod_curso_profissional AS r20s39,
        s.cod_serie AS serieId,
        e.dependencia_administrativa

        FROM pmieducar.turma t
        INNER JOIN pmieducar.serie s ON (t.ref_ref_cod_serie = s.cod_serie)
        INNER JOIN pmieducar.curso c ON (s.ref_cod_curso = c.cod_curso)
        INNER JOIN pmieducar.escola e ON (t.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
        WHERE t.cod_turma = $1
        AND COALESCE(t.nao_informar_educacenso, 0) = 0
        AND t.ativo = 1
        AND t.visivel = TRUE
        AND (SELECT 1
              FROM pmieducar.matricula_turma mt
             INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
              WHERE mt.ref_cod_turma = t.cod_turma
              AND (mt.ativo = 1 OR mt.data_exclusao > DATE($3))
              AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($2) AND DATE($3)
              LIMIT 1) IS NOT NULL';

        // Transforma todos resultados em variáveis
        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('return_only' => 'first-row', 'params' => array($turmaId, $data_ini, $data_fim))));
        if ($r20s1) {

            $r20s5 = $this->convertStringToCenso($r20s5);

            //Dias da semana não podem ser nullos, 1 ou 0
            for ($i = 11; $i < 18; $i++) {
                ${'r20s' . $i} = (${'r20s' . $i} ? '1' : '0');
            }

            // Atribui 0 (Não lecionado) para todas as disciplinas por padrão.
            $r20s40 = $r20s41 = $r20s42 = $r20s43 = $r20s44 = $r20s45 = $r20s46 = $r20s47 = $r20s48 = $r20s49 =
            $r20s50 = $r20s51 = $r20s52 = $r20s53 = $r20s54 = $r20s55 = $r20s56 = $r20s57 = $r20s58 = $r20s59 = $r20s60 =
            $r20s61 = $r20s62 = $r20s63 = $r20s64 = $r20s65 = 0;

            // Se a turma não presta atendimento educacional especializado AEE esses campos precisam ser nulos
            if ($r20s18 != 5) {
                $r20s26 = $r20s27 = $r20s28 = $r20s29 = $r20s30 = $r20s31 = $r20s32 = $r20s33 = $r20s34 = $r20s35 = $r20s36 = null;
            }

            /**
             * @var integer $dependencia_administrativa
             * @var integer $r20s18 Tipo de atendimento
             * @var integer $r20s37 Modalidade
             * @var integer $r20s38 Etapa de ensino
             * @var integer $r20s19 Turma mais educacao
             * @var integer $r20s6 Tipo mediação
             */
            $turmaMaisEducacao = new ValueTurmaMaisEducacao();
            $turmaMaisEducacao->setDependenciaAdministrativa($dependencia_administrativa);
            $turmaMaisEducacao->setTipoAtendimento($r20s18);
            $turmaMaisEducacao->setModalidade($r20s37);
            $turmaMaisEducacao->setEtapaEnsino($r20s38);
            $turmaMaisEducacao->setTurmaMaisEducacao($r20s19);
            $turmaMaisEducacao->setTipoMediacao($r20s6);

            $r20s19 = $turmaMaisEducacao->getValue();

            $coddigoEducacensoToSeq =
                array(
                    1 => '40',
                    2 => '41',
                    3 => '42',
                    4 => '43',
                    5 => '44',
                    6 => '45',
                    7 => '46',
                    8 => '47',
                    30 => '48',
                    9 => '49',
                    10 => '50',
                    11 => '51',
                    12 => '52',
                    13 => '53',
                    14 => '54',
                    28 => '55',
                    29 => '56',
                    16 => '57',
                    17 => '58',
                    20 => '59',
                    21 => '60',
                    23 => '61',
                    25 => '62',
                    26 => '63',
                    27 => '64',
                    99 => '65'
                );
            try {
                $componentesTurma = App_Model_IedFinder::getComponentesTurma($serieid, $escolaId, $turmaId);
            } catch (Exception $e) {
                $componentesTurma = array();
            }

            foreach ($componentesTurma as $componente) {
                // Só serão consideradas disciplinas tipificadas com o código do Educacenso
                if ($componente->codigo_educacenso) {
                    // Pega o código educacenso
                    $codigoEducacenso = ComponenteCurricular_Model_CodigoEducacenso::getInstance();
                    $codigoEducacenso = $codigoEducacenso->getKey($componente->codigo_educacenso);

                    // Código da disciplina no i-Educar
                    $codigoSistema = $componente->id;

                    // Verifica se é disciplina padrão ano letivo. Se for, será considerado que existe professor
                    // vinculado a disciplina na sala de aula

                    $professorVinculado = (bool)Portabilis_Utils_Database::selectField
                    ('SELECT 1
              FROM modules.professor_turma
            INNER JOIN modules.professor_turma_disciplina ON(professor_turma_disciplina.professor_turma_id = professor_turma.id)
             WHERE professor_turma.turma_id = $1
               AND professor_turma_disciplina.componente_curricular_id = $2
               AND (SELECT 1
                      FROM pmieducar.matricula_turma mt
                     INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
                     WHERE mt.ref_cod_turma = professor_turma.turma_id
                       AND (mt.ativo = 1  OR mt.data_exclusao > DATE($4))
                       AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
                     LIMIT 1) IS NOT NULL
               ',
                        array('params' => array($turmaId, $codigoSistema, $data_ini, $data_fim)));

                    if (array_key_exists($codigoEducacenso, $coddigoEducacensoToSeq)) {
                        if (${'r20s' . $coddigoEducacensoToSeq[$codigoEducacenso]} == 1) {
                            continue;
                        } elseif (${'r20s' . $coddigoEducacensoToSeq[$codigoEducacenso]} == 2) {
                            if (!$professorVinculado) {
                                continue;
                            }
                        }
                        ${'r20s' . $coddigoEducacensoToSeq[$codigoEducacenso]} = ($professorVinculado ? 1 : 2);
                    }
                }

            }

            $atividadeComplementar = 4;
            $atendimentoEducEspecializado = 5;

            $educInfantilCreche = 1;
            $educInfantilPreEscola = 2;
            $educInfantilUnificada = 3;
            $ejaEnsinoFundamental = 65;

            //Percorre todos os campos de disciplinas
            for ($i = 40; $i < 66; $i++) {
                if ($r20s18 == $atividadeComplementar || $r20s18 == $atendimentoEducEspecializado) {
                    ${'r20s' . $i} = '';
                } else {
                    if ($r20s38 == $educInfantilCreche ||
                        $r20s38 == $educInfantilPreEscola ||
                        $r20s38 == $educInfantilUnificada ||
                        $r20s38 == $ejaEnsinoFundamental) {
                        ${'r20s' . $i} = '';
                    }
                }
            }

            if ($r20s18 == $atividadeComplementar || $r20s18 == $atendimentoEducEspecializado) {
                $r20s37 = $r20s38 = '';
            }

            $this->turma_presencial_ou_semi = $r20s6;
            $d = '|';
            $return = '';

            for ($i = 1; $i <= 65; $i++) {
                $return .= ${'r20s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);

            return $return . "\n";
        }
    }

    protected function exportaDadosRegistro30($servidorId, $escolaId)
    {
        $sql =
            ' SELECT
        \'30\' AS r30s1,
        ece.cod_escola_inep AS r30s2,
        ecd.cod_docente_inep AS r30s3,
        s.cod_servidor AS r30s4,
        p.nome AS r30s5,
        p.email AS r30s6,
        NULL AS r30s7,
        fis.data_nasc AS r30s8,
        fis.sexo AS r30s9,
        r.raca_educacenso AS r30s10,
        (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae) AS r30s12,
        (SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_pai) AS r30s13,
        coalesce(fis.nacionalidade,1) AS r30s14,
        (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) AS r30s15,
        uf.cod_ibge AS r30s16,
        m.cod_ibge AS r30s17


        FROM  pmieducar.servidor s
        INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
        INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
        INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN cadastro.fisica_raca rc ON (rc.ref_idpes = fis.idpes)
        LEFT JOIN cadastro.raca r ON (r.cod_raca = rc.ref_cod_raca)
        LEFT JOIN public.municipio m ON (m.idmun = fis.idmun_nascimento)
        LEFT JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
        LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
        WHERE s.cod_servidor = $1
          AND COALESCE(t.nao_informar_educacenso, 0) = 0
          AND e.cod_escola = $2
          AND t.ativo = 1
          AND t.visivel = TRUE
        LIMIT 1
    ';

        // Transforma todos resultados em variáveis
        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('return_only' => 'first-row', 'params' => array($servidorId, $escolaId))));
        if ($r30s1) {
            $r30s5 = $this->convertStringToCenso($r30s5);
            $r30s6 = strtoupper($r30s6);
            $r30s8 = Portabilis_Date_Utils::pgSQLToBr($r30s8);
            $r30s9 = $r30s9 == 'M' ? 1 : 2;
            $r30s10 = is_numeric($r30s10) ? $r30s10 : 0;

            $r30s11 = ($r30s12 || $r30s13) ? 1 : 0;

            $r30s12 = $this->convertStringToAlpha($r30s12);
            $r30s13 = $this->convertStringToAlpha($r30s13);

            if ($r30s14 != '1') {
                $r30s16 = $r30s17 = null;
            }

            if ($r30s14 == '1' || $r30s14 == '2') {
                $r30s15 = 76;
            }

            if ($r30s14 == "1") {
                if (is_null($r30s16) || is_null($r30s17)) {
                    $this->msg .= "Dados para formular o registro 30 da escola {$escolaId} não encontrados. Verifique se os municípios e UFs dos servidores brasileiros possuem código INEP cadastrados.<br/>";
                    $this->error = true;
                }
            }

            $sql = 'SELECT DISTINCT(deficiencia_educacenso) AS id FROM cadastro.fisica_deficiencia,
              cadastro.deficiencia WHERE cod_deficiencia = ref_cod_deficiencia AND ref_idpes = $1
              AND deficiencia_educacenso IS NOT NULL';

            $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sql, array('params' => array($r30s4)));

            $r30s19 = $r30s20 = $r30s21 = $r30s22 = $r30s23 = $r30s24 = $r30s25 = 0;

            $deficienciaToSeq = array(
                1 => '19',
                2 => '20',
                3 => '21',
                4 => '22',
                5 => '23',
                6 => '24',
                7 => '25'
            );
            $r30s18 = 0;

            $arrayDeficienciasProfessor = [];
            foreach ($deficiencias as $deficiencia_educacenso) {
                $deficiencia_educacenso = $deficiencia_educacenso['id'];
                if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)) {
                    ${'r30s' . $deficienciaToSeq[$deficiencia_educacenso]} = 1;
                    $r30s18 = 1;
                }

                $arrayDeficienciasProfessor[] = $deficienciaToSeq[$deficiencia_educacenso];
            }


            $validaDeficienciaMultipla = new ValueDeficienciaMultipla(new DeficienciaMultiplaProfessor(), $arrayDeficienciasProfessor);
            $r30s26 = $validaDeficienciaMultipla->getValue();

            if ($r30s18 == 0) {
                $r30s19 = $r30s20 = $r30s21 = $r30s22 = $r30s23 = $r30s24 = $r30s25 = $r30s26 = null;
            }

            $r30s7 = null;

            $d = '|';
            $return = '';
            $numeroRegistros = 26;

            for ($i = 1; $i <= $numeroRegistros; $i++) {
                $return .= ${'r30s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);

            return $return . "\n";
        }
    }

    protected function exportaDadosRegistro40($servidorId, $escolaId)
    {
        $sql =
            'SELECT

        \'40\' AS r40s1,
        ece.cod_escola_inep AS r40s2,
    ecd.cod_docente_inep AS r40s3,
        s.cod_servidor AS r40s4,
        fis.cpf AS r40s5,
        b.zona_localizacao AS r40s6,
        ep.cep AS r40s7,
        l.idtlog || l.nome AS r40s8,
    ep.numero AS r40s9,
        ep.complemento AS r40s10,
        b.nome AS r40s11,
        uf.cod_ibge AS r40s12,
        m.cod_ibge AS r40s13

        FROM    pmieducar.servidor s
        INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
    INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
    INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
    INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
         LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
         LEFT JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
         LEFT JOIN public.bairro b ON (clb.idbai = b.idbai)
         LEFT JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
         LEFT JOIN public.distrito d ON (d.iddis = b.iddis)
         LEFT JOIN public.municipio m ON (d.idmun = m.idmun)
         LEFT JOIN public.uf ON (uf.sigla_uf = m.sigla_uf)
         LEFT JOIN public.pais ON (pais.idpais = uf.idpais)
         LEFT JOIN public.logradouro l ON (l.idlog = cl.idlog)
     LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
        WHERE s.cod_servidor = $1
      AND COALESCE(t.nao_informar_educacenso, 0) = 0
      AND e.cod_escola = $2
      AND t.ativo = 1
      AND t.visivel = TRUE
        LIMIT 1
    ';

        // Transforma todos resultados em variáveis
        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('return_only' => 'first-row', 'params' => array($servidorId, $escolaId))));
        if ($r40s1) {
            $r40s5 = $this->cpfToCenso($r40s5);

            $r40s8 = $this->convertStringToCenso($r40s8);
            $r40s9 = $this->convertStringToCenso($r40s9);
            $r40s10 = $this->convertStringToCenso($r40s10);
            $r40s11 = $this->convertStringToCenso($r40s11);

            $d = '|';
            $return = '';
            $numeroRegistros = 13;

            for ($i = 1; $i <= $numeroRegistros; $i++) {
                $return .= ${'r40s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);

            return $return . "\n";
        }
    }

    protected function exportaDadosRegistro50($servidorId, $escolaId)
    {
        $sql = <<<'SQL'
            SELECT
        '50' AS r50s1,
        ece.cod_escola_inep AS r50s2,
    ecd.cod_docente_inep AS r50s3,
        s.cod_servidor AS r50s4,
        esc.escolaridade AS r50s5,
        situacao_curso_superior_1 AS r50s6,
        formacao_complementacao_pedagogica_1 AS r50s7,
    (SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_1) AS r50s8,
        (SELECT grau_academico FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_1) AS grau_academico_curso_superior_1,
        ano_inicio_curso_superior_1 AS r50s9,
        ano_conclusao_curso_superior_1 AS r50s10,
        (SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_1) AS r50s11,
        situacao_curso_superior_2 AS r50s12,
        formacao_complementacao_pedagogica_2 AS r50s13,
    (SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_2) AS r50s14,
    (SELECT grau_academico FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_2) AS grau_academico_curso_superior_2,
        ano_inicio_curso_superior_2 AS r50s15,
        ano_conclusao_curso_superior_2 AS r50s16,
        (SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_2) AS r50s17,
        situacao_curso_superior_3 AS r50s18,
        formacao_complementacao_pedagogica_3 AS r50s19,
        (SELECT curso_id FROM modules.educacenso_curso_superior ecs WHERE ecs.id = codigo_curso_superior_3) AS r50s20,
    (SELECT grau_academico FROM modules.educacenso_curso_superior ecs
    WHERE ecs.id = codigo_curso_superior_3) AS grau_academico_curso_superior_3,
        ano_inicio_curso_superior_3 AS r50s21,
        ano_conclusao_curso_superior_3 AS r50s22,
        (SELECT ies_id FROM modules.educacenso_ies ei WHERE ei.id = instituicao_curso_superior_3) AS r50s23,
        (ARRAY[1] <@ pos_graduacao)::INT AS r50s24,
        (ARRAY[2] <@ pos_graduacao)::INT AS r50s25,
        (ARRAY[3] <@ pos_graduacao)::INT AS r50s26,
        (ARRAY[4] <@ pos_graduacao)::INT AS r50s27,
        (ARRAY[1] <@ curso_formacao_continuada)::INT AS r50s28,
        (ARRAY[2] <@ curso_formacao_continuada)::INT AS r50s29,
        (ARRAY[3] <@ curso_formacao_continuada)::INT AS r50s30,
        (ARRAY[4] <@ curso_formacao_continuada)::INT AS r50s31,
        (ARRAY[5] <@ curso_formacao_continuada)::INT AS r50s32,
        (ARRAY[6] <@ curso_formacao_continuada)::INT AS r50s33,
        (ARRAY[7] <@ curso_formacao_continuada)::INT AS r50s34,
        (ARRAY[8] <@ curso_formacao_continuada)::INT AS r50s35,
        (ARRAY[9] <@ curso_formacao_continuada)::INT AS r50s36,
        (ARRAY[10] <@ curso_formacao_continuada)::INT AS r50s37,
        (ARRAY[11] <@ curso_formacao_continuada)::INT AS r50s38,
        (ARRAY[12] <@ curso_formacao_continuada)::INT AS r50s39,
        (ARRAY[13] <@ curso_formacao_continuada)::INT AS r50s40,
        (ARRAY[14] <@ curso_formacao_continuada)::INT AS r50s41,
        (ARRAY[15] <@ curso_formacao_continuada)::INT AS r50s42,
        (ARRAY[16] <@ curso_formacao_continuada)::INT AS r50s43,
        s.situacao_curso_superior_1,
        s.situacao_curso_superior_2,
        s.situacao_curso_superior_3 
        FROM    pmieducar.servidor s
        INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
    INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
    INNER JOIN pmieducar.turma t ON (t.cod_turma = pt.turma_id)
    INNER JOIN pmieducar.escola e ON (e.cod_escola = t.ref_ref_cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN cadastro.escolaridade esc ON (esc.idesco = s.ref_idesco)
    LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
        WHERE s.cod_servidor = $1
      AND COALESCE(t.nao_informar_educacenso, 0) = 0
      AND e.cod_escola = $2
      AND t.ativo = 1
      AND t.visivel = TRUE
        LIMIT 1
SQL;

        // Transforma todos resultados em variáveis
        extract(Portabilis_Utils_Database::fetchPreparedQuery(
            $sql,
            [
                'return_only' => 'first-row',
                'params' => [
                    $servidorId,
                    $escolaId
                ]
            ]
        ));

        if ($r50s1) {
            $d = '|';
            $return = '';
            $numeroRegistros = 43;

            if ($this->isCursoSuperiorBachareladoOuTecnologoCompleto($grau_academico_curso_superior_1,
                $situacao_curso_superior_1)) {
                if (is_null($r50s7)) {
                    $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 7 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
                    $this->error = true;
                }
            } elseif ($grau_academico_curso_superior_1 == self::LICENCIATURA) {
                $r50s7 = null;
            }

            if ($this->isCursoSuperiorBachareladoOuTecnologoCompleto($grau_academico_curso_superior_2,
                $situacao_curso_superior_2)) {
                if (is_null($r50s13)) {
                    $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 14 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
                    $this->error = true;
                }
            } elseif ($grau_academico_curso_superior_2 == self::LICENCIATURA) {
                $r50s13 = null;
            }

            if ($this->isCursoSuperiorBachareladoOuTecnologoCompleto($grau_academico_curso_superior_3,
                $situacao_curso_superior_3)) {
                if (is_null($r50s19)) {
                    $this->msg .= "Dados para formular o registro 50 do servidor {$servidorId} com problemas. O registro 21 é obrigatório para cursos do tipo BACHARELADO ou TECNOLOGO.<br/>";
                    $this->error = true;
                }
            } elseif ($grau_academico_curso_superior_3 == self::LICENCIATURA) {
                $r50s19 = null;
            }

            if ($r50s6 != 2) {
                $r50s9 = null;
            }
            if ($r50s6 != 1) {
                $r50s10 = null;
            }

            if ($r50s12 != 2) {
                $r50s15 = null;
            }
            if ($r50s12 != 1) {
                $r50s16 = null;
            }

            if ($r50s18 != 2) {
                $r50s21 = null;
            }
            if ($r50s18 != 1) {
                $r50s22 = null;
            }

            if ($r50s6 != 1) {
                $r50s7 = null;
            }
            if ($r50s12 != 1) {
                $r50s13 = null;
            }
            if ($r50s18 != 1) {
                $r50s19 = null;
            }

            $situacaoConcluido = ($r50s6 == 1 || $r50s12 == 1 || $r50s18 == 1);

            if (!$situacaoConcluido) {
                $r50s24 = $r50s25 = $r50s26 = $r50s27 = null;
            }

            if ($r50s43 == 1) {
                $r50s28 = $r50s29 = $r50s30 = $r50s31 = $r50s32 = $r50s33 = $r50s34 = $r50s35 = $r50s36 = $r50s37 = $r50s38 = $r50s39 = $r50s40 = $r50s41 = $r50s42 = 0;
            }

            $cont = 0;
            for ($i = 1; $i <= $numeroRegistros; $i++) {
                if ($i >= 31) {
                    $return .= (${'r50s' . $i} == 1 ? 1 : 0) . $d;
                } else {
                    $return .= ${'r50s' . $i} . $d;
                }
            }

            $return = substr_replace($return, "", -1);

            return $return . "\n";
        }
    }

    protected function exportaDadosRegistro51($servidorId, $escolaId, $data_ini, $data_fim, $ano)
    {

        $sql =
            'SELECT

            \'51\' AS r51s1,
            ece.cod_escola_inep AS r51s2,
      ecd.cod_docente_inep AS r51s3,
            s.cod_servidor AS r51s4,
            t.cod_turma AS r51s6,
            pt.funcao_exercida AS r51s7,
            pt.tipo_vinculo AS r51s8,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id
                ORDER BY codigo_educacenso
                OFFSET 0
                LIMIT 1
            ) AS r51s9,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 1
                LIMIT 1
            ) AS r51s10,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 2
                LIMIT 1
            ) AS r51s11,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 3
                LIMIT 1
            ) AS r51s12,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 4
                LIMIT 1
            ) AS r51s13,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 5
                LIMIT 1
            ) AS r51s14,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 6
                LIMIT 1
            ) AS r51s15,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 7
                LIMIT 1
            ) AS r51s16,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 8
                LIMIT 1
            ) AS r51s17,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 9
                LIMIT 1
            ) AS r51s18,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 10
                LIMIT 1
            ) AS r51s19,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 11
                LIMIT 1
            ) AS r51s20,

            (
            SELECT DISTINCT(cc.codigo_educacenso)

                FROM modules.componente_curricular cc
                INNER JOIN modules.professor_turma_disciplina ptd ON (cc.id = ptd.componente_curricular_id)

                WHERE   ptd.professor_turma_id = pt.id

                ORDER BY codigo_educacenso
                OFFSET 12
                LIMIT 1
            ) AS r51s21,
      t.tipo_atendimento AS tipo_atendimento,
      t.etapa_educacenso AS etapa_ensino,
      e.dependencia_administrativa AS dependencia_administrativa


            FROM    pmieducar.servidor s
            INNER JOIN cadastro.fisica fis ON (fis.idpes = s.cod_servidor)
            INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
            INNER JOIN modules.professor_turma pt ON (pt.servidor_id = s.cod_servidor)
            INNER JOIN pmieducar.turma t ON (pt.turma_id = t.cod_turma)
      INNER JOIN pmieducar.escola e ON (t.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
      LEFT JOIN modules.educacenso_cod_docente ecd ON ecd.cod_servidor = s.cod_servidor
            WHERE s.cod_servidor = $1
            AND e.cod_escola = t.ref_ref_cod_escola
      AND COALESCE(t.nao_informar_educacenso, 0) = 0
      AND e.cod_escola = $2
      AND t.ativo = 1
      AND t.visivel = TRUE
      AND t.ano = $5
      AND (SELECT 1
             FROM pmieducar.matricula_turma mt
            INNER JOIN pmieducar.matricula m ON(mt.ref_cod_matricula = m.cod_matricula)
            WHERE mt.ref_cod_turma = t.cod_turma
              AND (mt.ativo = 1 OR mt.data_exclusao > DATE($3))
              AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
            LIMIT 1) IS NOT NULL
    ';


        // Transforma todos resultados em variáveis
        $d = '|';
        $return = '';
        $numeroRegistros = 21;

        $docente = 1;
        $docenteTitular = 5;
        $docenteTutor = 6;

        $atividadeComplementar = 4;
        $atendimentoEducEspecializado = 5;

        $educInfantilCreche = 1;
        $educInfantilPreEscola = 2;
        $educInfantilUnificada = 3;
        $ejaEnsinoFundamental = 65;

        foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($servidorId, $escolaId, $data_ini, $data_fim, $ano))) as $reg) {
            extract($reg);
            for ($i = 1; $i <= $numeroRegistros; $i++) {

                $escolaPrivada = $dependencia_administrativa == 4;

                $funcaoDocente = ($r51s7 == $docente || $r51s7 == $docenteTutor || $r51s7 == $docenteTitular);

                if (!$funcaoDocente || $escolaPrivada) {
                    $r51s8 = '';
                }

                //Validação das disciplinas
                if ($i > 8) {
                    $atividadeDiferenciada = ($tipo_atendimento == $atividadeComplementar ||
                        $tipo_atendimento == $atendimentoEducEspecializado);
                    $etapaEnsino = ($etapa_ensino == $educInfantilCreche ||
                        $etapa_ensino == $educInfantilPreEscola ||
                        $etapa_ensino == $educInfantilUnificada ||
                        $etapa_ensino == $ejaEnsinoFundamental);

                    if (!$funcaoDocente || $atividadeDiferenciada || $etapaEnsino) {
                        ${'r51s' . $i} = '';
                    }
                }

                $return .= ${'r51s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);
            $return .= "\n";
        }
        return $return;
    }

    protected function exportaDadosRegistro60($escolaId, $ano, $data_ini, $data_fim, $alunoId)
    {

        $sql =
            'SELECT

      DISTINCT(a.cod_aluno) AS r60s4,
      p.idpes,
      \'60\' AS r60s1,
      ece.cod_escola_inep AS r60s2,
      eca.cod_aluno_inep AS r60s3,
      p.nome AS r60s5,
      fis.data_nasc AS r60s6, /*tratar formato*/
      fis.sexo AS r60s7, /*tratar na aplicação formato*/
      r.raca_educacenso AS r60s8,
      /*se não tiver r60s10 e 11 é 0 se tiver um dos dois é 1*/
      COALESCE((SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_mae), a.nm_mae) AS r60s10,
      COALESCE((SELECT nome FROM cadastro.pessoa WHERE pessoa.idpes = fis.idpes_pai), a.nm_pai) AS r60s11,
      COALESCE(fis.nacionalidade,1) AS r60s12,
      (SELECT cod_ibge FROM public.pais WHERE pais.idpais = fis.idpais_estrangeiro) AS r60s13,
      uf.cod_ibge AS r60s14,
      mun.cod_ibge AS r60s15,
      (ARRAY[1] <@ recursos_prova_inep)::INT AS r60s30,
      (ARRAY[2] <@ recursos_prova_inep)::INT AS r60s31,
      (ARRAY[3] <@ recursos_prova_inep)::INT AS r60s32,
      (ARRAY[4] <@ recursos_prova_inep)::INT AS r60s33,
      (ARRAY[5] <@ recursos_prova_inep)::INT AS r60s34,
      (ARRAY[6] <@ recursos_prova_inep)::INT AS r60s35,
      (ARRAY[7] <@ recursos_prova_inep)::INT AS r60s36,
      (ARRAY[8] <@ recursos_prova_inep)::INT AS r60s37,
      (ARRAY[9] <@ recursos_prova_inep)::INT AS r60s38,
      fis.nacionalidade AS nacionalidade

      FROM  pmieducar.aluno a
      INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
      INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
      INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
      INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
      INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
      LEFT JOIN cadastro.fisica_raca rc ON (rc.ref_idpes = fis.idpes)
      LEFT JOIN cadastro.raca r ON (r.cod_raca = rc.ref_cod_raca)
      LEFT JOIN public.municipio mun ON (mun.idmun = fis.idmun_nascimento)
      LEFT JOIN public.uf ON (uf.sigla_uf = mun.sigla_uf)
      LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

      WHERE e.cod_escola = $1
      AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
      AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
      AND m.ano = $2
      AND a.cod_aluno = $5
    ';

        // Transforma todos resultados em variáveis
        $d = '|';
        $return = '';
        $numeroRegistros = 39;
        $estrangeiro = 3;
        $naturalizadoBrasileiro = 2;

        $sqlDeficiencias = 'SELECT DISTINCT(deficiencia_educacenso) AS id FROM cadastro.fisica_deficiencia,
                        cadastro.deficiencia WHERE cod_deficiencia = ref_cod_deficiencia AND ref_idpes = $1
                        AND deficiencia_educacenso IS NOT NULL';

        foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
            extract($reg);

            $r60s5 = $this->convertStringToCenso($r60s5);

            $r60s6 = Portabilis_Date_Utils::pgSQLToBr($r60s6);
            $r60s7 = $r60s7 == 'M' ? 1 : 2;
            $r60s8 = is_numeric($r60s8) ? $r60s8 : 0;
            $r60s9 = (int)!(is_null($r60s10) && is_null($r60s11));

            $r60s10 = $this->convertStringToAlpha($r60s10);
            $r60s11 = $this->convertStringToAlpha($r60s11);

            if ($r60s12 == '1' || $r60s12 == '2') {
                $r60s13 = 76;
            }

            if ($nacionalidade == $estrangeiro || $nacionalidade == $naturalizadoBrasileiro) {
                $r60s14 = $r60s15 = null;
            }

            $deficiencias = Portabilis_Utils_Database::fetchPreparedQuery($sqlDeficiencias,
                array('params' => array($idpes)));

            // Reseta deficiências (DEFAULT NULL)
            $r60s16 = 0;
            $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 =
            $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = null;

            $r60s39 = null;

            // Define 'tipodeficiencia' => 'seqleiaute'
            $deficienciaToSeq = MapeamentoDeficienciasAluno::getArrayMapeamentoDeficiencias();

            if (count($deficiencias) == 0) {
                $r60s30 = $r60s31 = $r60s32 = $r60s33 = $r60s34 = $r60s35 = $r60s36 = $r60s37 = $r60s38 = null;
            }

            $arrayDeficienciasAluno = [];
            // Se tiver alguma deficiência, a seq 16 deve ser 1
            if (count($deficiencias) > 0) {
                $r60s16 = 1;
                $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 = $r60s24 =
                $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = 0;

                foreach ($deficiencias as $deficiencia_educacenso) {
                    $deficiencia_educacenso = $deficiencia_educacenso['id'];
                    if (array_key_exists($deficiencia_educacenso, $deficienciaToSeq)) {
                        ${'r60s' . $deficienciaToSeq[$deficiencia_educacenso]} = 1;
                    }

                    $arrayDeficienciasAluno[] = $deficienciaToSeq[$deficiencia_educacenso];
                }
            }
            // Se o aluno não tiver deficiências não pode ser informado recursos para provas
            if ($r60s16) {
                $r60s39 = null;
            } else {
                $r60s17 = $r60s18 = $r60s19 = $r60s20 = $r60s21 = $r60s22 = $r60s23 =
                $r60s25 = $r60s26 = $r60s27 = $r60s28 = $r60s29 = null;
            }

            if (!$this->precisaDeAuxilioEmProvaPorDeficiencia($deficiencias)) {
                $r60s30 = null;
                $r60s31 = null;
                $r60s32 = null;
                $r60s33 = null;
                $r60s34 = null;
                $r60s35 = null;
                $r60s36 = null;
                $r60s37 = null;
                $r60s38 = null;
            } else {
                $r60s39 = 1;
            }

            $validaDeficienciaMultipla = new ValueDeficienciaMultipla(new DeficienciaMultiplaAluno(), $arrayDeficienciasAluno);
            $r60s24 = $validaDeficienciaMultipla->getValue();


            //O campo 39 recebe 0 quando algum campo de 30 à 38 for igual a 1
            for ($i = 30; $i <= 38; $i++) {
                if (${'r60s' . $i} == 1) {
                    $r60s39 = 0;
                }
            }

            //O campo 39 deve ser diferente de com 1 quando o campo 17 ou 21 for igual a 1.
            if ($r60s17 || $r60s21) {
                $r60s39 = 0;
            }

            if ($r60s39) {
                for ($i = 30; $i <= 38; $i++) {
                    ${'r60s' . $i} = 0;
                }
            }

            for ($i = 1; $i <= $numeroRegistros; $i++) {
                $return .= ${'r60s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);
            $return .= "\n";
        }

        return $return;
    }

    protected function precisaDeAuxilioEmProvaPorDeficiencia($deficiencias)
    {
        $deficienciasLayout = MapeamentoDeficienciasAluno::getArrayMapeamentoDeficiencias();

        unset($deficienciasLayout[13]);

        if (count($deficiencias) > 0) {
            foreach ($deficiencias as $deficiencia) {
                $deficiencia = $deficiencia['id'];
                if (array_key_exists($deficiencia, $deficienciasLayout)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim, $alunoId)
    {

        $sql =
            '  SELECT

        DISTINCT(a.cod_aluno) AS r70s4,
        \'70\' AS r70s1,
        ece.cod_escola_inep AS r70s2,
        eca.cod_aluno_inep AS r70s3,
        REGEXP_REPLACE(fd.rg,\'[^a-zA-Z0-9ª°-]\',\'\',\'g\') AS r70s5,
        oer.codigo_educacenso AS r70s6,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_exp_rg) AS r70s7,
        fd.data_exp_rg AS r70s8,
        tipo_cert_civil,
        num_termo AS r70s11,
        num_folha AS r70s12,
        num_livro AS r70s13,
        data_emissao_cert_civil AS r70s14,
        (SELECT cod_ibge FROM public.uf WHERE uf.sigla_uf = fd.sigla_uf_cert_civil) AS r70s15,
        cci.cod_municipio AS r70s16,
        id_cartorio AS r70s17,
        certidao_nascimento AS r70s18,
        fis.cpf AS r70s19,
        fd.passaporte AS r70s20,
        fis.nis_pis_pasep AS r70s21,
        fis.zona_localizacao_censo AS r70s22,
        ep.cep AS r70s23,
        l.idtlog || l.nome AS r70s24,
        ep.numero AS r70s25,
        ep.complemento AS r70s26,
        b.nome AS r70s27,
        uf.cod_ibge AS r70s28,
        mun.cod_ibge AS r70s29,
        fis.nacionalidade AS nacionalidade

        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
         LEFT JOIN cadastro.documento fd ON (fis.idpes = fd.idpes)
         LEFT JOIN cadastro.orgao_emissor_rg oer ON (fd.idorg_exp_rg = oer.idorg_rg)
         LEFT JOIN cadastro.codigo_cartorio_inep cci ON (cci.id = fd.cartorio_cert_civil_inep)
        INNER JOIN cadastro.pessoa p ON (fis.idpes = p.idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
         LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
         LEFT JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
         LEFT JOIN public.bairro b ON (clb.idbai = b.idbai)
         LEFT JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
         LEFT JOIN public.distrito d ON (d.iddis = b.iddis)
         LEFT JOIN public.municipio mun ON (d.idmun = mun.idmun)
         LEFT JOIN public.uf ON (uf.sigla_uf = mun.sigla_uf)
         LEFT JOIN public.pais ON (pais.idpais = uf.idpais)
         LEFT JOIN public.logradouro l ON (l.idlog = cl.idlog)
         LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

        WHERE e.cod_escola = $1
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
        AND m.ano = $2
        AND a.cod_aluno = $5
    ';

        // Transforma todos resultados em variáveis
        $d = '|';
        $return = '';
        $numeroRegistros = 29;

        $estrangeiro = 3;

        foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
            extract($reg);

            $r70s8 = Portabilis_Date_Utils::pgSQLToBr($r70s8);
            $r70s14 = Portabilis_Date_Utils::pgSQLToBr($r70s14);

            $r70s18 = $this->convertStringToCertNovoFormato($r70s18);

            $r70s19 = $this->cpfToCenso($r70s19);

            $r70s24 = $this->convertStringToCenso($r70s24);
            $r70s25 = $this->convertStringToCenso($r70s25);
            $r70s26 = $this->convertStringToCenso(substr($r70s26, 0, 20));
            $r70s27 = $this->convertStringToCenso($r70s27);

            if ($r70s21 == 0) {
                $r70s21 = null;
            }
            if ($r70s5 == 0) {
                $r70s5 = null;
            }

            if (!$r70s5) {
                $r70s6 = null;
                $r70s7 = null;
                $r70s8 = null;
            }

            // Validações referentes a certidões (Modelo antigo e novo, nascimento e casamento)
            $r70s9 = $r70s10 = null;
            if (is_null($tipo_cert_civil) && !empty($r70s18)) {
                $r70s9 = 2;
                $r70s10 = null;
                $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = null;
                $r70s18 = str_replace(' ', '', $r70s18);
            } elseif ($tipo_cert_civil == 91) {
                if (!(is_null($r70s11) || is_null($r70s15) || is_null($r70s17))) {
                    $r70s9 = $r70s10 = 1;
                } else {
                    $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = null;
                }

            } elseif ($tipo_cert_civil == 92) {
                if (!(is_null($r70s11) || is_null($r70s15) || is_null($r70s17))) {
                    $r70s9 = 1;
                    $r70s10 = 2;
                } else {
                    $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = null;
                }
            } else {
                $r70s9 = $r70s10 = $r70s11 = $r70s12 = $r70s13 = $r70s14 = $r70s15 = $r70s16 = $r70s17 = $r70s18 = null;
            }
            // fim das validações de certidões //

            if ($nacionalidade == $estrangeiro) {
                for ($i = 5; $i < 19; $i++) {
                    ${'r70s' . $i} = null;
                }
            } else {
                $r70s20 = null;
            }

            for ($i = 1; $i <= $numeroRegistros; $i++) {
                $return .= ${'r70s' . $i} . $d;
            }

            $return = $this->upperAndUnaccent(substr_replace($return, "", -1));

            $return .= "\n";
        }

        return $return;
    }

    protected function exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim, $alunoId)
    {

        $sql =
            '  SELECT

        \'80\' AS r80s1,
        ece.cod_escola_inep AS r80s2,
        eca.cod_aluno_inep AS r80s3,
        a.cod_aluno AS r80s4,
        t.cod_turma AS r80s6,
        mt.turma_unificada AS r80s8,
        mt.etapa_educacenso AS r80s9,
        a.recebe_escolarizacao_em_outro_espaco AS r80s10,
        ta.responsavel AS transporte_escolar,
        t.etapa_educacenso,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 1
        ) AS r80s13,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 2
        ) AS r80s14,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 3
        ) AS r80s15,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 4
        ) AS r80s16,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 5
        ) AS r80s17,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 6
        ) AS r80s18,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 7
        ) AS r80s19,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 8
        ) AS r80s20,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 9
        ) AS r80s21,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 10
        ) AS r80s22,
        (
          SELECT COUNT(1)
          FROM modules.veiculo v
          INNER JOIN modules.itinerario_transporte_escolar ite ON (ite.ref_cod_veiculo = v.cod_veiculo)
          INNER JOIN modules.rota_transporte_escolar rte ON (ite.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          INNER JOIN modules.pessoa_transporte pt ON (pt.ref_cod_rota_transporte_escolar = rte.cod_rota_transporte_escolar)
          WHERE pt.ref_idpes = fis.idpes
          AND v.ref_cod_tipo_veiculo = 11
        ) AS r80s23,

        a.veiculo_transporte_escolar,
        t.tipo_atendimento

        FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = mt.ref_cod_turma)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
        LEFT JOIN modules.transporte_aluno ta ON (ta.aluno_id = a.cod_aluno)
        LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno

        WHERE e.cod_escola = $1
        AND COALESCE(t.nao_informar_educacenso, 0) = 0
        AND t.ativo = 1
        AND t.visivel = TRUE
        AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
        AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
        AND m.ano = $2
        AND a.cod_aluno = $5
        AND m.ativo = 1
        AND COALESCE(mt.remanejado, FALSE) = FALSE
        AND (CASE WHEN m.aprovado = 3
                  THEN mt.ativo = 1
                  ELSE mt.sequencial = (SELECT MAX(sequencial)
                                          FROM pmieducar.matricula_turma
                                         WHERE matricula_turma.ref_cod_matricula = m.cod_matricula)
            END)
    ';

        // Transforma todos resultados em variáveis
        $d = '|';
        $return = '';
        $numeroRegistros = 24;
        $atividadeComplementar = 4;
        $atendimentoEducEspecializado = 5;

        foreach (Portabilis_Utils_Database::fetchPreparedQuery($sql,
            array('params' => array($escolaId, $ano, $data_ini, $data_fim, $alunoId))) as $reg) {
            extract($reg);

            if ($tipo_atendimento == $atividadeComplementar || $tipo_atendimento == $atendimentoEducEspecializado) {
                $r80s10 = '';
            }

            if (!in_array($etapa_educacenso, App_Model_Educacenso::etapasEnsinoUnificadas())) {
                $r80s8 = '';
            }

            $etapasEducacensoMulti = array(12, 13, 22, 23, 24, 56, 64, 72);

            if (!in_array($etapa_educacenso, $etapasEducacensoMulti)) {
                $r80s9 = null;
            }

            $r80s10 = ($r80s10 == 0 ? null : $r80s10);

            for ($i = 13; $i <= 23; $i++) {
                ${'r80s' . $i} = 0;
            }

            if (is_null($transporte_escolar)) {
                $r80s11 = null;
            } else {
                $r80s11 = (($transporte_escolar == 0) ? 0 : 1);
                if ($r80s11) {
                    $r80s12 = $transporte_escolar;
                }
            }
            ${'r80s' . ($veiculo_transporte_escolar + 12)} = 1;
            $utiliza_algum_veiculo = false;
            for ($i = 13; $i <= 23; $i++) {
                $utiliza_algum_veiculo = (${'r80s' . $i} == 1) || $utiliza_algum_veiculo;
            }

            if (!$transporte_escolar) {
                for ($i = 12; $i <= 23; $i++) {
                    ${'r80s' . $i} = null;
                }
            }

            if ($transporte_escolar && !$utiliza_algum_veiculo) {
                $this->msg .= "Dados para formular o registro 80 campo 11 da escola {$escolaId} com problemas. Verifique se o campo tipo de veículo foi preenchido no aluno {$alunoId}.<br/>";
                $this->error = true;
            }

            if ($this->turma_presencial_ou_semi == 1 || $this->turma_presencial_ou_semi == 2) {
                if (is_null($r80s11)) {
                    $this->msg .= "Dados para formular o registro 80 campo 11 da escola {$escolaId} com problemas. Verifique se o campo transporte escolar foi preenchido para aluno {$alunoId}.<br/>";
                    $this->error = true;
                }
            }

            if (in_array($etapa_educacenso, App_Model_Educacenso::etapasEnsinoUnificadas())) {
                if (empty($r80s8)) {
                    $this->msg .= "Dados para formular o registro 80 campo 8 da escola {$escolaId} com problemas. Verifique se o campo etapa da turma unificada foi preenchido para aluno {$alunoId}.<br/>";
                    $this->error = true;
                }
            }

            // fim validações transporte escolar

            for ($i = 1; $i <= $numeroRegistros; $i++) {
                $return .= ${'r80s' . $i} . $d;
            }

            $return = substr_replace($return, "", -1);
            $return .= "\n";
        }

        return $return;
    }

    protected function exportaDadosRegistro99()
    {
        return "99|\n";
    }

    protected function exportaDadosRegistro89($escolaId)
    {
        $sql = "SELECT '89' AS r89s1,
                   educacenso_cod_escola.cod_escola_inep AS r89s2,
                   gestor_f.cpf AS r89s3,
                   gestor_p.nome AS r89s4,
                   escola.cargo_gestor AS r89s5,
                   gestor_p.email AS r89s6
              FROM pmieducar.escola
              LEFT JOIN modules.educacenso_cod_escola ON (educacenso_cod_escola.cod_escola = escola.cod_escola)
              LEFT JOIN cadastro.fisica gestor_f ON (gestor_f.idpes = escola.ref_idpes_gestor)
              LEFT JOIN cadastro.pessoa gestor_p ON (gestor_p.idpes = escola.ref_idpes_gestor)
             WHERE escola.cod_escola = $1";

        $numeroRegistros = 6;
        $return = '';

        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array(
            'return_only' => 'first-row',
            'params' => array($escolaId)
        )));

        $r89s3 = $this->cpfToCenso($r89s3);
        $r89s4 = $this->convertStringToAlpha($r89s4);
        $r89s6 = $this->convertEmailToCenso($r89s6);

        for ($i = 1; $i <= $numeroRegistros; $i++) {
            $return .= ${'r89s' . $i} . '|';
        }

        $return = substr_replace($return, "", -1);
        $return .= "\n";

        return $return;
    }

    protected function exportaDadosRegistro90($escolaId, $turmaId, $matriculaId)
    {

        $sql = "SELECT '90' AS r90s1,
                   educacenso_cod_escola.cod_escola_inep AS r90s2,
                   educacenso_cod_aluno.cod_aluno_inep AS r90s5,
                   matricula.ref_cod_aluno AS r90s6,
                   matricula.aprovado AS r90s8
            FROM pmieducar.matricula
            INNER JOIN pmieducar.escola ON (escola.cod_escola = matricula.ref_ref_cod_escola)
            INNER JOIN modules.educacenso_cod_escola ON (escola.cod_escola = educacenso_cod_escola.cod_escola)
             LEFT JOIN modules.educacenso_cod_aluno ON (educacenso_cod_aluno.cod_aluno = matricula.ref_cod_aluno)
            WHERE escola.cod_escola = $1
              AND matricula.cod_matricula = $2";

        $numeroRegistros = 8;
        $return = '';

        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array(
            'return_only' => 'first-row',
            'params' => array($escolaId, $matriculaId)
        )));

        $turma = new clsPmieducarTurma($turmaId);
        $inep = $turma->getInep();

        $turma = $turma->detalhe();
        $serieId = $turma['ref_ref_cod_serie'];

        $serie = new clsPmieducarSerie($serieId);
        $serie = $serie->detalhe();

        $anoConcluinte = $serie['concluinte'] == 2;

        $r90s3 = $turmaId;
        $r90s4 = ($inep ? $inep : null);
        $r90s7 = null;

        // Atualiza situação para código do censo
        switch ($r90s8) {
            case 4:
                $r90s8 = 1;
                break;
            case 6:
                $r90s8 = 2;
                break;
            case 15:
                $r90s8 = 3;
                break;
            case 2:
                $r90s8 = 4;
                break;
            case 1:
                $r90s8 = ($anoConcluinte ? 6 : 5);
                break;
            case 3:
                $r90s8 = 7;
                break;
        }

        for ($i = 1; $i <= $numeroRegistros; $i++) {
            $return .= ${'r90s' . $i} . '|';
        }

        $return = substr_replace($return, "", -1);
        $return .= "\n";

        return $return;
    }

    protected function exportaDadosRegistro91($escolaId, $turmaId, $matriculaId)
    {

        $sql = "SELECT '91' AS r91s1,
                   educacenso_cod_escola.cod_escola_inep AS r91s2,
                   educacenso_cod_aluno.cod_aluno_inep AS r91s5,
                   matricula.ref_cod_aluno AS r91s6,
                   curso.modalidade_curso AS r91s9,
                   matricula.aprovado AS r91s11
            FROM pmieducar.matricula
            INNER JOIN pmieducar.escola ON (escola.cod_escola = matricula.ref_ref_cod_escola)
            INNER JOIN modules.educacenso_cod_escola ON (escola.cod_escola = educacenso_cod_escola.cod_escola)
             LEFT JOIN modules.educacenso_cod_aluno ON (educacenso_cod_aluno.cod_aluno = matricula.ref_cod_aluno)
            INNER JOIN pmieducar.curso ON (curso.cod_curso = matricula.ref_cod_curso)
            WHERE escola.cod_escola = $1
              AND matricula.cod_matricula = $2";

        $numeroRegistros = 11;
        $return = '';

        extract(Portabilis_Utils_Database::fetchPreparedQuery($sql, array(
            'return_only' => 'first-row',
            'params' => array($escolaId, $matriculaId)
        )));

        $turma = new clsPmieducarTurma($turmaId);
        $inep = $turma->getInep();

        $turma = $turma->detalhe();
        $serieId = $turma['ref_ref_cod_serie'];

        $serie = new clsPmieducarSerie($serieId);
        $serie = $serie->detalhe();

        $anoConcluinte = $serie['concluinte'] == 2;
        $etapaEducacenso = $turma['etapa_educacenso'];

        $etapasValidasEducacenso = array(3, 12, 13, 22, 23, 24, 56, 64, 72);

        $tipoMediacaoDidaticoPedagogico = $turma['tipo_mediacao_didatico_pedagogico'];

        $r91s3 = $turmaId;
        $r91s4 = ($inep ? $inep : null);
        $r91s7 = null;
        $r91s8 = ($inep ? null : $tipoMediacaoDidaticoPedagogico);
        $r91s9 = ($inep ? null : $r91s9);
        $r91s10 = (in_array($etapaEducacenso, $etapasValidasEducacenso) ? $etapaEducacenso : null);

        // Atualiza situação para código do censo
        switch ($r91s11) {
            case 4:
                $r91s11 = 1;
                break;
            case 6:
                $r91s11 = 2;
                break;
            case 15:
                $r91s11 = 3;
                break;
            case 2:
                $r91s11 = 4;
                break;
            case 1:
                $r91s11 = ($anoConcluinte ? 6 : 5);
                break;
            case 3:
                $r91s11 = 7;
                break;
        }

        for ($i = 1; $i <= $numeroRegistros; $i++) {
            $return .= ${'r91s' . $i} . '|';
        }

        $return = substr_replace($return, "", -1);
        $return .= "\n";

        return $return;
    }

    protected function cpfToCenso($cpf)
    {
        $cpf = str_replace(array('.', '-'), '', int2CPF($cpf));
        return $cpf == '00000000000' ? null : $cpf;
    }

    protected function cnpjToCenso($cnpj)
    {
        $cnpj = str_replace(array('.', '-', '/'), '', int2CNPJ($cnpj));
        return $cnpj == '00000000000000' ? null : $cnpj;
    }

    protected function upperAndUnaccent($string)
    {
        $string = Portabilis_String_Utils::toUtf8($string);
        $string = preg_replace(array(
            "/(á|à|ã|â|ä)/",
            "/(Á|À|Ã|Â|Ä)/",
            "/(é|è|ê|ë)/",
            "/(É|È|Ê|Ë)/",
            "/(í|ì|î|ï)/",
            "/(Í|Ì|Î|Ï)/",
            "/(ó|ò|õ|ô|ö)/",
            "/(Ó|Ò|Õ|Ô|Ö)/",
            "/(ú|ù|û|ü)/",
            "/(Ú|Ù|Û|Ü)/",
            "/(ñ)/",
            "/(Ñ)/",
            "/(ç)/",
            "/(Ç)/"
        ),
            explode(" ", "a A e E i I o O u U n N c C"), $string);

        return strtoupper($string);
    }

    protected function convertStringToAlpha($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras
        $alphas = range('A', 'Z');
        $caracteresAceitos = array(" ");
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);


        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        //Elimina espaços indesejados
        $string = trim($string);
        $string = preg_replace('/( )+/', ' ', $string);

        return $string;
    }

    protected function convertStringToCenso($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras e numeros e alguns caracteres especiais
        $alphas = range('A', 'Z');
        $numbers = range(0, 9);
        $caracteresAceitos = array(" ", "ª", "º", "-");
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        //Elimina espaços indesejados
        $string = trim($string);
        $string = preg_replace('/( )+/', ' ', $string);

        return $string;
    }

    protected function convertStringToCertNovoFormato($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas números e letra X
        $numbers = range(0, 9);
        $caracteresAceitos = array(" ", "x", "X");
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        return $string;
    }

    protected function convertEmailToCenso($string)
    {
        $string = $this->upperAndUnaccent($string);

        //Aceita apenas letras e numeros e alguns caracteres especiais
        $alphas = range('A', 'Z');
        $numbers = range(0, 9);
        $caracteresAceitos = array("_", "-", "@", ".");
        $caracteresAceitos = array_merge($numbers, $caracteresAceitos);
        $caracteresAceitos = array_merge($alphas, $caracteresAceitos);

        //Aplica filtro na string eliminando caracteres indesejados
        $regex = sprintf('/[^%s]/u', preg_quote(join($caracteresAceitos), '/'));
        $string = preg_replace($regex, '', $string);

        return $string;
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'educacenso-export')) {
            $this->appendResponse($this->educacensoExport());
        } elseif ($this->isRequestFor('get', 'educacenso-export-fase2')) {
            $this->appendResponse($this->educacensoExportFase2());
        } else {
            $this->notImplementedOperationError();
        }
    }

    /**
     * Retorna true se o grau acadêmido informado for bacharelado ou tecnólogo e se a situação informada for concluído
     *
     * @param $grauAcademico
     * @param $situacao
     * @return bool
     */
    private function isCursoSuperiorBachareladoOuTecnologoCompleto($grauAcademico, $situacao): bool
    {
        if ($situacao != iEducar\App\Model\Servidor::SITUACAO_CURSO_SUPERIOR_CONCLUIDO) {
            return false;
        }

        return in_array($grauAcademico, [
            self::BACHARELADO,
            self::TECNOLOGO
        ]);
    }
}
