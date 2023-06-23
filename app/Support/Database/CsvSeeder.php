<?php

namespace App\Support\Database;

use Exception;
use Generator;
use Illuminate\Database\Seeder;

abstract class CsvSeeder extends Seeder
{
    /**
     * CSV filename.
     *
     * @var string
     */
    protected $filename;

    /**
     * Model name.
     *
     * @var string
     */
    protected $model;

    /**
     * Return CSV filename.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getCsvFilename()
    {
        if (empty($this->filename)) {
            throw new Exception('CSV file not defined.');
        }

        return $this->filename;
    }

    /**
     * Return model name.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getModelName()
    {
        if (empty($this->model)) {
            throw new Exception('Model name not defined.');
        }

        return $this->model;
    }

    /**
     * Read a CSV file and return your content into a generator.
     *
     * @return Generator
     *
     * @throws Exception
     */
    public function read()
    {
        $file = file($this->getCsvFilename());

        $header = str_getcsv(array_shift($file));

        foreach ($file as $line) {
            $data = str_getcsv($line);
            $data = array_map(function ($item) {
                return $item === '' ? null : $item;
            }, $data);

            yield array_combine($header, $data);
        }
    }

    /**
     * Run seed.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        $model = $this->getModelName();
        $model = new $model();

        foreach ($this->read() as $data) {
            $model->newQuery()->updateOrCreate($data);
        }
    }
}
