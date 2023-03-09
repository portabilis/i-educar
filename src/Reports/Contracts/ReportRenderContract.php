<?php

namespace iEducar\Reports\Contracts;

interface ReportRenderContract
{
    public function render(array $data);
}
