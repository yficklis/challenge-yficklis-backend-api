<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TagsModel;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    public function index()
    {
    }

    public function show($id)
    {
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:tags',
            'id_repository' => 'required',
            'created_tag_by_username' => 'required'
        ], array(
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute field must be unique.'
        ));

        if ($validator->fails()) {
            $errors = $validator->errors();


            if (!empty($errors->first('name'))) {
                echo $errors->first('name');
                die;
            }

            if (!empty($errors->first('id_repository'))) {
                echo $errors->first('id_repository');
                die;
            }

            if (!empty($errors->first('created_tag_by_username'))) {
                echo $errors->first('created_tag_by_username');
                die;
            }
        }

        $TagsModel = new TagsModel();
        $TagsModel->name = request('name');
        $TagsModel->description = request('description');
        $TagsModel->id_repository = request('id_repository');
        $TagsModel->created_tag_by_username = request('created_tag_by_username');
        $TagsModel->save();
        return $TagsModel;
    }

    public function update(Request $request)
    {
    }

    public function destroy($id)
    {
    }
}
