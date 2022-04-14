<?php

namespace iEducar\Support\Output;

use App\Contracts\Output;

class NullOutput implements Output
{
    public function progressAdvance()
    {
        return;
    }

    public function info($message)
    {
        return;
    }

    public function progressStart($max)
    {
        return;
    }

    public function progressFinish()
    {
        return;
    }
}
