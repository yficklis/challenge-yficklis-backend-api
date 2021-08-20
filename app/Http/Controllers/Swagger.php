<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(title="API Swagger Laravel", version="0.1")
 */
class Swagger extends Controller
{

    public function index()
    {
        return view('documentation.index');
    }

    public function config()
    {
        $openapi = \OpenApi\Generator::scan([$_SERVER['DOCUMENT_ROOT'] . '/../app/Http/Controllers/']);
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }
}
