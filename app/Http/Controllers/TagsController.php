<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagsModel;

class TagsController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/tags", tags = {"TAGS"}, summary="List of all tags registered.",
     *     @OA\Response(response="200", description="ok")
     * )
     */

    public function index()
    {
        return TagsModel::all();
    }

    public function show($id)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/tags", tags = {"TAGS"}, summary="Create a new tag by id repository.",
     * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   description="Tag name",
     *                   type="string"
     *                ),
     *                @OA\Property(
     *                   property="description",
     *                   description="Description about tag (Opcional)",
     *                   type="text"
     *                ),
     *                @OA\Property(
     *                   property="id_repository",
     *                   description="ID of the GitHub Repository",
     *                   type="number"
     *                ),
     *                @OA\Property(
     *                   property="created_tag_by_username",
     *                   description="Username of the Github",
     *                   type="string"
     *                ),
     *           )
     *       )
     *   ),
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function store(Request $request): string
    {
        return (new TagsModel())->createNewTag($request->all());
    }

    /**
     * @OA\Put(
     *     path="/api/tags/{id}", tags = {"TAGS"}, summary="Updated the attributes of the tag by ID.",
     * @OA\Parameter(
     *         description="ID Tag",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema( type="integer", format="int64")
     *     ),
     * * @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="name",
     *                   description="Tag name ",
     *                   type="string"
     *                ),
     *                @OA\Property(
     *                   property="description",
     *                   description="Description about tag (Opcional)",
     *                   type="string"
     *                ),
     *                @OA\Property(
     *                   property="id_repository",
     *                   description="ID of the GitHub Repository (Opcional)",
     *                   type="number"
     *                ),
     *                @OA\Property(
     *                   property="created_tag_by_username",
     *                   description="Username of the Github (Opcional)",
     *                   type="string"
     *                ),
     *           )
     *       )
     *   ),
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function update(Request $request, $id): string
    {
        return (new TagsModel())->editTag($request->all(), $id);
    }

    /**
     * @OA\Delete(
     *     path="/api/tags/{id}", tags = {"TAGS"}, summary="Delete the tag by ID",
     * @OA\Parameter(
     *         description="ID Tag",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema( type="integer", format="int64")
     *     ),
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function destroy($id): string
    {
        return (new TagsModel)->deleteTagIfExists($id);
    }

    /**
     * @OA\Get(
     *     path="/api/tags/search/{name}", tags = {"TAGS"}, summary="Search by a specific tag name.",
     * @OA\Parameter(
     *         description="Name tag",
     *         in="path",
     *         name="name",
     *         required=true,
     *         @OA\Schema( type="string")
     *     ),
     *     @OA\Response(response="200", description="ok")
     * )
     */
    public function search($name): string
    {
        return (new TagsModel())->tagAssignedToRepository($name);
    }
}
