<?php

namespace Tests\Request\Support;

use Tests\Browser\Support\Category;

/**
 * As telas de `ieducar/intranet` que a varredura abre.
 *
 * A lista é descoberta em tempo de execução, e não declarada, de propósito: tela
 * nova entra na varredura sozinha, que é justamente o que se quer de uma rede de
 * segurança de legado.
 *
 * Só entram `_lst` e `_cad`, que abrem sem parâmetro. As `_det` precisam do id
 * de um registro e não têm como ser varridas sem um cenário por tela — é a mesma
 * razão pela qual a maior parte delas está na lista de exclusão do
 * `legacy:create:tests`.
 */
final class Screens
{
    /**
     * Telas que não são telas: upload, integração com S3 e utilitários que não
     * respondem HTML. Vem da mesma lista que o `legacy:create:tests` mantém em
     * `excludeRoutersList()`.
     *
     * @var array<int, string>
     */
    private const NOT_SCREENS = [
        'S3.php',
        's3_config.php',
        'upload.php',
        'upload_just_pdf.php',
        'file_check.php',
        'file_check_just_pdf.php',
        'index.php',
        'logof.php',
        'manutencao.php',
        'migra_alunos.php',
        'suspenso.php',
    ];

    /**
     * Telas que hoje devolvem 5xx ao abrir, com o erro que dão.
     *
     * São defeitos, não configuração: nenhuma delas precisa de parâmetro para
     * renderizar um formulário em branco ou uma listagem vazia. A cópia de
     * vínculos já estava na `excludeRoutersList()` do `legacy:create:tests` —
     * alguém esbarrou nela e excluiu da geração em vez de corrigir.
     *
     * A lista não apodrece: o teste `segue quebrada` confere que cada uma
     * continua falhando, e passa a falhar quando alguém consertar — que é a
     * deixa para tirar a tela daqui e ela voltar para a varredura.
     *
     * @var array<string, string>
     */
    public const BROKEN = [
        'copia_vinculos_servidores_cad.php' => 'Call to a member function getKey() on null',
        'educar_matricula_historico_cad.php' => 'Missing required parameter for [Route: enrollments.enrollment-history]',
    ];

    /**
     * As telas quebradas, do nome do arquivo para a rota.
     *
     * @return array<string, string>
     */
    public static function broken(): array
    {
        $telas = [];

        foreach (array_keys(self::BROKEN) as $arquivo) {
            $telas[$arquivo] = '/intranet/' . $arquivo;
        }

        return $telas;
    }

    /**
     * As telas varridas, do nome do arquivo para a rota.
     *
     * As 23 telas de catálogo ficam de fora porque o CategoryScreenTest já as
     * percorre inteiras — aqui a varredura cobre o que ninguém cobre.
     *
     * @return array<string, string>
     */
    public static function all(): array
    {
        $cobertas = array_map(
            static fn (Category $category): string => $category->screen,
            array_values(Category::all()),
        );

        $telas = [];

        // O caminho sai daqui, e não do base_path(): o Pest monta os datasets
        // antes de bootar o Laravel, então nenhum helper do framework existe
        // neste ponto.
        $intranet = dirname(__DIR__, 3) . '/ieducar/intranet';

        foreach (glob($intranet . '/*_{lst,cad}.php', GLOB_BRACE) ?: [] as $caminho) {
            $arquivo = basename($caminho);

            if (in_array($arquivo, self::NOT_SCREENS, true)) {
                continue;
            }

            if (array_key_exists($arquivo, self::BROKEN)) {
                continue;
            }

            if (in_array(substr($arquivo, 0, -8), $cobertas, true)) {
                continue;
            }

            $telas[$arquivo] = '/intranet/' . $arquivo;
        }

        return $telas;
    }
}
