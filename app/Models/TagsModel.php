<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use App\Models\RepositoriesModel;
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
            $IdsRepository = $this->getIdRepositoryByTagName($name);
            return json_encode($this->getRepositoriesByTag($IdsRepository));
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }

    private function getIdRepositoryByTagName($name): object
    {
        return $this->select('id_repository', 'name')->where('name', 'like', '%' . $name . '%')->get();
    }

    private function getRepositoriesByTag($IdsRepository): array
    {
        if (empty($IdsRepository)) {
            return array('error' => 'This name tag does not exist.');
        }

        $repositoriesByTag = array();

        foreach ($IdsRepository as $id) {
           $repositoriesByTag[] = $this->findSearchRepositoriesById($id->id_repository, $id->name);
        }

        return (empty($repositoriesByTag)) ? array('error' => 'This name tag does not exist.') : $repositoriesByTag;
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
            $this->getRepositoriesToCheckUser();
            $this->getRepositoriesToCheckIdRepository();
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

    private function getRepositoriesToCheckUser(): void
    {
        $repositories = (new RepositoriesModel())->get();
        $arrUsername = array();
        foreach ($repositories as  $repository) {
            $arrUsername[] = $repository->username;
        }

        if (!in_array(trim(request('created_tag_by_username')), $arrUsername)) {
            $repositories = json_encode((new RepositoriesModel())->listRepositories(request('created_tag_by_username')));
            (new RepositoriesModel())->saveRepositories($repositories);
        }
    }

    private function getRepositoriesToCheckIdRepository(): void
    {
        $query = RepositoriesModel::where('username', '=', request('created_tag_by_username'))->get();
        $arrIdRepository = array();
        foreach ($query as $repositoriesByUser) {
            $arrIdRepository[] = $repositoriesByUser->repository_id;
        }

        if (!in_array(trim(request('id_repository')), $arrIdRepository)) {
            throw new InvalidArgumentException("This user is not linked with this ID.");
        }
    }

    private function findSearchRepositoriesById($id, $name): array
    {
        $repository = RepositoriesModel::where('repository_id', '=', $id)->get();
        foreach ($repository as $dataRepositor) {
            return [
                'repository_id' => $dataRepositor->repository_id,
                'repository_name' => $dataRepositor->repository_name,
                'description' => $dataRepositor->description,
                'http_url' => $dataRepositor->http_url,
                'language' => $dataRepositor->language,
                'username' => $dataRepositor->username,
                'tagName' => $name
            ];
        }
        return array();
    }

}
