<?php

class CoreExt_Validate_Choice extends CoreExt_Validate_Abstract
{
    /**
     * @see CoreExt_Validate_Abstract#_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        $options = [
            'choices' => [],
            'multiple' => false,
            'trim' => false,
            'choice_error' => 'A opção "@value" não existe.',
        ];

        $options['multiple_error'] = [
            'singular' => $options['choice_error'],
            'plural' => 'As opções "@value" não existem.'
        ];

        return $options;
    }

    /**
     * @see CoreExt_Validate_Abstract#_validate($value)
     */
    protected function _validate($value)
    {
        if ($this->_hasOption('choices')) {
            $value = $this->_getStringArray($value);
            $choices = $this->_getStringArray($this->getOption('choices'));

            if ($this->_hasOption('multiple') && false == $this->getOption('multiple')) {
                if (in_array($value, $choices, true)) {
                    return true;
                }
                throw new Exception($this->_getErrorMessage('choice_error', ['@value' => $this->getSanitizedValue()]));
            } else {
                if (in_array($value, [$choices], true)) {
                    return true;
                }
                throw new Exception(
                    $this->_getErrorMessage(
                        'multiple_error',
                        ['@value' => array_diff($value, $this->getOption('choices'))]
                    )
                );
            }
        }

        return true;
    }

    /**
     * Retorna um array de strings ou um valor numérico como string.
     *
     * @param array|int|float $value
     *
     * @return array|string
     */
    protected function _getStringArray($value)
    {
        if (is_array($value)) {
            $return = [];

            foreach ($value as $v) {
                $return[] = (string) $v;
            }

            return $return;
        }

        return (string) $value;
    }
}
