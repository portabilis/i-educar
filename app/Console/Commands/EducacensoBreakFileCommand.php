<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EducacensoBreakFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Educacenso:break {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Break in file CENSO in new files to each record 00';
    private $filename;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = ($this->argument('filename'));

        if (! $this->openFile($filename)) {
            dd('Não foi possivel abrir o Arquivo');
            return false;
        }

        $string = '';
        $file = 1;
        $filename = fopen($filename, 'r');
        $delimiter = '00|';
        $pointer = 1;

        while (true) {
            echo $file." Arquivo \n";
            echo $pointer." Linha \n";
            $line = fgets($filename, $pointer);

            if ((substr($line, 0, 3) != '00|')) {
                $string .= $line;
            } else {
                $newFile = fopen('insert'.$file.'.txt','w+');
                if ($newFile == false) {
                    dd('Não foi possível criar o arquivo.');
                    return false;
                }
                fwrite($newFile, $string);
                $file++;
                $string='';
            }

        $pointer++;
        } fclose($filename);
    }

    public function openFile($filename)
    {
        if (! $filename = fopen($filename,'r')) {
            return false;
        }

        if ($filename == false) {
            return false;
        }

        return true;
    }

}