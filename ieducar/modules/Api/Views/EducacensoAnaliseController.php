<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'intranet/include/clsBanco.inc.php';

class EducacensoAnaliseController extends ApiCoreController
{

  protected function analisaEducacensoRegistro00() {

    $escola = $this->getRequest()->escola;
    $ano    = $this->getRequest()->ano;

    $sql = "SELECT educacenso_cod_escola.cod_escola_inep AS inep,
                   fisica_gestor.cpf AS cpf_gestor_escolar,
                   pessoa_gestor.nome AS nome_gestor_escolar,
                   escola.cargo_gestor AS cargo_gestor_escolar,
                   EXTRACT(YEAR FROM modulo1.data_inicio) AS data_inicio,
                   EXTRACT(YEAR FROM modulo2.data_fim) AS data_fim,
                   escola.latitude AS latitude,
                   escola.longitude AS longitude,
                   municipio.cod_ibge AS inep_municipio,
                   uf.cod_ibge AS inep_uf,
                   distrito.cod_ibge AS inep_distrito,
                   pessoa.nome AS nome_escola
              FROM pmieducar.escola
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = escola.ref_idpes)
             INNER JOIN pmieducar.escola_ano_letivo ON (escola_ano_letivo.ref_cod_escola = escola.cod_escola)
             INNER JOIN pmieducar.ano_letivo_modulo modulo1 ON (modulo1.ref_ref_cod_escola = escola.cod_escola
                          AND modulo1.ref_ano = escola_ano_letivo.ano
                          AND modulo1.sequencial = 1)
             INNER JOIN pmieducar.ano_letivo_modulo modulo2 ON (modulo2.ref_ref_cod_escola = escola.cod_escola
                          AND modulo2.ref_ano = escola_ano_letivo.ano
                          AND modulo2.sequencial = (SELECT MAX(sequencial)
                                                      FROM pmieducar.ano_letivo_modulo
                                                     WHERE ref_ano = escola_ano_letivo.ano
                                                       AND ref_ref_cod_escola = escola.cod_escola))
              LEFT JOIN cadastro.pessoa pessoa_gestor ON (pessoa_gestor.idpes = escola.ref_idpes_gestor)
              LEFT JOIN cadastro.fisica fisica_gestor ON (fisica_gestor.idpes = escola.ref_idpes_gestor)
              LEFT JOIN modules.educacenso_cod_escola ON (educacenso_cod_escola.cod_escola = escola.cod_escola)
              LEFT JOIN cadastro.endereco_pessoa ON (endereco_pessoa.idpes = escola.ref_idpes)
              LEFT JOIN public.bairro ON (bairro.idbai = endereco_pessoa.idbai)
              LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
              LEFT JOIN public.uf ON (uf.sigla_uf = municipio.sigla_uf)
              LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)
             WHERE escola.cod_escola = $1
               AND escola_ano_letivo.ano = $2";

    $escola = $this->fetchPreparedQuery($sql, array($escola, $ano));

    if(empty($escola)){
      $this->messenger->append("O ano letivo {$ano} não foi definido.");
      return array('title' => "Análise exportação - Registro 00");
    }

    $escola       = $escola[0];
    $nomeEscola   = Portabilis_String_Utils::toUtf8(strtoupper($escola["nome_escola"]));
    $anoAtual     = date("Y");
    $anoAnterior  = $anoAtual-1;
    $anoPosterior = $anoAtual+1;

    $mensagem = array();

    if (!$escola["inep"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se a escola possui o código INEP cadastrado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Código INEP)");
    }
    if (!$escola["cpf_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar possui o CPF cadastrado.",
                          "path" => "(Pessoa FJ > Pessoa física > Editar > Campo: CPF)");
    }
    if (!$escola["nome_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o(a) gestor(a) escolar foi informado(a).",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Gestor escolar)");
    }
    if (!$escola["cargo_gestor_escolar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o cargo do(a) gestor(a) escolar foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Campo: Cargo do gestor escolar)");
    }
    if ($escola["data_inicio"] != $anoAtual && $escola["data_inicio"] != $anoAnterior) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data inicial da primeira etapa foi cadastrada corretamente.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data inicial)");
    }
    if ($escola["data_fim"] != $anoAtual && $escola["data_fim"] != $anoPosterior) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} possui valor inválido. Verifique se a data final da última etapa foi cadastrada corretamente.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar ano letivo > Ok > Campo: Data final)");
    }
    if ((!$escola["latitude"]) && $escola["longitude"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a longitude foi informada, portanto obrigatoriamente a latitude também deve ser informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Latitude)");
    }
    if ((!$escola["longitude"]) && $escola["latitude"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verificamos que a latitude foi informada, portanto obrigatoriamente a longitude também deve ser informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados gerais > Campo: Longitude)");
    }
    if (!$escola["inep_uf"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código da UF informada, foi cadastrado conforme a 'Tabela de UF'.",
                          "path" => "(Endereçamento > Estado > Editar > Campo: Código INEP)");
    }
    if (!$escola["inep_municipio"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do município informado, foi cadastrado conforme a 'Tabela de Municípios'.",
                          "path" => "(Endereçamento > Município > Editar > Campo: Código INEP)");
    }
    if (!$escola["inep_distrito"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 00 da escola {$nomeEscola} não encontrados. Verifique se o código do distrito informado, foi cadastrado conforme a 'Tabela de Distritos'.",
                          "path" => "(Endereçamento > Distrito > Editar > Campo: Código INEP)");
    }

    return array('mensagens' => $mensagem,
                 'title'     => "Análise exportação - Registro 00");
  }

  protected function analisaEducacensoRegistro10() {

    $escola = $this->getRequest()->escola;

    $sql = "SELECT escola.local_funcionamento AS local_funcionamento,
                   escola.condicao AS condicao,
                   escola.agua_consumida AS agua_consumida,
                   pessoa.nome AS nome_escola,
                   escola.agua_rede_publica AS agua_rede_publica,
                   escola.agua_poco_artesiano AS agua_poco_artesiano,
                   escola.agua_cacimba_cisterna_poco AS agua_cacimba_cisterna_poco,
                   escola.agua_fonte_rio AS agua_fonte_rio,
                   escola.agua_inexistente AS agua_inexistente,
                   escola.energia_rede_publica AS energia_rede_publica,
                   escola.energia_gerador AS energia_gerador,
                   escola.energia_outros AS energia_outros,
                   escola.energia_inexistente AS energia_inexistente,
                   escola.esgoto_fossa AS esgoto_fossa,
                   escola.esgoto_inexistente AS esgoto_inexistente,
                   escola.lixo_coleta_periodica AS lixo_coleta_periodica,
                   escola.lixo_queima AS lixo_queima,
                   escola.lixo_joga_outra_area AS lixo_joga_outra_area,
                   escola.lixo_recicla AS lixo_recicla,
                   escola.lixo_enterra AS lixo_enterra,
                   escola.lixo_outros AS lixo_outros,
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
                   escola.total_funcionario AS total_funcionario,
                   escola.atendimento_aee AS atendimento_aee,
                   escola.atividade_complementar AS atividade_complementar,
                   escola.localizacao_diferenciada AS localizacao_diferenciada,
                   escola.didatico_nao_utiliza AS didatico_nao_utiliza,
                   escola.didatico_quilombola AS didatico_quilombola,
                   escola.didatico_indigena AS didatico_indigena,
                   escola.lingua_ministrada AS lingua_ministrada
              FROM pmieducar.escola
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = escola.ref_idpes)
             WHERE escola.cod_escola = $1";

    $escola = $this->fetchPreparedQuery($sql, array($escola));

    if(empty($escola)){
      $this->messenger->append("Ocorreu algum problema ao decorrer da análise.");
      return array('title' => "Análise exportação - Registro 10");
    }

    $escola        = $escola[0];
    $nomeEscola    = Portabilis_String_Utils::toUtf8(strtoupper($escola["nome_escola"]));
    $predioEscolar = 3; //Valor fixo definido no cadastro de escola

    $existeAbastecimentoAgua = ($escola["agua_rede_publica"] ||
                                $escola["agua_poco_artesiano"] ||
                                $escola["agua_cacimba_cisterna_poco"] ||
                                $escola["agua_fonte_rio"] ||
                                $escola["agua_inexistente"]);

    $existeAbastecimentoEnergia = ($escola["energia_rede_publica"] ||
                                   $escola["energia_gerador"] ||
                                   $escola["energia_outros"] ||
                                   $escola["energia_inexistente"]);

    $existeEsgotoSanitario = ($escola["esgoto_fossa"] || $escola["esgoto_inexistente"]);

    $existeDestinacaoLixo = ($escola["lixo_coleta_periodica"] ||
                             $escola["lixo_queima"] ||
                             $escola["lixo_joga_outra_area"] ||
                             $escola["lixo_recicla"] ||
                             $escola["lixo_enterra"] ||
                             $escola["lixo_outros"]);

    $existeDependencia = ($escola["dependencia_sala_diretoria"] || $escola["dependencia_sala_professores"] ||
                          $escola["dependncia_sala_secretaria"] || $escola["dependencia_laboratorio_informatica"] ||
                          $escola["dependencia_laboratorio_ciencias"] || $escola["dependencia_sala_aee"] ||
                          $escola["dependencia_quadra_coberta"] || $escola["dependencia_quadra_descoberta"] ||
                          $escola["dependencia_cozinha"] || $escola["dependencia_biblioteca"] ||
                          $escola["dependencia_sala_leitura"] || $escola["dependencia_parque_infantil"] ||
                          $escola["dependencia_bercario"] || $escola["dependencia_banheiro_fora"] ||
                          $escola["dependencia_banheiro_dentro"] || $escola["dependencia_banheiro_infantil"] ||
                          $escola["dependencia_banheiro_deficiente"] || $escola["dependencia_banheiro_chuveiro"] ||
                          $escola["dependencia_refeitorio"] || $escola["dependencia_dispensa"] ||
                          $escola["dependencia_aumoxarifado"] || $escola["dependencia_auditorio"] ||
                          $escola["dependencia_patio_coberto"] || $escola["dependencia_patio_descoberto"] ||
                          $escola["dependencia_alojamento_aluno"] || $escola["dependencia_alojamento_professor"] ||
                          $escola["dependencia_area_verde"] || $escola["dependencia_lavanderia"] ||
                          $escola["dependencia_nenhuma_relacionada"]);

    $existeEquipamentos = ($escola["televisoes"] || $escola["videocassetes"] ||
                           $escola["dvds"] || $escola["antenas_parabolicas"] ||
                           $escola["copiadoras"] || $escola["retroprojetores"] ||
                           $escola["impressoras"] || $escola["aparelhos_de_som"] ||
                           $escola["projetores_digitais"] || $escola["faxs"] ||
                           $escola["maquinas_fotograficas"] || $escola["computadores"] ||
                           $escola["computadores_administrativo"] || $escola["computadores_alunos"]);

    $existeMaterialDidatico = ($escola["didatico_nao_utiliza"] || $escola["didatico_quilombola"] || $escola["didatico_indigena"]);

    $mensagem = array();

    if (!$escola["local_funcionamento"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. não encontrados. Verifique se o local de funcionamento da escola foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campo: Local de funcionamento)");
    }
    if($escola["local_funcionamento"] == $predioEscolar && !$escola["condicao"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto obrigatoriamente é necessário informar qual a forma de ocupação do prédio.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campo: Condição)");
    }
    if (!$escola["agua_consumida"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a água consumida pelos alunos foi informada",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campo: Água consumida pelos alunos)");
    }
    if (!$existeAbastecimentoAgua) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas do abastecimento de água foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campos: Abastecimento de água)");
    }
    if (!$existeAbastecimentoEnergia) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas do abastecimento de energia elétrica foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campos: Abastecimento de energia elétrica)");
    }
    if (!$existeEsgotoSanitario) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se alguma opção de esgoto sanitário foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campos: Esgoto sanitário)");
    }
    if (!$existeDestinacaoLixo) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se uma das formas da destinação do lixo foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Infraestrutura > Campos: Destinação do lixo)");
    }
    if (!$existeDependencia) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Nenhum campo foi preenchido referente as dependências existentes na escola, portanto todos serão registrados como NÃO.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dependências > Campos: Dependências existentes na escola)");
    }
    if($escola["local_funcionamento"] == $predioEscolar && !$escola["dependencia_numero_salas_existente"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que o local de funcionamento da escola é em um prédio escolar, portanto obrigatoriamente é necessário informar o número de salas de aula existentes na escola.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dependências > Campo: Dependências existentes na escola - Número de salas de aula existentes na escola)");
    }
    if (!$escola['dependencia_numero_salas_utilizadas']) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o número de salas utilizadas como sala de aula foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dependências > Campo: Dependências existentes na escola – Número de salas utilizadas como sala de aula)");
    }
    if (!$existeEquipamentos) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Nenhum campo foi preenchido referente a quantidade de equipamentos existentes na escola, portanto todos serão registrados como NÃO.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Equipamentos > Campos: Quantidade de equipamentos)");
    }
    if (!$escola["total_funcionario"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o total de funcionários da escola foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dependências > Campo: Total de funcionários da escola)");
    }
    if (!$escola["atendimento_aee"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se o atendimento educacional especializado - AEE foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados do ensino > Campo: Atendimento educacional especializado - AEE)");
    }
    if (!$escola["atividade_complementar"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a atividade complementar foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados do ensino > Campo: Atividade complementar)");
    }
    if (!$escola["localizacao_diferenciada"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se a localização diferenciada da escola foi informada.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados do ensino > Campo: Localização diferenciada da escola)");
    }
    if (!$existeMaterialDidatico) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verifique se algum material didático específico para atendimento à diversidade sócio-cultural foi informado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados do ensino > Campo: Materiais didáticos específicos para atendimento à diversidade sócio-cultural)");
    }
    if (!$escola["lingua_ministrada"]) {
      $mensagem[] = array("text" => "Dados para formular o registro 10 da escola {$nomeEscola} não encontrados. Verificamos que a escola trabalha com educação indígena, portanto obrigatoriamente é necessário informar a língua em que o ensino é ministrado.",
                          "path" => "(Cadastros > Escola > Cadastrar > Editar > Aba: Dados do ensino > Campo: Língua em que o ensino é ministrado)");
    }

    return array('mensagens' => $mensagem,
                 'title'     => "Análise exportação - Registro 10");
  }

  protected function analisaEducacensoRegistro20() {

    $escola = $this->getRequest()->escola;
    $ano    = $this->getRequest()->ano;

    $sql = "SELECT pessoa.nome AS nome_escola,
                   turma.nm_turma AS nome_turma,
                   turma.hora_inicial AS hora_inicial,
                   turma.hora_final AS hora_final,
                   (SELECT TRUE
                      FROM pmieducar.turma_dia_semana
                     WHERE ref_cod_turma = turma.cod_turma LIMIT 1) AS dias_semana,
                   turma.tipo_atendimento AS tipo_atendimento,
                   turma.atividade_complementar_1 AS atividade_complementar_1,
                   turma.atividade_complementar_2 AS atividade_complementar_2,
                   turma.atividade_complementar_3 AS atividade_complementar_3,
                   turma.atividade_complementar_4 AS atividade_complementar_4,
                   turma.atividade_complementar_5 AS atividade_complementar_5,
                   turma.atividade_complementar_6 AS atividade_complementar_6,
                   turma.aee_braille AS aee_braille,
                   turma.aee_recurso_optico AS aee_recurso_optico ,
                   turma.aee_estrategia_desenvolvimento AS aee_estrategia_desenvolvimento,
                   turma.aee_tecnica_mobilidade AS aee_tecnica_mobilidade,
                   turma.aee_libras AS aee_libras,
                   turma.aee_caa AS aee_caa,
                   turma.aee_curricular AS aee_curricular,
                   turma.aee_soroban AS aee_soroban,
                   turma.aee_informatica AS aee_informatica,
                   turma.aee_lingua_escrita AS aee_lingua_escrita,
                   turma.aee_autonomia AS aee_autonomia
              FROM pmieducar.escola
             INNER JOIN cadastro.pessoa ON (pessoa.idpes = escola.ref_idpes)
             INNER JOIN pmieducar.turma ON (turma.ref_ref_cod_escola = escola.cod_escola)
             WHERE escola.cod_escola = $1
               AND turma.ano = $2
               AND turma.ativo = 1
               AND escola.ativo = 1";

    $turmas = $this->fetchPreparedQuery($sql, array($escola, $ano));

    if(empty($turmas)){
      $this->messenger->append("Ocorreu algum problema ao decorrer da análise.");
      return array('title' => "Análise exportação - Registro 20");
    }

    $mensagem = array();

    foreach ($turmas as $turma) {

      $nomeEscola = Portabilis_String_Utils::toUtf8(strtoupper($turma["nome_escola"]));
      $nomeTurma  = Portabilis_String_Utils::toUtf8(strtoupper($turma["nome_turma"]));
      $atividadeComplementar = ($turma["tipo_atendimento"] == 4); //Código 4 fixo no cadastro de turma
      $existeAtividadeComplementar = ($turma["atividade_complementar_1"] || $turma["atividade_complementar_2"] ||
                                      $turma["atividade_complementar_3"] || $turma["atividade_complementar_4"] ||
                                      $turma["atividade_complementar_5"] || $turma["atividade_complementar_6"]);
      $atendimentoAee = ($turma["tipo_atendimento"] == 5); //Código 5 fixo no cadastro de turma
      $existeAee = ($turma["aee_braille"] || $turma["aee_recurso_optico"] ||
                    $turma["aee_estrategia_desenvolvimento"] || $turma["aee_tecnica_mobilidade"] ||
                    $turma["aee_libras"] || $turma["aee_caa"] ||
                    $turma["aee_curricular"] || $turma["aee_soroban"] ||
                    $turma["aee_informatica"] || $turma["aee_lingua_escrita"] ||
                    $turma["aee_autonomia"]);

      if (!$turma["hora_inicial"]) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o horário inicial da turma {$nomeTurma} foi cadastrado.",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados gerais > Campo: Hora inicial)");
      }
      if (!$turma["hora_final"]) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o horário final da turma {$nomeTurma} foi cadastrado.",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados gerais > Campo: Hora final)");
      }
      if (!$turma["dias_semana"]) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. É necessário informar ao menos um dia da semana para a turma presencial {$nomeTurma}.",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados gerais > Campos: Dia semana, Hora inicial e Hora final)");
      }
      if (!$turma["tipo_atendimento"]) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verifique se o tipo de atendimento da turma {$nomeTurma} foi cadastrado.",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados adicionais > Campo: Tipo de atendimento)");
      }
      if ($atividadeComplementar && !$existeAtividadeComplementar) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de atendimento da turma {$nomeTurma} é de atividade complementar, portanto obrigatoriamente é necessário informar o código de ao menos uma atividade conforme a 'Tabela de Tipo de Atividade Complementar'.",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados adicionais > Campo: Código do tipo de atividade complementar)");   
      }
      if ($atendimentoAee && !$existeAee) {
        $mensagem[] = array("text" => "Dados para formular o registro 20 da escola {$nomeEscola} não encontrados. Verificamos que o tipo de atendimento da turma {$nomeTurma} é de educação especializada - AEE, portanto obrigatoriamente é necessário informar ao menos uma atividade realizada. ",
                            "path" => "(Cadastros > Turma > Cadastrar > Editar > Aba: Dados adicionais > Campos: De Ensino do Sistema Braille à Estratégias para autonomia no ambiente escolar)");
      }
    }

    return array('mensagens' => $mensagem,
                 'title'     => "Análise exportação - Registro 20");

  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'registro-00'))
      $this->appendResponse($this->analisaEducacensoRegistro00());
    else if ($this->isRequestFor('get', 'registro-10'))
      $this->appendResponse($this->analisaEducacensoRegistro10());
    else if ($this->isRequestFor('get', 'registro-20'))
      $this->appendResponse($this->analisaEducacensoRegistro20());
    else
      $this->notImplementedOperationError();
  }
}
