<?php

namespace iEducar\Support\Output;

use App\Contracts\Output;
use Illuminate\Console\OutputStyle;

class CommandOutput implements Output
{
    /**
     * @var OutputStyle
     */
    private $output;

    public function __construct(OutputStyle $outputStyle)
    {
        $this->output = $outputStyle;
    }


    public function progressAdvance()
    {
        $this->output->progressAdvance();
    }

    public function info($message)
    {
        $this->output->writeln($message);
    }
}
