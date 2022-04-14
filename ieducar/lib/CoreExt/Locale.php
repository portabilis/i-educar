<?php

class CoreExt_Locale extends CoreExt_Singleton
{
    /**
     * @var string
     */
    protected $_culture = 'en_US';

    /**
     * Culture padrão para en_US, para evitar problemas com cálculos com
     * números de precisão arbitrária.
     *
     * @var string
     */
    protected $_defaultCulture = 'en_US';

    /**
     * Cache de informações sobre um culture.
     *
     * @var array
     */
    protected $_cultureInfo = [];

    /**
     * Culture configurada atualmente. Informacional.
     *
     * @var string
     */
    public $actualCulture = 'en_US';

    /**
     * @see CoreExt_Singleton#getInstance()
     */
    public static function getInstance()
    {
        $instance = self::_getInstance(__CLASS__);
        $instance->setLocale($instance->getCulture());

        return $instance;
    }

    /**
     * Setter.
     *
     * @param string $culture
     *
     * @return CoreExt_Locale Provê interface fluída
     */
    public function setCulture($culture)
    {
        $this->_culture = $culture;

        return $this;
    }

    /**
     * Getter.
     *
     * @return string
     */
    public function getCulture()
    {
        return $this->_culture;
    }

    /**
     * Setter.
     *
     * Configura o locale para uma cultura especifica ou usa o valor corrente
     * da classe.
     *
     * Tenta usar um locale  (por ser padrão do banco de dados e da
     * aplicação). Como fallback, usa o locale genérico C e um UTF-8 para
     * LC_NUMERIC.
     *
     * Como não existe consistência na nomenclarura dos encodings entre os
     * sistemas operacionais, tenta variações como  e ISO_8859_1.
     *
     * @link http://linux.die.net/man/3/setlocale Linux setlocale(3) man page
     *
     * @param string|NULL $culture
     */
    public function setLocale($culture = null)
    {
        if (is_null($culture)) {
            $culture = $this->getCulture();
        }

        $actualCulture = $this->_setLocale(LC_ALL, [$culture . '.',
      $culture . '.', $culture . '.ISO88591', $culture . '.iso88591',
      $culture . '.ISO8859-1', $culture . '.iso8859-1', $culture . 'ISO_8859_1',
      $culture . '.iso_8859_1', $culture . '.ISO8859_1', $culture . '.iso8859_1']);

        if (false == $actualCulture) {
            $actualCulture = [];
            $actualCulture['LC_ALL']     = $this->_setlocale(LC_ALL, ['C']);
            $actualCulture['LC_NUMERIC'] = $this->_setlocale(LC_NUMERIC, [$culture.'.UTF-8',
        $culture . '.UTF8', $culture . '.utf-8', $culture . '.utf8',
        $culture . '.UTF_8', $culture . '.utf_8']);
        }

        $this->actualCulture = $actualCulture;

        if (!isset($this->_cultureInfo[$culture])) {
            $this->_cultureInfo[$culture] = localeconv();
        }
    }

    /**
     * Chama a função setlocale().
     *
     * @param string $category
     * @param array  $locale
     *
     * @return NULL|string Retorna NULL em caso de erro
     */
    protected function _setLocale($category, array $locale)
    {
        return setlocale($category, $locale);
    }

    /**
     * Reseta o locale para en_US.
     */
    public function resetLocale()
    {
        $this->setLocale($this->_defaultCulture);
    }

    /**
     *
     * @param string|NULL $index
     *
     * @return array|string
     */
    public function getCultureInfo($index = null)
    {
        $info = localeconv();
        if (null != $index && isset($info[$index])) {
            $info = $info[$index];
        }

        return $info;
    }
}
