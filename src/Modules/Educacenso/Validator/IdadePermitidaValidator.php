<?php

namespace iEducar\Modules\Educacenso\Validator;

/**
 * Valida se a idade (calculada pela regra do Censo: ano do Censo menos o ano de
 * nascimento) está dentro da faixa etária permitida pelo layout do Censo Escolar.
 *
 * Não bloqueia quando a idade não pôde ser calculada (data de nascimento ausente)
 * ou quando não há faixa aplicável (etapa sem faixa definida): nesses casos não há
 * o que validar. A mensagem devolve apenas o predicado; o consumidor prefixa o sujeito.
 */
class IdadePermitidaValidator implements EducacensoValidator
{
    private $message = '';

    private $idade;

    private $faixa;

    private $contexto;

    /**
     * @param int|null   $idade    idade pela regra do Censo
     * @param array|null $faixa    faixa [mínima, máxima] permitida
     * @param string     $contexto descrição do que a faixa representa (ex.: a etapa de ensino)
     */
    public function __construct(?int $idade, ?array $faixa, string $contexto)
    {
        $this->idade = $idade;
        $this->faixa = $faixa;
        $this->contexto = $contexto;
    }

    public function isValid(): bool
    {
        if ($this->idade === null || $this->faixa === null) {
            return true;
        }

        [$minima, $maxima] = $this->faixa;

        if ($this->idade >= $minima && $this->idade <= $maxima) {
            return true;
        }

        $this->message = "possui {$this->idade} anos no Censo (ano do Censo menos o ano de nascimento), fora da faixa de {$minima} a {$maxima} anos permitida para {$this->contexto}.";

        return false;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
