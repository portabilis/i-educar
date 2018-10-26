<?php

class Mimetype
{
    public function getType($filename)
    {
        $filename = basename($filename);
        $filename = explode('.', $filename);
        $filename = $filename[count($filename)-1];

        return $this->privFindType($filename);
    }

    protected function privFindType($ext)
    {
        $mimetypes = $this->privBuildMimeArray();

        if (isset($mimetypes[$ext])) {
            return $mimetypes[$ext];
        }

        return false;
    }

    protected function privBuildMimeArray()
    {
        return [
            'doc' => 'application/msword',
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'pdf' => 'application/pdf',
            'xls' => 'application/vnd.ms-excel',
        ];
    }
}
