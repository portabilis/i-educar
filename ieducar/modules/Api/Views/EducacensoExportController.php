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
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;

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
        $obj_permissoes = new clsPermissoes;
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
        $educacensoRepository = new EducacensoRepository;
        $registro00Model = new Registro00;
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
        $educacensoRepository = new EducacensoRepository;
        $registro10Model = new Registro10;
        $registro10 = new Registro10Data($educacensoRepository, $registro10Model);
        $data = $registro10->getExportFormatData($escolaId, $ano);

        return ArrayToCenso::format($data) . PHP_EOL;
    }

    protected function exportaDadosRegistro20($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository;
        $registro20Model = new Registro20;
        $registro20 = new Registro20Data($educacensoRepository, $registro20Model);
        $data = $registro20->getExportFormatData($escolaId, $ano);

        return implode(PHP_EOL, array_map(function ($record) {
            return ArrayToCenso::format($record);
        }, $data)) . PHP_EOL;
    }

    protected function exportaDadosRegistro30($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository;

        $registro40Model = new Registro40;
        $registro40 = new Registro40Data($educacensoRepository, $registro40Model);

        $registro50Model = new Registro50;
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);

        $registro60Model = new Registro60;
        $registro60 = new Registro60Data($educacensoRepository, $registro60Model);

        /** @var Registro40[] $gestores */
        $gestores = $registro40->getExportFormatData($escolaId);

        /** @var Registro50[] $docentes */
        $docentes = $registro50->getExportFormatData($escolaId, $ano);

        /** @var Registro60[] $alunos */
        $alunos = $registro60->getExportFormatData($escolaId, $ano);

        $registro30Data = new Registro30Data($educacensoRepository, new Registro30);
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
                $pessoa->registro, // 01 - Tipo de registro
                $pessoa->inepEscola, // 02 - Código da escola - INEP
                $pessoa->codigoPessoa, // 03 - Código da pessoa física no sistema próprio
                $pessoa->getInep(), // 04 - Identificação única (INEP)
                (string) $pessoa->cpf, // 05 - CPF
                $pessoa->nomePessoa, // 06 - Nome completo
                $pessoa->dataNascimento, // 07 - Data de nascimento
                $pessoa->filiacao, // 08 - Filiação
                $pessoa->filiacao1, // 09 - Filiação 1
                $pessoa->filiacao2, // 10 - Filiação 2
                $pessoa->sexo, // 11 - Sexo
                $pessoa->raca, // 12 - Cor / Raça
                $pessoa->povoIndigena, // 13 - Povo indígena
                $pessoa->nacionalidade, // 14 - Nacionalidade
                $pessoa->paisNacionalidade, // 15 - País de nacionalidade
                $pessoa->municipioNascimento, // 16 - Município de nascimento
                $pessoa->deficiencia, // 17 - Deficiência, transtorno do espectro autista e altas habilidades ou superdotação
                $pessoa->deficienciaCegueira, // 18 - Cegueira
                $pessoa->deficienciaBaixaVisao, // 19 - Baixa visão
                $pessoa->deficienciaVisaoMonocular, // 20 - Visão monocular
                $pessoa->deficienciaSurdez, // 21 - Surdez
                $pessoa->deficienciaAuditiva, // 22 - Deficiência auditiva
                $pessoa->deficienciaSurdoCegueira, // 23 - Surdocegueira
                $pessoa->deficienciaFisica, // 24 - Deficiência física
                $pessoa->deficienciaIntelectual, // 25 - Deficiência intelectual
                $pessoa->deficienciaMultipla(), // 26 - Deficiência múltipla
                $pessoa->deficienciaAutismo, // 27 - Transtorno do espectro autista
                $pessoa->deficienciaAltasHabilidades, // 28 - Altas habilidades / superdotação
                $pessoa->transtorno, // 29 - Pessoa física com transtorno(s) que impacta(m) o desenvolvimento da aprendizagem
                $pessoa->transtornoDiscalculia, // 30 - Discalculia ou outro transtorno da matemática e raciocínio lógico
                $pessoa->transtornoDisgrafia, // 31 - Disgrafia, Disortografia ou outro transtorno da escrita e ortografia
                $pessoa->transtornoDislalia, // 32 - Dislalia ou outro transtorno da linguagem e comunicação
                $pessoa->transtornoDislexia, // 33 - Dislexia
                $pessoa->transtornoTdah, // 34 - Transtorno do Déficit de Atenção com Hiperatividade (TDAH)
                $pessoa->transtornoTpac, // 35 - Transtorno do Processamento Auditivo Central (TPAC)
                $pessoa->recursoLedor, // 36 - Auxílio ledor
                $pessoa->recursoTranscricao, // 37 - Auxílio transcrição
                $pessoa->recursoGuia, // 38 - Guia-Intérprete
                $pessoa->recursoTradutor, // 39 - Tradutor-Intérprete de Libras
                $pessoa->recursoLeituraLabial, // 40 - Leitura Labial
                $pessoa->recursoProvaAmpliada, // 41 - Prova Ampliada (Fonte 18)
                $pessoa->recursoProvaSuperampliada, // 42 - Prova superampliada (Fonte 24)
                $pessoa->recursoAudio, // 43 - CD com áudio para deficiente visual
                $pessoa->recursoLinguaPortuguesaSegundaLingua, // 44 - Prova de Língua Portuguesa como Segunda Língua para surdos e deficientes auditivos
                $pessoa->recursoVideoLibras, // 45 - Prova em Vídeo em Libras
                $pessoa->recursoBraile, // 46 - Material didático em Braille
                $pessoa->provaBraile, // 47 - Prova em Braille
                $pessoa->recursoTempoAdicional, // 48 - Tempo adicional
                $pessoa->recursoNenhum, // 49 - Nenhum recurso
                $pessoa->certidaoNascimento, // 50 - Número da matrícula da certidão de nascimento (certidão nova)
                $pessoa->paisResidencia, // 51 - País de residência
                $pessoa->cep, // 52 - CEP
                $pessoa->cep ? $pessoa->municipioResidencia : '', // 53 - Município de residência
                $pessoa->localizacaoResidencia, // 54 - Localização/ Zona de residência
                $pessoa->localizacaoDiferenciada, // 55 - Localização diferenciada de residência
                $pessoa->escolaridade, // 56 - Maior nível de escolaridade concluído
                $pessoa->tipoEnsinoMedioCursado, // 57 - Tipo de ensino médio cursado
                $pessoa->formacaoCurso[0], // 58 - Código do Curso 1
                $pessoa->formacaoAnoConclusao[0], // 59 - Ano de Conclusão 1
                $pessoa->formacaoInstituicao[0], // 60 - Instituição de educação superior 1
                $pessoa->formacaoCurso[1], // 61 - Código do Curso 2
                $pessoa->formacaoAnoConclusao[1], // 62 - Ano de Conclusão 2
                $pessoa->formacaoInstituicao[1], // 63 - Instituição de educação superior 2
                $pessoa->formacaoCurso[2], // 64 - Código do Curso 3
                $pessoa->formacaoAnoConclusao[2], // 65 - Ano de Conclusão 3
                $pessoa->formacaoInstituicao[2], // 66 - Instituição de educação superior 3
                $pessoa->complementacaoPedagogica[0], // 67 - Área do conhecimento/ componentes curriculares 1
                $pessoa->complementacaoPedagogica[1], // 68 - Área do conhecimento/ componentes curriculares 2
                $pessoa->complementacaoPedagogica[2], // 69 - Área do conhecimento/ componentes curriculares 3
                $pessoa->posGraduacoes[0]->type_id, // 70 - Tipo de pós-graduação 1
                $pessoa->posGraduacoes[0]->area_id, // 71 - Área da pós-graduação 1
                $pessoa->posGraduacoes[0]->completion_year, // 72 - Ano de conclusão da pós-graduação 1
                $pessoa->posGraduacoes[1]->type_id, // 73 - Tipo de pós-graduação 2
                $pessoa->posGraduacoes[1]->area_id, // 74 - Área da pós-graduação 2
                $pessoa->posGraduacoes[1]->completion_year, // 75 - Ano de conclusão da pós-graduação 2
                $pessoa->posGraduacoes[2]->type_id, // 76 - Tipo de pós-graduação 3
                $pessoa->posGraduacoes[2]->area_id, // 77 - Área da pós-graduação 3
                $pessoa->posGraduacoes[2]->completion_year, // 78 - Ano de conclusão da pós-graduação 3
                $pessoa->posGraduacoes[3]->type_id, // 79 - Tipo de pós-graduação 4
                $pessoa->posGraduacoes[3]->area_id, // 80 - Área da pós-graduação 4
                $pessoa->posGraduacoes[3]->completion_year, // 81 - Ano de conclusão da pós-graduação 4
                $pessoa->posGraduacoes[4]->type_id, // 82 - Tipo de pós-graduação 5
                $pessoa->posGraduacoes[4]->area_id, // 83 - Área da pós-graduação 5
                $pessoa->posGraduacoes[4]->completion_year, // 84 - Ano de conclusão da pós-graduação 5
                $pessoa->posGraduacoes[5]->type_id, // 85 - Tipo de pós-graduação 6
                $pessoa->posGraduacoes[5]->area_id, // 86 - Área da pós-graduação 6
                $pessoa->posGraduacoes[5]->completion_year, // 87 - Ano de conclusão da pós-graduação 6
                $pessoa->posGraduacaoNaoPossui, // 88 - Não tem pós-graduação concluída
                $pessoa->formacaoContinuadaCreche, // 89 - Creche (0 a 3 anos)
                $pessoa->formacaoContinuadaPreEscola, // 90 - Pré-escola (4 e 5 anos)
                $pessoa->formacaoContinuadaAnosIniciaisFundamental, // 91 - Anos iniciais do ensino fundamental
                $pessoa->formacaoContinuadaAnosFinaisFundamental, // 92 - Anos finais do ensino fundamental
                $pessoa->formacaoContinuadaEnsinoMedio, // 93 - Ensino médio
                $pessoa->formacaoContinuadaEducacaoJovensAdultos, // 94 - Educação de jovens e adultos
                $pessoa->formacaoContinuadaEducacaoEspecial, // 95 - Educação especial
                $pessoa->formacaoContinuadaEducacaoIndigena, // 96 - Educação Indígena
                $pessoa->formacaoContinuadaEducacaoCampo, // 97 - Educação do campo
                $pessoa->formacaoContinuadaEducacaoAmbiental, // 98 - Educação ambiental
                $pessoa->formacaoContinuadaEducacaoDireitosHumanos, // 99 - Educação em direitos humanos
                $pessoa->formacaoContinuadaEducacaoBilingueSurdos, // 100 - Educação bilíngue de surdos
                $pessoa->formacaoContinuadaEducacaoTecnologiaInformacaoComunicacao, // 101 - Educação e Tecnologia de Informação e Comunicação (TIC)
                $pessoa->formacaoContinuadaGeneroDiversidadeSexual, // 102 - Gênero e diversidade sexual
                $pessoa->formacaoContinuadaDireitosCriancaAdolescente, // 103 - Direitos de criança e adolescente
                $pessoa->formacaoContinuadaEducacaoRelacoesEticoRaciais, // 104 - Educação para as relações étnico-raciais e História e cultura afro-brasileira e africana
                $pessoa->formacaoContinuadaEducacaoGestaoEscolar, // 105 - Gestão escolar
                $pessoa->formacaoContinuadaEducacaoOutros, // 106 - Outros
                $pessoa->formacaoContinuadaEducacaoNenhum, // 107 - Nenhum
                $pessoa->email, // 108 - E-mail
            ];

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
    }

    protected function exportaDadosRegistro40($escolaId)
    {
        $educacensoRepository = new EducacensoRepository;
        $registro40Model = new Registro40;
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
        $educacensoRepository = new EducacensoRepository;
        $registro50Model = new Registro50;
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);

        $quantidadeComponentes = 25;

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
                $docente->registro, // 1 - Tipo de registro
                $docente->inepEscola, // 2 - Código de escola - Inep
                $docente->codigoPessoa, // 3 - Código da pessoa física no sistema próprio
                $docente->inepDocente, // 4 - Identificação única (Inep)
                $docente->codigoTurma, // 5 - Código da Turma na Entidade/Escola
                $docente->inepTurma, // 6 - Código da turma no INEP
                $docente->funcaoDocente, // 7 - Função que exerce na turma
                $docente->tipoVinculo, // 8 - Situação funcional/regime de contratação/tipo de vínculo
            ];

            for ($count = 0; $count <= $quantidadeComponentes - 1; $count++) {
                $data[] = $docente->componentes[$count]; // 9 a 33 - Componentes curriculares
            }

            $data[] = $docente->areaItinerario ? ((is_array($docente->areaItinerario) && in_array(TipoItinerarioFormativo::LINGUANGENS, $docente->areaItinerario)) ? 1 : 0) : ''; // 34 - Linguagens e suas tecnologias
            $data[] = $docente->areaItinerario ? ((is_array($docente->areaItinerario) && in_array(TipoItinerarioFormativo::MATEMATICA, $docente->areaItinerario)) ? 1 : 0) : ''; // 35 - Matemática e suas tecnologias
            $data[] = $docente->areaItinerario ? ((is_array($docente->areaItinerario) && in_array(TipoItinerarioFormativo::CIENCIAS_NATUREZA, $docente->areaItinerario)) ? 1 : 0) : ''; // 36 - Ciências da natureza e suas tecnologias
            $data[] = $docente->areaItinerario ? ((is_array($docente->areaItinerario) && in_array(TipoItinerarioFormativo::CIENCIAS_HUMANAS, $docente->areaItinerario)) ? 1 : 0) : ''; // 37 - Ciências humanas e sociais aplicadas
            $data[] = $docente->lecionaItinerarioTecnicoProfissional; // 38 - Profissional escolar leciona no Itinerário de formação técnica e profissional (IFTP)

            $stringCenso .= ArrayToCenso::format($data) . PHP_EOL;
        }

        return $stringCenso;
    }

    protected function exportaDadosRegistro60($escolaId, $ano)
    {
        $educacensoRepository = new EducacensoRepository;
        $registro60Model = new Registro60;
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
                $aluno->registro, // 01 - Tipo de Registro
                $aluno->inepEscola, // 02 - Código da Escola - INEP
                $aluno->codigoPessoa, // 03 - Código da pessoa física no sistema próprio
                $aluno->inepAluno, // 04 - Identificação única (Inep)
                $aluno->codigoTurma, // 05 - Código da Turma na Entidade/Escola
                $aluno->inepTurma, // 06 - Código da turma no INEP
                $aluno->matriculaAluno, // 07 - Código da Matrícula do(a) aluno(a)
                $aluno->etapaAluno, // 08 - Turma multi
                $aluno->tipoAtendimentoDesenvolvimentoFuncoesGognitivas, // 09 - Desenvolvimento de funções cognitivas
                $aluno->tipoAtendimentoDesenvolvimentoVidaAutonoma, // 10 - Desenvolvimento de vida autônoma
                $aluno->tipoAtendimentoEnriquecimentoCurricular, // 11 - Enriquecimento curricular
                $aluno->tipoAtendimentoEnsinoInformaticaAcessivel, // 12 - Ensino de informática acessível
                $aluno->tipoAtendimentoEnsinoLibras, // 13 - Ensino da Língua Brasileira de Sinais (Libras)
                $aluno->tipoAtendimentoEnsinoLinguaPortuguesa, // 14 - Ensino da Língua Portuguesa como Segunda Língua
                $aluno->tipoAtendimentoEnsinoSoroban, // 15 - Ensino das técnicas do cálculo no Soroban
                $aluno->tipoAtendimentoEnsinoBraile, // 16 - Ensino de Sistema Braille
                $aluno->tipoAtendimentoEnsinoOrientacaoMobilidade, // 17 - Ensino de técnicas para orientação e mobilidade
                $aluno->tipoAtendimentoEnsinoCaa, // 18 - Ensino de uso da Comunicação Alternativa e Aumentativa (CAA)
                $aluno->tipoAtendimentoEnsinoRecursosOpticosNaoOpticos, // 19 - Ensino de uso de recursos ópticos e não ópticos
                $aluno->recebeEscolarizacaoOutroEspacao, // 20 - Recebe escolarização em outro espaço (diferente da escola)
                $aluno->transportePublico, // 21 - Transporte escolar público
                $aluno->poderPublicoResponsavelTransporte, // 22 - Poder Público responsável pelo transporte escolar
                $aluno->veiculoTransporteBicicleta, // 23 - Rodoviário - Bicicleta
                $aluno->veiculoTransporteMicroonibus, // 24 - Rodoviário - Microônibus
                $aluno->veiculoTransporteOnibus, // 25 - Rodoviário - Ônibus
                $aluno->veiculoTransporteTracaoAnimal, // 26 - Rodoviário – Tração Animal
                $aluno->veiculoTransporteVanKonbi, // 27 - Rodoviário - Vans/Kombis
                $aluno->veiculoTransporteOutro, // 28 - Rodoviário - Outro
                $aluno->veiculoTransporteAquaviarioCapacidade5, // 29 - Aquaviário - Capacidade de até 5 aluno(a)s
                $aluno->veiculoTransporteAquaviarioCapacidade5a15, // 30 - Aquaviário - Capacidade entre 5 a 15 aluno(a)s
                $aluno->veiculoTransporteAquaviarioCapacidade15a35, // 31 - Aquaviário - Capacidade entre 15 a 35 aluno(a)s
                $aluno->veiculoTransporteAquaviarioCapacidadeAcima35, // 32 - Aquaviário - Capacidade acima de 35 aluno(a)s
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
