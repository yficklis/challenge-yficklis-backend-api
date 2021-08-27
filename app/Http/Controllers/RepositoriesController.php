<?php

namespace App\Http\Controllers;

use InvalidArgumentException;
use App\Models\RepositoriesModel;

class RepositoriesController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/repositories",
     *  tags = {"REPOSITORIES"},
     *  summary = "List of all started.",
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function index()
    {
    }

    /**
     * @OA\Get(
     *  path="/api/repositories/{user}",
     *  tags = {"REPOSITORIES"},
     *  summary = "List of all started repositories by username.",
     * @OA\Parameter(
     *         description="Parameter with mutliple examples",
     *         in="path",
     *         name="user",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function show($user): string
    {
        try {
            $repositories = json_encode((new RepositoriesModel())->listRepositories($user));
            (new RepositoriesModel())->saveRepositories($repositories);
            return $repositories;
        } catch (InvalidArgumentException $th) {
            return json_encode(array('errors' => $th->getMessage()));
        }
    }
}
