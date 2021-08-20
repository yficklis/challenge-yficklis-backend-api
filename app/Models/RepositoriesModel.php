<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class RepositoriesModel extends Model
{
    use HasFactory;

    public $table = 'repositories';

    protected $fillable = [
        'repository_id',
        'repository_name',
        'description',
        'http_url',
        'language',
        'username'
    ];

    private static function optionsHTTPS(): array
    {
        return [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ];
    }

    private function getStarredByUserOnGitHub($user): array
    {
        try {
            $context = stream_context_create(self::optionsHTTPS());
            $content = file_get_contents("https://api.github.com/users/{$user}/starred", false, $context);
            return json_decode($content);
        } catch (\Throwable $th) {
            throw new InvalidArgumentException(json_encode(['invalid' => 'Empty name user or Invalid, please try again!']));
        }
    }

    public function listRepositories($user): array
    {
        $repositories = $this->getStarredByUserOnGitHub($user);
        if (empty($repositories)) {
            throw new InvalidArgumentException(json_encode(['emptyStars' => "This user doesn't have any Stars!"]));
        }
        $dataReturn = array();
        foreach ($repositories as $key => $repository) {
            $dataReturn[] = array(
                'repository_id' => $repository->id,
                'repository_name' => $repository->name,
                'description' => $repository->description,
                'http_url' => $repository->html_url,
                'language' => $repository->language,
                'username' => $user
            );
        }

        return $dataReturn;
    }

    public function saveRepositories($repositories): void
    {
        if (empty($repositories)) {
            throw new InvalidArgumentException(json_encode(['emptyStars' => "This user doesn't have any Stars!"]));
        }

        $repositories = json_decode($repositories);
        foreach ($repositories as $repository) {
            if (!$this->checkDirectoryDuplication($repository->repository_id)) {
                $this->create([
                    'repository_id' => $repository->repository_id,
                    'repository_name' => $repository->repository_name,
                    'description' => $repository->description,
                    'http_url' => $repository->http_url,
                    'language' => $repository->language,
                    'username' => $repository->username,
                ]);
            }
        }
    }

    private function checkDirectoryDuplication($repositoryId): bool
    {
        if (empty($repositoryId)) {
            throw new InvalidArgumentException(json_encode(['requiredRepository' => "The repository is required to validate"]));
        }

        $repositories = array();

        foreach ($this->get() as  $repository) {
            $repositories[] = $repository->repository_id;
        }
        return in_array($repositoryId, $repositories);
    }
}
