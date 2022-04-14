<?php

class CoreExt_Config implements Countable, Iterator
{

    /**
     * Array de sobre sobrecarga
     *
     * @var array
     */
    protected $config;

    /**
     * Array com mensagens de erro causadas por funções PHP.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Índice interno do array para a implementação da interface Iterator.
     *
     * @var int
     */
    private $_index = 0;

    /**
     * Quantidade de items do array de sobrecarga $config para a implementação da interface Countable.
     *
     * @var int
     */
    private $_count = 0;

    /**
     * Construtor da classe.
     *
     * @param  $array  Array associativo com as diretivas de configuração.
     */
    public function __construct($array)
    {
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $this->config[$key] = new self($val);
            } else {
                $this->config[$key] = $val;
            }
        }

        $this->_count = count($this->config);
    }

    /**
     * Retorna o valor do array de sobrecarga $config.
     *
     * Este método deve ser usado toda vez que a variável de configuração puder
     * ser sobrescrita por um storage de configuração externa ao código, como o
     * arquivo ini. Um exemplo seria para a criação de um arquivo on the fly no
     * filesystem. No código pode ser assumido que o local padrão será
     * intranet/tmp mas, se esse valor puder ser sobrescrito pelo ini, esse método
     * deverá ser utilizado:
     * <code>
     * $dir = $config->get($config->app->filesystem->tmp_dir, 'intranet/tmp');
     * </code>
     *
     * Se a variável de configuração não for sobrescrita por um arquivo ini ou
     * array de configuração, o valor padrão (segundo parâmetro) será utilizado.
     *
     * @param mixed $value1 Valor retornado pelo array de configuração sobrecarregado
     * @param mixed $value2 Valor padrão caso não exista uma configuração sobrecarregada
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get($value1, $value2 = null)
    {
        if (null != $value1) {
            return $value1;
        }

        if (null == $value2) {
            throw new Exception('O segundo parâmetro deve conter algum valor não nulo.');
        }

        return $value2;
    }

    /**
     * Retorna o valor armazenado pelo índice no array de sobrecarga $config.
     *
     * @param $key   Índice (nome) da variável criada por sobrecarga
     * @param $value Valor padrão caso o índice não exista
     *
     * @return mixed O valor armazenado em
     */
    private function getFrom($key, $value = null)
    {
        if (array_key_exists($key, $this->config)) {
            $value = $this->config[$key];
        }

        return $value;
    }

    /**
     * Implementação do método mágico __get().
     *
     * @param $key
     *
     * @return unknown_type
     */
    public function __get($key)
    {
        return $this->getFrom($key);
    }

    /**
     * Retorna o conteúdo do array de sobrecarga em um array associativo simples.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->config as $key => $value) {
            $array[$key] = $value;
        }

        return $array;
    }

    /**
     * Implementação do método count() da interface Countable.
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Implementação do método next() da interface Iterator.
     */
    public function next()
    {
        next($this->config);
        ++$this->_index;
    }

    /**
     * Implementação do método next() da interface Iterator.
     */
    public function rewind()
    {
        reset($this->config);
        $this->_index = 0;
    }

    /**
     * Implementação do método current() da interface Iterator.
     */
    public function current()
    {
        return current($this->config);
    }

    /**
     * Implementação do método key() da interface Iterator.
     */
    public function key()
    {
        return key($this->config);
    }

    /**
     * Implementação do método valid() da interface Iterator.
     */
    public function valid()
    {
        return $this->_index < $this->_count && $this->_index > -1;
    }

    /**
     * Merge recursivo mantendo chaves distintas.
     *
     * Realiza um merge recursivo entre dois arrays. É semelhante a função PHP
     * {@link http://php.net/array_merge_recursive array_merge_recursive} exceto
     * pelo fato de que esta mantém apenas um valor de uma chave do array ao invés
     * de criar múltiplos valores para a mesma chave como na função original.
     *
     * @author  Daniel Smedegaard Buus <daniel@danielsmedegaardbuus.dk>
     *
     * @link    http://www.php.net/manual/pt_BR/function.array-merge-recursive.php#89684  Código fonte original
     *
     * @param array $arr1
     * @param array $arr2
     *
     * @return array
     */
    protected function &arrayMergeRecursiveDistinct(&$arr1, &$arr2)
    {
        $merged = $arr1;

        if (is_array($arr2)) {
            foreach ($arr2 as $key => $val) {
                if (is_array($arr2[$key])) {
                    $merged[$key] = isset($merged[$key]) && is_array($merged[$key])
                        ? $this->arrayMergeRecursiveDistinct($merged[$key], $arr2[$key])
                        : $arr2[$key];
                } else {
                    $merged[$key] = $val;
                }
            }
        }

        return $merged;
    }

    /**
     * Método callback para a função set_error_handler().
     *
     * Handler para os erros internos da classe. Dessa forma, é possível usar
     * os blocos try/catch para lançar exceções.
     *
     * @see  http://php.net/set_error_handler
     *
     * @param  $errno
     * @param  $errstr
     */
    protected function configErrorHandler($errno, $errstr)
    {
        $this->errors[] = [$errno => $errstr];
    }
}
