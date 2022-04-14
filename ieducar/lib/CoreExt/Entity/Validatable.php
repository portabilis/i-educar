<?php

interface CoreExt_Entity_Validatable extends CoreExt_Validate_Validatable
{
    /**
     * Configura uma coleção de CoreExt_Validate_Interface na instância.
     *
     * @return CoreExt_Entity Provê interface fluída
     */
    public function setValidatorCollection(array $validators);

    /**
     * Retorna um array de itens CoreExt_Validate_Interface da instância.
     *
     * @return array
     */
    public function getValidatorCollection();

    /**
     * Retorna um array de CoreExt_Validate_Interface padrão para as propriedades
     * de CoreExt_Entity.
     *
     * Cada item do array precisa ser um item associativo com o mesmo nome do
     * atributo público definido pelo array $_data:
     *
     * <code>
     * <?php
     * // Uma classe concreta de CoreExt_Entity com as propriedades públicas
     * // nome e telefone poderia ter os seguintes validadores.
     * array(
     *   'nome' => new CoreExt_Validate_Alpha(),
     *   'telefone' => new CoreExt_Validate_Alphanum()
     * );
     * </code>
     *
     * @return array
     */
    public function getDefaultValidatorCollection();
}
