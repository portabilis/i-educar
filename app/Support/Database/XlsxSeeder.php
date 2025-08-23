<?php

namespace App\Support\Database;

use App\Exceptions\XlsxException;
use Generator;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

abstract class XlsxSeeder extends Seeder
{
    /**
     * XLSX filename.
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
     * Sheet index to read (0-based).
     *
     * @var int
     */
    protected $sheetIndex = 0;

    /**
     * Starting row (1-based, usually 2 to skip header).
     *
     * @var int
     */
    protected $startRow = 2;

    /**
     * Return XLSX filename.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getXlsxFilename()
    {
        if (empty($this->filename)) {
            throw new XlsxException('XLSX file not defined.');
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
            throw new XlsxException('Model name not defined.');
        }

        return $this->model;
    }

    /**
     * Read an XLSX file and return content as generator.
     *
     * @return Generator
     *
     * @throws Exception
     */
    public function read()
    {
        $spreadsheet = IOFactory::load($this->getXlsxFilename());
        $worksheet = $spreadsheet->getSheet($this->sheetIndex);

        // Get header from first row
        $header = $this->getHeaderRow($worksheet);

        // Get highest row number
        $highestRow = $worksheet->getHighestRow();

        for ($row = $this->startRow; $row <= $highestRow; $row++) {
            $data = [];

            for ($col = 'A'; $col <= $worksheet->getHighestColumn(); $col++) {
                $cellValue = $worksheet->getCell($col . $row)->getCalculatedValue();
                $data[] = $cellValue === '' ? null : $cellValue;
            }

            // Only yield if row has data
            if (!empty(array_filter($data, fn ($value) => $value !== null))) {
                yield array_combine($header, $data);
            }
        }
    }

    /**
     * Get header row from worksheet.
     */
    protected function getHeaderRow(Worksheet $worksheet): array
    {
        $header = [];
        $headerRow = $this->startRow - 1; // Header is usually one row before start

        for ($col = 'A'; $col <= $worksheet->getHighestColumn(); $col++) {
            $cellValue = $worksheet->getCell($col . $headerRow)->getCalculatedValue();
            $header[] = $cellValue;
        }

        return $header;
    }

    /**
     * Transform data before saving.
     * Override this method to customize data transformation.
     */
    protected function transformData(array $data): array
    {
        return $data;
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
        $modelInstance = new $model;

        foreach ($this->read() as $data) {
            $transformedData = $this->transformData($data);
            if (!empty($transformedData)) {
                $modelInstance->newQuery()->updateOrCreate($transformedData);
            }
        }
    }
}
