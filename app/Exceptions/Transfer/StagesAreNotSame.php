<?php

namespace App\Exceptions\Transfer;

class StagesAreNotSame extends TransferException
{
    public function __construct()
    {
        $message = 'As escolas ou turmas trabalham com quantidade de etapas diferentes.';

        parent::__construct($message);
    }
}
