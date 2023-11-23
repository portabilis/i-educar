<?php

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\Registro60;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\ArrayToCenso;
use iEducar\Modules\Educacenso\Data\Registro00 as Registro00Data;
use iEducar\Modules\Educacenso\Data\Registro10 as Registro10Data;
use iEducar\Modules\Educacenso\Data\Registro20 as Registro20Data;
use iEducar\Modules\Educacenso\Data\Registro30 as Registro30Data;
use iEducar\Modules\Educacenso\Data\Registro40 as Registro40Data;
use iEducar\Modules\Educacenso\Data\Registro50 as Registro50Data;
use iEducar\Modules\Educacenso\Data\Registro60 as Registro60Data;
use iEducar\Modules\Educacenso\Deficiencia\MapeamentoDeficienciasAluno;
use iEducar\Modules\Educacenso\ExportRule\CargoGestor;
use iEducar\Modules\Educacenso\ExportRule\ComponentesCurriculares;
use iEducar\Modules\Educacenso\ExportRule\CriterioAcessoGestor;
use iEducar\Modules\Educacenso\ExportRule\ItinerarioFormativoAluno;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoResponsavelTransporte;
use iEducar\Modules\Educacenso\ExportRule\RecebeEscolarizacaoOutroEspaco;
use iEducar\Modules\Educacenso\ExportRule\RegrasEspecificasRegistro30;
use iEducar\Modules\Educacenso\ExportRule\RegrasGeraisRegistro30;
use iEducar\Modules\Educacenso\ExportRule\TiposAee;
use iEducar\Modules\Educacenso\ExportRule\TipoVinculoGestor;
use iEducar\Modules\Educacenso\ExportRule\TipoVinculoServidor;
use iEducar\Modules\Educacenso\ExportRule\TransporteEscolarPublico;
use iEducar\Modules\Educacenso\ExportRule\TurmaMulti;
use iEducar\Modules\Educacenso\ExportRule\UnidadesCurricularesServidor;
use iEducar\Modules\Educacenso\ExportRule\VeiculoTransporte;
use iEducar\Modules\Educacenso\Formatters;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento as ModelSituacaoFuncionamento;

/**
 * Class EducacensoExportController
 *
 * @deprecated Essa versão da API pública será descontinuada
 */
class EducacensoExportController extends ApiCoreController
{
    use Formatters;

    public $ref_cod_escola;

    public $ref_cod_escola_;

    public $ref_cod_serie;

    public $ref_cod_serie_;

    public $ref_usuario_exc;

    public $ref_usuario_cad;

    public $hora_inicial;

    public $hora_final;

    public $data_cadastro;

    public $data_exclusao;

    public $ativo;

    public $hora_inicio_intervalo;

    public $hora_fim_intervalo;

    public $hora_fim_intervalo_;

    public $ano;

    public $ref_cod_instituicao;

    public $msg = '';

    public $error = false;

    public $turma_presencial_ou_semi;

    const TECNOLOGO = 1;

    const LICENCIATURA = 2;

    const BACHARELADO = 3;

    protected function educacensoExport()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $conteudo = $this->exportaDadosCensoPorEscola(
            $escola,
            $ano,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim)
        );

        if ($this->error) {
            return [
                'error' => true,
                'mensagem' => $this->msg,
            ];
        }

        return ['conteudo' => $conteudo];
    }

    protected function exportaDadosCensoPorEscola($escolaId, $ano, $data_ini, $data_fim)
    {
        $obj_permissoes = new clsPermissoes();
        $obj_permissoes->permissao_cadastra(
            846,
            $this->pessoa_logada,
            7,
            'educar_index.php'
        );
        $this->ref_cod_instituicao = $obj_permissoes->getInstituicao($this->pessoa_logada);
        $continuaExportacao = true;
        $export = $this->exportaDadosRegistro00($escolaId, $ano, $continuaExportacao);

        if ($continuaExportacao) {
            $export .= $this->exportaDadosRegistro10($escolaId, $ano);
            $export .= $this->exportaDadosRegistro20($escolaId, $ano);
        }

        $export .= $this->exportaDadosRegistro30($escolaId, $ano);
        $export .= $this->exportaDadosRegistro40($escolaId);

        if ($continuaExportacao) {
            $export .= $this->exportaDadosRegistro50($escolaId, $ano);
            $export .= $this->exportaDadosRegistro60($escolaId, $ano);
        }

        $export .= $this->exportaDadosRegistro99();

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

        return Portabilis_Utils_Database::fetchPreparedQuery(
            $sql,
            ['params' => [$escolaId, $ano, $data_ini, $data_fim]]
        );
    }

    protected function exportaDadosRegistro00($escolaId, $ano, &$continuaExportacao)
    {
        $educacensoRepository = new EducacensoRepository();
        $registro00Model = new Registro00();
        $registro00 = new Registro00Data($educacensoRepository, $registro00Model);
        $data = $registro00->getExportFormatData($escolaId, $ano);

        if (empty($registro00->codigoInep)) {
            $this->msg .= "Dados para formular o registro 00 da escola {$registro00->nomeEscola} não encontrados. Verifique se a escola possuí endereço normalizado, código do INEP e dados do gestor cadastrados.<br/>";
            $this->error = true;
        }

        $continuaExportacao = !in_array($registro00->situacaoFuncionamento, [ModelSituacaoFuncionamento::EXTINTA, ModelSituacaoFuncionamento::PARALISADA]);

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

        return implode(PHP_EOL, array_map(function ($record) {
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

        $registro30Data = new Registro30Data($educacensoRepository, new Registro30());
        $registro30Data->setArrayDataByType($gestores, Registro30::TIPO_MANAGER);
        $registro30Data->setArrayDataByType($docentes, Registro30::TIPO_TEACHER);
        $registro30Data->setArrayDataByType($alunos, Registro30::TIPO_STUDENT);

        $pessoas = $registro30Data->getExportFormatData($escolaId);
        $stringCenso = '';

        foreach ($pessoas as $pessoa) {
            $pessoa = RegrasGeraisRegistro30::handle($pessoa);
            /** @var Registro30 $pessoa */
            $pessoa = RegrasEspecificasRegistro30::handle($pessoa);

            $data = [
                $pessoa->registro,
                $pessoa->inepEscola,
                $pessoa->codigoPessoa,
                $pessoa->getInep(),
                (string) $pessoa->cpf,
                $pessoa->nomePessoa,
                $pessoa->dataNascimento,
                $pessoa->filiacao,
                $pessoa->filiacao1,
                $pessoa->filiacao2,
                $pessoa->sexo,
                $pessoa->raca,
                $pessoa->nacionalidade,
                $pessoa->paisNacionalidade,
                $pessoa->municipioNascimento,
                $pessoa->deficiencia,
                $pessoa->deficienciaCegueira,
                $pessoa->deficienciaBaixaVisao,
                $pessoa->deficienciaVisaoMonocular,
                $pessoa->deficienciaSurdez,
                $pessoa->deficienciaAuditiva,
                $pessoa->deficienciaSurdoCegueira,
                $pessoa->deficienciaFisica,
                $pessoa->deficienciaIntelectual,
                $pessoa->deficienciaMultipla(),
                $pessoa->deficienciaAutismo,
                $pessoa->deficienciaAltasHabilidades,
                $pessoa->recursoLedor,
                $pessoa->recursoTranscricao,
                $pessoa->recursoGuia,
                $pessoa->recursoTradutor,
                $pessoa->recursoLeituraLabial,
                $pessoa->recursoProvaAmpliada,
                $pessoa->recursoProvaSuperampliada,
                $pessoa->recursoAudio,
                $pessoa->recursoLinguaPortuguesaSegundaLingua,
                $pessoa->recursoVideoLibras,
                $pessoa->recursoBraile,
                $pessoa->recursoNenhum,
                $pessoa->certidaoNascimento,
                $pessoa->paisResidencia,
                $pessoa->cep,
                $pessoa->cep ? $pessoa->municipioResidencia : '',
                $pessoa->localizacaoResidencia,
                $pessoa->localizacaoDiferenciada,
                $pessoa->escolaridade,
                $pessoa->tipoEnsinoMedioCursado,
                $pessoa->formacaoCurso[0],
                $pessoa->formacaoAnoConclusao[0],
                $pessoa->formacaoInstituicao[0],
                $pessoa->formacaoCurso[1],
                $pessoa->formacaoAnoConclusao[1],
                $pessoa->formacaoInstituicao[1],
                $pessoa->formacaoCurso[2],
                $pessoa->formacaoAnoConclusao[2],
                $pessoa->formacaoInstituicao[2],
                $pessoa->complementacaoPedagogica[0],
                $pessoa->complementacaoPedagogica[1],
                $pessoa->complementacaoPedagogica[2],
                $pessoa->posGraduacoes[0]->type_id,
                $pessoa->posGraduacoes[0]->area_id,
                $pessoa->posGraduacoes[0]->completion_year,
                $pessoa->posGraduacoes[1]->type_id,
                $pessoa->posGraduacoes[1]->area_id,
                $pessoa->posGraduacoes[1]->completion_year,
                $pessoa->posGraduacoes[2]->type_id,
                $pessoa->posGraduacoes[2]->area_id,
                $pessoa->posGraduacoes[2]->completion_year,
                $pessoa->posGraduacoes[3]->type_id,
                $pessoa->posGraduacoes[3]->area_id,
                $pessoa->posGraduacoes[3]->completion_year,
                $pessoa->posGraduacoes[4]->type_id,
                $pessoa->posGraduacoes[4]->area_id,
                $pessoa->posGraduacoes[4]->completion_year,
                $pessoa->posGraduacoes[5]->type_id,
                $pessoa->posGraduacoes[5]->area_id,
                $pessoa->posGraduacoes[5]->completion_year,
                $pessoa->posGraduacaoNaoPossui,
                $pessoa->formacaoContinuadaCreche,
                $pessoa->formacaoContinuadaPreEscola,
                $pessoa->formacaoContinuadaAnosIniciaisFundamental,
                $pessoa->formacaoContinuadaAnosFinaisFundamental,
                $pessoa->formacaoContinuadaEnsinoMedio,
                $pessoa->formacaoContinuadaEducacaoJovensAdultos,
                $pessoa->formacaoContinuadaEducacaoEspecial,
                $pessoa->formacaoContinuadaEducacaoIndigena,
                $pessoa->formacaoContinuadaEducacaoCampo,
                $pessoa->formacaoContinuadaEducacaoAmbiental,
                $pessoa->formacaoContinuadaEducacaoDireitosHumanos,
                $pessoa->formacaoContinuadaEducacaoBilingueSurdos,
                $pessoa->formacaoContinuadaEducacaoTecnologiaInformaçãoComunicacao,
                $pessoa->formacaoContinuadaGeneroDiversidadeSexual,
                $pessoa->formacaoContinuadaDireitosCriancaAdolescente,
                $pessoa->formacaoContinuadaEducacaoRelacoesEticoRaciais,
                $pessoa->formacaoContinuadaEducacaoGestaoEscolar,
                $pessoa->formacaoContinuadaEducacaoOutros,
                $pessoa->formacaoContinuadaEducacaoNenhum,
                $pessoa->email,
            ];

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
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
            /** @var Registro40 $gestor */
            $gestor = TipoVinculoGestor::handle($gestor);

            $data = [
                $gestor->registro,
                $gestor->inepEscola,
                $gestor->codigoPessoa,
                $gestor->inepGestor,
                $gestor->cargo,
                $gestor->criterioAcesso,
                $gestor->tipoVinculo,
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

        $quantidadeComponentes = 25;
        $quantidadeUnidadesCurriculares = 8;

        /** @var Registro50[] $docentes */
        $docentes = $registro50->getExportFormatData($escolaId, $ano);

        $stringCenso = '';
        foreach ($docentes as $docente) {
            $docente = TipoVinculoServidor::handle($docente);
            /** @var Registro50 $docente */
            $docente = ComponentesCurriculares::handle($docente);
            /** @var Registro50 $docente */
            $docente = UnidadesCurricularesServidor::handle($docente);

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

            for ($count = 1; $count <= $quantidadeUnidadesCurriculares; $count++) {
                $data[] = $docente->unidadesCurriculares === null ? '' : (int) in_array($count, $docente->unidadesCurriculares);
            }

            $data[] = $docente->outrasUnidadesCurricularesObrigatorias;

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
            $aluno = ItinerarioFormativoAluno::handle($aluno);

            $data = [
                $aluno->registro,
                $aluno->inepEscola,
                $aluno->codigoPessoa,
                $aluno->inepAluno,
                $aluno->codigoTurma,
                $aluno->inepTurma,
                $aluno->matriculaAluno,
                $aluno->etapaAluno,
                $aluno->tipoItinerarioLinguagens,
                $aluno->tipoItinerarioMatematica,
                $aluno->tipoItinerarioCienciasNatureza,
                $aluno->tipoItinerarioCienciasHumanas,
                $aluno->tipoItinerarioFormacaoTecnica,
                $aluno->tipoItinerarioIntegrado,
                $aluno->composicaoItinerarioLinguagens,
                $aluno->composicaoItinerarioMatematica,
                $aluno->composicaoItinerarioCienciasNatureza,
                $aluno->composicaoItinerarioCienciasHumanas,
                $aluno->composicaoItinerarioFormacaoTecnica,
                $aluno->cursoItinerario,
                $aluno->codCursoProfissional,
                $aluno->itinerarioConcomitante,
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
                $aluno->veiculoTransporteAquaviarioCapacidadeAcima35,
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
        return '99|';
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'educacenso-export')) {
            $this->appendResponse($this->educacensoExport());
        } else {
            $this->notImplementedOperationError();
        }
    }

    /**
     * Retorna true se o grau acadêmido informado for bacharelado ou tecnólogo e se a situação informada for concluído
     */
    private function isCursoSuperiorBachareladoOuTecnologoCompleto($grauAcademico, $situacao): bool
    {
        if ($situacao != iEducar\App\Model\Servidor::SITUACAO_CURSO_SUPERIOR_CONCLUIDO) {
            return false;
        }

        return in_array($grauAcademico, [
            self::BACHARELADO,
            self::TECNOLOGO,
        ]);
    }
}
