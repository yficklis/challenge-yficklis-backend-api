<?php

namespace App\Http\Controllers;

use InvalidArgumentException;

class RepositoriesController extends Controller
{
    public function index()
    {
    }

    public function show($user): string
    {
        try {
            if (empty($user)) {
                throw new InvalidArgumentException('Empty name user or Invalid, please try again!');
            }
            return json_encode($this->listRepositories($user));
        } catch (InvalidArgumentException $th) {
            return json_encode(array('error' => $th->getMessage()));
        }
    }

    public static function optionsHTTPS(): array
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

    public function getStarredByUserOnGitHub($user): array
    {
        $context = stream_context_create(self::optionsHTTPS());
        $content = file_get_contents("https://api.github.com/users/{$user}/starred", false, $context);
        return json_decode($content);
    }

    public function listRepositories($user): array
    {
        $repositories = $this->getStarredByUserOnGitHub($user);
        if (empty($repositories)) {
            throw new InvalidArgumentException("This user doesn't have any Stars!");
        }

        foreach ($repositories as $key => $repository) {
            $dataReturn[] = array(
                'repository_id' => $repository->id,
                'repository_name' => $repository->name,
                'description' => $repository->description,
                'http_url' => $repository->html_url,
                'language' => $repository->language
            );
        }

        return $dataReturn;
    }
}
