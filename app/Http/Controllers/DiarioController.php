<?php

namespace App\Http\Controllers;

use App\Services\DiarioControllerService;

class DiarioController extends Controller
{
    public function __construct(
        protected DiarioControllerService $service
    ) {}
}
