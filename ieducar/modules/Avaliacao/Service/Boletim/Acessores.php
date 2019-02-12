<?php

trait Avaliacao_Service_Boletim_Acessores
{
    /**
     * @var array
     */
    protected $_options = [
        'matricula' => null,
        'etapas' => null,
        'usuario' => null
    ];

    /**
     * @see CoreExt_Configurable::getOptions()
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * @see CoreExt_Configurable::setOptions()
     *
     * @param array $options
     *
     * @return $this
     *
     * @throws CoreExt_Service_Exception
     */
    public function setOptions(array $options = [])
    {
        if (!isset($options['matricula'])) {
            require_once 'CoreExt/Service/Exception.php';
            throw new CoreExt_Service_Exception('É necessário informar o número de matrícula do aluno.');
        }

        if (isset($options['ComponenteDataMapper'])) {
            $this->setComponenteDataMapper($options['ComponenteDataMapper']);
            unset($options['ComponenteDataMapper']);
        }

        if (isset($options['ComponenteTurmaDataMapper'])) {
            $this->setComponenteTurmaDataMapper($options['ComponenteTurmaDataMapper']);
            unset($options['ComponenteTurmaDataMapper']);
        }

        if (isset($options['RegraDataMapper'])) {
            $this->setRegraDataMapper($options['RegraDataMapper']);
            unset($options['RegraDataMapper']);
        }

        if (isset($options['NotaAlunoDataMapper'])) {
            $this->setNotaAlunoDataMapper($options['NotaAlunoDataMapper']);
            unset($options['NotaAlunoDataMapper']);
        }

        if (isset($options['NotaComponenteDataMapper'])) {
            $this->setNotaComponenteDataMapper($options['NotaComponenteDataMapper']);
            unset($options['NotaComponenteDataMapper']);
        }

        if (isset($options['NotaComponenteMediaDataMapper'])) {
            $this->setNotaComponenteMediaDataMapper($options['NotaComponenteMediaDataMapper']);
            unset($options['NotaComponenteMediaDataMapper']);
        }

        if (isset($options['FaltaAlunoDataMapper'])) {
            $this->setFaltaAlunoDataMapper($options['FaltaAlunoDataMapper']);
            unset($options['FaltaAlunoDataMapper']);
        }

        if (isset($options['FaltaAbstractDataMapper'])) {
            $this->setFaltaAbstractDataMapper($options['FaltaAbstractDataMapper']);
            unset($options['FaltaAbstractDataMapper']);
        }

        if (isset($options['ParecerDescritivoAlunoDataMapper'])) {
            $this->setParecerDescritivoAlunoDataMapper($options['ParecerDescritivoAlunoDataMapper']);
            unset($options['ParecerDescritivoAlunoDataMapper']);
        }

        if (isset($options['ParecerDescritivoAbstractDataMapper'])) {
            $this->setParecerDescritivoAbstractDataMapper($options['ParecerDescritivoAbstractDataMapper']);
            unset($options['ParecerDescritivoAbstractDataMapper']);
        }

        if (isset($options['NotaGeralAbstractDataMapper'])) {
            $this->setNotaGeralAbstractDataMapper($options['NotaGeralAbstractDataMapper']);
            unset($options['NotaGeralAbstractDataMapper']);
        }

        if (isset($options['NotaGeralDataMapper'])) {
            $this->setNotaGeralDataMapper($options['NotaGeralDataMapper']);
            unset($options['NotaGeralDataMapper']);
        }

        if (isset($options['MediaGeralDataMapper'])) {
            $this->setMediaGeralDataMapper($options['MediaGeralDataMapper']);
            unset($options['MediaGeralDataMapper']);
        }

        if (isset($options['componenteCurricularId'])) {
            $this->setComponenteCurricularId($options['componenteCurricularId']);
            unset($options['componenteCurricularId']);
        }

        $defaultOptions = array_keys($this->getOptions());
        $passedOptions = array_keys($options);

        if (0 < count(array_diff($passedOptions, $defaultOptions))) {
            throw new InvalidArgumentException(
                sprintf('A classe %s não suporta as opções: %s.', get_class($this), implode(', ', $passedOptions))
            );
        }

        $this->_options = array_merge($this->getOptions(), $options);

        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->_options[$key];
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->_options[$key] = $value;

        return $this;
    }
}
