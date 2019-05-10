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

            $registro51 = $this->exportaDadosRegistro51($servidor['id'], $escolaId, $data_ini, $data_fim, $ano);
            if (!empty($registro51)) {
                $export .= $registro51;
            }
        }

        $export .= $this->exportaDadosRegistro30($escolaId, $ano);
        $export .= $this->exportaDadosRegistro40($escolaId);
        $export .= $this->exportaDadosRegistro50($escolaId, $ano);
        $export .= $this->exportaDadosRegistro60($escolaId, $ano);

        foreach ($this->getAlunos($escolaId, $ano, $data_ini, $data_fim) as $alunoId) {
            $registro70 = $this->exportaDadosRegistro70($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
            $registro80 = $this->exportaDadosRegistro80($escolaId, $ano, $data_ini, $data_fim, $alunoId['id']);
            if (!empty($registro70) && !empty($registro80)) {
                $export .= $registro70 . $registro80;
            }
        }
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

    protected function exportaDadosRegistro30($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository();

        $registro40Model = new Registro40();
        $registro40 = new Registro40Data($educacensoRepository, $registro40Model);

        $registro50Model = new Registro50();
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);

        $registro60Model = new Registro60();
        $registro60 = new Registro60Data($educacensoRepository, $registro60Model);

        /** @var Registro40[] $gestores */
        $gestores = $registro40->getExportFormatData($escolaId);

        /** @var Registro50[] $docentes */
        $docentes = $registro50->getExportFormatData($escolaId, $ano);

        /** @var Registro60[] $alunos */
        $alunos = $registro60->getExportFormatData($escolaId, $ano);
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
