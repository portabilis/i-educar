<?php

interface CoreExt_Validate_Validatable
{
    /**
     * Retorna TRUE caso a propriedade seja válida.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isValid($key = '');

    /**
     * Configura um CoreExt_Validate_Interface para uma propriedade da classe.
     *
     * @param string                     $key
     * @param CoreExt_Validate_Interface $validator
     *
     * @return CoreExt_Validate_Validatable Provê interface fluída
     */
    public function setValidator($key, CoreExt_Validate_Interface $validator);

    /**
     * Retorna a instância CoreExt_Validate_Interface para uma propriedade da
     * classe ou NULL caso nenhum validador esteja atribuído.
     *
     * @param string $key
     *
     * @return CoreExt_Validate_Interface|NULL
     */
    public function getValidator($key);
}
