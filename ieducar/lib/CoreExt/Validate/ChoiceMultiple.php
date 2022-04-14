<?php

class CoreExt_Validate_ChoiceMultiple extends CoreExt_Validate_Choice
{
    /**
     * @see CoreExt_Validate_Choice::_getDefaultOptions()
     */
    protected function _getDefaultOptions()
    {
        return array_merge(
            parent::_getDefaultOptions(),
            ['multiple' => true]
        );
    }
}
