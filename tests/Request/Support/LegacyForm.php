<?php

namespace Tests\Request\Support;

use DOMDocument;
use PHPUnit\Framework\ExpectationFailedException;

/**
 * Leitura do HTML que o clsCadastro devolve.
 *
 * A camada de requisição não inventa o payload do POST: ela abre o formulário
 * por GET e colhe dele o que a tela já sabe. Isso rende três coisas que um
 * payload escrito à mão não tem.
 *
 * Os campos ocultos vêm preenchidos pelo próprio `Inicializar()`, inclusive o
 * `tipoacao` (que a tela põe em `Novo` ou `Editar`) e a chave primária com o
 * nome que a tela usa — que nem sempre é o do parâmetro do GET: em
 * funcionario_vinculo o oculto se chama `cod_vinculo` e quem abre a edição é
 * `cod_funcionario_vinculo`.
 *
 * As `<option>` dão a tradução de rótulo para valor. O descritor declara os
 * `<select>` pelo rótulo, que é o que o usuário vê e o que o rebranding não pode
 * mudar; o POST precisa do valor. Traduzir lendo o HTML de hoje deixa a
 * asserção mais forte, não mais fraca: se o rótulo sumir, o teste para aqui.
 */
final class LegacyForm
{
    private function __construct(
        private readonly DOMDocument $document,
    ) {}

    /**
     * O HTML do legado não fecha metade das tags e mistura aspas simples com
     * duplas, então os erros do libxml são silenciados de propósito — o parser
     * de HTML aceita tudo isso. O prolog de XML é o que garante que acentos de
     * rótulos como "Não" e "Educação superior" cheguem em UTF-8.
     */
    public static function from(string $html): self
    {
        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);

        $document->loadHTML('<?xml encoding="UTF-8" ?>' . $html);

        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return new self($document);
    }

    /**
     * Todos os campos ocultos do formulário, já com o valor que a tela pôs.
     *
     * @return array<string, string>
     */
    public function hidden(): array
    {
        $hidden = [];

        foreach ($this->document->getElementsByTagName('input') as $input) {
            if (mb_strtolower($input->getAttribute('type')) !== 'hidden') {
                continue;
            }

            $name = $input->getAttribute('name');

            if ($name === '') {
                continue;
            }

            $hidden[$name] = $input->getAttribute('value');
        }

        return $hidden;
    }

    /**
     * As opções de um `<select>`, do valor para o rótulo.
     *
     * @return array<string, string>
     */
    public function options(string $field): array
    {
        $options = [];

        foreach ($this->document->getElementsByTagName('select') as $select) {
            if ($select->getAttribute('name') !== $field) {
                continue;
            }

            foreach ($select->getElementsByTagName('option') as $option) {
                $options[$option->getAttribute('value')] = trim($option->textContent);
            }

            break;
        }

        return $options;
    }

    /**
     * O valor da opção com o rótulo informado.
     *
     * A falha lista as opções que existiam, como o Form::cascade do browser
     * faz, para o diagnóstico ser o mesmo nas duas camadas.
     */
    public function value(string $field, string $label): string
    {
        $options = $this->options($field);

        foreach ($options as $value => $text) {
            if ($text === $label || $value === $label) {
                return $value;
            }
        }

        $encontradas = $options === [] ? 'nenhuma' : implode(' | ', array_map(
            static fn (string $value, string $text): string => "{$value}={$text}",
            array_keys($options),
            $options,
        ));

        throw new ExpectationFailedException(
            "O campo [{$field}] não tem a opção [{$label}]. Opções: {$encontradas}."
        );
    }
}
