<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use App\Http\Controllers\RepositoriesController;
use Illuminate\Support\Facades\Validator;

class TagsModel extends Model
{
    use HasFactory;

    public $table = 'tags';

    protected $fillable = [
        'name',
        'description',
        'id_repository',
        'created_tag_by_username',
    ];

    public function tagAssignedToRepository($name): string
    {
        try {
            $tags = $this->getTagsByName($name);
            return json_encode($this->getRepositoriesUsersByTag($tags));
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }

    private function getTagsByName($name): object
    {
        return $this->where('name', 'like', '%' . $name . '%')->get();
    }

    private function getRepositoriesUsersByTag($tags): array
    {
        if (empty($tags)) {
            return json_encode(array('error' => 'This name tag does not exist.'));
        }

        $repositoriesAssignedTag = array();

        foreach ($tags as $tag) {
            $repositories = (new RepositoriesModel())->listRepositories($tag->created_tag_by_username);
            if (empty($repositories)) {
                throw new InvalidArgumentException("This user doesn't have any Stars!");
            }

            $repositoriesAssignedTag = $this->distinctRepositoryById($tag->id_repository, $repositories);
        }
        return $repositoriesAssignedTag;
    }

    private function distinctRepositoryById($id, $repositories): array
    {

        if (empty($id)) {
            throw new InvalidArgumentException("The ID field is required.");
        }

        if (empty($repositories)) {
            throw new InvalidArgumentException("The Repositories array field is required.");
        }

        $arrRepositoryByUser = array();
        foreach ($repositories as  $repository) {
            if ($id == $repository['repository_id']) {
                $arrRepositoryByUser[$id] = $repository;
            }
        }
        return $arrRepositoryByUser;
    }


    private function verifyDuplicatedTagByIdRepository($name, $idRepository): bool
    {
        if (empty($name)) {
            throw new InvalidArgumentException("The name tag is required to virify duplicated tag.");
        }

        if (empty($idRepository)) {
            throw new InvalidArgumentException("The repository id is required to virify duplicated tag.");
        }

        $tagsById = array();

        foreach ($this->get() as  $myTags) {
            if ($myTags->id_repository == $idRepository) {
                $tagsById[] = $myTags->name;
            }
        }
        return in_array($name, $tagsById);
    }


    public function createNewTag(array $request): string
    {

        try {
            $validator = Validator::make($request, [
                'name' => 'required',
                'id_repository' => 'required',
                'created_tag_by_username' => 'required'
            ], array(
                'required' => 'The :attribute field is required.',
                'unique' => 'The :attribute field must be unique.'
            ));

            if ($validator->fails()) {
                return json_encode($validator->errors()->toArray());
            }

            $validateNameTag = $this->verifyDuplicatedTagByIdRepository(trim(request('name')), request('id_repository'));
            if ($validateNameTag) {
                return json_encode(array('error' => 'The  tag name must be unique by repository.'));
            }

            $newTag = $this->create([
                'name' => trim(request('name')),
                'description' => request('description'),
                'id_repository' => request('id_repository'),
                'created_tag_by_username' => request('created_tag_by_username'),
            ]);
            return json_encode($newTag);
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }

    public function editTag(array $request, int $id)
    {
        try {
            $validateNameTag = $this->verifyDuplicatedTagByIdRepository(trim(request('name')), request('id_repository'));
            if ($validateNameTag) {
                return json_encode(array('error' => 'The tag name must be already exists in repository.'));
            }

            $tagsModel = $this->find($id);

            $success = $tagsModel->update([
                'name' => request('name'),
                'description' => request('description'),
                'id_repository' => request('id_repository'),
                'created_tag_by_username' => request('created_tag_by_username'),
            ]);

            return json_encode(['success' => $success]);
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }

    public function deleteTagIfExists($id): string
    {
        if (empty($id) or empty($this->find($id))) {
            return json_encode(array('error' => 'The tag id does not exist.'));
        }

        $this->destroy($id);
        return json_encode(array('success' => true));
    }
}
