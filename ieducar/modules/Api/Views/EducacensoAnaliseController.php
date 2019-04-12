<?php

use App\Models\Educacenso\Registro00;
use App\Models\School;
use App\Repositories\EducacensoRepository;
use iEducar\Modules\Educacenso\Data\Registro00 as Registro00Data;
use iEducar\Modules\Educacenso\LocalizacaoDiferenciadaEscola;
use iEducar\Modules\Educacenso\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Validator\CnpjMantenedoraPrivada;
use iEducar\Modules\Educacenso\Validator\Telefone;

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'intranet/include/clsBanco.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'lib/App/Model/Educacenso.php';

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

        $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($escola->nome));
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

        if (strlen($escola->nome) < 4) {
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

        if ($escola->localizacaoDiferenciada == LocalizacaoDiferenciadaEscola::AREA_ASSENTAMENTO && $escola->zonaLocalizacao == App_Model_ZonaLocalizacao::URBANA) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a zona/localização da escola é urbana, portanto a localização diferenciada da escola não pode ser área de assentamento;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Localização diferenciada da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola->orgaoVinculado && $escola->dependenciaAdministrativa != DependenciaAdministrativaEscola::PRIVADA) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se órgão que a escola pública está vinculada foi informado;",
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados gerais > Campo: Órgão que a escola pública está vinculada)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        $cnpjMantenedoraPrivada = new CnpjMantenedoraPrivada($escola);
        if (!$cnpjMantenedoraPrivada->isValid()) {
            $mensagem[] = [
                'text' => $cnpjMantenedoraPrivada->getMessage(),
                'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: CNPJ da mantenedora principal da escola privada)',
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
                'text' => "<span class='avisos-educacenso'><b>Aviso!</b> Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que o código do órgão regional de ensino não foi preenchido, caso seu estado possua uma subdivisão e a escola {$nomeEscola} não for federal vinculada a Setec, o código deve ser inserido conforme a 'Tabela de Órgãos Regionais'.</span>",
                'path' => '(Escola > Cadastros > Instituição > Editar > Aba: Dados gerais > Campo: Código do órgão regional de ensino)',
                'linkPath' => "/intranet/educar_instituicao_cad.php?cod_instituicao={$codInstituicao}",
                'fail' => false
            ];
        }

        if ($escola->dependenciaAdministrativa == MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS && $escola->situacaoFuncionamento == Regulamentacao::SIM) {
            if (!$escola->categoriaEscolaPrivada) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a dependência administrativa da escola é privada, portanto é necessário informar qual a categoria desta unidade escolar.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Categoria da escola privada)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$escola->conveniadaPoderPublico) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a dependência administrativa da escola é privada, portanto é necessário informar qual o tipo de convênio desta unidade escolar.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Conveniada com poder público)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }

            if (!$escola->mantenedoraEscolaPrivada) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a dependência administrativa da escola é privada, portanto é necessário informar qual o tipo de mantenedora desta unidade escolar.",
                    'path' => '(Escola > Cadastros > Escolas > Editar > Aba: Dados do ensino > Campo: Mantenedora da escola privada)',
                    'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 00'
        ];
    }

    protected function analisaEducacensoRegistro10()
    {
        $escola = $this->getRequest()->escola;

        $sql = 'SELECT escola.cod_escola AS cod_escola,
                   escola.local_funcionamento AS local_funcionamento,
                   escola.condicao AS condicao,
                   escola.agua_consumida AS agua_consumida,
                   (ARRAY[1] <@ escola.abastecimento_agua)::int AS agua_rede_publica,
                   (ARRAY[2] <@ escola.abastecimento_agua)::int AS agua_poco_artesiano,
                   (ARRAY[3] <@ escola.abastecimento_agua)::int AS agua_cacimba_cisterna_poco,
                   (ARRAY[4] <@ escola.abastecimento_agua)::int AS agua_fonte_rio,
                   (ARRAY[5] <@ escola.abastecimento_agua)::int AS agua_inexistente,
                   (ARRAY[1] <@ escola.abastecimento_energia)::int AS energia_rede_publica,
                   (ARRAY[2] <@ escola.abastecimento_energia)::int AS energia_gerador,
                   (ARRAY[3] <@ escola.abastecimento_energia)::int AS energia_outros,
                   (ARRAY[4] <@ escola.abastecimento_energia)::int AS energia_inexistente,
                   (ARRAY[1] <@ escola.esgoto_sanitario)::int AS esgoto_rede_publica,
                   (ARRAY[2] <@ escola.esgoto_sanitario)::int AS esgoto_fossa,
                   (ARRAY[3] <@ escola.esgoto_sanitario)::int AS esgoto_inexistente,
                   (ARRAY[1] <@ escola.destinacao_lixo)::int AS lixo_coleta_periodica,
                   (ARRAY[2] <@ escola.destinacao_lixo)::int AS lixo_queima,
                   (ARRAY[3] <@ escola.destinacao_lixo)::int AS lixo_joga_outra_area,
                   (ARRAY[4] <@ escola.destinacao_lixo)::int AS lixo_recicla,
                   (ARRAY[5] <@ escola.destinacao_lixo)::int AS lixo_enterra,
                   (ARRAY[6] <@ escola.destinacao_lixo)::int AS lixo_outros,
                   escola.dependencia_sala_diretoria AS dependencia_sala_diretoria,
                   escola.dependencia_sala_professores AS dependencia_sala_professores,
                   escola.dependencia_sala_secretaria AS dependncia_sala_secretaria,
                   escola.dependencia_laboratorio_informatica AS dependencia_laboratorio_informatica,
                   escola.dependencia_laboratorio_ciencias AS dependencia_laboratorio_ciencias,
                   escola.dependencia_sala_aee AS dependencia_sala_aee,
                   escola.dependencia_quadra_coberta AS dependencia_quadra_coberta,
                   escola.dependencia_quadra_descoberta AS dependencia_quadra_descoberta,
                   escola.dependencia_cozinha AS dependencia_cozinha,
                   escola.dependencia_biblioteca AS dependencia_biblioteca,
                   escola.dependencia_sala_leitura AS dependencia_sala_leitura,
                   escola.dependencia_parque_infantil AS dependencia_parque_infantil,
                   escola.dependencia_bercario AS dependencia_bercario,
                   escola.dependencia_banheiro_fora AS dependencia_banheiro_fora,
                   escola.dependencia_banheiro_dentro AS dependencia_banheiro_dentro,
                   escola.dependencia_banheiro_infantil AS dependencia_banheiro_infantil,
                   escola.dependencia_banheiro_deficiente AS dependencia_banheiro_deficiente,
                   escola.dependencia_banheiro_chuveiro AS dependencia_banheiro_chuveiro,
                   escola.dependencia_refeitorio AS dependencia_refeitorio,
                   escola.dependencia_dispensa AS dependencia_dispensa,
                   escola.dependencia_aumoxarifado AS dependencia_aumoxarifado,
                   escola.dependencia_auditorio AS dependencia_auditorio,
                   escola.dependencia_patio_coberto AS dependencia_patio_coberto,
                   escola.dependencia_patio_descoberto AS dependencia_patio_descoberto,
                   escola.dependencia_alojamento_aluno AS dependencia_alojamento_aluno,
                   escola.dependencia_alojamento_professor AS dependencia_alojamento_professor,
                   escola.dependencia_area_verde AS dependencia_area_verde,
                   escola.dependencia_lavanderia AS dependencia_lavanderia,
                   escola.dependencia_nenhuma_relacionada AS dependencia_nenhuma_relacionada,
                   escola.dependencia_numero_salas_existente AS dependencia_numero_salas_existente,
                   escola.dependencia_numero_salas_utilizadas AS dependencia_numero_salas_utilizadas,
                   escola.televisoes AS televisoes,
                   escola.videocassetes AS videocassetes,
                   escola.dvds AS dvds,
                   escola.antenas_parabolicas AS antenas_parabolicas,
                   escola.copiadoras AS copiadoras,
                   escola.retroprojetores AS retroprojetores,
                   escola.impressoras AS impressoras,
                   escola.aparelhos_de_som AS aparelhos_de_som,
                   escola.projetores_digitais AS projetores_digitais,
                   escola.faxs AS faxs,
                   escola.maquinas_fotograficas AS maquinas_fotograficas,
                   escola.computadores AS computadores,
                   escola.computadores_administrativo AS computadores_administrativo,
                   escola.computadores_alunos AS computadores_alunos,
                   escola.impressoras_multifuncionais AS impressoras_multifuncionais,
                   escola.total_funcionario AS total_funcionario,
                   escola.atendimento_aee AS atendimento_aee,
                   escola.atividade_complementar AS atividade_complementar,
                   escola.localizacao_diferenciada AS localizacao_diferenciada,
                   escola.materiais_didaticos_especificos AS materiais_didaticos_especificos,
                   escola.lingua_ministrada AS lingua_ministrada,
                   escola.educacao_indigena AS educacao_indigena,
                   juridica.fantasia AS nome_escola
              FROM pmieducar.escola
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
             WHERE escola.cod_escola = $1';

        $escola = $this->fetchPreparedQuery($sql, [$escola]);

        if (empty($escola)) {
            $this->messenger->append('Ocorreu algum problema ao decorrer da análise.');

            return [
                'title' => 'Análise exportação - Registro 10'
            ];
        }

        $escola = $escola[0];
        $nomeEscola = strtoupper($escola['nome_escola']);
        $codEscola = $escola['cod_escola'];
        $predioEscolar = 3; //Valor fixo definido no cadastro de escola

        $existeAbastecimentoAgua = (
            $escola['agua_rede_publica'] ||
            $escola['agua_poco_artesiano'] ||
            $escola['agua_cacimba_cisterna_poco'] ||
            $escola['agua_fonte_rio'] ||
            $escola['agua_inexistente']
        );

        $existeAbastecimentoEnergia = (
            $escola['energia_rede_publica'] ||
            $escola['energia_gerador'] ||
            $escola['energia_outros'] ||
            $escola['energia_inexistente']
        );

        $existeEsgotoSanitario = (
            $escola['esgoto_rede_publica'] ||
            $escola['esgoto_fossa'] ||
            $escola['esgoto_inexistente']
        );

        $existeDestinacaoLixo = (
            $escola['lixo_coleta_periodica'] ||
            $escola['lixo_queima'] ||
            $escola['lixo_joga_outra_area'] ||
            $escola['lixo_recicla'] ||
            $escola['lixo_enterra'] ||
            $escola['lixo_outros']
        );

        $existeDependencia = (
            $escola['dependencia_sala_diretoria'] ||
            $escola['dependencia_sala_professores'] ||
            $escola['dependncia_sala_secretaria'] ||
            $escola['dependencia_laboratorio_informatica'] ||
            $escola['dependencia_laboratorio_ciencias'] ||
            $escola['dependencia_sala_aee'] ||
            $escola['dependencia_quadra_coberta'] ||
            $escola['dependencia_quadra_descoberta'] ||
            $escola['dependencia_cozinha'] ||
            $escola['dependencia_biblioteca'] ||
            $escola['dependencia_sala_leitura'] ||
            $escola['dependencia_parque_infantil'] ||
            $escola['dependencia_bercario'] ||
            $escola['dependencia_banheiro_fora'] ||
            $escola['dependencia_banheiro_dentro'] ||
            $escola['dependencia_banheiro_infantil'] ||
            $escola['dependencia_banheiro_deficiente'] ||
            $escola['dependencia_banheiro_chuveiro'] ||
            $escola['dependencia_refeitorio'] ||
            $escola['dependencia_dispensa'] ||
            $escola['dependencia_aumoxarifado'] ||
            $escola['dependencia_auditorio'] ||
            $escola['dependencia_patio_coberto'] ||
            $escola['dependencia_patio_descoberto'] ||
            $escola['dependencia_alojamento_aluno'] ||
            $escola['dependencia_alojamento_professor'] ||
            $escola['dependencia_area_verde'] ||
            $escola['dependencia_lavanderia'] ||
            $escola['dependencia_nenhuma_relacionada']
        );

        $existeEquipamentos = (
            $escola['televisoes'] ||
            $escola['videocassetes'] ||
            $escola['dvds'] ||
            $escola['antenas_parabolicas'] ||
            $escola['copiadoras'] ||
            $escola['retroprojetores'] ||
            $escola['impressoras'] ||
            $escola['aparelhos_de_som'] ||
            $escola['projetores_digitais'] ||
            $escola['faxs'] ||
            $escola['maquinas_fotograficas'] ||
            $escola['computadores'] ||
            $escola['computadores_administrativo'] ||
            $escola['computadores_alunos'] ||
            $escola['impressoras_multifuncionais']
        );

        $existeMaterialDidatico = $escola['materiais_didaticos_especificos'];

        $mensagem = [];

        if (!$escola['local_funcionamento']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o local de funcionamento da escola foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campo: Local de funcionamento)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola['local_funcionamento'] == $predioEscolar && !$escola['condicao']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto obrigatoriamente é necessário informar qual a forma de ocupação do prédio.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campo: Condição)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola['agua_consumida']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a água consumida pelos alunos foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campo: Água consumida pelos alunos)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeAbastecimentoAgua) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas do abastecimento de água foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campos: Abastecimento de água)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeAbastecimentoEnergia) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas do abastecimento de energia elétrica foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campos: Abastecimento de energia elétrica)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeEsgotoSanitario) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se alguma opção de esgoto sanitário foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campos: Esgoto sanitário)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeDestinacaoLixo) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas da destinação do lixo foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Infraestrutura > Campos: Destinação do lixo)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }
        if (!$existeDependencia) {
            $mensagem[] = [
                'text' => "<span class='avisos-educacenso'><b>Aviso!</b> Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Nenhum campo foi preenchido referente as dependências existentes na escola, portanto todos serão registrados como NÃO.</span>",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dependências > Campos: Dependências existentes na escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => false
            ];
        }

        if ($escola['local_funcionamento'] == $predioEscolar && !$escola['dependencia_numero_salas_existente']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto obrigatoriamente é necessário informar o número de salas de aula existentes na escola.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dependências > Campo: Número de salas de aula existentes na escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola['dependencia_numero_salas_utilizadas']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o número de salas utilizadas como sala de aula foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dependências > Campo: Número de salas utilizadas como sala de aula)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeEquipamentos) {
            $mensagem[] = [
                'text' => "<span class='avisos-educacenso'><b>Aviso!</b> Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Nenhum campo foi preenchido referente a quantidade de equipamentos existentes na escola, portanto todos serão registrados como NÃO.</span>",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Equipamentos > Campos: Quantidade de equipamentos)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => false
            ];
        }

        if (!$escola['total_funcionario']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o total de funcionários da escola foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dependências > Campo: Total de funcionários da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola['atendimento_aee'] < 0) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o atendimento educacional especializado - AEE foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados do ensino > Campo: Atendimento educacional especializado - AEE)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola['atividade_complementar'] < 0) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a atividade complementar foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados do ensino > Campo: Atividade complementar)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$escola['localizacao_diferenciada']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a localização diferenciada da escola foi informada.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados do ensino > Campo: Localização diferenciada da escola)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if (!$existeMaterialDidatico) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se algum material didático específico para atendimento à diversidade sócio-cultural foi informado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados do ensino > Campo: Materiais didáticos específicos para atendimento à diversidade sócio-cultural)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
                'fail' => true
            ];
        }

        if ($escola['educacao_indigena'] && !$escola['lingua_ministrada']) {
            $mensagem[] = [
                'text' => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que a escola trabalha com educação indígena, portanto obrigatoriamente é necessário informar a língua em que o ensino é ministrado.",
                'path' => '(Escola > Cadastros > Escolas > Cadastrar > Editar > Aba: Dados do ensino > Campo: Língua em que o ensino é ministrado)',
                'linkPath' => "/intranet/educar_escola_cad.php?cod_escola={$codEscola}",
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

        $sql = 'SELECT turma.cod_turma AS cod_turma,
                   turma.nm_turma AS nome_turma,
                   turma.hora_inicial AS hora_inicial,
                   turma.hora_final AS hora_final,
                   turma.dias_semana[1] AS dias_semana,
                   turma.tipo_atendimento AS tipo_atendimento,
                   turma.atividades_complementares[1] AS atividades_complementares,
                   turma.atividades_aee[1] AS atividades_aee,
                   turma.etapa_educacenso AS etapa_educacenso,
                   juridica.fantasia AS nome_escola
              FROM pmieducar.escola
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
             INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola)
             WHERE escola.cod_escola = $1
               AND COALESCE(turma.nao_informar_educacenso, 0) = 0
               AND turma.ano = $2
               AND turma.ativo = 1
               AND turma.visivel = TRUE
               AND escola.ativo = 1';

        $turmas = $this->fetchPreparedQuery($sql, [$escola, $ano]);

        if (empty($turmas)) {
            $this->messenger->append('Ocorreu algum problema ao decorrer da análise.');

            return ['title' => 'Análise exportação - Registro 20'];
        }

        $mensagem = [];

        foreach ($turmas as $turma) {
            $nomeEscola = strtoupper($turma['nome_escola']);
            $nomeTurma = strtoupper($turma['nome_turma']);
            $codTurma = $turma['cod_turma'];
            $atividadeComplementar = ($turma['tipo_atendimento'] == 4); //Código 4 fixo no cadastro de turma
            $existeAtividadeComplementar = ($turma['atividades_complementares']);
            $atendimentoAee = ($turma['tipo_atendimento'] == 5); //Código 5 fixo no cadastro de turma
            $existeAee = ($turma['atividades_aee']);

            switch ($turma['tipo_atendimento']) {
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

            if (!$turma['hora_inicial']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o horário inicial da turma {$nomeTurma} foi cadastrado.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados gerais > Campo: Hora inicial)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if (!$turma['hora_final']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o horário final da turma {$nomeTurma} foi cadastrado.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados gerais > Campo: Hora final)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if (!$turma['dias_semana']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. É necessário informar ao menos um dia da semana para a turma presencial {$nomeTurma}.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados gerais > Campo: Dias da semana)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if (is_null($turma['tipo_atendimento']) || $turma['tipo_atendimento'] < 0) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o tipo de atendimento da turma {$nomeTurma} foi cadastrado.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados adicionais > Campo: Tipo de atendimento)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if (!$atendimentoAee && !$atividadeComplementar && !$turma['etapa_educacenso']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de atendimento da turma {$nomeTurma} é '{$nomeAtendimento}', portanto é necessário informar qual a etapa de ensino.",
                    'path' => '(Escola > Cadastros > Turmas > Aba: Dados adicionais > Campo: Etapa de ensino)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if ($atividadeComplementar && !$existeAtividadeComplementar) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de atendimento da turma {$nomeTurma} é de atividade complementar, portanto obrigatoriamente é necessário informar o código de ao menos uma atividade.",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados adicionais > Campo: Código do tipo de atividade complementar)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }

            if ($atendimentoAee && !$existeAee) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de atendimento da turma {$nomeTurma} é de educação especializada - AEE, portanto obrigatoriamente é necessário informar ao menos uma atividade realizada. ",
                    'path' => '(Escola > Cadastros > Turmas > Cadastrar > Editar > Aba: Dados adicionais > Campo: Atividades do Atendimento Educacional Especializado - AEE)',
                    'linkPath' => "/intranet/educar_turma_cad.php?cod_turma={$codTurma}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 20'
        ];
    }

    protected function analisaEducacensoRegistro30()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT pessoa.idpes AS idpes,
                   juridica.fantasia AS nome_escola,
                   raca.raca_educacenso AS cor_raca,
                   fisica.nacionalidade AS nacionalidade,
                   uf.cod_ibge AS uf_inep,
                   uf.sigla_uf AS sigla_uf,
                   municipio.cod_ibge AS municipio_inep,
                   municipio.idmun AS idmun,
                   pessoa.nome AS nome_servidor
              FROM modules.professor_turma
             INNER JOIN pmieducar.turma ON (turma.cod_turma = professor_turma.turma_id)
             INNER JOIN pmieducar.escola ON (escola.cod_escola = turma.ref_ref_cod_escola)
             INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
              LEFT JOIN cadastro.fisica_raca ON (fisica_raca.ref_idpes = professor_turma.servidor_id)
              LEFT JOIN cadastro.raca ON (raca.cod_raca = fisica_raca.ref_cod_raca)
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = professor_turma.servidor_id)
             INNER JOIN cadastro.fisica ON (fisica.idpes = professor_turma.servidor_id)
              LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = professor_turma.servidor_id)
              LEFT JOIN public.municipio ON (municipio.idmun = fisica.idmun_nascimento)
              LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
             WHERE professor_turma.ano = $1
                AND NOT EXISTS (SELECT 1 FROM
                pmieducar.servidor_alocacao
                WHERE servidor.cod_servidor = servidor_alocacao.ref_cod_servidor
                AND escola.cod_escola = servidor_alocacao.ref_cod_escola
                AND turma.ano = servidor_alocacao.ano
                AND servidor_alocacao.data_admissao > DATE($3)
                )
               AND turma.ativo = 1
               AND turma.visivel = TRUE
               AND COALESCE(turma.nao_informar_educacenso, 0) = 0
               AND turma.ano = professor_turma.ano
               AND escola.cod_escola = $2
               AND servidor.ativo = 1
             GROUP BY professor_turma.servidor_id,
                      juridica.fantasia,
                      raca.raca_educacenso,
                      fisica.nacionalidade,
                      uf.cod_ibge,
                      uf.sigla_uf,
                      municipio.cod_ibge,
                      municipio.idmun,
                      pessoa.nome,
                      pessoa.idpes
              ORDER BY nome_servidor';

        $servidores = $this->fetchPreparedQuery($sql, [$ano, $escola, Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        if (empty($servidores)) {
            $this->messenger->append('Nenhum servidor encontrado.');

            return ['title' => 'Análise exportação - Registro 30'];
        }

        $mensagem = [];
        $brasileiro = 1;

        foreach ($servidores as $servidor) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_escola']));
            $nomeServidor = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_servidor']));
            $idpesServidor = $servidor['idpes'];
            $siglaUF = $servidor['sigla_uf'];
            $codMunicipio = $servidor['idmun'];

            if (is_null($servidor['cor_raca'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verifique se a raça do(a) servidor(a) {$nomeServidor} foi informada.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Raça)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesServidor}",
                    'fail' => true
                ];
            }

            if (!$servidor['nacionalidade']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verifique se a nacionalidade do(a) servidor(a) {$nomeServidor} foi informada.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Nacionalidade)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesServidor}",
                    'fail' => true
                ];
            } else {

                if ($servidor['nacionalidade'] == $brasileiro && !$servidor['uf_inep']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verificamos que a nacionalidade do(a) servidor(a) {$nomeServidor} é brasileiro(a), portanto é necessário preencher o código da UF de nascimento conforme a 'Tabela de UF'.",
                        'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                        'linkPath' => "/intranet/public_uf_cad.php?sigla_uf={$siglaUF}",
                        'fail' => true
                    ];
                }

                if ($servidor['nacionalidade'] == $brasileiro && !$servidor['municipio_inep']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verificamos que a nacionalidade do(a) servidor(a) {$nomeServidor} é brasileiro(a), portanto é necessário preencher o código do município de nascimento conforme a 'Tabela de Municípios'.",
                        'path' => '(Endereçamento > Cadastros > Municípios > Editar > Campo: Código INEP)',
                        'linkPath' => "/intranet/public_municipio_cad.php?idmun={$codMunicipio}",
                        'fail' => true
                    ];
                }
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 30'
        ];
    }

    protected function analisaEducacensoRegistro40()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT pessoa.idpes AS idpes,
                   juridica.fantasia AS nome_escola,
                   fisica.nacionalidade AS nacionalidade,
                   uf.cod_ibge AS uf_inep,
                   uf.sigla_uf AS sigla_uf,
                   municipio.cod_ibge AS municipio_inep,
                   municipio.idmun AS idmun,
                   pessoa.nome AS nome_servidor,
                   fisica.cpf AS cpf,
                   endereco_pessoa.cep AS cep
             FROM modules.professor_turma
            INNER JOIN pmieducar.turma ON (turma.cod_turma = professor_turma.turma_id)
            INNER JOIN pmieducar.escola ON (escola.cod_escola = turma.ref_ref_cod_escola)
            INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
            INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
            INNER JOIN cadastro.pessoa ON (pessoa.idpes = professor_turma.servidor_id)
            INNER JOIN cadastro.fisica ON (fisica.idpes = professor_turma.servidor_id)
             LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = professor_turma.servidor_id)
             LEFT JOIN public.logradouro ON (logradouro.idlog = endereco_pessoa.idlog)
             LEFT JOIN public.municipio ON (municipio.idmun = logradouro.idmun)
             LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
            WHERE professor_turma.ano = $1
            AND NOT EXISTS (SELECT 1 FROM
                pmieducar.servidor_alocacao
                WHERE servidor.cod_servidor = servidor_alocacao.ref_cod_servidor
                AND escola.cod_escola = servidor_alocacao.ref_cod_escola
                AND turma.ano = servidor_alocacao.ano
                AND servidor_alocacao.data_admissao > DATE($3)
                )
              AND turma.ativo = 1
              AND turma.visivel = TRUE
              AND COALESCE(turma.nao_informar_educacenso, 0) = 0
              AND turma.ano = professor_turma.ano
              AND escola.cod_escola = $2
              AND servidor.ativo = 1
            GROUP BY pessoa.idpes,
                     professor_turma.servidor_id,
                     juridica.fantasia,
                     fisica.nacionalidade,
                     uf.cod_ibge,
                     uf.sigla_uf,
                     municipio.cod_ibge,
                     municipio.idmun,
                     pessoa.nome,
                     fisica.cpf,
                     endereco_pessoa.cep
            ORDER BY pessoa.nome';

        $servidores = $this->fetchPreparedQuery($sql, [$ano, $escola, Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        if (empty($servidores)) {
            $this->messenger->append('Nenhum servidor encontrado.');

            return ['title' => 'Análise exportação - Registro 40'];
        }

        $mensagem = [];

        foreach ($servidores as $servidor) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_escola']));
            $nomeServidor = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_servidor']));
            $siglaUF = $servidor['sigla_uf'];
            $idpesServidor = $servidor['idpes'];
            $codMunicipio = $servidor['idmun'];
            $naturalidadeBrasileiro = ($servidor['nacionalidade'] == 1 || $servidor['nacionalidade'] == 2);

            if ($naturalidadeBrasileiro && !$servidor['cpf']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que a nacionalidade do(a) servidor(a) {$nomeServidor} é brasileiro(a)/naturalizado brasileiro(a), portanto é necessário informar seu CPF.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: CPF)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesServidor}",
                    'fail' => true
                ];
            }

            if ($servidor['cep'] && !$servidor['uf_inep']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que no cadastro do(a) servidor(a) {$nomeServidor} o endereçamento foi informado, portanto é necessário cadastrar código da UF informada conforme a 'Tabela de UF'.",
                    'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                    'linkPath' => "/intranet/public_uf_cad.php?sigla_uf={$siglaUF}",
                    'fail' => true
                ];
            }

            if ($servidor['cep'] && !$servidor['municipio_inep']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 40 da escola {$nomeEscola} não encontrados. Verificamos que no cadastro do(a) servidor(a) {$nomeServidor} o endereçamento foi informado, portanto é necessário cadastrar código do município informado conforme a 'Tabela de Municípios'.",
                    'path' => '(Endereçamento > Cadastros > Municípios > Editar > Campo: Código INEP)',
                    'linkPath' => "/intranet/public_municipio_cad.php?idmun={$codMunicipio}",
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
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT juridica.fantasia AS nome_escola,
                   pessoa.nome AS nome_servidor,
                   pessoa.idpes AS idpes_servidor,
                   escolaridade.escolaridade AS escolaridade,
                   escolaridade.descricao AS descricao_escolaridade,
                   servidor.ref_cod_instituicao AS instituicao_id,
                   servidor.ref_idesco AS codigo_escolaridade,
                   servidor.situacao_curso_superior_1 AS situacao_curso_superior_1,
                   servidor.codigo_curso_superior_1 AS codigo_curso_superior_1,
                   servidor.ano_inicio_curso_superior_1 AS ano_inicio_curso_superior_1,
                   servidor.ano_conclusao_curso_superior_1 AS ano_conclusao_curso_superior_1,
                   servidor.instituicao_curso_superior_1 AS instituicao_curso_superior_1,
                   servidor.situacao_curso_superior_2 AS situacao_curso_superior_2,
                   servidor.codigo_curso_superior_2 AS codigo_curso_superior_2,
                   servidor.ano_inicio_curso_superior_2 AS ano_inicio_curso_superior_2,
                   servidor.ano_conclusao_curso_superior_2 AS ano_conclusao_curso_superior_2,
                   servidor.instituicao_curso_superior_2 AS instituicao_curso_superior_2,
                   servidor.situacao_curso_superior_3 AS situacao_curso_superior_3,
                   servidor.codigo_curso_superior_3 AS codigo_curso_superior_3,
                   servidor.ano_inicio_curso_superior_3 AS ano_inicio_curso_superior_3,
                   servidor.ano_conclusao_curso_superior_3 AS ano_conclusao_curso_superior_3,
                   servidor.instituicao_curso_superior_3 AS instituicao_curso_superior_3,
                   (ARRAY[1] <@ servidor.pos_graduacao)::int AS pos_especializacao,
                   (ARRAY[2] <@ servidor.pos_graduacao)::int AS pos_mestrado,
                   (ARRAY[3] <@ servidor.pos_graduacao)::int AS pos_doutorado,
                   (ARRAY[4] <@ servidor.pos_graduacao)::int AS pos_nenhuma,
                   (ARRAY[1] <@ servidor.curso_formacao_continuada)::int AS curso_creche,
                   (ARRAY[2] <@ servidor.curso_formacao_continuada)::int AS curso_pre_escola,
                   (ARRAY[3] <@ servidor.curso_formacao_continuada)::int AS curso_anos_iniciais,
                   (ARRAY[4] <@ servidor.curso_formacao_continuada)::int AS curso_anos_finais,
                   (ARRAY[5] <@ servidor.curso_formacao_continuada)::int AS curso_ensino_medio,
                   (ARRAY[6] <@ servidor.curso_formacao_continuada)::int AS curso_eja,
                   (ARRAY[7] <@ servidor.curso_formacao_continuada)::int AS curso_educacao_especial,
                   (ARRAY[8] <@ servidor.curso_formacao_continuada)::int AS curso_educacao_indigena,
                   (ARRAY[9] <@ servidor.curso_formacao_continuada)::int AS curso_educacao_campo,
                   (ARRAY[10] <@ servidor.curso_formacao_continuada)::int AS curso_educacao_ambiental,
                   (ARRAY[11] <@ servidor.curso_formacao_continuada)::int AS curso_educacao_direitos_humanos,
                   (ARRAY[12] <@ servidor.curso_formacao_continuada)::int AS curso_genero_diversidade_sexual,
                   (ARRAY[13] <@ servidor.curso_formacao_continuada)::int AS curso_direito_crianca_adolescente,
                   (ARRAY[14] <@ servidor.curso_formacao_continuada)::int AS curso_relacoes_etnicorraciais,
                   (ARRAY[15] <@ servidor.curso_formacao_continuada)::int AS curso_outros,
                   (ARRAY[16] <@ servidor.curso_formacao_continuada)::int AS curso_nenhum
              FROM modules.professor_turma
             INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
             INNER JOIN pmieducar.turma ON (turma.cod_turma = professor_turma.turma_id)
             INNER JOIN pmieducar.escola ON (escola.cod_escola = turma.ref_ref_cod_escola)
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
              LEFT JOIN cadastro.escolaridade ON (escolaridade.idesco = servidor.ref_idesco)
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = professor_turma.servidor_id)
             WHERE professor_turma.ano = $1
               AND turma.ativo = 1
               AND NOT EXISTS (SELECT 1 FROM
                pmieducar.servidor_alocacao
                WHERE servidor.cod_servidor = servidor_alocacao.ref_cod_servidor
                AND escola.cod_escola = servidor_alocacao.ref_cod_escola
                AND turma.ano = servidor_alocacao.ano
                AND servidor_alocacao.data_admissao > DATE($3)
                )
               AND turma.visivel = TRUE
               AND COALESCE(turma.nao_informar_educacenso, 0) = 0
               AND turma.ano = professor_turma.ano
               AND escola.cod_escola = $2
               AND servidor.ativo = 1
             GROUP BY professor_turma.servidor_id,
                      juridica.fantasia,
                      pessoa.nome,
                      pessoa.idpes,
                      servidor.ref_cod_instituicao,
                      servidor.ref_idesco,
                      servidor.situacao_curso_superior_1,
                      servidor.codigo_curso_superior_1 ,
                      servidor.ano_inicio_curso_superior_1,
                      servidor.ano_conclusao_curso_superior_1,
                      servidor.instituicao_curso_superior_1,
                      servidor.situacao_curso_superior_2,
                      servidor.codigo_curso_superior_2,
                      servidor.ano_inicio_curso_superior_2,
                      servidor.ano_conclusao_curso_superior_2,
                      servidor.instituicao_curso_superior_2,
                      servidor.situacao_curso_superior_3,
                      servidor.codigo_curso_superior_3,
                      servidor.ano_inicio_curso_superior_3,
                      servidor.ano_conclusao_curso_superior_3,
                      servidor.instituicao_curso_superior_3,
                      servidor.pos_graduacao,
                      servidor.curso_formacao_continuada,
                      escolaridade.escolaridade,
                      escolaridade.descricao
             ORDER BY pessoa.nome';

        $servidores = $this->fetchPreparedQuery($sql, [$ano, $escola, Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        if (empty($servidores)) {
            $this->messenger->append('Nenhum servidor encontrado.');

            return ['title' => 'Análise exportação - Registro 50'];
        }

        $mensagem = [];
        $superiorCompleto = 6;
        $situacaoConcluido = 1;
        $situacaoCursando = 2;

        foreach ($servidores as $servidor) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_escola']));
            $nomeServidor = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_servidor']));
            $idpesServidor = $servidor['idpes_servidor'];
            $instituicaoId = $servidor['instituicao_id'];

            $existeCursoConcluido = (
                $servidor['situacao_curso_superior_1'] == $situacaoConcluido ||
                $servidor['situacao_curso_superior_2'] == $situacaoConcluido ||
                $servidor['situacao_curso_superior_3'] == $situacaoConcluido
            );

            $existePosGraduacao = (
                $servidor['pos_especializacao'] ||
                $servidor['pos_mestrado'] ||
                $servidor['pos_doutorado'] ||
                $servidor['pos_nenhuma']
            );

            $existeCursoFormacaoContinuada = (
                $servidor['curso_creche'] ||
                $servidor['curso_pre_escola'] ||
                $servidor['curso_anos_iniciais'] ||
                $servidor['curso_anos_finais'] ||
                $servidor['curso_ensino_medio'] ||
                $servidor['curso_eja'] ||
                $servidor['curso_educacao_especial'] ||
                $servidor['curso_educacao_indigena'] ||
                $servidor['curso_educacao_campo'] ||
                $servidor['curso_educacao_ambiental'] ||
                $servidor['curso_educacao_direitos_humanos'] ||
                $servidor['curso_genero_diversidade_sexual'] ||
                $servidor['curso_direito_crianca_adolescente'] ||
                $servidor['curso_relacoes_etnicorraciais'] ||
                $servidor['curso_outros'] ||
                $servidor['curso_nenhum']
            );

            if (!$servidor['codigo_escolaridade']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verifique se a escolaridade do(a) servidor(a) {$nomeServidor} foi informada.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Escolaridade)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['codigo_escolaridade'] && !$servidor['escolaridade']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verifique se o campo escolaridade educacenso do(a) servidor(a) {$nomeServidor}, foi informado para a escolaridade {$servidor['descricao_escolaridade']}.",
                    'path' => '(Servidores > Escolaridade > Editar > Campo: Escolaridade Educacenso)',
                    'linkPath' => "/intranet/educar_escolaridade_cad.php?idesco={$servidor['codigo_escolaridade']}",
                    'fail' => true
                ];
            }

            if ($servidor['escolaridade'] == $superiorCompleto && !$servidor['situacao_curso_superior_1']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a escolaridade do(a) servidor(a) {$nomeServidor} é superior, portanto é necessário informar a situação do curso superior 1.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Situação do curso superior 1)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_1'] && !$servidor['codigo_curso_superior_1']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a escolaridade do(a) servidor(a) {$nomeServidor} é superior, portanto é necessário informar o nome do curso superior 1.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Curso superior 1)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_1'] == $situacaoCursando && !$servidor['ano_inicio_curso_superior_1']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} está cursando um curso superior, portanto é necessário informar o ano de início deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de início do curso superior 1)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_1'] == $situacaoConcluido && !$servidor['ano_conclusao_curso_superior_1']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} concluiu um curso superior, portanto é necessário informar o ano de conclusão deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de conclusão do curso superior 1)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_1'] && !$servidor['instituicao_curso_superior_1']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a escolaridade do(a) servidor(a) {$nomeServidor} é superior, portanto é necessário informar o nome da instituição do curso superior 1.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Instituição do curso superior 1)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_2'] && !$servidor['codigo_curso_superior_2']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a situação do curso superior 2 do(a) servidor(a) {$nomeServidor} foi informada, portanto é necessário informar o nome do curso superior 2.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Curso superior 2)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_2'] == $situacaoCursando && !$servidor['ano_inicio_curso_superior_2']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} está cursando um curso superior 2, portanto é necessário informar o ano de início deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de início do curso superior 2)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_2'] == $situacaoConcluido && !$servidor['ano_conclusao_curso_superior_2']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} concluiu um curso superior 2, portanto é necessário informar o ano de conclusão deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de conclusão do curso superior 2)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_2'] && !$servidor['instituicao_curso_superior_2']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a situação do curso superior 2 do(a) servidor(a) {$nomeServidor} foi informada, portanto é necessário informar também o nome da instituição deste respectivo curso. ",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Instituição do curso superior 2)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_3'] && !$servidor['codigo_curso_superior_3']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a situação do curso superior 3 do(a) servidor(a) {$nomeServidor} foi informada, portanto é necessário informar o nome do curso superior 3.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Curso superior 3)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_3'] == $situacaoCursando && !$servidor['ano_inicio_curso_superior_3']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} está cursando um curso superior 3, portanto é necessário informar o ano de início deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de início do curso superior 3)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_3'] == $situacaoConcluido && !$servidor['ano_conclusao_curso_superior_3']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} concluiu um curso superior 3, portanto é necessário informar o ano de conclusão deste respectivo curso.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Ano de conclusão do curso superior 3)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($servidor['situacao_curso_superior_3'] && !$servidor['instituicao_curso_superior_3']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verificamos que a situação do curso superior 3 do(a) servidor(a) {$nomeServidor} foi informada, portanto é necessário informar também o nome da instituição deste respectivo curso. ",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Instituição do curso superior 3)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if ($existeCursoConcluido && !$existePosGraduacao) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verifique se alguma das opções de Pós-Graduação foi informada para o(a) servidor(a) {$nomeServidor}.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Campo: Possui pós-Graduação)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }

            if (!$existeCursoFormacaoContinuada) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 50 da escola {$nomeEscola} não encontrados. Verifique se alguma das opções de Curso de Formação Continuada foi informada para o(a) servidor(a) {$nomeServidor}.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Editar > Aba: Dados adicionais > Possui cursos de formação continuada)',
                    'linkPath' => "/intranet/educar_servidor_cad.php?cod_servidor={$idpesServidor}&ref_cod_instituicao={$instituicaoId}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 50'
        ];
    }

    protected function analisaEducacensoRegistro51()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT professor_turma.id AS vinculo_id,
                   professor_turma.instituicao_id AS instituicao_id,
                   juridica.fantasia AS nome_escola,
                   pessoa.nome AS nome_servidor,
                   pessoa.idpes AS idpes_servidor,
                   professor_turma.tipo_vinculo AS tipo_vinculo,
                   professor_turma.funcao_exercida AS funcao_exercida,
                   turma.nm_turma AS nm_turma,
                   EXISTS (SELECT 1
                             FROM modules.professor_turma_disciplina ptd
                            WHERE professor_turma_id = professor_turma.id
                              AND NOT EXISTS (SELECT 1
                                                FROM relatorio.view_componente_curricular vcc
                                               WHERE vcc.cod_turma = turma.cod_turma
                                                 AND vcc.id = ptd.componente_curricular_id)) AS nao_existe_disciplina_turma
              FROM modules.professor_turma
             INNER JOIN pmieducar.turma ON (turma.cod_turma = professor_turma.turma_id)
             INNER JOIN pmieducar.escola ON (escola.cod_escola = turma.ref_ref_cod_escola)
             INNER JOIN pmieducar.servidor ON (servidor.cod_servidor = professor_turma.servidor_id)
              LEFT JOIN cadastro.fisica_raca ON (fisica_raca.ref_idpes = professor_turma.servidor_id)
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = professor_turma.servidor_id)
             INNER JOIN cadastro.fisica ON (fisica.idpes = professor_turma.servidor_id)
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
             WHERE professor_turma.ano = $1
               AND NOT EXISTS (SELECT 1 FROM
                pmieducar.servidor_alocacao
                WHERE servidor.cod_servidor = servidor_alocacao.ref_cod_servidor
                AND escola.cod_escola = servidor_alocacao.ref_cod_escola
                AND turma.ano = servidor_alocacao.ano
                AND servidor_alocacao.data_admissao > DATE($3)
                )
                AND turma.ativo = 1
               AND turma.visivel = TRUE
               AND COALESCE(turma.nao_informar_educacenso, 0) = 0
               AND turma.ano = professor_turma.ano
               AND escola.cod_escola = $2
               AND servidor.ativo = 1
             GROUP BY professor_turma.id,
                      professor_turma.servidor_id,
                      juridica.fantasia,
                      pessoa.nome,
                      pessoa.idpes,
                      professor_turma.tipo_vinculo,
                      professor_turma.funcao_exercida,
                      turma.cod_turma,
                      turma.nm_turma
             ORDER BY pessoa.nome';

        $servidores = $this->fetchPreparedQuery($sql, [$ano, $escola, Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        if (empty($servidores)) {
            $this->messenger->append('Nenhum servidor encontrado.');

            return ['title' => 'Análise exportação - Registro 51'];
        }

        $mensagem = [];
        $docente = [1, 5, 6];

        foreach ($servidores as $servidor) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_escola']));
            $nomeServidor = Portabilis_String_Utils::toUtf8(strtoupper($servidor['nome_servidor']));
            $nomeTurma = $servidor['nm_turma'];
            $idpesServidor = $servidor['idpes_servidor'];
            $instituicaoId = $servidor['instituicao_id'];
            $vinculoId = $servidor['vinculo_id'];
            $componenteNaoExisteNaTurma = $servidor['nao_existe_disciplina_turma'] == 't';

            $funcaoDocente = in_array($servidor['funcao_exercida'], $docente);

            if ($funcaoDocente && !$servidor['tipo_vinculo']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 51 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} é docente e possui vínculo com turmas, portanto é necessário informar qual o seu tipo de vínculo.",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Vincular professor a turmas > Campo: Tipo do vínculo)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$vinculoId}&ref_cod_instituicao={$instituicaoId}&ref_cod_servidor={$idpesServidor}",
                    'fail' => true
                ];
            }
            if ($componenteNaoExisteNaTurma) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 51 da escola {$nomeEscola} não encontrados. Verificamos que o(a) servidor(a) {$nomeServidor} possui vínculo com disciplina que não existe para a turma {$nomeTurma}",
                    'path' => '(Servidores > Cadastros > Servidores > Cadastrar > Vincular professor a turmas > Campo: Tipo do vínculo)',
                    'linkPath' => "/intranet/educar_servidor_vinculo_turma_cad.php?id={$vinculoId}&ref_cod_instituicao={$instituicaoId}&ref_cod_servidor={$idpesServidor}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 51'
        ];
    }

    protected function analisaEducacensoRegistro60()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT pessoa.idpes AS idpes,
                   juridica.fantasia AS nome_escola,
                   pessoa.nome AS nome_aluno,
                   raca.raca_educacenso AS cor_raca,
                   fisica.nacionalidade AS nacionalidade,
                   uf.cod_ibge AS uf_inep,
                   uf.sigla_uf AS sigla_uf,
                   municipio.cod_ibge AS municipio_inep,
                   municipio.idmun AS idmun,
                   aluno.cod_aluno AS cod_aluno,
                   aluno.recursos_prova_inep[1] AS recursos_prova_inep,
                   EXISTS (SELECT 1
                             FROM cadastro.fisica_deficiencia fd,
                                  cadastro.deficiencia d
                            WHERE d.cod_deficiencia = fd.ref_cod_deficiencia
                              AND fd.ref_idpes = aluno.ref_idpes
                              AND d.deficiencia_educacenso IS NOT NULL) AS possui_deficiencia
              FROM pmieducar.aluno
             INNER JOIN pmieducar.matricula ON (matricula.ref_cod_aluno = aluno.cod_aluno)
             INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
             INNER JOIN pmieducar.turma ON (turma.cod_turma = matricula_turma.ref_cod_turma)
             INNER JOIN pmieducar.escola ON (escola.cod_escola = matricula.ref_ref_cod_escola)
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
             INNER JOIN cadastro.fisica ON (fisica.idpes = pessoa.idpes)
              LEFT JOIN cadastro.fisica_raca ON (fisica_raca.ref_idpes = fisica.idpes)
              LEFT JOIN cadastro.raca ON (raca.cod_raca = fisica_raca.ref_cod_raca)
              LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = fisica.idpes)
              LEFT JOIN public.municipio ON (municipio.idmun = fisica.idmun_nascimento)
              LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
             WHERE aluno.ativo = 1
               AND turma.ativo = 1
               AND turma.visivel = TRUE
               AND COALESCE(turma.nao_informar_educacenso, 0) = 0
               AND matricula.ativo = 1
               AND matricula_turma.ativo = 1
               AND matricula.ano = $1
               AND escola.cod_escola = $2
               AND COALESCE(matricula.data_matricula,matricula.data_cadastro) BETWEEN DATE($3) AND DATE($4)
               AND (matricula.aprovado = 3 OR DATE(COALESCE(matricula.data_cancel,matricula.data_exclusao)) > DATE($4))
             ORDER BY nome_aluno';

        $alunos = $this->fetchPreparedQuery($sql, [$ano,
            $escola,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim)]);

        if (empty($alunos)) {
            $this->messenger->append('Nenhum aluno encontrado.');

            return ['title' => 'Análise exportação - Registro 60'];
        }

        $mensagem = [];
        $brasileiro = 1;

        foreach ($alunos as $aluno) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_escola']));
            $nomeAluno = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_aluno']));
            $codPessoa = $aluno['idpes'];
            $codAluno = $aluno['cod_aluno'];
            $siglaUF = $aluno['sigla_uf'];
            $codMunicipio = $aluno['idmun'];

            if (is_null($aluno['cor_raca'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verifique se a raça do(a) aluno(a) {$nomeAluno} foi informada.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Raça)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$codPessoa}",
                    'fail' => true
                ];
            }

            if (!$aluno['nacionalidade']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verifique se a nacionalidade do(a) aluno(a) {$nomeAluno} foi informada.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Nacionalidade)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$codPessoa}",
                    'fail' => true
                ];
            } else {
                if ($aluno['nacionalidade'] == $brasileiro && !$aluno['uf_inep']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verificamos que a nacionalidade do(a) aluno(a) {$nomeAluno} é brasileiro(a), portanto é necessário preencher o código da UF de nascimento conforme a 'Tabela de UF'.",
                        'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                        'linkPath' => "/intranet/public_uf_cad.php?sigla_uf={$siglaUF}",
                        'fail' => true
                    ];
                }

                if ($aluno['nacionalidade'] == $brasileiro && !$aluno['municipio_inep']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 30 da escola {$nomeEscola} não encontrados. Verificamos que a nacionalidade do(a) aluno(a) {$nomeAluno} é brasileiro(a), portanto é necessário preencher o código do município de nascimento conforme a 'Tabela de Municípios'.",
                        'path' => '(Endereçamento > Cadastros > Municípios > Editar > Campo: Código INEP)',
                        'linkPath' => "/intranet/public_municipio_cad.php?idmun={$codMunicipio}",
                        'fail' => true
                    ];
                }

                if (dbBool($aluno['possui_deficiencia']) && empty($aluno['recursos_prova_inep'])) {
                    $mensagem[] = [
                        'text' => "<span class='avisos-educacenso'><b>Aviso!</b> Dados para formular o registro 60 da escola {$nomeEscola} não encontrados. Verificamos que o(a) aluno(a) {$nomeAluno} possui uma(s) deficiência(s), porém nenhum recurso para a prova INEP foi selecionado.</span>",
                        'path' => '(Escola > Cadastros > Alunos > Cadastrar > Editar > Aba: Dados educacenso > Campo: Recursos prova INEP)',
                        'linkPath' => "/module/Cadastro/aluno?id={$codAluno}",
                        'fail' => false
                    ];
                }
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 60'
        ];
    }

    protected function analisaEducacensoRegistro70()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT juridica.fantasia AS nome_escola,
                   pessoa.idpes AS idpes_aluno,
                   pessoa.nome AS nome_aluno,
                   documento.rg AS rg,
                   documento.sigla_uf_exp_rg AS sigla_uf_rg,
                   documento.idorg_exp_rg AS orgao_emissor_rg,
                   documento.tipo_cert_civil AS tipo_cert_civil,
                   documento.num_termo AS num_termo,
                   documento.sigla_uf_cert_civil AS uf_cartorio,
                   codigo_cartorio_inep.id_cartorio AS cod_cartorio,
                   uf.cod_ibge AS uf_inep,
                   uf.sigla_uf AS sigla_uf,
                   municipio.cod_ibge AS municipio_inep,
                   municipio.idmun AS idmun,
                   uf_cartorio.cod_ibge AS uf_inep_cartorio,
                   uf_rg.cod_ibge AS uf_inep_rg,
                   fisica.nacionalidade AS nacionalidade,
                   endereco_pessoa.cep AS cep,
                   fisica.zona_localizacao_censo AS zona_localizacao
              FROM pmieducar.aluno
              JOIN pmieducar.escola ON escola.cod_escola = $2
             INNER JOIN cadastro.juridica ON (juridica.idpes = escola.ref_idpes)
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = aluno.ref_idpes)
             INNER JOIN cadastro.fisica ON (fisica.idpes = pessoa.idpes)
              LEFT JOIN cadastro.documento ON (documento.idpes = pessoa.idpes)
              LEFT JOIN cadastro.codigo_cartorio_inep ON (codigo_cartorio_inep.id = documento.cartorio_cert_civil_inep)
              LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = pessoa.idpes)
              LEFT JOIN public.logradouro ON (logradouro.idlog = endereco_pessoa.idlog)
              LEFT JOIN public.municipio ON (municipio.idmun = logradouro.idmun)
              LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
              LEFT JOIN public.uf uf_cartorio ON (uf_cartorio.sigla_uf = documento.sigla_uf_cert_civil)
              LEFT JOIN public.uf uf_rg ON (uf_rg.sigla_uf = documento.sigla_uf_exp_rg)
              LEFT JOIN public.bairro ON (bairro.idbai = endereco_pessoa.idbai)
             WHERE aluno.ativo = 1
                AND EXISTS(
                    SELECT 1
                    FROM pmieducar.matricula
                    INNER JOIN pmieducar.matricula_turma ON (matricula_turma.ref_cod_matricula = matricula.cod_matricula)
                    INNER JOIN pmieducar.turma ON (turma.cod_turma = matricula_turma.ref_cod_turma)
                    WHERE matricula.ref_cod_aluno = aluno.cod_aluno
                    AND turma.ativo = 1
                    AND turma.visivel = TRUE
                    AND COALESCE(turma.nao_informar_educacenso, 0) = 0
                   AND matricula.ativo = 1
                   AND matricula_turma.ativo = 1
                   AND matricula.ano = $1
                   AND escola.cod_escola = matricula.ref_ref_cod_escola
                   AND COALESCE(matricula.data_matricula,matricula.data_cadastro) BETWEEN DATE($3) AND DATE($4)
                    AND (matricula.aprovado = 3 OR DATE(COALESCE(matricula.data_cancel,matricula.data_exclusao)) > DATE($4))
                )
             ORDER BY nome_aluno';

        $alunos = $this->fetchPreparedQuery($sql, [
            $ano,
            $escola,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim)
        ]);

        if (empty($alunos)) {
            $this->messenger->append('Nenhum aluno encontrado.');

            return ['title' => 'Análise exportação - Registro 70'];
        }

        $mensagem = [];
        $nascimentoAntigoFormato = 91;
        $casamentoAntigoFormato = 92;
        $estrangeiro = 3;

        foreach ($alunos as $aluno) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_escola']));
            $nomeAluno = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_aluno']));
            $idpesAluno = $aluno['idpes_aluno'];
            $siglaUF = $aluno['sigla_uf'];
            $codMunicipio = $aluno['idmun'];

            if ($aluno['rg']) {
                if (!$aluno['orgao_emissor_rg']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o número da identidade do(a) aluno(a) {$nomeAluno} foi informada, portanto é necessário informar também o órgão emissor da identidade.",
                        'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: RG / Data emissão)',
                        'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                        'fail' => true
                    ];
                }
                if (!$aluno['sigla_uf_rg']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o número da identidade do(a) aluno(a) {$nomeAluno} foi informada, portanto é necessário informar também o estado da identidade.",
                        'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: RG / Data emissão)',
                        'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                        'fail' => true
                    ];
                }
                if ($aluno['sigla_uf_rg'] && !$aluno['uf_inep_rg']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o estado da identidade do(a) aluno(a) {$nomeAluno} foi informado, portanto é necessário preencher o código deste estado conforme a 'Tabela de UF'.",
                        'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                        'linkPath' => "/intranet/public_uf_cad.php?sigla_uf={$siglaUF}",
                        'fail' => true
                    ];
                }
            }

            $certidaoAntigoFormato = (
                $aluno['tipo_cert_civil'] == $nascimentoAntigoFormato ||
                $aluno['tipo_cert_civil'] == $casamentoAntigoFormato
            );

            if ($certidaoAntigoFormato && $aluno['nacionalidade'] != $estrangeiro) {
                if (!$aluno['num_termo']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o tipo da certidão civil do(a) aluno(a) {$nomeAluno} foi informada, portanto é necessário informar também o número do termo da certidão.",
                        'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Termo)',
                        'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                        'fail' => true
                    ];
                }

                if (!$aluno['uf_cartorio']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o número do termo da certidão civil do(a) aluno(a) {$nomeAluno} foi informado, portanto é necessário informar também o estado de emissão.",
                        'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Estado emissão / Data emissão)',
                        'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                        'fail' => true
                    ];
                }

                if ($aluno['uf_cartorio'] && !$aluno['uf_inep_cartorio']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o estado do cartório do(a) aluno(a) {$nomeAluno} foi informado, portanto é necessário preencher o código deste estado conforme a 'Tabela de UF'.",
                        'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                        'fail' => true
                    ];
                }

                if (!$aluno['cod_cartorio']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que o número da certidão civil do(a) aluno(a) {$nomeAluno} foi informada, portanto é necessário informar também o código do cartório conforme a 'Tabela de Cartórios'.",
                        'path' => '(Pessoas > Cadastros > Pessoas físicas > Cadastrar > Editar > Campo: Estado emissão / Data emissão)',
                        'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                        'fail' => true
                    ];
                }
            }

            if ($aluno['cep'] && !$aluno['uf_inep']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que no cadastro do(a) aluno(a) {$nomeAluno} o endereçamento foi informado, portanto é necessário cadastrar código da UF informada conforme a 'Tabela de UF'.",
                    'path' => '(Endereçamento > Cadastros > Estados > Editar > Campo: Código INEP)',
                    'linkPath' => "/intranet/public_uf_cad.php?sigla_uf={$siglaUF}",
                    'fail' => true
                ];
            }

            if ($aluno['cep'] && !$aluno['municipio_inep']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verificamos que no cadastro do(a) aluno(a) {$nomeAluno} o endereçamento foi informado, portanto é necessário cadastrar código do município informado conforme a 'Tabela de Municípios'.",
                    'path' => '(Endereçamento > Cadastros > Municípios > Editar > Campo: Código INEP)',
                    'linkPath' => "/intranet/public_municipio_cad.php?idmun={$codMunicipio}",
                    'fail' => true
                ];
            }

            if (!$aluno['zona_localizacao']) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 70 da escola {$nomeEscola} não encontrados. Verifique se a zona/localização do (a) aluno(a) $nomeAluno foi informada.",
                    'path' => '(Pessoas > Cadastros > Pessoas físicas > Campo: Zona localização)',
                    'linkPath' => "/intranet/atendidos_cad.php?cod_pessoa_fj={$idpesAluno}",
                    'fail' => true
                ];
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 70'
        ];
    }

    protected function analisaEducacensoRegistro80()
    {
        $escola = $this->getRequest()->escola;
        $ano = $this->getRequest()->ano;
        $data_ini = $this->getRequest()->data_ini;
        $data_fim = $this->getRequest()->data_fim;

        $sql = 'SELECT a.cod_aluno AS cod_aluno,
                   juridica.fantasia AS nome_escola,
                   pe.nome AS nome_aluno,
                   ta.responsavel AS transporte_escolar,
                   a.veiculo_transporte_escolar AS veiculo_transporte_escolar,
                   t.tipo_atendimento AS tipo_atendimento,
                   a.recebe_escolarizacao_em_outro_espaco AS recebe_escolarizacao_em_outro_espaco,
                   t.etapa_educacenso AS etapa_ensino,
                   mt.etapa_educacenso AS etapa_turma,
                   mt.turma_unificada AS turma_unificada,
                   m.cod_matricula AS cod_matricula
              FROM  pmieducar.aluno a
        INNER JOIN cadastro.fisica fis ON (fis.idpes = a.ref_idpes)
        INNER JOIN pmieducar.matricula m ON (m.ref_cod_aluno = a.cod_aluno)
        INNER JOIN pmieducar.matricula_turma mt ON (mt.ref_cod_matricula = m.cod_matricula)
        INNER JOIN pmieducar.turma t ON (t.cod_turma = mt.ref_cod_turma)
        INNER JOIN pmieducar.escola e ON (m.ref_ref_cod_escola = e.cod_escola)
        INNER JOIN cadastro.juridica ON (juridica.idpes = e.ref_idpes)
        INNER JOIN cadastro.pessoa pe ON (pe.idpes = a.ref_idpes)
        INNER JOIN modules.educacenso_cod_escola ece ON (ece.cod_escola = e.cod_escola)
         LEFT JOIN modules.transporte_aluno ta ON (ta.aluno_id = a.cod_aluno)
         LEFT JOIN modules.educacenso_cod_aluno eca ON a.cod_aluno = eca.cod_aluno
             WHERE e.cod_escola = $2
               AND COALESCE(t.nao_informar_educacenso, 0) = 0
               AND t.ativo = 1
               AND t.visivel = TRUE
               AND COALESCE(m.data_matricula,m.data_cadastro) BETWEEN DATE($3) AND DATE($4)
               AND (m.aprovado = 3 OR DATE(COALESCE(m.data_cancel,m.data_exclusao)) > DATE($4))
               AND m.ano = $1
               AND m.ativo = 1
               AND COALESCE(mt.remanejado, FALSE) = FALSE
               AND (CASE WHEN m.aprovado = 3
                    THEN mt.ativo = 1
                    ELSE mt.sequencial = (SELECT MAX(sequencial)
                                          FROM pmieducar.matricula_turma
                                         WHERE matricula_turma.ref_cod_matricula = m.cod_matricula)
                    END)
          ORDER BY pe.nome';

        $alunos = $this->fetchPreparedQuery($sql, [
            $ano,
            $escola,
            Portabilis_Date_Utils::brToPgSQL($data_ini),
            Portabilis_Date_Utils::brToPgSQL($data_fim)
        ]);

        if (empty($alunos)) {
            $this->messenger->append('Nenhum aluno encontrado.');

            return ['title' => 'Análise exportação - Registro 80'];
        }

        $mensagem = [];
        $transporteEstadual = 1;
        $transporteMunicipal = 2;
        $atividadeComplementar = 4;
        $atendimentoEducEspecializado = 5;
        $etapasEnsinoCorrecao = [12, 13, 22, 23, 24, 72, 56, 64];

        foreach ($alunos as $aluno) {
            $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_escola']));
            $nomeAluno = Portabilis_String_Utils::toUtf8(strtoupper($aluno['nome_aluno']));
            $codAluno = $aluno['cod_aluno'];
            $codMatricula = $aluno['cod_matricula'];

            if (is_null($aluno['transporte_escolar'])) {
                $mensagem[] = [
                    'text' => "Dados para formular o registro 80 da escola {$nomeEscola} não encontrados. Verifique se o transporte púlblico foi informado para o(a) aluno(a) {$nomeAluno}.",
                    'path' => '(Escola > Cadastros > Alunos > Cadastrar > Editar > Campo: Transporte escolar público)',
                    'linkPath' => "/module/Cadastro/aluno?id={$codAluno}",
                    'fail' => true
                ];
            }

            if ($aluno['transporte_escolar'] == $transporteMunicipal || $aluno['transporte_escolar'] == $transporteEstadual) {
                if (!$aluno['veiculo_transporte_escolar']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 80 da escola {$nomeEscola} não encontrados. Verificamos que o(a) aluno(a) {$nomeAluno} utiliza o transporte público, portanto é necessário informar qual o tipo de veículo utilizado.",
                        'path' => '(Escola > Cadastros > Alunos > Cadastrar > Editar > Campo: Veículo utilizado)',
                        'linkPath' => "/module/Cadastro/aluno?id={$codAluno}",
                        'fail' => true
                    ];
                }
            }

            if ($aluno['tipo_atendimento'] != $atividadeComplementar &&
                $aluno['tipo_atendimento'] != $atendimentoEducEspecializado) {
                if (!$aluno['recebe_escolarizacao_em_outro_espaco']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 80 da escola {$nomeEscola} não encontrados. Verificamos que a turma vinculada a este aluno(a) {$nomeAluno} não é de Atividade complementar e nem de AEE, portanto é necessário informar se o mesmo recebe escolarização em um espaço diferente da respectiva escola.",
                        'path' => '(Escola > Cadastros > Alunos > Cadastrar > Editar > Aba: Dados educacenso > Campo: Recebe escolarização em outro espaço (diferente da escola))',
                        'linkPath' => "/module/Cadastro/aluno?id={$codAluno}",
                        'fail' => true
                    ];
                }
            }

            if (in_array($aluno['etapa_ensino'], $etapasEnsinoCorrecao)) {
                if (!$aluno['etapa_turma']) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 80 do(a) aluno(a) {$nomeAluno} não encontrados. Verificamos que a etapa do aluno não foi informada.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar (matrícula do ano atual) > Etapa do aluno)',
                        'linkPath' => "/intranet/educar_matricula_etapa_turma_cad.php?ref_cod_matricula={$codMatricula}&ref_cod_aluno={$codAluno}",
                        'fail' => true
                    ];
                }
            }

            if (in_array($aluno['etapa_ensino'], App_Model_Educacenso::etapasEnsinoUnificadas())) {
                if (empty($aluno['turma_unificada'])) {
                    $mensagem[] = [
                        'text' => "Dados para formular o registro 80 do(a) aluno(a) {$nomeAluno} não encontrados. Verificamos que a etapa da turma unificada do aluno não foi informada.",
                        'path' => '(Escola > Cadastros > Alunos > Visualizar (matrícula do ano atual) > Etapa da turma unificada)',
                        'linkPath' => "/intranet/educar_matricula_turma_unificada_cad.php?ref_cod_matricula={$codMatricula}&ref_cod_aluno={$codAluno}",
                        'fail' => true
                    ];
                }
            }
        }

        return [
            'mensagens' => $mensagem,
            'title' => 'Análise exportação - Registro 80'
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

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'registro-00')) {
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
        } elseif ($this->isRequestFor('get', 'registro-51')) {
            $this->appendResponse($this->analisaEducacensoRegistro51());
        } elseif ($this->isRequestFor('get', 'registro-60')) {
            $this->appendResponse($this->analisaEducacensoRegistro60());
        } elseif ($this->isRequestFor('get', 'registro-70')) {
            $this->appendResponse($this->analisaEducacensoRegistro70());
        } elseif ($this->isRequestFor('get', 'registro-80')) {
            $this->appendResponse($this->analisaEducacensoRegistro80());
        } elseif ($this->isRequestFor('get', 'registro-89')) {
            $this->appendResponse($this->analisaEducacensoRegistro89());
        } elseif ($this->isRequestFor('get', 'registro-90')) {
            $this->appendResponse($this->analisaEducacensoRegistro90());
        } elseif ($this->isRequestFor('get', 'registro-91')) {
            $this->appendResponse($this->analisaEducacensoRegistro91());
        }  elseif ($this->isRequestFor('get', 'school-is-active')) {
            $this->appendResponse($this->schoolIsActive());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
