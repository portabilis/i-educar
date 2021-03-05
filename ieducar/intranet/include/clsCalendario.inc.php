<?php


    public function _generateFormValues($formValues = [], $invalidNames = [])
    {
        $ret = '';

        if (is_array($formValues) && 0 < count($formValues)) {
            foreach ($formValues as $name => $value) {
                if (in_array($name, $invalidNames)) {
                    continue;
                }

                $ret .= sprintf(
                    '<input id="cal_%s" name="%s" type="hidden" value="%s" />',
                    $name,
                    $name,
                    $value
                );
            }
        }

        return $ret;
    }
}
