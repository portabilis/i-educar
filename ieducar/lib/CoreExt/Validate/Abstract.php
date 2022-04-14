<?php

abstract class CoreExt_Validate_Abstract implements CoreExt_Validate_Interface
{
    /**
     * Opções de configuração geral da classe.
     *
     * @var array
     */
    protected $_options = [
        'required' => true,
        'trim' => true,
    ];

    /**
     * Valor não sanitizado que foi informado ao validador.
     *
     * @var mixed
     */
    protected $_value = null;

    /**
     * Valor sanitizado.
     *
     * @var mixed
     */
    protected $_sanitized = null;

    /**
     * Mensagem padrão para erros de valor obrigatório.
     *
     * @var string
     */
    protected $_requiredMessage = 'Obrigatório.';

    /**
     * Mensagem padrão para erros de invalidez.
     *
     * @var string
     */
    protected $_invalidMessage = 'Inválido.';

    /**
     * Construtor.
     *
     * Pode receber array com opções de configuração da classe.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->_options = array_merge($this->getOptions(), $this->_getDefaultOptions());

        $this->setOptions($options);
    }

    /**
     * Configura as opções do validador.
     *
     * Método de checagem de opções inspirado na técnica empregada no
     * {@link http://www.symfony-project.org symfony framework}.
     *
     * @param array $options
     *
     * @throws InvalidArgumentException Lança exceção não verificada caso alguma
     *                                  opção passada ao método não exista na definição da classe
     */
    public function setOptions(array $options = [])
    {
        $defaultOptions = array_keys($this->getOptions());
        $passedOptions = array_keys($options);

        if (0 < count(array_diff($passedOptions, $defaultOptions))) {
            throw new InvalidArgumentException(
                sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
            );
        }

        $this->_options = array_merge($this->getOptions(), $options);
    }

    /**
     * @see CoreExt_Validate_Interface#getOptions()
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Verifica se uma opção está setada.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function _hasOption($key)
    {
        return
            isset($this->_options[$key]) &&
            !$this->_isEmpty($this->_options[$key]);
    }

    /**
     * Retorna um valor de opção de configuração ou NULL caso a opção não esteja
     * setada.
     *
     * @param string $key
     *
     * @return mixed|NULL
     */
    public function getOption($key)
    {
        return $this->_hasOption($key) ? $this->_options[$key] : null;
    }

    /**
     * Permite que uma classe que estenda CoreExt_Validate_Abstract a definir
     * valores de opções pré-definidos adequados ao caso específico.
     *
     * @return array
     */
    abstract protected function _getDefaultOptions();

    /**
     * @see CoreExt_Validate_Interface#isValid($value)
     */
    public function isValid($value)
    {
        $this->_value = $value;
        $value = $this->_sanitize($value);

        if (true == $this->getOption('trim') && is_string($value)) {
            $value = trim($value);
        }

        $this->_sanitized = $value;

        if (true == $this->getOption('required') && $this->_isEmpty($value)) {
            throw new Exception($this->_requiredMessage);
        }

        return $this->_validate($value);
    }

    /**
     * Toda classe que estende CoreExt_Validate_Abstract deve implementar esse
     * método com a lógica de validação adequada.
     *
     * @param string $value
     *
     * @return bool
     */
    abstract protected function _validate($value);

    /**
     * Realiza uma sanitização
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function _sanitize($value)
    {
        return $value;
    }

    /**
     * Verifica se um dado valor está vazio.
     *
     * Como vazio, entende-se string vazia (''), array sem itens (array()), o
     * valor NULL e zero (0) numérico.
     *
     * @param mixed $value
     *
     * @return bool
     */
    protected function _isEmpty($value)
    {
        return in_array(
            $value,
            ['', [], null],
            true
        );
    }

    /**
     * Retorna uma mensagem de erro configurada em $_options.
     *
     * A mensagem de erro pode ser uma string ou um array. Se for uma string,
     * ocorrerá a substituição dos placeholders. Se for um array, deverá ser
     * especificado duas mensagens de erro, uma para a forma singular e outra
     * para o plural. O placeholder @value será verificado para definir se a
     * mensagem deve ser formatada no plural ou no singular.
     *
     * Exemplo de array de mensagem de erro que usa variante de número:
     *
     * <code>
     * <?php
     * $message = array(
     *   array(
     *     'singular' => '@value problema encontrado.'
     *     'plural'   => '@value problemas encontrados.'
     *   )
     * );
     *
     * // Iria imprimir:
     * // singular (@value = 1): 1 problema encontrado
     * // plural (@value = 4): 4 problemas encontrados
     * </code>
     *
     * @param array|string $key     O identificador da mensagem no array $_options
     * @param array        $options Array associativo para substituição de valores
     *
     * @return string
     *
     * @todo   Implementar substituição com formatação padrão, semelhante ao
     *   a função Drupal {@link http://api.drupal.org/t t()}.
     * @todo   Implementar formatação singular/plural em uma classe diferente,
     *         como método público, permitindo realizar o teste.
     */
    protected function _getErrorMessage($key, array $options = [])
    {
        $message = $this->getOption($key);

        if (is_array($message)) {
            // Verifica o tipo de @value para determinar a quantidade de $count
            if (is_array($options['@value'])) {
                $count = count($options['@value']);
            } elseif (is_numeric($options['@value'])) {
                $count = count($options['@value']);
            } else {
                $count = 1;
            }

            if (1 < $count) {
                $message = $message['plural'];
                $options['@value'] = implode(', ', $options['@value']);
            } else {
                $message = $message['singular'];
                $options['@value'] = array_shift($options['@value']);
            }
        }

        return strtr($message, $options);
    }

    /**
     * @see CoreExt_Validate_Interface#getValue()
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @see CoreExt_Validate_Interface#getSanitizedValue()
     */
    public function getSanitizedValue()
    {
        return $this->_sanitized;
    }
}
