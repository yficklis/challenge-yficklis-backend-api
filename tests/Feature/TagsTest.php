<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\TagsModel;

class TagsTest extends TestCase
{
    use RefreshDatabase;
    /**
     *
     * @return void
     */

    public function testTagsShouldThrowAnErrorIfNotOk()
    {

        $this->get('api/tags')
            ->assertStatus(self::HTTP_OK);
    }

    public function testTagsShouldThrowAnErrorIfNameMissing()
    {

        $this->post('api/tags',  ['name' => ''])
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['name']]);
    }

    public function testTagsShouldThrowAnErrorIfCreatedTagByUsernameMissing()
    {

        $this->post('api/tags',  ['created_tag_by_username' => ''])
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['created_tag_by_username']]);
    }

    public function testTagsShouldThrowAnErrorIfIdRepositoryMissing()
    {

        $this->post('api/tags',  ['id_repository' => ''])
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['id_repository']]);
    }

    public function testTagsShouldThrowAnErrorIfUniqueTagMissing()
    {

        $tagTest = [
            'name' => 'tagTest',
            'description' => 'Tag Test Description',
            'id_repository' => 107111421,
            'created_tag_by_username' => 'mlanes',
        ];

        TagsModel::create($tagTest);

        $this->post('api/tags',  $tagTest)
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['uniqueTag']]);
    }

    public function testTagsShouldThrowAnErrorIfUniqueIdMissing()
    {

        $tagTest = [
            'name' => 'tagTest',
            'description' => 'Tag Test Description',
            'id_repository' => 23211,
            'created_tag_by_username' => 'mlanes',
        ];

        TagsModel::create($tagTest);

        $this->post('api/tags',  $tagTest)
            ->assertStatus(self::HTTP_OK)
            ->assertJsonStructure(['errors' => ['userNotExistId']]);
    }

    public function testTagsShouldReturnSuccessIfAllGoesWell()
    {

        $tagTest = [
            'name' => 'tagTest3',
            'description' => 'Tag Test Description',
            'id_repository' => 107111421,
            'created_tag_by_username' => 'yficklis',
        ];

        TagsModel::create($tagTest);

        $this->post('api/tags',  $tagTest)
            ->assertStatus(self::HTTP_OK)
            ->assertJson(['data'=>['name'=>$tagTest['name']]]);
    }
}
