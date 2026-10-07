<?php

namespace Tests\Request\Support;

use Database\Factories\ManagerAccessCriteriaFactory;
use Database\Factories\ManagerLinkTypeFactory;
use Database\Factories\ManagerRoleFactory;

/**
 * Os dados do formulário de escola que os testes dele compartilham.
 *
 * As listas de múltipla escolha da tela são montadas por JavaScript, então o
 * HTML não diz o que o navegador enviaria nelas; os valores ficam aqui, iguais
 * aos do estado `complete()` da factory de escola.
 */
final class SchoolFormData
{
    /**
     * Os campos de escolha única e de texto do Censo, com valores que o
     * formulário oferece. Cada um corresponde a uma coluna de mesmo nome em
     * pmieducar.escola.
     */
    public const FIELDS = [
        'caracteristica_escolar' => 1,
        'esfera_administrativa' => 3,
        'unidade_vinculada_outra_instituicao' => 0,
        'ato_criacao' => 'Decreto 1 de 1990',
        'ato_autorizativo' => 'Portaria 2 de 1991',
        'lei_conclusao_ensino_medio' => 'Lei 3 de 2000',
        'condicao' => 1,
        'predio_compartilhado_outra_escola' => 0,
        'agua_potavel_consumo' => 1,
        'alimentacao_escolar_alunos' => 1,
        'compartilha_espacos_atividades_integracao' => 0,
        'usa_espacos_equipamentos_atividades_regulares' => 0,
        'possui_dependencias' => 1,
        'numero_salas_utilizadas_dentro_predio' => 8,
        'numero_salas_utilizadas_fora_predio' => 2,
        'numero_salas_climatizadas' => 4,
        'numero_salas_acessibilidade' => 3,
        'numero_salas_cantinho_leitura' => 1,
        'acesso_internet' => 1,
        'quantidade_computadores_alunos_mesa' => 10,
        'quantidade_computadores_alunos_portateis' => 5,
        'quantidade_computadores_alunos_tablets' => 3,
        'lousas_digitais' => 1,
        'televisoes' => 2,
        'dvds' => 1,
        'aparelhos_de_som' => 1,
        'projetores_digitais' => 2,
        'atendimento_aee' => 1,
        'fundamental_ciclo' => 0,
        'exame_selecao_ingresso' => 0,
        'acao_area_ambiental' => 1,
        'projeto_politico_pedagogico' => 1,
        'lingua_ministrada' => 1,
        'localizacao_diferenciada' => 7,
    ];

    /**
     * Os campos de múltipla escolha. No POST vão como lista; no banco viram um
     * vetor do Postgres, como "{1,2}".
     */
    public const MULTIPLE = [
        'orgao_vinculado_escola' => [2],
        'local_funcionamento' => [3],
        'abastecimento_agua' => [1, 2],
        'abastecimento_energia' => [1],
        'esgoto_sanitario' => [1],
        'destinacao_lixo' => [1],
        'tratamento_lixo' => [2],
        'salas_gerais' => [1],
        'salas_funcionais' => [1],
        'banheiros' => [1],
        'laboratorios' => [1],
        'salas_atividades' => [1],
        'dormitorios' => [1],
        'areas_externas' => [1],
        'recursos_acessibilidade' => [2],
        'equipamentos' => [1],
        'uso_internet' => [1],
        'rede_local' => [3],
        'equipamentos_acesso_internet' => [1],
        'instrumentos_pedagogicos' => [1],
        'orgaos_colegiados' => [2],
        'reserva_vagas_cotas' => [2],
        'acoes_area_ambiental' => [2],
        'codigo_lingua_indigena' => [2],
        'poder_publico_parceria_convenio' => [1],
        'formas_contratacao_parceria_escola_secretaria_estadual' => [5],
        'formas_contratacao_parceria_escola_secretaria_municipal' => [5],
    ];

    /**
     * As linhas da tabela de gestores, do jeito que o formulário as envia.
     *
     * @param array<int, int> $employees códigos dos servidores, na ordem das linhas
     * @param int $chief índice da linha do gestor principal
     * @return array<string, array<int, mixed>>
     */
    public static function managers(array $employees, int $chief = 0): array
    {
        $rows = count($employees);

        return [
            'servidor_id' => $employees,
            'managers_role_id' => array_fill(0, $rows, ManagerRoleFactory::new()->director()->getKey()),
            'managers_access_criteria_id' => array_fill(0, $rows, ManagerAccessCriteriaFactory::new()->indication()->getKey()),
            'managers_link_type_id' => array_fill(0, $rows, ManagerLinkTypeFactory::new()->permanent()->getKey()),
            'managers_chief' => array_map(static fn (int $row) => $row === $chief ? 1 : 0, array_keys($employees)),
            'managers_email' => array_fill(0, $rows, ''),
            'managers_inep_id' => array_fill(0, $rows, ''),
        ];
    }
}
