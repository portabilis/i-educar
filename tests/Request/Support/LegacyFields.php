<?php

namespace Tests\Request\Support;

use DOMDocument;
use DOMElement;

/**
 * O que um navegador enviaria ao submeter o formulário de uma tela legada.
 *
 * O teste de uma tela abre o formulário por GET, colhe daqui todos os campos
 * com o valor que a tela pôs e sobrescreve só o que interessa. Assim o POST
 * leva o mesmo conjunto de campos que o usuário levaria — inclusive os ocultos,
 * o `tipoacao` e a chave do registro em edição — sem o teste precisar conhecer
 * cada um.
 *
 * Segue as regras do navegador: campo desabilitado e botão não são enviados,
 * caixa de seleção e botão de rádio só entram quando marcados, e um `<select>`
 * sem opção marcada envia a primeira.
 */
final class LegacyFields
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    public static function from(string $html, array $overrides = []): array
    {
        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);

        $document->loadHTML('<?xml encoding="UTF-8" ?>' . $html);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $pairs = [];

        foreach (['input', 'select', 'textarea'] as $tag) {
            foreach ($document->getElementsByTagName($tag) as $element) {
                $name = $element->getAttribute('name');

                if ($name === '' || $element->hasAttribute('disabled')) {
                    continue;
                }

                foreach (self::values($element) as $value) {
                    $pairs[] = urlencode($name) . '=' . urlencode($value);
                }
            }
        }

        parse_str(implode('&', $pairs), $fields);

        return array_replace($fields, $overrides);
    }

    /**
     * @return array<int, string>
     */
    private static function values(DOMElement $element): array
    {
        if ($element->tagName === 'textarea') {
            return [$element->textContent];
        }

        if ($element->tagName === 'select') {
            return self::selected($element);
        }

        $type = mb_strtolower($element->getAttribute('type'));

        if (in_array($type, ['submit', 'button', 'reset', 'image', 'file'], true)) {
            return [];
        }

        if (in_array($type, ['checkbox', 'radio'], true)) {
            if (!$element->hasAttribute('checked')) {
                return [];
            }

            return [$element->hasAttribute('value') ? $element->getAttribute('value') : 'on'];
        }

        return [$element->getAttribute('value')];
    }

    /**
     * @return array<int, string>
     */
    private static function selected(DOMElement $select): array
    {
        $options = [];
        $selected = [];

        foreach ($select->getElementsByTagName('option') as $option) {
            $value = $option->hasAttribute('value') ? $option->getAttribute('value') : trim($option->textContent);

            $options[] = $value;

            if ($option->hasAttribute('selected')) {
                $selected[] = $value;
            }
        }

        if ($selected !== [] || $select->hasAttribute('multiple')) {
            return $selected;
        }

        return array_slice($options, 0, 1);
    }
}
