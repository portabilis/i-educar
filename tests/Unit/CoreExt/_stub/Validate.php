<?php

class CoreExt_ValidateStub extends CoreExt_Validate_Abstract
{
    protected function _getDefaultOptions()
    {
        return [];
    }

    protected function _validate($value)
    {
        return true;
    }
}
