<?php

namespace App\Http\Controllers;

use InvalidArgumentException;
use App\Models\RepositoriesModel;

class RepositoriesController extends Controller
{
    public function index()
    {
    }

    public function show($user): string
    {
        try {
            $repositories = json_encode((new RepositoriesModel())->listRepositories($user));
            (new RepositoriesModel())->saveRepositories($repositories);
            return $repositories;
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }
}
