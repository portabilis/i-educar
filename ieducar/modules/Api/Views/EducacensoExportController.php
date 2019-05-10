<?php

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\Registro60;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\ArrayToCenso;
use iEducar\Modules\Educacenso\Data\Registro00 as Registro00Data;
use iEducar\Modules\Educacenso\Data\Registro10 as Registro10Data;
use iEducar\Modules\Educacenso\Data\Registro20 as Registro20Data;
use iEducar\Modules\Educacenso\Data\Registro40 as Registro40Data;
use iEducar\Modules\Educacenso\Data\Registro50 as Registro50Data;
use iEducar\Modules\Educacenso\Data\Registro60 as Registro60Data;
use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaAluno;
use iEducar\Modules\Educacenso\Deficiencia\DeficienciaMultiplaProfessor;
use iEducar\Modules\Educacenso\Deficiencia\MapeamentoDeficienciasAluno;
use iEducar\Modules\Educacenso\Deficiencia\ValueDeficienciaMultipla;
use iEducar\Modules\Educacenso\ExportRule\CargoGestor;
use iEducar\Modules\Educacenso\ExportRule\ComponentesCurriculares;
use iEducar\Modules\Educacenso\ExportRule\CriterioAcessoGestor;
use iEducar\Modules\Educacenso\ExportRule\DependenciaAdministrativa;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoResponsavelTransporte;
use iEducar\Modules\Educacenso\ExportRule\RecebeEscolarizacaoOutroEspaco;
use iEducar\Modules\Educacenso\ExportRule\Regulamentacao;
use iEducar\Modules\Educacenso\ExportRule\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\ExportRule\TiposAee;
use iEducar\Modules\Educacenso\ExportRule\TipoVinculoServidor;
use iEducar\Modules\Educacenso\ExportRule\TransporteEscolarPublico;
use iEducar\Modules\Educacenso\ExportRule\TurmaMulti;
use iEducar\Modules\Educacenso\ExportRule\VeiculoTransporte;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\Educacenso\ValueTurmaMaisEducacao;
use Illuminate\Support\Facades\Session;

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
    use Formatters;

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
        $this->pessoa_logada = Session::get('id_pessoa');

        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(846, $this->pessoa_logada, 7,
            'educar_index.php');
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        $continuaExportacao = true;
        $export = $this->exportaDadosRegistro00($escolaId, $ano, $continuaExportacao);
        if (!$continuaExportacao) {
            return $export;
        }

        $export .= $this->exportaDadosRegistro10($escolaId, $ano);
        $export .= $this->exportaDadosRegistro20($escolaId, $ano);
        foreach ($this->getServidores($escolaId, $ano, $data_ini, $data_fim) as $servidor) {
            $export .= $this->exportaDadosRegistro30($servidor['id'], $escolaId);
        }

        $export .= $this->exportaDadosRegistro40($escolaId);
        $export .= $this->exportaDadosRegistro50($escolaId, $ano);
        $export .= $this->exportaDadosRegistro60($escolaId, $ano);

        $export .= $this->exportaDadosRegistro99();
        return $export;
    }

    protected function exportaDadosCensoPorEscolaFase2($escolaId, $ano, $data_ini, $data_fim)
    {
        $this->pessoa_logada = Session::get('id_pessoa');

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

    protected function exportaDadosRegistro00($escolaId, $ano, &$continuaExportacao)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro00Model = new Registro00();
        $registro00 = new Registro00Data($educacensoRepository, $registro00Model);
        $escola = $registro00->getExportFormatData($escolaId, $ano);

        if (empty($escola->codigoInep)) {
            $this->msg .= "Dados para formular o registro 00 da escola {$escolaId} não encontrados. Verifique se a escola possuí endereço normalizado, código do INEP e dados do gestor cadastrados.<br/>";
            $this->error = true;
        }

        $escola = SituacaoFuncionamento::handle($escola);
        $escola = DependenciaAdministrativa::handle($escola);
        $escola = Regulamentacao::handle($escola);

        $continuaExportacao = !in_array($escola->situacaoFuncionamento, [2, 3]);

        $data = [
            $escola->registro,
            $escola->codigoInep,
            $escola->situacaoFuncionamento,
            $escola->inicioAnoLetivo,
            $escola->fimAnoLetivo,
            $escola->nome,
            $escola->cep,
            $escola->codigoIbgeMunicipio,
            $escola->codigoIbgeDistrito,
            $escola->logradouro,
            $escola->numero,
            $escola->complemento,
            $escola->bairro,
            $escola->ddd,
            $escola->telefone,
            $escola->telefoneOutro,
            $escola->email,
            $escola->orgaoRegional,
            $escola->zonaLocalizacao,
            $escola->localizacaoDiferenciada,
            $escola->dependenciaAdministrativa,
            $escola->orgaoEducacao,
            $escola->orgaoSeguranca,
            $escola->orgaoSaude,
            $escola->orgaoOutro,
            $escola->mantenedoraEmpresa,
            $escola->mantenedoraSindicato,
            $escola->mantenedoraOng,
            $escola->mantenedoraInstituicoes,
            $escola->mantenedoraSistemaS,
            $escola->mantenedoraOscip,
            $escola->categoriaEscolaPrivada,
            $escola->conveniadaPoderPublico,
            $escola->cnpjMantenedoraPrincipal,
            $escola->cnpjEscolaPrivada,
            $escola->regulamentacao,
            $escola->esferaFederal,
            $escola->esferaEstadual,
            $escola->esferaMunicipal,
            $escola->unidadeVinculada,
            $escola->inepEscolaSede,
            $escola->codigoIes,
        ];

        return ArrayToCenso::format($data) . PHP_EOL;
    }

    protected function exportaDadosRegistro10($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro10Model = new Registro10();
        $registro10 = new Registro10Data($educacensoRepository, $registro10Model);
        $data = $registro10->getExportFormatData($escolaId, $ano);

        return ArrayToCenso::format($data) . PHP_EOL;
    }

    protected function exportaDadosRegistro20($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro20Model = new Registro20();
        $registro20 = new Registro20Data($educacensoRepository, $registro20Model);
        $data = $registro20->getExportFormatData($escolaId, $ano);

        return implode(PHP_EOL, array_map(function($record) {
            return ArrayToCenso::format($record);
        }, $data)) . PHP_EOL;
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

    protected function exportaDadosRegistro40($escolaId)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro40Model = new Registro40();
        $registro40 = new Registro40Data($educacensoRepository, $registro40Model);

        /** @var Registro40[] $gestores */
        $gestores = $registro40->getExportFormatData($escolaId);

        $stringCenso = '';
        foreach ($gestores as $gestor) {
            $gestor = CargoGestor::handle($gestor);
            /** @var Registro40 $gestor */
            $gestor = CriterioAcessoGestor::handle($gestor);

            $data = [
                $gestor->registro,
                $gestor->inepEscola,
                $gestor->codigoPessoa,
                $gestor->inepGestor,
                $gestor->cargo,
                $gestor->criterioAcesso,
                $gestor->especificacaoCriterioAcesso,
                $gestor->tipoVinculo
            ];

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
    }

    protected function exportaDadosRegistro50($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro50Model = new Registro50();
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);

        $quantidadeComponentes = 15;

        /** @var Registro50[] $docentes */
        $docentes = $registro50->getExportFormatData($escolaId, $ano);

        $stringCenso = '';
        foreach ($docentes as $docente) {
            $docente = TipoVinculoServidor::handle($docente);
            /** @var Registro50 $docente */
            $docente = ComponentesCurriculares::handle($docente);

            $data = [
                $docente->registro,
                $docente->inepEscola,
                $docente->codigoPessoa,
                $docente->inepDocente,
                $docente->codigoTurma,
                $docente->inepTurma,
                $docente->funcaoDocente,
                $docente->tipoVinculo,
            ];

            for ($count = 0; $count <= $quantidadeComponentes - 1; $count++) {
                $data[] = $docente->componentes[$count];
            }

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
    }

    protected function exportaDadosRegistro60($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro60Model = new Registro60();
        $registro60 = new Registro60Data($educacensoRepository, $registro60Model);

        /** @var Registro60[] $alunos */
        $alunos = $registro60->getExportFormatData($escolaId, $ano);

        $stringCenso = '';
        foreach ($alunos as $aluno) {
            $aluno = TurmaMulti::handle($aluno);
            $aluno = TiposAee::handle($aluno);
            $aluno = RecebeEscolarizacaoOutroEspaco::handle($aluno);
            $aluno = TransporteEscolarPublico::handle($aluno);
            $aluno = VeiculoTransporte::handle($aluno);
            /** @var Registro60 $aluno */
            $aluno = PoderPublicoResponsavelTransporte::handle($aluno);

            $data = [
                $aluno->registro,
                $aluno->inepEscola,
                $aluno->codigoPessoa,
                $aluno->inepAluno,
                $aluno->codigoTurma,
                $aluno->inepTurma,
                $aluno->matriculaAluno,
                $aluno->etapaAluno,
                $aluno->tipoAtendimentoDesenvolvimentoFuncoesGognitivas,
                $aluno->tipoAtendimentoDesenvolvimentoVidaAutonoma,
                $aluno->tipoAtendimentoEnriquecimentoCurricular,
                $aluno->tipoAtendimentoEnsinoInformaticaAcessivel,
                $aluno->tipoAtendimentoEnsinoLibras,
                $aluno->tipoAtendimentoEnsinoLinguaPortuguesa,
                $aluno->tipoAtendimentoEnsinoSoroban,
                $aluno->tipoAtendimentoEnsinoBraile,
                $aluno->tipoAtendimentoEnsinoOrientacaoMobilidade,
                $aluno->tipoAtendimentoEnsinoCaa,
                $aluno->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos,
                $aluno->recebeEscolarizacaoOutroEspacao,
                $aluno->transportePublico,
                $aluno->poderPublicoResponsavelTransporte,
                $aluno->veiculoTransporteBicicleta,
                $aluno->veiculoTransporteMicroonibus,
                $aluno->veiculoTransporteOnibus,
                $aluno->veiculoTransporteTracaoAnimal,
                $aluno->veiculoTransporteVanKonbi,
                $aluno->veiculoTransporteOutro,
                $aluno->veiculoTransporteAquaviarioCapacidade5,
                $aluno->veiculoTransporteAquaviarioCapacidade5a15,
                $aluno->veiculoTransporteAquaviarioCapacidade15a35,
                $aluno->veiculoTransporteAquaviarioCapacidadeAcima35
            ];

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
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
