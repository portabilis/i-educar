<?php

namespace Tests\Request\Support;

use Tests\Browser\Support\Category;

/**
 * Traduz um descritor de tela de catálogo no payload do POST.
 *
 * O descritor é o mesmo que a suíte de browser usa — ele é só dados, e é lido
 * daqui de fora sem que nada precise mudar nele. O que muda é o meio: lá os
 * campos são digitados num navegador, aqui viram um array de POST.
 */
final class CategoryPayload
{
    /**
     * @param  array<string, int|string>  $context  o que o cenário criou e só se
     *                                              sabe em tempo de execução
     * @return array<string, int|string>
     */
    public static function for(
        Category $category,
        LegacyForm $form,
        string $name,
        array $context = [],
    ): array {
        // A base é o que a tela já preencheu sozinha: tipoacao e a chave.
        $payload = $form->hidden();

        foreach ($category->context as $field) {
            $payload[$field] = $context[$field];
        }

        foreach ($category->selects as $field => $label) {
            $payload[$field] = $form->value($field, $label);
        }

        $payload[$category->field] = $name;

        foreach ($category->fields as $field => $value) {
            $payload[$field] = $value;
        }

        return $payload;
    }
}
