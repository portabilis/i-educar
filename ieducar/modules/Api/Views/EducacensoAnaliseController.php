<?php

use App\Models\Educacenso\Registro00;
use App\Models\Educacenso\Registro10;
use App\Models\Educacenso\Registro20;
use App\Models\Educacenso\Registro30;
use App\Models\Educacenso\Registro40;
use App\Models\Educacenso\Registro50;
use App\Models\Educacenso\Registro60;
use App\Models\Individual;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App\Models\School;
use App\Repositories\EducacensoRepository;
use App\Services\SchoolClass\AvailableTimeService;
use iEducar\Modules\Educacenso\Analysis\Register30CommonDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30ManagerDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30StudentDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherAndManagerDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherAndStudentDataAnalysis;
use iEducar\Modules\Educacenso\Analysis\Register30TeacherDataAnalysis;
use iEducar\Modules\Educacenso\Data\Registro00 as Registro00Data;
use iEducar\Modules\Educacenso\Data\Registro10 as Registro10Data;
use iEducar\Modules\Educacenso\Data\Registro20 as Registro20Data;
use iEducar\Modules\Educacenso\Data\Registro30 as Registro30Data;
use iEducar\Modules\Educacenso\Data\Registro40 as Registro40Data;
use iEducar\Modules\Educacenso\Data\Registro50 as Registro50Data;
use iEducar\Modules\Educacenso\Data\Registro60 as Registro60Data;
use iEducar\Modules\Educacenso\ExportRule\PoderPublicoConveniado as ExportRulePoderPublicoConveniado;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\LinguaMinistrada;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\LocalizacaoDiferenciadaEscola;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\PoderPublicoConveniado;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Model\SchoolManagerAccessCriteria;
use iEducar\Modules\Educacenso\Model\SchoolManagerRole;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoItinerarioFormativo;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\UnidadeVinculadaComOutraInstituicao;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;
use iEducar\Modules\Educacenso\Validator\CnpjMantenedoraPrivada;
use iEducar\Modules\Educacenso\Validator\FormasContratacaoEscolaValidator;
use iEducar\Modules\Educacenso\Validator\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Validator\InepNumberValidator;
use iEducar\Modules\Educacenso\Validator\Telefone;
use iEducar\Modules\Servidores\Model\FuncaoExercida;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated Essa versão da API pública será descontinuada
 */
class EducacensoAnaliseController extends ApiCoreController
{
    private function schoolIsActive()
    {
        $schoolId = $this->getRequest()->school_id;
        $school = School::findOrFail($schoolId);
        $active = !in_array($school->situacao_funcionamento, ['2', '3']);

        return [
            'active' => $active
        ];
    }

    protected function analisaEducacensoRegistro00()
    {
        $escolaId = $this->getRequest()->escola;
        $ano    = $this->getRequest()->ano;

        $educacensoRepository = new EducacensoRepository();
        $registro00Model = new Registro00();
        $registro00 = new Registro00Data($educacensoRepository, $registro00Model);

        /** @var Registro00 $escola */
        $escola = $registro00->getData($escolaId, $ano);

        if (empty($escola)) {
            $this->messenger->append("O ano letivo {$ano} não foi definido.");

            return [
                'title' => 'Análise exportação - Registro 00'
            ];
        }

        $nomeEscola = Portabilis_String_Utils::toUtf8(mb_strtoupper($escola->nome));
        $codEscola = $escola->idEscola;

        $codInstituicao = $escola->idInstituicao;
        $codDistrito = $escola->idDistrito;
        $anoAtual = $ano;
        $anoAnterior = $anoAtual-1;
        $anoPosterior = $anoAtual+1;

        $mensagem = [];

        if (strlen($escola->nome) > 100) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Insira no máximo 100 letras no nome da escola;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (strlen(str_replace(' ', '', $escola->nome)) < 4) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Insira no mínimo 4 letras no nome da escola;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (strlen($escola->logradouro) > 100) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Insira no máximo 100 letras no nome do logradouro da escola;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Logradouro)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (strlen($escola->bairro) > 50) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Insira no máximo 50 letras no nome do bairro da escola;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Bairro)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        $telefoneValidator = new Telefone('Telefone 1', $escola->telefone);
        if ($escola->telefone && !$telefoneValidator->isValid()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. <br>" . implode('<br>', $telefoneValidator->getMessage()) . ';',
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Telefone 1)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        $telefoneValidator = new Telefone('Telefone 2', $escola->telefoneOutro);
        if ($escola->telefoneOutro && !$telefoneValidator->isValid()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. <br>" . implode('<br>', $telefoneValidator->getMessage()) . ';',
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Telefone 2)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->ddd && !$escola->telefone) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Insira o Telefone 1 quando o campo: DDD estiver preenchido;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: (DDD) / Telefone 1)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->telefone && $escola->telefoneOutro && ($escola->telefone == $escola->telefoneOutro)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. O campo (DDD) / Telefone 2 não pode ser igual ao campo (DDD) / Telefone 1;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: (DDD) / Telefone 2)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (strlen($escola->email) > 50) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Insira no máximo 50 letras ou símbolos no e-mail da escola;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: E-mail)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->zonaLocalizacao) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se a zona/localização da escola foi informada;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Zona localização)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (empty($escola->localizacaoDiferenciada)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados.  Verifique se a localização diferenciada da escola foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Localização diferenciada da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->localizacaoDiferenciada == LocalizacaoDiferenciadaEscola::AREA_ASSENTAMENTO && $escola->zonaLocalizacao == App_Model_ZonaLocalizacao::URBANA) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a zona/localização da escola é urbana, portanto a localização diferenciada da escola não pode ser área de assentamento;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Localização diferenciada da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->orgaoVinculado && $escola->dependenciaAdministrativa != DependenciaAdministrativaEscola::PRIVADA) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se órgão ao qual a escola pública está vinculada foi informado;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Órgão ao qual a escola pública está vinculada)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        $cnpjMantenedoraPrivada = new CnpjMantenedoraPrivada($escola);
        if (!$cnpjMantenedoraPrivada->isValid()) {
            $mensagem[] = [
                'text' => $cnpjMantenedoraPrivada->getMessage(),
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: CNPJ da mantenedora principal da escola privada)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->esferaAdministrativa && ($escola->regulamentacao == Regulamentacao::SIM || $escola->regulamentacao == Regulamentacao::EM_TRAMITACAO)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a escola é regulamentada ou está em tramitação pelo conselho/órgão, portanto é necessário informar qual a esfera administrativa;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais >  Campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!(new AdministrativeDomainValidator(
                $escola->esferaAdministrativa,
                $escola->regulamentacao,
                $escola->dependenciaAdministrativa,
                $escola->codigoIbgeMunicipio
            ))->isValid()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verificamos que a esfera administrativa foi preenchida incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais >  Campo: Esfera administrativa do conselho ou órgão responsável pela Regulamentação/Autorização)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->codigoInep) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se a escola possui o código INEP cadastrado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Código INEP)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->anoInicioAnoLetivo != $anoAtual && $escola->anoInicioAnoLetivo != $anoAnterior) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data inicial da primeira etapa foi cadastrada corretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar ano letivo > Ok > Seção: Etapas do ano letivo)',
                'linkPath' => "/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola={$codEscola}&ano={$anoAtual}",
                'fail' => true
            ];
        }

        if ($escola->anoFimAnoLetivo != $anoAtual && $escola->anoFimAnoLetivo != $anoPosterior) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data final da última etapa foi cadastrada corretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar ano letivo > Ok > Seção: Etapas do ano letivo)',
                'linkPath' => "/intranet/educar_ano_letivo_modulo_cad.php?ref_cod_escola={$codEscola}&ano={$anoAtual}",
                'fail' => true
            ];
        }

        if (!$escola->codigoIbgeMunicipio) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do município informado, foi cadastrado conforme a 'Tabela de Municípios'.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Cidade)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->codigoIbgeDistrito) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do distrito informado, foi cadastrado conforme a 'Tabela de Distritos'.",
                'path' => '(Endereçamento > Cadastros > Distritos > Editar > Campo: Código INEP)',
                'linkPath' => "/intranet/public_distrito_det.php?iddis={$codDistrito}",
                'fail' => true
            ];
        }

        if (!$escola->orgaoRegional) {
            $mensagem[] = [
                'text' => "<span class='avisos-educacenso'><b>Aviso não impeditivo:</b> Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que o código do órgão regional de ensino não foi preenchido, caso seu estado possua uma subdivisão e a escola {$nomeEscola} não for federal vinculada a Setec, o código deve ser inserido conforme a 'Tabela de Órgãos Regionais'.</span>",
                'path' => '(Escola > Cadastros > Instituição > Editar > Aba: Dados gerais > Campo: Código do órgão regional de ensino)',
                'linkPath' => "/intranet/educar_instituicao_cad.php?cod_instituicao={$codInstituicao}",
                'fail' => false
            ];
        }

        if ($escola->dependenciaAdministrativa == DependenciaAdministrativaEscola::PRIVADA) {
            if (!$escola->categoriaEscolaPrivada) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a dependência administrativa da escola é privada, portanto é necessário informar qual a categoria desta unidade escolar.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do gerais > Campo: Categoria da escola privada)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$escola->mantenedoraEscolaPrivada) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a dependência administrativa da escola é privada, portanto é necessário informar qual o tipo de mantenedora desta unidade escolar.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Mantenedora da escola privada)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }
        }

        if ($escola->situacaoFuncionamento == SituacaoFuncionamento::EM_ATIVIDADE && empty(array_filter($escola->poderPublicoConveniado))) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (in_array(PoderPublicoConveniado::NAO_POSSUI, $escola->poderPublicoConveniado) && count($escola->poderPublicoConveniado) > 1) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} possui valor inválido. Verificamos que o Poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Poder público responsável pela parceria ou convênio entre a Administração Pública e outras instituições)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (
            (in_array(PoderPublicoConveniado::MUNICIPAL, $escola->poderPublicoConveniado) ||
            in_array(PoderPublicoConveniado::ESTADUAL, $escola->poderPublicoConveniado))
            && empty(array_filter($escola->formasContratacaoPoderPublico))
        ) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se as formas de contratação entre a Administração Pública e outras instituições foram informadas.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Formas de contratação entre a Administração Pública e outras instituições)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        $formasContratacaoValidator = new FormasContratacaoEscolaValidator(
            $escola->dependenciaAdministrativa,
            $escola->categoriaEscolaPrivada,
            $escola->formasContratacaoPoderPublico
        );

        if (!$formasContratacaoValidator->isValid()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verificamos que as formas de contratação entre a Administração Pública e outras instituições foram informadas incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Formas de contratação entre a Administração Pública e outras instituições)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (
            (in_array(PoderPublicoConveniado::MUNICIPAL, $escola->poderPublicoConveniado) ||
            in_array(PoderPublicoConveniado::ESTADUAL, $escola->poderPublicoConveniado)) &&
            $escola->NaoPossuiQuantidadeDeMatriculasAtendidas()
        ) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a escola não preencheu nenhuma informação referente ao Número de matrículas atendidas por meio da parceria ou convênio, portanto é necessário informar pelo menos um dos campos.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Matrículas atendidas por convênio > Seção: Número de matrículas atendidas por meio da parceria ou convênio)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->unidadeVinculada === UnidadeVinculadaComOutraInstituicao::EDUCACAO_BASICA && empty($escola->inepEscolaSede)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que essa unidade está vinculada à uma escola da educação básica, portanto é necessário informar o código INEP da escola sede.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Código da Escola Sede)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->unidadeVinculada === UnidadeVinculadaComOutraInstituicao::EDUCACAO_BASICA && $escola->inepEscolaSede == $escola->codigoInep) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verificamos que essa unidade está vinculada à uma escola da educação básica, portanto o código INEP da escola sede deve ser diferente do INEP da escola atual.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Código da Escola Sede)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola->unidadeVinculada === UnidadeVinculadaComOutraInstituicao::ENSINO_SUPERIOR && empty($escola->codigoIes)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que essa unidade está vinculada à uma instituição de ensino superior, portanto é necessário informar o código da instituição.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Código da IES)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 00'
        ];
    }

    protected function analisaEducacensoRegistro10()
    {
        $escolaId = $this->getRequest()->escola;

        $educacensoRepository = new EducacensoRepository();
        $registro10Model = new Registro10();
        $registro10 = new Registro10Data($educacensoRepository, $registro10Model);

        $escola = $registro10->getData($escolaId);

        if (empty($escola)) {
            $this->messenger->append('Ocorreu algum problema ao decorrer da análise.');

            return [
                'title' => 'Análise exportação - Registro 10'
            ];
        }

        $mensagem = [];

        if (empty($escola->localFuncionamento)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se o local de funcionamento da escola foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Local de funcionamento)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->predioEscolar() && empty($escola->condicao)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto é necessário informar qual a forma de ocupação do prédio.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Forma de ocupação do prédio)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->predioEscolar() && is_null($escola->predioCompartilhadoOutraEscola)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto é necessário informar se a escola compartilha o prédio com outra escola.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Prédio compartilhado com outra escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->predioCompartilhadoOutraEscola == 1 && empty($escola->codigoInepEscolaCompartilhada)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a escola compartilha o prédio com outra escola, portanto é necessário informar o(s) código(s) INEP(s) da(s) escola(s) compartilhada(s).",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campos: Código da escola que compartilha o prédio 1)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeAbastecimentoAgua()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se uma das formas do abastecimento de água foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Abastecimento de água)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->aguaInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => " Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que o abastecimento de água foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Abastecimento de água)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeAbastecimentoEnergia()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se uma das fontes de energia elétrica foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Fonte de energia elétrica)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->energiaInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que a fonte de energia elétrica foi preenchida incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Fonte de energia elétrica)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeEsgotoSanitario()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se alguma opção de esgotamento sanitário foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Esgotamento sanitário)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->esgotoSanitarioInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que o esgotamento sanitário foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Esgotamento sanitário)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeDestinacaoLixo()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se uma das formas da destinação do lixo foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Destinação do lixo)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeTratamentoLixo()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se alguma opção do tratamento do lixo/resíduos que a escola realiza foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Tratamento do lixo/resíduos que a escola realiza)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->tratamentoLixoInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => " Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que o tratamento do lixo/resíduos foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Tratamento do lixo/resíduos que a escola realiza)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->possuiDependencias != 1) {
            $mensagem[] = [
                'text' => "<span class='avisos-educacenso'><b>Aviso não impeditivo: </b> Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Nenhum campo foi preenchido referente as dependências existentes na escola, portanto todos serão registrados como <b>não</b>.</span>",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campo: Possui dependências?)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => false
            ];
        }

        if ($escola->possuiDependencias == 1 && $escola->naoPossuiDependencias()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a escola possui dependências, portanto é necessário informar pelo menos uma dependência.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campos: Salas gerais, Sala funcionais, Banheiros, Laboratórios, Salas de atividades, Dormitórios e Áreas externas)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeRecursosAcessibilidade()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se alguma opção dos recursos de acessibilidade que a escola possui foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campo: Recursos de acessibilidade)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->recursosAcessibilidadeInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => " Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que o recurso de acessibilidade foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campo: Recursos de acessibilidade)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->predioEscolar() && !$escola->numeroSalasUtilizadasDentroPredio) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se o número de salas de aula utilizadas na escola dentro do prédio escolar da escola foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campo: Número de salas de aula utilizadas na escola dentro do prédio escolar)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->predioEscolar() && !$escola->numeroSalasUtilizadasForaPredio) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se o número de salas de aula utilizadas na escola fora do prédio escolar da escola foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dependências > Campo: Número de salas de aula utilizadas na escola fora do prédio escolar)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (count(array_filter($escola->equipamentos)) == 0) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se os equipamentos da escola foram informados.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Equipamentos da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        } elseif ($escola->equipamentosPreenchidosIncorretamente()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que os equipamentos da escola foram preenchidos incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Equipamentos da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->existeUsoInternet()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se alguma opção de acesso à internet foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Acesso à internet)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->usoInternetInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que o acesso à internet foi preenchido incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Acesso à internet)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->alunosUsamInternet() && empty($escola->equipamentosAcessoInternet)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se alguma opção de equipamentos que os aluno(a)s usam para acessar a internet da escola foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Equipamentos que os aluno(a)s usam para acessar a internet da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->usaInternet() && is_null($escola->acessoInternet)) {
            $mensagem[] = [
                'text' => " Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se a internet banda larga foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Campo: Possui internet banda larga)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->equipamentosAcessoInternetComputadorMesa() && $escola->quantidadeComputadoresAlunosNaoPreenchida()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se pelo menos um dos campos da seção Quantidade de computadores de uso dos alunos foi preenchido.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Equipamentos > Seção: Quantidade de computadores de uso dos alunos)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->semFuncionariosParaFuncoes && !$escola->quantidadeProfissionaisPreenchida()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a escola não preencheu nenhuma informação referente à quantidade de profissionais, portanto é necessário informar pelo menos um profissional.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Recursos > Seção: Quantidade de profissionais)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (is_null($escola->alimentacaoEscolarAlunos)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a alimentação escolar para os alunos(as) não foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Infraestrutura > Campo: Alimentação escolar para os alunos(as))',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (count(array_filter($escola->instrumentosPedagogicos)) == 0) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se os instrumentos, materiais socioculturais e/ou pedagógicos da escola foram informados.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino aprendizagem)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        } elseif ($escola->instrumentosPedagogicosPreenchidosIncorretamente()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que os instrumentos, materiais socioculturais e/ou pedagógicos da escola foram preenchidos incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Instrumentos, materiais socioculturais e/ou pedagógicos em uso na escola para o desenvolvimento de atividades de ensino aprendizagem)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (empty($escola->orgaosColegiados)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se os órgãos colegiados em funcionamento na escola foram informados.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Órgãos colegiados em funcionamento na escola',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if (is_null($escola->educacaoIndigena)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a educação escolar indígena não foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Educação escolar indígena)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->educacaoIndigena == 1 && !$escola->linguaMinistrada) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a língua em que o ensino é ministrado não foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Língua em que o ensino é ministrado)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->linguaMinistrada == LinguaMinistrada::INDIGENA && empty($escola->codigoLinguaIndigena)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verificamos que a(s) língua(s) indígena(s) não foi(ram) informada(s).",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Línguas indígenas)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->exameSelecaoIngresso == 1 && empty($escola->reservaVagasCotas)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} não encontrados. Verifique se a reserva de vagas por sistema de cotas para grupos específicos de alunos(as) foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Reserva de vagas por sistema de cotas para grupos específicos de alunos(as))',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->reservaVagasCotasInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que a reserva de vagas por sistema de cotas para grupos específicos de alunos(as) foi preenchida incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Reserva de vagas por sistema de cotas para grupos específicos de alunos(as))',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        if ($escola->orgaosColegiadosInexistenteEOutrosCamposPreenchidos()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$escola->nomeEscola} possui valor inválido. Verificamos que os órgãos colegiados em funcionamento na escola foram preenchidos incorretamente.",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Órgãos colegiados em funcionamento na escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$escola->codEscola}",
                'fail' => true
            ];
        }

        return ['mensagens' => $mensagem,
                 'title'     => 'Análise exportação - Registro 10'];
    }

    protected function analisaEducacensoRegistro20()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;

        $educacensoRepository = new EducacensoRepository();
        $registro20Model = new Registro20();
        $registro20 = new Registro20Data($educacensoRepository, $registro20Model);

        $turmas = $registro20->getData($escola, $ano);

        if (empty($turmas)) {
            $this->messenger->append('Ocorreu algum problema ao decorrer da análise.');

            return [
                'title' => 'Análise exportação - Registro 20'
            ];
        }

        $mensagem = [];
        $chavesTurmas = [];

        foreach ($turmas as $turma) {
            $nomeEscola = mb_strtoupper($turma->nomeEscola);
            $nomeTurma = mb_strtoupper($turma->nomeTurma);
            $atividadeComplementar = ($turma->tipoAtendimento == 4); //Código 4 fixo no cadastro de turma
            $existeAtividadeComplementar = !empty(array_filter($turma->atividadesComplementares));

            $chaveTurma = "{$nomeTurma}|{$turma->tipoMediacaoDidaticoPedagogico}|{$turma->horaInicial}|{$turma->horaFinal}|{$turma->tipoAtendimento}|{$turma->localFuncionamentoDiferenciado}|{$turma->modalidadeCurso}|{$turma->etapaEducacenso}";

            if (isset($chavesTurmas[$chaveTurma])) {
                $mensagem = [[
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} possui valor inválido. Verificamos que existem turmas duplicadas com o nome {$nomeTurma}.",
                    'path' => '(Escola > Cadastros > Turmas)',
                    'linkPath' => "/intranet/educar_turma_lst.php?busca=S&ano={$ano}&ref_cod_escola={$escola}&ref_cod_curso={$turma->codCurso}&ref_cod_serie={$turma->codSerie}&nm_turma={$turma->nomeTurma}",
                    'fail' => true
                ]];
                break;
            } else {
                $chavesTurmas[$chaveTurma] = true;
            }

            switch ($turma->tipoAtendimento) {
                case 0:
                    $nomeAtendimento = 'Não se aplica';
                    break;
                case 1:
                    $nomeAtendimento = 'Classe hospitalar';
                    break;
                case 2:
                    $nomeAtendimento = 'Unidade de internação socioeducativa';
                    break;
                case 3:
                    $nomeAtendimento = 'Unidade prisional';
                    break;
            }

            if (!$turma->possuiServidor) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} não possui nenhum docente vinculado.",
                    'path' => '(Servidores > Cadastros > Servidores)',
                    'linkPath' => '/intranet/educar_servidor_lst.php',
                    'fail' => true
                ];
            }

            if (!empty($turma->etapaEducacenso) && $turma->etapaEducacenso != 1 && !$turma->possuiServidorDocente) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} não possui nenhum Docente ou Docente titular - coordenador de tutoria (de módulo ou disciplina) - EAD vinculado.",
                    'path' => '(Servidores > Cadastros > Servidores)',
                    'linkPath' => '/intranet/educar_servidor_lst.php',
                    'fail' => true
                ];
            }

            if ($turma->possuiServidorLibrasOuAuxiliarEad && !$turma->possuiServidorDiferenteLibrasOuAuxiliarEad) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a turma {$nomeTurma} possui vínculo apenas com docente(s): Tradutor(es)-Intérprete(s) de Libras ou Docente(s) tutor(es) - Auxiliar(es) (de módulo ou disciplina) - EAD.",
                    'path' => '(Servidores > Cadastros > Servidores)',
                    'linkPath' => '/intranet/educar_servidor_lst.php',
                    'fail' => true
                ];
            }

            if ($turma->possuiServidorLibras && !$turma->possuiAlunoNecessitandoTradutor && !$turma->possuiServidorNecessitandoTradutor) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a turma {$nomeTurma} possui vínculo com docente(s): Tradutor(es)-Intérprete(s) de Libras, porém não possui nenhum aluno(a) ou outro profissional escolar em sala de aula com surdez, deficiência auditiva ou surdocegueira.",
                    'path' => '(Servidores > Cadastros > Servidores)',
                    'linkPath' => '/intranet/educar_servidor_lst.php',
                    'fail' => true
                ];
            }

            if (strlen($nomeTurma) > 80) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. possui valor inválido. Insira no máximo 80 letras no nome da turma {$nomeTurma}.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Campo: Nome da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (empty($turma->tipoMediacaoDidaticoPedagogico)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se o tipo de mediação didático pedagógica da turma {$nomeTurma} foi informado.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Tipo de mediação didático pedagógico)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ((empty($turma->horaInicial) || empty($turma->horaFinal)) && $turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é presencial, portanto é necessário informar os horários de funcionamento.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Seção: Horário de funcionamento da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            } elseif (!empty($turma->horaInicial) && !empty($turma->horaFinal) && !$turma->horarioFuncionamentoValido()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verifique se o horário da turma {$nomeTurma} foi preenchido com um valor válido.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Seção: Horário de funcionamento da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (empty(array_filter($turma->diasSemana)) && $turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é presencial, portanto é necessário informar os dias da semana em que ela funciona.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Campo: Dias da semana)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (is_null($turma->tipoAtendimento) || $turma->tipoAtendimento < 0) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se o tipo de atendimento da turma {$nomeTurma} foi informado.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Tipo de atendimento)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ($turma->tipoAtendimento != 0 && in_array($turma->tipoMediacaoDidaticoPedagogico, [App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA, App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL])) {
                $descricaoTipoMediacao = (App_Model_TipoMediacaoDidaticoPedagogico::getInstance()->getEnums())[$turma->tipoMediacaoDidaticoPedagogico];

                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que o tipo de mediação da turma {$nomeTurma} é {$descricaoTipoMediacao}, portanto o tipo de atendimento deve ser obrigatoriamente escolarização.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Tipo de atendimento)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ($turma->tipoAtendimento == TipoAtendimentoTurma::ESCOLARIZACAO && count(array_filter($turma->estruturaCurricular)) == 0) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se a estrutura curricular da turma {$nomeTurma} foi informada.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Estrutura curricular)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (
                $turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL &&
                count(array_filter($turma->estruturaCurricular)) > 0 &&
                !in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $turma->estruturaCurricular)
            ) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que o tipo de mediação didático pedagógico da turma {$nomeTurma} é semipresencial, portanto a turma deve ter estrutura curricular de formação geral básica.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Estrutura curricular)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ($atividadeComplementar && !$existeAtividadeComplementar) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é de atividades complementares, portanto é necessário informar quais atividades complementares são trabalhadas.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Tipos de atividades complementares)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ($turma->modalidadeCurso == ModalidadeCurso::EJA && $turma->tipoAtendimento == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a modalidade do curso da turma {$nomeTurma} é Educação de Jovens e Adultos (EJA), portanto o tipo de atendimento da turma não pode ser atividade complementar.",
                    'path' => '(Escola > Cadastros > Cursos > Editar > Campo: Modalidade do curso)',
                    'linkPath' => "/intranet/educar_curso_cad.php?cod_curso={$turma->codCurso}",
                    'fail' => true
                ];
            }

            if (is_null($turma->localFuncionamentoDiferenciado) && in_array($turma->tipoMediacaoDidaticoPedagogico, [App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL, App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é presencial ou semipresencial, portanto é necessário informar se ela possui local de funcionamento diferenciado.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Local de funcionamento diferenciado da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ((!in_array(LocalFuncionamento::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVA, $turma->localFuncionamento) && $turma->localFuncionamentoDiferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO) || (!in_array(LocalFuncionamento::UNIDADE_PRISIONAL, $turma->localFuncionamento) && $turma->localFuncionamentoDiferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL)) {
                $valuesDescription = $turma->getLocalFuncionamentoDescriptiveValue();
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que o local de funcionamento da escola é {$valuesDescription}. Portanto, o local de funcionamento diferenciado da turma {$nomeTurma}, deve estar de acordo com o local da escola.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Local de funcionamento diferenciado da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (($turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL && !in_array($turma->modalidadeCurso, [ModalidadeCurso::EDUCACAO_ESPECIAL, ModalidadeCurso::EJA])) ||
                ($turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA && !in_array($turma->modalidadeCurso, [ModalidadeCurso::ENSINO_REGULAR, ModalidadeCurso::EJA, ModalidadeCurso::EDUCACAO_PROFISSIONAL]))
            ) {
                $valuesDescription = $turma->getModalidadeCursoDescriptiveValue();
                $opcoesPermitidas = $turma->getTipoMediacaoValidaParaModalidadeCurso();
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a modalidade do curso da turma {$nomeTurma} é {$valuesDescription}, portanto a mediação da turma deve ser {$opcoesPermitidas}.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Tipo de mediação didático pedagógico)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (($turma->formacaoGeralBasica() || $turma->estruturaCurricularNaoSeAplica()) && is_null($turma->etapaEducacenso)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se alguma opção de etapa de ensino da turma {$nomeTurma} foi informada.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Etapa de ensino)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            } elseif ($turma->formacaoGeralBasica() || $turma->estruturaCurricularNaoSeAplica()) {
                $valid = true;
                $opcoesEtapaEducacenso = '';

                switch ($turma->modalidadeCurso) {
                    case ModalidadeCurso::ENSINO_REGULAR:
                        if (!in_array($turma->etapaEducacenso, [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 35, 36, 37, 38, 41, 56])) {
                            $opcoesEtapaEducacenso = '1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 35, 36, 37, 38, 41 ou 56';
                            $valid = false;
                        }
                        break;
                    case ModalidadeCurso::EDUCACAO_ESPECIAL:
                        if (!in_array($turma->etapaEducacenso, [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 56, 39, 40, 69, 70, 71, 72, 73, 74, 64, 67, 68])) {
                            $opcoesEtapaEducacenso = '1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 56, 39, 40, 69, 70, 71, 72, 73, 74, 64, 67 ou 68';
                            $valid = false;
                        }
                        break;
                    case ModalidadeCurso::EJA:
                        if (!in_array($turma->etapaEducacenso, [69, 70, 71, 72])) {
                            $opcoesEtapaEducacenso = '69, 70, 71 ou 72';
                            $valid = false;
                        }
                        break;
                    case ModalidadeCurso::EDUCACAO_PROFISSIONAL:
                        if (!in_array($turma->etapaEducacenso, [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68])) {
                            $opcoesEtapaEducacenso = '30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67 ou 68';
                            $valid = false;
                        }
                        break;
                }

                if (!$valid) {
                    $modalidadeCursoDescription = $turma->getModalidadeCursoDescriptiveValue();
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a modalidade do curso da turma {$nomeTurma} é {$modalidadeCursoDescription}, portanto a etapa de ensino deve ser uma das seguintes opções: {$opcoesEtapaEducacenso}.",
                        'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Etapa de ensino)',
                        'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                        'fail' => true
                    ];
                }

                $valid = true;
                $opcoesEtapaEducacenso = '';

                switch ($turma->tipoMediacaoDidaticoPedagogico) {
                    case App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL:
                        if (!in_array($turma->etapaEducacenso, [69, 70, 71, 72])) {
                            $opcoesEtapaEducacenso = '69, 70, 71 ou 72';
                            $valid = false;
                        }
                        break;
                    case App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA:
                        if (!in_array($turma->etapaEducacenso, [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67, 68])) {
                            $opcoesEtapaEducacenso = '25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 70, 71, 73, 74, 64, 67 ou 68';
                            $valid = false;
                        }
                        break;
                }

                if (!$valid) {
                    $descricaoTipoMediacao = (App_Model_TipoMediacaoDidaticoPedagogico::getInstance()->getEnums())[$turma->tipoMediacaoDidaticoPedagogico];
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que o tipo de mediação didático-pedagógica da turma {$nomeTurma} é {$descricaoTipoMediacao}, portanto a etapa de ensino deve ser uma das seguintes opções: {$opcoesEtapaEducacenso}.",
                        'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Etapa de ensino)',
                        'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                        'fail' => true
                    ];
                }

                $opcoesValidas = [];
                $estruturaCurricularDescritiva = '';

                if ($turma->itinerarioFormativo() && $turma->etapaEducacenso) {
                    $estruturaCurricularDescritiva = 'Itinerário formativo';
                    $opcoesValidas = [24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 71, 74, 67];
                    $valid = in_array($turma->etapaEducacenso, $opcoesValidas);
                }

                if ($turma->estruturaCurricularNaoSeAplica() && $turma->etapaEducacenso) {
                    $estruturaCurricularDescritiva = 'Não se aplica';
                    $opcoesValidas = [1, 2, 3, 24, 39, 40, 64, 68];
                    $valid = in_array($turma->etapaEducacenso, $opcoesValidas);
                }

                if(!$valid) {
                    $opcoesValidas = implode(', ', $opcoesValidas);
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que a estrutura curricular da turma {$nomeTurma} é: {$estruturaCurricularDescritiva}, portanto a etapa de ensino deve ser uma das seguintes opções: {$opcoesValidas}",
                        'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Etapa de ensino)',
                        'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                        'fail' => true
                    ];
                }

                if (in_array($turma->localFuncionamentoDiferenciado, [App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO, App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL]) && in_array($turma->etapaEducacenso, [1, 2, 3, 56])) {
                    $descricaoLocalDiferenciado = $turma->getLocalFuncionamentoDiferenciadoDescription();
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola}  possui valor inválido. Verificamos que o local de funcionamento diferenciado da turma {$nomeTurma} é {$descricaoLocalDiferenciado}, portanto a etapa de ensino não pode ser nenhuma das seguintes opções: 1, 2, 3 ou 56.",
                        'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Etapa de ensino)',
                        'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                        'fail' => true
                    ];
                }
            }

            if ($turma->formacaoGeralBasica() && $turma->itinerarioFormativo() && in_array($turma->etapaEducacenso, [25, 30, 35])) {
                $descricaoEtapa = $turma->etapaEducacensoDescritiva();
                $mensagem[] = [
                    'text' => "<span class='avisos-educacenso'><b>Aviso não impeditivo:</b> Dados para formular o registro 20 da escola {$turma->nomeEscola} sujeito à valor inválido. Verificamos que a turma {$nomeTurma} é de formação geral básica e itinerário formativo, e a etapa de ensino é {$descricaoEtapa}, portanto você pode definir os itinerários dos alunos individualmente.",
                    'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Tipo do itinerário formativo)',
                    'linkPath' => "/intranet/educar_aluno_lst.php",
                    'fail' => false
                ];
            }

            $formasDeOrganizacaoDaTurma = new FormaOrganizacaoTurma($turma);

            if (!empty($turma->etapaEducacenso) && !in_array($turma->etapaEducacenso, [1, 2, 3, 24]) && empty($turma->formasOrganizacaoTurma)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se a forma de organização da turma {$nomeTurma} foi informada.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Formas de organização da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            } elseif (!$formasDeOrganizacaoDaTurma->isValid()) {
                $descricaoEtapa = $turma->etapaEducacensoDescritiva();
                $descricaoFormaOrganizacao = $turma->formaOrganizacaoTurmaDescritiva();
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} possui valor inválido. Verificamos que etapa de ensino da turma {$nomeTurma} é {$descricaoEtapa}, portanto a forma de organização da turma não pode ser {$descricaoFormaOrganizacao}.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Formas de organização da turma)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if ($turma->itinerarioFormativo() && count(array_filter($turma->unidadesCurriculares)) == 0) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se as unidades curriculares da turma {$nomeTurma} foram informadas.",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Unidade curricular)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            if (count($turma->unidadesCurricularesSemDocenteVinculado()) > 0) {
                foreach ($turma->unidadesCurricularesSemDocenteVinculado() as $unidadeCurricular) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que a unidade curricular {$unidadeCurricular} faz parte da turma {$nomeTurma}, portanto deve haver um docente vinculado à mesma.",
                        'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Unidade(s) curricular(es) que leciona)',
                        'linkPath' => '/intranet/educar_servidor_lst.php',
                        'fail' => true
                    ];
                }
            }

            if (empty($turma->codCursoProfissional) && in_array($turma->etapaEducacenso, [30, 31, 32, 33, 34, 39, 40, 64, 74])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se o código dos cursos de educação profissional da turma {$nomeTurma} foi informado",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados adicionais > Campo: Curso de educação profissional)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            }

            try {
                $componentes = $turma->componentes();
            } catch (Throwable) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se alguma disciplina da turma {$nomeTurma} foi informada",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Seção: Componentes curriculares definidos em séries da escola)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
                continue;
            }

            if (empty($componentes)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se alguma disciplina da turma {$nomeTurma} foi informada",
                    'path' => '(Escola > Cadastros > Turmas > Editar > Aba: Dados gerais > Seção: Componentes curriculares definidos em séries da escola)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$turma->codTurma}",
                    'fail' => true
                ];
            } else {
                $componenteIds = $turma->componentesIds();
                $codigosEducacenso = $turma->componentesCodigosEducacenso();

                $disciplinesWithoutTeacher = $registro20->getDisciplinesWithoutTeacher($turma->codTurma, $componenteIds);

                $educacaoDistancia = $turma->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA;

                foreach ($disciplinesWithoutTeacher as $discipline) {
                    $mensagem[] = [
                        'text' => $educacaoDistancia ? "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verificamos que o tipo de mediação da turma {$nomeTurma} é educação a distância, portanto a disciplina {$discipline->nome} deve possuir um docente vinculado." : "<span class='avisos-educacenso'><b>Aviso não impeditivo:</b> Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. A disciplina {$discipline->nome} da turma {$nomeTurma} não possui docente vinculado, portanto será exportada como: 2 (Sim, oferece disciplina sem docente vinculado).</span>",
                        'path' => '(Servidores > Cadastros > Servidores)',
                        'linkPath' => '/intranet/educar_servidor_lst.php',
                        'fail' => $educacaoDistancia
                    ];
                }

                $componenteNulo = null;

                foreach ($componentes as $componente) {
                    if (empty($componente->get('codigo_educacenso'))) {
                        $componenteNulo = $componente;
                        break;
                    }

                    if (in_array($componente->get('codigo_educacenso'), $turma->getForbiddenDisciplines())) {
                        $mensagem[] = [
                            'text' => "<span class='avisos-educacenso'><b>Aviso não impeditivo:</b> Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. A disciplina {$componente->get('nome')} da turma {$nomeTurma} não está de acordo com a Tabela de Regras de Disciplinas do Censo, portanto não será exportada.</span>",
                            'path' => '(Escola > Cadastros > Componentes curriculares > Editar > Disciplina Educacenso)',
                            'linkPath' => "/module/ComponenteCurricular/edit?id={$componente->get('id')}",
                            'fail' => false
                        ];
                    }
                }

                if ($componenteNulo) {
                    $mensagem = [[
                        'text' => "Dados para formular o registro 20 da escola {$turma->nomeEscola} não encontrados. Verifique se a disciplina do educacenso foi informada para a disciplina {$componente->get('nome')}.",
                        'path' => '(Escola > Cadastros > Componentes curriculares > Editar > Disciplina Educacenso)',
                        'linkPath' => "/module/ComponenteCurricular/edit?id={$componenteNulo->get('id')}",
                        'fail' => true
                    ]];

                    break;
                }
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 20'
        ];
    }

    protected function analisaEducacensoRegistro30()
    {
        $escolaId = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;

        $educacensoRepository = new EducacensoRepository();

        $registro40Model = new Registro40();
        $registro40 = new Registro40Data($educacensoRepository, $registro40Model);

        $registro50Model = new Registro50();
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);

        $registro60Model = new Registro60();
        $registro60 = new Registro60Data($educacensoRepository, $registro60Model);

        /** @var Registro40[] $gestores */
        $gestores = $registro40->getData($escolaId);

        /** @var Registro50[] $docentes */
        $docentes = $registro50->getData($escolaId, $ano);

        /** @var Registro60[] $alunos */
        $alunos = $registro60->getData($escolaId, $ano);

        $registro30Data = new Registro30Data($educacensoRepository, new Registro30());
        $registro30Data->setArrayDataByType($gestores, Registro30::TIPO_MANAGER);
        $registro30Data->setArrayDataByType($docentes, Registro30::TIPO_TEACHER);
        $registro30Data->setArrayDataByType($alunos, Registro30::TIPO_STUDENT);

        $pessoas = $registro30Data->getData($escolaId);

        $mensagem = [];

        $mensagem[] = [
            'text' => '<span class=\'avisos-educacenso\'><b>Aviso não impeditivo:</b> O campo: País de residência possui valor padrão: Brasil. Certifique-se que os(as) alunos(as) ou docentes residentes de outro país, que não seja Brasil, possuam o País de residência informado corretamente.</span>',
            'path' => '(Pessoas > Cadastros > Pessoas físicas > Editar > Campo: País de residência)',
            'linkPath' => '/intranet/atendidos_lst.php',
            'fail' => false
        ];

        if (DB::table('cadastro.raca')->whereNull('raca_educacenso')->exists()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 30 da escola {$pessoas[0]->nomeEscola} não encontrados. Verifique se a(s) raça(s) do educacenso foi(ram) informada(s).",
                'path' => '(Pessoas > Cadastros > Tipos > Tipos de cor ou raça)',
                'linkPath' => '/intranet/educar_raca_lst.php',
                'fail' => true
            ];
        }

        if (DB::table('cadastro.deficiencia')->whereNull('deficiencia_educacenso')->exists()) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 30 da escola {$pessoas[0]->nomeEscola} não encontrados. Verifique se a(s) deficiência(s) do educacenso foi(ram) informada(s).",
                'path' => '(Pessoas > Cadastros > Tipos > Tipos de deficiência)',
                'linkPath' => '/intranet/educar_deficiencia_lst.php',
                'fail' => true
            ];
        }

        foreach ($pessoas as $pessoa) {
            $commonDataAnalysis = new Register30CommonDataAnalysis($pessoa);
            $commonDataAnalysis->run();
            $mensagem = array_merge($mensagem, $commonDataAnalysis->getMessages());

            if ($pessoa->isManager()) {
                $managerDataAnalysis = new Register30ManagerDataAnalysis($pessoa);
                $managerDataAnalysis->run();
                $mensagem = array_merge($mensagem, $managerDataAnalysis->getMessages());
            }

            if ($pessoa->isTeacher()) {
                $teacherDataAnalysis = new Register30TeacherDataAnalysis($pessoa);
                $teacherDataAnalysis->run();
                $mensagem = array_merge($mensagem, $teacherDataAnalysis->getMessages());
            }

            if ($pessoa->isStudent()) {
                $studentDataAnalysis = new Register30StudentDataAnalysis($pessoa);
                $studentDataAnalysis->setYear($this->getRequest()->ano);
                $studentDataAnalysis->run();
                $mensagem = array_merge($mensagem, $studentDataAnalysis->getMessages());
            }

            if ($pessoa->isTeacher() || $pessoa->isStudent()) {
                $teacherAndStudentDataAnalysis = new Register30TeacherAndStudentDataAnalysis($pessoa);
                $teacherAndStudentDataAnalysis->run();
                $mensagem = array_merge($mensagem, $teacherAndStudentDataAnalysis->getMessages());
            }

            if ($pessoa->isTeacher() || $pessoa->isManager()) {
                $teacherAndManagerDataAnalysis = new Register30TeacherAndManagerDataAnalysis($pessoa);
                $teacherAndManagerDataAnalysis->run();
                $mensagem = array_merge($mensagem, $teacherAndManagerDataAnalysis->getMessages());
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 30'
        ];
    }

    protected function analisaEducacensoRegistro40()
    {
        $escolaId = $this->getRequest()->escola;

        $educacensoRepository = new EducacensoRepository();
        $registro40Model = new Registro40();
        $registro40 = new Registro40Data($educacensoRepository, $registro40Model);
        $gestores = $registro40->getData($escolaId);

        $escola = LegacySchool::query()->find($escolaId);

        $nomeEscola = $escola->name;
        $codEscola = $escolaId;

        $mensagem = [];

        if (empty($gestores)) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verifique se algum(a) gestor(a) escolar foi informado(a).",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: dados gerais > Tabela Gestores escolares)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];

            return [
                'mensagens' => $mensagem,
                'title' => 'Análise exportação - Registro 40'
            ];
        }

        if (count($gestores) > 3) {
            $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} possui valor inválido. A escola não pode ter mais de 3 gestores escolares.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: dados gerais > Tabela Gestores escolares)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
        }

        foreach ($gestores as $gestor) {
            $nomeGestor = Individual::find($gestor->codigoPessoa)->realName;

            if (empty($gestor->cargo)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verifique se o cargo do gestor(a) {$nomeGestor} foi informado.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Campo: Cargo do(a) gestor(a))',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$gestor->criterioAcesso && $gestor->cargo == SchoolManagerRole::DIRETOR && $gestor->situacaoFuncionamento == SituacaoFuncionamento::EM_ATIVIDADE) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que o gestor escolar {$nomeGestor} é diretor(a) e a situação de funcionamento da escola é em atividade, portanto é necessário informar o critério de acesso ao cargo.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: Critério de acesso ao cargo)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            $dependenciaAdministraticaDesc = DependenciaAdministrativaEscola::getDescriptiveValues()[$gestor->dependenciaAdministrativa];

            if ($gestor->criterioAcesso == SchoolManagerAccessCriteria::PROPRIETARIO && $gestor->isDependenciaAdministrativaPublica()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} possui valor inválido. Verificamos que a escola é {$dependenciaAdministraticaDesc}, portanto o critério de acesso ao cargo do(a) gestor(a) {$nomeGestor} não pode ser proprietário(a) ou sócio(a)-proprietário(a) da escola.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: Critério de acesso ao cargo)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            $rolesInvalid = [
                SchoolManagerAccessCriteria::CONCURSO,
                SchoolManagerAccessCriteria::PROCESSO_ELEITORAL_COMUNIDADE,
                SchoolManagerAccessCriteria::PROCESSO_SELETIVO_COMUNIDADE,
            ];

            $criterioAcessoDesc = SchoolManagerAccessCriteria::getDescriptiveValues()[$gestor->criterioAcesso];

            if (!$gestor->isDependenciaAdministrativaPublica() && in_array($gestor->criterioAcesso, $rolesInvalid)) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} possui valor inválido. Verificamos que a escola é privada, portanto o critério de acesso ao cargo do(a) gestor(a) {$nomeGestor} não pode ser {$criterioAcessoDesc}.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: Critério de acesso ao cargo)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$gestor->especificacaoCriterioAcesso && $gestor->criterioAcesso == SchoolManagerAccessCriteria::OUTRO) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que o gestor escolar {$nomeGestor} possui o critério de acesso ao cargo definido como \"outros\", portanto é necessário informar a especificação do critério.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: Especificação do critério de acesso)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$gestor->tipoVinculo &&
                $gestor->cargo === SchoolManagerRole::DIRETOR &&
                $gestor->isDependenciaAdministrativaPublica() &&
                $escola->situacao_funcionamento === SituacaoFuncionamento::EM_ATIVIDADE
            ) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que o gestor escolar {$nomeGestor} é diretor(a) e a dependência administrativa da escola é {$dependenciaAdministraticaDesc} e a situação de funcionamento da escola é em atividade, portanto é necessário informar o tipo de vínculo.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Link: Dados adicionais do(a) gestor(a) > Campo: Tipo de vínculo)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!(new InepNumberValidator($gestor->inepGestor))->isValid()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola}  possui valor inválido. Verifique se o código INEP do gestor {$nomeGestor} possui 12 dígitos.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Tabela Gestores escolares > Campo: INEP)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 40'
        ];
    }

    protected function analisaEducacensoRegistro50()
    {
        $escolaId = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;

        $educacensoRepository = new EducacensoRepository();
        $registro50Model = new Registro50();
        $registro50 = new Registro50Data($educacensoRepository, $registro50Model);
        $docentes = $registro50->getData($escolaId, $ano);

        if (empty($docentes)) {
            $this->messenger->append('Nenhum docente encontrado.');

            return ['title' => 'Análise exportação - Registro 50'];
        }

        $mensagem = [];
        foreach ($docentes as $docente) {
            if (!$docente->funcaoDocente) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} não encontrados. Verifique se a função do(a) docente {$docente->nomeDocente} foi informada.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Função exercida)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            if (!$docente->isTitularOrTutor() && $docente->tipoMediacaoTurma == TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} possui valor inválido. Verificamos que o tipo de mediação da turma {$docente->nomeTurma} é educação a distância, portanto a função exercida do(a) docente {$docente->nomeDocente} deve ser obrigatoriamente docente titular ou docente tutor.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Função exercida)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            $tipoAtendimentoDesc = TipoAtendimentoTurma::getDescriptiveValues()[$docente->tipoAtendimentoTurma];

            if ($docente->funcaoDocente == FuncaoExercida::AUXILIAR_EDUCACIONAL  && $docente->tipoAtendimentoTurma != TipoAtendimentoTurma::ESCOLARIZACAO) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} possui valor inválido. Verificamos que o tipo de atendimento da turma {$docente->nomeTurma} é {$tipoAtendimentoDesc}, portanto a função exercida do(a) docente {$docente->nomeDocente} não pode ser auxiliar/assistente educacional.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Função exercida)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            if ($docente->funcaoDocente == FuncaoExercida::MONITOR_ATIVIDADE_COMPLEMENTAR  && $docente->tipoAtendimentoTurma != TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} possui valor inválido. Verificamos que o tipo de atendimento da turma {$docente->nomeTurma} é {$tipoAtendimentoDesc}, portanto a função exercida do(a) docente {$docente->nomeDocente} não pode ser profissional/monitor de atividade complementar.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Função exercida)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            $etapasValidasParaInstrutor = [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68];

            if ($docente->funcaoDocente == FuncaoExercida::INSTRUTOR_EDUCACAO_PROFISSIONAL &&
                (
                    !in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $docente->estruturaCurricular)
                    || !in_array($docente->etapaEducacensoTurma, $etapasValidasParaInstrutor)
                )) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} possui valor inválido. Verificamos que estrutura curricular da turma {$docente->nomeTurma} é {$docente->estruturasCurricularesDescritivas()} e a etapa de ensino é {$docente->estapaEducacensoDescritiva()}, portanto a função exercida do(a) docente {$docente->nomeDocente} não pode ser instrutor da Educação Profissional.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Função exercida)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            if (!$docente->tipoVinculo) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} não encontrados. Verifique se o tipo de vínculo do(a) docente {$docente->nomeDocente} foi informada.",
                    'path' => '(Servidores > Cadastros > Servidores > Vincular professor a turmas > Editar > Campo: Tipo do vínculo)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$docente->idAlocacao}&ref_cod_instituicao={$docente->idInstituicao}&ref_cod_servidor={$docente->idServidor}",
                    'fail' => true
                ];
            }

            if (!(new InepNumberValidator($docente->inepDocente))->isValid()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$docente->nomeEscola} possui valor inválido. Verifique se o código INEP do docente {$docente->nomeDocente}, vinculado à turma {$docente->nomeTurma}, possui 12 dígitos.",
                    'path' => '(Servidores > Cadastros > Servidores > Editar > Aba: Dados gerais > Campo: Código INEP)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$docente->idServidor}&ref_cod_instituicao={$docente->idInstituicao}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 50'
        ];
    }

    protected function analisaEducacensoRegistro60()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;

        $educacensoRepository = new EducacensoRepository();
        $registro60Model = new Registro60();
        $registro60 = new Registro60Data($educacensoRepository, $registro60Model);

        $alunos = $registro60->getData($escola, $ano);

        if (empty($alunos)) {
            $this->messenger->append('Nenhum aluno encontrado.');

            return ['title' => 'Análise exportação - Registro 60'];
        }

        $mensagem = [];
        $countAtividadesComplementar = [];
        $notAvaliableTime = [];

        /** @var LegacySchool $school */
        $school = LegacySchool::query()->findOrFail($escola);
        $educacensoDate = new DateTime($school->institution->data_educacenso);

        $avaliableTimeService = new AvailableTimeService();

        $avaliableTimeService->onlyUntilEnrollmentDate($educacensoDate)->onlySchoolClassesInformedOnCensus();

        foreach ($alunos as $aluno) {
            $nomeEscola = mb_strtoupper($aluno->nomeEscola);
            $nomeAluno = mb_strtoupper($aluno->nomeAluno);
            $nomeTurma = mb_strtoupper($aluno->nomeTurma);
            $codigoAluno = $aluno->codigoAluno;
            $codigoTurma = $aluno->codigoTurma;
            $codigoMatricula = $aluno->codigoMatricula;

            if (!$avaliableTimeService->isAvailable($codigoAluno, $codigoTurma)) {
                $notAvaliableTime[$codigoAluno] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} possui valor inválido. Verificamos que o(a) aluno(a) {$nomeAluno} possui mais de um vínculo em diferentes turmas presenciais com horário e dias coincidentes.",
                    'path' => '(Escola > Cadastros > Alunos > Seção: Matrículas)',
                    'linkPath' => "/intranet/educar_aluno_det.php?cod_aluno={$codigoAluno}",
                    'fail' => true
                ];
            }

            if ($aluno->isAtividadeComplementarOrAee()) {
                $countAtividadesComplementar[$codigoAluno][] = [
                    'codigoAluno' => $codigoAluno,
                    'nomeAluno' => $nomeAluno,
                    'nomeEscola' => $nomeEscola
                ];
            }

            if (!$aluno->etapaAluno && in_array($aluno->etapaTurma, App_Model_Educacenso::etapas_multisseriadas())) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é multisseriada, portanto é necessário informar qual a etapa do(a) aluno(a) {$nomeAluno}.",
                    'path' => '(Escola > Cadastros > Alunos > Visualizar > Etapa do aluno > Campo: Etapa do aluno na turma)',
                    'linkPath' => "/intranet/educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$codigoMatricula}&ref_cod_aluno={$codigoAluno}",
                    'fail' => true
                ];
            }

            if ($aluno->analisaDadosItinerario()) {
                if (
                    in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $aluno->estruturaCurricularTurma) &&
                    count($aluno->estruturaCurricularTurma) === 1 &&
                    $aluno->tipoItinerarioNaoPreenchido()
                ) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a estrutura curricular da turma {$nomeTurma} é itinerário formativo, portanto é necessário informar o tipo do itinerário formativo do(a) aluno(a) {$nomeAluno}.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Tipo do itinerário formativo)',
                        'linkPath' => "/enrollment-formative-itinerary/{$aluno->enturmacaoId}",
                        'fail' => true
                    ];
                }

                $etapasObrigatorias = [26, 27, 28, 31, 32, 33, 36, 37, 38, 71, 74];

                if (
                    in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $aluno->estruturaCurricularTurma) &&
                    in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $aluno->estruturaCurricularTurma) &&
                    in_array($aluno->etapaTurma, $etapasObrigatorias) &&
                    $aluno->tipoItinerarioNaoPreenchido()
                ) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a estrutura curricular da turma {$nomeTurma} é formação geral básica/itinerário formativo e a etapa de ensino é {$aluno->etapaTurmaDescritiva()}, portanto é necessário informar o tipo do itinerario formativo do(a) aluno(a) {$nomeAluno}.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Tipo do itinerário formativo)',
                        'linkPath' => "/enrollment-formative-itinerary/{$aluno->enturmacaoId}",
                        'fail' => true
                    ];
                }

                if ($aluno->tipoItinerarioIntegrado && $aluno->composicaoItinerarioNaoPreenchido()) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de itinerário formativo do(a) aluno(a) {$nomeAluno} foi preenchido com a opção de itinerário formativo integrado, portanto é necessário informar a composição do itinerário formativo integrado.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Composição do itinerário formativo integrado)',
                        'linkPath' => "/enrollment-formative-itinerary/{$aluno->enturmacaoId}",
                        'fail' => true
                    ];
                }

                if ($aluno->composicaoItinerarioFormacaoTecnica && empty($aluno->cursoItinerario)) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a composição do itinerário formativo do(a) aluno(a) {$nomeAluno} foi preenchido com a opção de formação técnica e profissional, portanto é necessário informar o tipo do curso do itinerário de formação técnica e profissional.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Tipo do curso do itinerário de formação técnica e profissional)',
                        'linkPath' => "/enrollment-formative-itinerary/{$aluno->enturmacaoId}",
                        'fail' => true
                    ];
                }

                if ($aluno->composicaoItinerarioFormacaoTecnica && $aluno->itinerarioConcomitante === null) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a composição do itinerário formativo do(a) aluno(a) {$nomeAluno} foi preenchido com a opção de formação técnica e profissional, portanto é necessário informar se é um itinerário concomitante intercomplementar à matrícula de formação geral básica.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar > Itinerário formativo > Campo: Itinerário concomitante intercomplementar à matrícula de formação geral básica)',
                        'linkPath' => "/enrollment-formative-itinerary/{$aluno->enturmacaoId}",
                        'fail' => true
                    ];
                }
            }

            if (isArrayEmpty($aluno->tipoAtendimentoMatricula) && $aluno->tipoAtendimentoTurma == TipoAtendimentoTurma::AEE) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que a turma {$nomeTurma} é de atendimento educacional especializado, portanto é necessário informar qual a tipo de atendimento do(a) aluno(a) {$nomeAluno}.",
                    'path' => '(Escola > Cadastros > Alunos > Visualizar > Tipo do AEE do aluno > Campo: Tipo de Atendimento Educacional Especializado do aluno na turma)',
                    'linkPath' => "/intranet/educar_matricula_turma_tipo_aee_cad.php?ref_cod_matricula={$codigoMatricula}&ref_cod_aluno={$codigoAluno}",
                    'fail' => true
                ];
            }

            if (is_null($aluno->transportePublico) && $aluno->transportePublicoRequired()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verifique se o transporte escolar público do(a) aluno(a) {$nomeAluno} foi informado.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados Pessoais > Campo: Transporte escolar público)',
                    'linkPath' => "/module/Cadastro/aluno?id={$codigoAluno}",
                    'fail' => true
                ];
            }

            if (isArrayEmpty($aluno->veiculoTransporteEscolar) && $aluno->veiculoTransporteEscolarRequired()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verifique se o tipo de veículo do transporte escolar público utilizado pelo(a) aluno(a) {$nomeAluno} foi informado.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados Pessoais > Campo: Veículo utilizado)',
                    'linkPath' => "/module/Cadastro/aluno?id={$codigoAluno}",
                    'fail' => true
                ];
            }

            if (!(new InepNumberValidator($aluno->inepAluno))->isValid()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} possui valor inválido. Verifique se o código INEP do aluno {$nomeAluno}, matriculado na turma {$nomeTurma}, possui 12 dígitos.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados gerais > Campo: Código INEP)',
                    'linkPath' => "/module/Cadastro/aluno?id={$codigoAluno}",
                    'fail' => true
                ];
            }

            if ((!$aluno->recebeEscolarizacaoOutroEspacao) && $aluno->recebeEscolarizacaoOutroEspacoIsRequired()) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verifique se a escolarização em outro espaço foi informada para o(a) aluno(a) {$nomeAluno}.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Aba: Dados Educacenso > Campo: Recebe escolarização em outro espaço (diferente da escola))',
                    'linkPath' => "/module/Cadastro/aluno?id={$codigoAluno}",
                    'fail' => true
                ];
            }
        }

        foreach ($notAvaliableTime as $notAvaliableTimeMessage) {
            $mensagem[] = $notAvaliableTimeMessage;
        }

        foreach ($countAtividadesComplementar as $atividadesAluno) {
            if (count($atividadesAluno) > 4) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$atividadesAluno[0]['nomeEscola']} possui valor inválido. Verificamos que o(a) aluno(a) {$atividadesAluno[0]['nomeAluno']} possui mais de quatro vínculos com turmas de AEE ou Atividade Complementar.",
                    'path' => '(Escola > Cadastros > Alunos > Seção: Matrículas)',
                    'linkPath' => "/intranet/educar_aluno_det.php?cod_aluno={$atividadesAluno[0]['codigoAluno']}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 60'
        ];
    }

    protected function analisaEducacensoRegistro89()
    {
        $escola = $this->getRequest()->escola;

        $sql = 'SELECT DISTINCT j.fantasia AS nome_escola,
                            ece.cod_escola_inep AS inep,
                            gp.nome AS nome_gestor,
                            gf.cpf AS cpf_gestor,
                            e.cargo_gestor
              FROM pmieducar.escola e
             INNER JOIN cadastro.juridica j ON (j.idpes = e.ref_idpes)
              LEFT JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
              LEFT JOIN cadastro.fisica gf ON (gf.idpes = e.ref_idpes_gestor)
              LEFT JOIN cadastro.pessoa gp ON (gp.idpes = e.ref_idpes_gestor)
             WHERE e.cod_escola = $1';

        $escolas = $this->fetchPreparedQuery($sql, [$escola]);

        $mensagem = [];

        foreach ($escolas as $escola) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(mb_strtoupper($escola['nome_escola']));

            if (is_null($escola['inep'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 89 da escola {$nomeEscola} não encontrados. Verifique se a escola possui o código INEP cadastrado.",
                    'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados gerais > Campo: Código INEP)',
                    'fail' => true
                ];
            }

            if ($escola['cpf_gestor'] <= 0) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 89 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar possui o CPF cadastrado.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: CPF)',
                    'fail' => true
                ];
            }

            if (is_null($escola['nome_gestor'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 89 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar foi informado(a).",
                    'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados gerais > Campo: Gestor escolar)',
                    'fail' => true
                ];
            }

            if (is_null($escola['cargo_gestor'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 89 da escola {$nomeEscola} não encontrados. Verifique se o cargo do(a) gestor(a) escolar foi informado.",
                    'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Campo: Cargo do gestor escolar)',
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 89'
        ];
    }

    protected function analisaEducacensoRegistro90()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT DISTINCT j.fantasia AS nome_escola,
                            t.nm_turma AS nome_turma,
                            ect.cod_turma_inep AS inep_turma,
                            p.nome AS nome_aluno,
                            eca.cod_aluno_inep AS inep_aluno
              FROM pmieducar.escola e
             INNER JOIN pmieducar.turma t ON (t.ref_ref_cod_escola = e.cod_escola)
             INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_turma = t.cod_turma)
             INNER JOIN pmieducar.matricula m ON (m.cod_matricula = mt.ref_cod_matricula)
             INNER JOIN pmieducar.aluno a ON (a.cod_aluno = m.ref_cod_aluno)
             INNER JOIN cadastro.pessoa p ON (p.idpes = a.ref_idpes)
             INNER JOIN cadastro.juridica j ON (j.idpes = e.ref_idpes)
              LEFT JOIN modules.educacenso_cod_aluno eca ON (eca.cod_aluno = a.cod_aluno)
              LEFT JOIN modules.educacenso_cod_turma ect ON (ect.cod_turma = t.cod_turma)
             WHERE e.cod_escola = $1
               AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
               AND m.aprovado IN (1, 2, 3, 4, 6, 15)
               AND m.ano = $2
             ORDER BY nome_turma';

        $alunos = $this->fetchPreparedQuery($sql, [$escola,
            $ano,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        $mensagem = [];
        $ultimaTurmaVerificada;

        foreach ($alunos as $aluno) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(mb_strtoupper($aluno['nome_escola']));
            $nomeTurma = Portabilis_String_Utils::toUtf8(mb_strtoupper($aluno['nome_turma']));
            $nomeAluno = Portabilis_String_Utils::toUtf8(mb_strtoupper($aluno['nome_aluno']));

            if (is_null($aluno['inep_turma']) && $ultimaTurmaVerificada != $aluno['nome_turma']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 90 da escola {$nomeEscola} não encontrados. Verifique se a turma {$nomeTurma} possui o código INEP cadastrado.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados adicionais > Campo: Código INEP)',
                    'linkPath' => '#',
                    'fail' => true
                ];
                $ultimaTurmaVerificada = $aluno['nome_turma'];
            }

            if (is_null($aluno['inep_aluno'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 90 da escola {$nomeEscola} não encontrados. Verifique se o(a) aluno(a) {$nomeAluno} possui o código INEP cadastrado.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Campo: Código INEP)',
                    'linkPath' => '#',
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 90'
        ];
    }

    protected function analisaEducacensoRegistro91()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;

        $sql = 'SELECT DISTINCT pa.nome AS nome_aluno,
                            pe.fantasia AS nome_escola,
                            eca.cod_aluno_inep AS cod_inep
              FROM pmieducar.aluno a
              LEFT JOIN modules.educacenso_cod_aluno eca ON (eca.cod_aluno = a.cod_aluno)
             INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
             INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
             INNER JOIN pmieducar.escola e ON (e.cod_escola = m.ref_ref_cod_escola)
             INNER JOIN cadastro.pessoa pa ON (pa.idpes = a.ref_idpes)
             INNER JOIN cadastro.juridica pe ON (pe.idpes = e.ref_idpes)
             INNER JOIN pmieducar.instituicao i ON (i.cod_instituicao = e.ref_cod_instituicao)
             WHERE e.cod_escola = $2
               AND m.aprovado IN (1, 2, 3, 4, 6, 15)
               AND m.ano = $1
               AND mt.data_enturmacao > i.data_educacenso
               AND i.data_educacenso IS NOT NULL
             ORDER BY nome_aluno';

        $alunos = $this->fetchPreparedQuery($sql, [
            $ano,
            $escola
        ]);

        $mensagem = [];

        foreach ($alunos as $aluno) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(mb_strtoupper($aluno['nome_escola']));
            $nomeAluno = Portabilis_String_Utils::toUtf8(mb_strtoupper($aluno['nome_aluno']));

            if (is_null($aluno['cod_inep'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 91 da escola {$nomeEscola} não encontrados. Verifique se o(a) aluno(a) {$nomeAluno} possui o código INEP cadastrado.",
                    'path' => '(Escola > Cadastros > Alunos > Editar > Campo: Código INEP)',
                    'linkPath' => '#',
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 91'
        ];
    }

    private function validaInstituicao()
    {
        $institution = LegacyInstitution::find($this->getRequest()->instituicao);

        return [ 'valid' => $institution && !empty($institution->data_educacenso)];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'valida-instituicao')) {
            $this->appendResponse($this->validaInstituicao());
        } elseif ($this->isRequestFor('get', 'registro-00')) {
            $this->appendResponse($this->analisaEducacensoRegistro00());
        } elseif ($this->isRequestFor('get', 'registro-10')) {
            $this->appendResponse($this->analisaEducacensoRegistro10());
        } elseif ($this->isRequestFor('get', 'registro-20')) {
            $this->appendResponse($this->analisaEducacensoRegistro20());
        } elseif ($this->isRequestFor('get', 'registro-30')) {
            $this->appendResponse($this->analisaEducacensoRegistro30());
        } elseif ($this->isRequestFor('get', 'registro-40')) {
            $this->appendResponse($this->analisaEducacensoRegistro40());
        } elseif ($this->isRequestFor('get', 'registro-50')) {
            $this->appendResponse($this->analisaEducacensoRegistro50());
        } elseif ($this->isRequestFor('get', 'registro-60')) {
            $this->appendResponse($this->analisaEducacensoRegistro60());
        } elseif ($this->isRequestFor('get', 'registro-89')) {
            $this->appendResponse($this->analisaEducacensoRegistro89());
        } elseif ($this->isRequestFor('get', 'registro-90')) {
            $this->appendResponse($this->analisaEducacensoRegistro90());
        } elseif ($this->isRequestFor('get', 'registro-91')) {
            $this->appendResponse($this->analisaEducacensoRegistro91());
        } elseif ($this->isRequestFor('get', 'school-is-active')) {
            $this->appendResponse($this->schoolIsActive());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
