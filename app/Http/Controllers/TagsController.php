<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagsModel;

class TagsController extends Controller
{
    public function index()
    {
        return TagsModel::all();
    }

    public function show($id)
    {
    }

    public function store(Request $request): string
    {
        return (new TagsModel())->createNewTag($request->all());
    }

    public function update(Request $request, $id): string
    {
        return (new TagsModel())->editTag($request->all(), $id);
    }

    public function destroy($id): string
    {
        return (new TagsModel)->deleteTagIfExists($id);
    }

    public function search($name): string
    {
        return (new TagsModel())->tagAssignedToRepository($name);
    }
}
