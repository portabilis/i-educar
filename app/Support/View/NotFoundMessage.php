<?php

namespace App\Support\View;

/**
 * Monta a mensagem exibida quando uma tela redireciona porque o registro
 * procurado não foi encontrado.
 *
 * O resultado já sai escapado: o partial de flash messages imprime o texto
 * sem escape, e o código costuma vir da URL.
 */
class NotFoundMessage
{
    /**
     * @param string          $registro Nome do registro em minúsculas, ex.: "aluno"
     * @param bool            $feminino Se o nome do registro é feminino, ex.: "matrícula"
     * @param int|string|null $codigo   Código procurado; só aparece quando é um inteiro positivo
     * @param string|null     $motivo   Substitui a frase padrão sobre o registro não existir
     * @param string|null     $dica     Substitui a frase padrão que orienta a usar a busca
     */
    public static function make(
        string $registro,
        bool $feminino = false,
        int|string|null $codigo = null,
        ?string $motivo = null,
        ?string $dica = null
    ): string {
        $vogal = $feminino ? 'a' : 'o';
        $codigo = filter_var($codigo, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        $motivo ??= $codigo !== false
            ? "O código {$codigo} não existe ou foi excluído."
            : 'O registro não existe ou foi excluído.';

        $dica ??= "Use a busca abaixo para localizá-l{$vogal}.";

        $titulo = mb_strtoupper(mb_substr($registro, 0, 1)) . mb_substr($registro, 1) . " não encontrad{$vogal}.";

        return e("{$titulo} {$motivo} {$dica}");
    }
}
