<?php

namespace App\Validators;

/**
 * Validador para controle e consistência de faixa etária no cadastro de séries.
 * Solução desenvolvida via TDD para atender à Issue #1095.
 */
class SerieFaixaEtariaValidator
{
    private array $messages = [];

    public function isValid($idadeInicial = null, $idadeFinal = null, $idadeIdeal = null): bool
    {
        $this->messages = [];

        $inicial = ($idadeInicial !== null && $idadeInicial !== '') ? (int) $idadeInicial : null;
        $final = ($idadeFinal !== null && $idadeFinal !== '') ? (int) $idadeFinal : null;
        $ideal = ($idadeIdeal !== null && $idadeIdeal !== '') ? (int) $idadeIdeal : null;

        // Validação 1: Idade ideal não pode ser negativa
        if ($ideal !== null && $ideal < 0) {
            $this->messages[] = 'A idade padrão (ideal) não pode ser negativa.';
        }

        // Validação 2: Idade inicial não pode ser negativa
        if ($inicial !== null && $inicial < 0) {
            $this->messages[] = 'A idade inicial da faixa etária não pode ser negativa.';
        }

        // Validação 3: Idade final não pode ser negativa
        if ($final !== null && $final < 0) {
            $this->messages[] = 'A idade final da faixa etária não pode ser negativa.';
        }

        // Validação 4: Consistência do intervalo (inicial <= final)
        if ($inicial !== null && $final !== null && $inicial > $final) {
            $this->messages[] = 'A idade inicial não pode ser maior que a idade final da faixa etária.';
        }

        return empty($this->messages);
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function getFirstMessage(): ?string
    {
        return $this->messages[0] ?? null;
    }
}
