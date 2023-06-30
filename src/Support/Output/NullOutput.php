<?php

namespace iEducar\Support\Output;

use App\Contracts\Output;

class NullOutput implements Output
{
    public function progressAdvance()
    {

    }

    public function info($message)
    {

    }

    public function progressStart($max)
    {

    }

    public function progressFinish()
    {

    }
}
