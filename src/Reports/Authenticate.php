<?php

namespace iEducar\Reports;

trait Authenticate
{
    public function authenticate()
    {
        return in_array('autenticar', $this->args) && $this->args['autenticar'] === true;
    }
}
