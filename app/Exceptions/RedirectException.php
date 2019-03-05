<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class RedirectException extends RuntimeException
{
    /**
     * @var string
     */
    private $url;

    /**
     * @param string         $url
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(string $url, int $code = 302, Throwable $previous = null)
    {
        $this->url = $url;

        parent::__construct('Redirect to: ' . $url, $code, $previous);
    }

    /**
     * Retorna a URL para onde a aplicação deve ser redirecionada.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
