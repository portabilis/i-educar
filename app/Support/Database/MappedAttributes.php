<?php

namespace App\Support\Database;

trait MappedAttributes
{
    /**
     * @return array
     */
    public function getMappedAttributes()
    {
        return [];
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getMappedAttribute($key)
    {
        $maps = array_flip($this->getMappedAttributes() ?? []);

        return array_key_exists($key, $maps)
            ? $maps[$key]
            : $key;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getTranslateMappedAttribute($key)
    {
        $maps = $this->getMappedAttributes();

        return array_key_exists($key, $maps)
            ? $maps[$key]
            : $key;
    }

    /**
     * Get an attribute from the model.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        $key = $this->getMappedAttribute($key);

        return parent::getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        $key = $this->getMappedAttribute($key);

        return parent::setAttribute($key, $value);
    }
}
