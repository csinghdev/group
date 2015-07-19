<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Input;
use Sorskod\Larasponse\Larasponse;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    protected $response;

    function __construct(Larasponse $response)
    {
        $this->response = $response;

        if(Input::has('includes'))
        {
            $this->response->parseIncludes(Input::get('includes'));
        }
    }
}
